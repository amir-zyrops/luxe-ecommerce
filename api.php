<?php

declare(strict_types=1);

require_once __DIR__ . "/includes/database.php";
require_once __DIR__ . "/includes/notifications.php";

session_name("LUXESESSID");
session_start();

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store");

try {
    $pdo = luxe_db();
    $action = $_GET["action"] ?? "";

    switch ($action) {
        case "state":
            $userId = current_user_id();
            json_response([
                "ok" => true,
                "profile" => $userId ? fetch_profile($pdo, $userId) : null,
                "cart" => $userId ? fetch_cart_items($pdo, $userId) : [],
                "wishlist" => $userId ? fetch_wishlist_items($pdo, $userId) : [],
            ]);
            break;

        case "products":
            json_response(["ok" => true, "products" => fetch_products($pdo)]);
            break;

        case "request_otp":
            require_method("POST");
            handle_request_otp($pdo, json_input());
            break;

        case "verify_otp":
            require_method("POST");
            handle_verify_otp($pdo, json_input());
            break;

        case "cart/save":
            require_method("POST");
            $cart = save_cart_items($pdo, require_user(), json_input()["items"] ?? []);
            json_response(["ok" => true, "cart" => $cart]);
            break;

        case "wishlist/save":
            require_method("POST");
            $wishlist = save_wishlist_items($pdo, require_user(), json_input()["items"] ?? []);
            json_response(["ok" => true, "wishlist" => $wishlist]);
            break;

        case "addresses/save":
            require_method("POST");
            $userId = require_user();
            save_address($pdo, $userId, json_input()["address"] ?? []);
            json_response(["ok" => true, "profile" => fetch_profile($pdo, $userId)]);
            break;

        case "addresses/delete":
            require_method("POST");
            $userId = require_user();
            $id = (int) (json_input()["id"] ?? 0);
            if ($id <= 0) {
                json_response(["ok" => false, "error" => "Invalid address id."], 422);
            }
            $stmt = $pdo->prepare("DELETE FROM addresses WHERE id = :id AND user_id = :user_id");
            $stmt->execute(["id" => $id, "user_id" => $userId]);
            json_response(["ok" => true, "profile" => fetch_profile($pdo, $userId)]);
            break;

        case "orders/create":
            require_method("POST");
            $userId = require_user();
            $order = create_order($pdo, $userId, json_input());
            $profile = fetch_profile($pdo, $userId);
            $notifications = $profile
                ? send_order_notifications($pdo, $userId, (string) $profile["email"], $order)
                : [];
            json_response([
                "ok" => true,
                "order" => public_order($order),
                "profile" => $profile,
                "cart" => fetch_cart_items($pdo, $userId),
                "notifications" => $notifications,
            ]);
            break;

        case "logout":
            require_method("POST");
            unset($_SESSION["luxe_user_id"]);
            json_response(["ok" => true]);
            break;

        default:
            json_response(["ok" => false, "error" => "Unknown API action."], 404);
    }
} catch (Throwable $error) {
    $status = $error instanceof DomainException ? 422 : ($error instanceof PDOException ? 503 : ($error instanceof RuntimeException ? 502 : 500));
    $message = match ($status) {
        422, 502 => $error->getMessage(),
        503 => "PostgreSQL backend is unavailable.",
        default => "Backend request failed.",
    };
    json_response([
        "ok" => false,
        "error" => $message,
        "detail" => getenv("LUXE_DEBUG_API") === "1" ? $error->getMessage() : null,
    ], $status);
}

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function json_input(): array
{
    $raw = file_get_contents("php://input");
    if (!$raw) {
        return [];
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function require_method(string $method): void
{
    if ($_SERVER["REQUEST_METHOD"] !== $method) {
        json_response(["ok" => false, "error" => "Method not allowed."], 405);
    }
}

function current_user_id(): ?int
{
    return isset($_SESSION["luxe_user_id"]) ? (int) $_SESSION["luxe_user_id"] : null;
}

function require_user(): int
{
    $userId = current_user_id();
    if (!$userId) {
        json_response(["ok" => false, "error" => "Sign in is required."], 401);
    }
    return $userId;
}

function handle_request_otp(PDO $pdo, array $input): void
{
    $email = normalized_email($input["email"] ?? "");
    $phone = clean_text($input["phone"] ?? "", 50);

    if (!$email || !$phone) {
        throw new DomainException("Enter a valid email and phone number.");
    }

    $userId = upsert_user($pdo, $email, $phone);
    $code = (string) random_int(1000, 9999);
    $stmt = $pdo->prepare(
        "INSERT INTO otp_codes (user_id, email, phone, code_hash, expires_at)
         VALUES (:user_id, :email, :phone, :code_hash, now() + interval '10 minutes')"
        . " RETURNING id"
    );
    $stmt->execute([
        "user_id" => $userId,
        "email" => $email,
        "phone" => $phone,
        "code_hash" => password_hash($code, PASSWORD_DEFAULT),
    ]);
    $otpId = (int) $stmt->fetchColumn();
    $delivery = luxe_send_checkout_otp($email, $phone, $code);
    log_notification_events($pdo, $userId, $delivery, ["email" => $email, "sms" => $phone], $otpId);

    if (getenv("LUXE_REQUIRE_OTP_DELIVERY") === "1"
        && (!luxe_notification_was_sent($delivery["email"] ?? null) || !luxe_notification_was_sent($delivery["sms"] ?? null))
    ) {
        throw new RuntimeException("Verification delivery failed. Check email and SMS provider settings.");
    }

    $sent = luxe_notification_was_sent($delivery["email"] ?? null) || luxe_notification_was_sent($delivery["sms"] ?? null);

    json_response([
        "ok" => true,
        "message" => $sent ? "Verification code sent." : "Verification code generated. Configure email/SMS providers for live delivery.",
        "delivery" => $delivery,
        "debug_code" => getenv("LUXE_DEBUG_OTP") === "1" ? $code : null,
    ]);
}

function handle_verify_otp(PDO $pdo, array $input): void
{
    $email = normalized_email($input["email"] ?? "");
    $phone = clean_text($input["phone"] ?? "", 50);
    $code = preg_replace("/\D+/", "", (string) ($input["code"] ?? ""));

    if (!$email || !$phone || strlen($code) !== 4) {
        throw new DomainException("Invalid verification details.");
    }

    $stmt = $pdo->prepare(
        "SELECT id, user_id, code_hash
         FROM otp_codes
         WHERE email = :email
           AND phone = :phone
           AND verified_at IS NULL
           AND expires_at > now()
           AND attempts < 5
         ORDER BY created_at DESC
         LIMIT 5"
    );
    $stmt->execute(["email" => $email, "phone" => $phone]);
    $codes = $stmt->fetchAll();

    foreach ($codes as $otp) {
        if (password_verify($code, (string) $otp["code_hash"])) {
            $pdo->beginTransaction();
            try {
                $pdo->prepare("UPDATE otp_codes SET verified_at = now() WHERE id = :id")
                    ->execute(["id" => $otp["id"]]);
                $pdo->prepare("UPDATE users SET phone = :phone WHERE id = :id")
                    ->execute(["phone" => $phone, "id" => $otp["user_id"]]);
                $_SESSION["luxe_user_id"] = (int) $otp["user_id"];
                $pdo->commit();
            } catch (Throwable $error) {
                $pdo->rollBack();
                throw $error;
            }

            json_response([
                "ok" => true,
                "profile" => fetch_profile($pdo, (int) $otp["user_id"]),
                "cart" => fetch_cart_items($pdo, (int) $otp["user_id"]),
                "wishlist" => fetch_wishlist_items($pdo, (int) $otp["user_id"]),
            ]);
        }
    }

    $pdo->prepare(
        "UPDATE otp_codes
         SET attempts = attempts + 1
         WHERE email = :email AND phone = :phone AND verified_at IS NULL AND expires_at > now()"
    )->execute(["email" => $email, "phone" => $phone]);

    json_response(["ok" => false, "error" => "Invalid or expired verification code."], 401);
}

function upsert_user(PDO $pdo, string $email, string $phone): int
{
    $stmt = $pdo->prepare(
        "INSERT INTO users (email, phone)
         VALUES (:email, :phone)
         ON CONFLICT (email) DO UPDATE SET phone = EXCLUDED.phone, updated_at = now()
         RETURNING id"
    );
    $stmt->execute(["email" => $email, "phone" => $phone]);
    return (int) $stmt->fetchColumn();
}

function fetch_profile(PDO $pdo, int $userId): ?array
{
    $stmt = $pdo->prepare("SELECT id, email, phone FROM users WHERE id = :id");
    $stmt->execute(["id" => $userId]);
    $user = $stmt->fetch();
    if (!$user) {
        return null;
    }

    return [
        "id" => (string) $user["id"],
        "email" => $user["email"],
        "phone" => $user["phone"],
        "addresses" => fetch_addresses($pdo, $userId),
        "orders" => fetch_orders($pdo, $userId),
    ];
}

function fetch_addresses(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare(
        "SELECT id, label, first_name, last_name, address_line, city, postal_code, is_default
         FROM addresses
         WHERE user_id = :user_id
         ORDER BY is_default DESC, updated_at DESC"
    );
    $stmt->execute(["user_id" => $userId]);

    return array_map(static function (array $row): array {
        return [
            "id" => (string) $row["id"],
            "label" => $row["label"],
            "first_name" => $row["first_name"],
            "last_name" => $row["last_name"],
            "address_line" => $row["address_line"],
            "city" => $row["city"],
            "postal_code" => $row["postal_code"],
            "is_default" => (bool) $row["is_default"],
        ];
    }, $stmt->fetchAll());
}

function fetch_orders(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare(
        "SELECT id, order_number, total, created_at
         FROM orders
         WHERE user_id = :user_id
         ORDER BY created_at DESC"
    );
    $stmt->execute(["user_id" => $userId]);
    $orders = [];

    foreach ($stmt->fetchAll() as $row) {
        $items = $pdo->prepare(
            "SELECT product_id, product_name, price, image_url, meta, quantity
             FROM order_items
             WHERE order_id = :order_id
             ORDER BY id"
        );
        $items->execute(["order_id" => $row["id"]]);

        $orders[] = [
            "id" => $row["order_number"],
            "date" => date("M j, Y", strtotime((string) $row["created_at"])),
            "total" => (float) $row["total"],
            "items" => array_map("map_line_item", $items->fetchAll()),
        ];
    }

    return $orders;
}

function fetch_products(PDO $pdo): array
{
    $stmt = $pdo->query(
        "SELECT product_slug, name, category, segment, price, image_url, default_color,
                available_colors, available_sizes, is_new_arrival, popularity
         FROM products
         WHERE active = true
         ORDER BY product_slug"
    );

    return array_map(static function (array $row): array {
        return [
            "id" => $row["product_slug"],
            "name" => $row["name"],
            "category" => $row["category"],
            "segment" => $row["segment"],
            "price" => (float) $row["price"],
            "image" => $row["image_url"],
            "defaultColor" => $row["default_color"],
            "colors" => json_decode((string) $row["available_colors"], true) ?: [],
            "sizes" => json_decode((string) $row["available_sizes"], true) ?: [],
            "newArrival" => (bool) $row["is_new_arrival"],
            "popularity" => (int) $row["popularity"],
        ];
    }, $stmt->fetchAll());
}

function fetch_cart_items(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare(
        "SELECT client_item_id, product_id, product_name, price, image_url, meta, quantity
         FROM cart_items
         WHERE user_id = :user_id
         ORDER BY created_at"
    );
    $stmt->execute(["user_id" => $userId]);

    return array_map("map_line_item", $stmt->fetchAll());
}

function save_cart_items(PDO $pdo, int $userId, mixed $items): array
{
    $items = is_array($items) ? array_slice($items, 0, 100) : [];

    $pdo->beginTransaction();
    try {
        $pdo->prepare("DELETE FROM cart_items WHERE user_id = :user_id")
            ->execute(["user_id" => $userId]);

        $stmt = $pdo->prepare(
            "INSERT INTO cart_items
             (client_item_id, user_id, product_id, product_name, price, image_url, meta, quantity)
             VALUES
             (:client_item_id, :user_id, :product_id, :product_name, :price, :image_url, :meta, :quantity)"
        );

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $line = sanitize_cart_item($pdo, $item);
            if (!$line) {
                continue;
            }
            $stmt->execute([
                "client_item_id" => $line["id"],
                "user_id" => $userId,
                "product_id" => $line["product_id"],
                "product_name" => $line["name"],
                "price" => $line["price"],
                "image_url" => $line["image"],
                "meta" => $line["meta"],
                "quantity" => $line["quantity"],
            ]);
        }

        $pdo->commit();
    } catch (Throwable $error) {
        $pdo->rollBack();
        throw $error;
    }

    return fetch_cart_items($pdo, $userId);
}

function fetch_wishlist_items(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare(
        "SELECT product_id, product_name, price, image_url, color, size_label, created_at
         FROM wishlist_items
         WHERE user_id = :user_id
         ORDER BY created_at DESC"
    );
    $stmt->execute(["user_id" => $userId]);

    return array_map(static function (array $row): array {
        return [
            "id" => $row["product_id"],
            "name" => $row["product_name"],
            "price" => (float) $row["price"],
            "image" => $row["image_url"],
            "color" => $row["color"],
            "size" => $row["size_label"],
            "savedAt" => strtotime((string) $row["created_at"]) * 1000,
        ];
    }, $stmt->fetchAll());
}

function save_wishlist_items(PDO $pdo, int $userId, mixed $items): array
{
    $items = is_array($items) ? array_slice($items, 0, 100) : [];

    $pdo->beginTransaction();
    try {
        $pdo->prepare("DELETE FROM wishlist_items WHERE user_id = :user_id")
            ->execute(["user_id" => $userId]);

        $stmt = $pdo->prepare(
            "INSERT INTO wishlist_items
             (user_id, product_id, product_name, price, image_url, color, size_label, metadata)
             VALUES
             (:user_id, :product_id, :product_name, :price, :image_url, :color, :size_label, CAST(:metadata AS jsonb))"
        );

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $id = clean_slug($item["id"] ?? "");
            $name = clean_text($item["name"] ?? "", 255);
            $price = money_value($item["price"] ?? 0);
            if (!$id || !$name || $price <= 0) {
                continue;
            }

            $stmt->execute([
                "user_id" => $userId,
                "product_id" => $id,
                "product_name" => $name,
                "price" => $price,
                "image_url" => clean_url($item["image"] ?? ""),
                "color" => clean_text($item["color"] ?? "", 80),
                "size_label" => clean_text($item["size"] ?? "", 80),
                "metadata" => json_encode(["savedAt" => $item["savedAt"] ?? null]),
            ]);
        }

        $pdo->commit();
    } catch (Throwable $error) {
        $pdo->rollBack();
        throw $error;
    }

    return fetch_wishlist_items($pdo, $userId);
}

function save_address(PDO $pdo, int $userId, mixed $input): int
{
    if (!is_array($input)) {
        throw new DomainException("Invalid address.");
    }

    $address = sanitize_address($input);
    $stmt = $pdo->prepare(
        "INSERT INTO addresses
         (user_id, label, first_name, last_name, address_line, city, postal_code, is_default)
         VALUES
         (:user_id, :label, :first_name, :last_name, :address_line, :city, :postal_code, :is_default)
         ON CONFLICT (user_id, label) DO UPDATE SET
           first_name = EXCLUDED.first_name,
           last_name = EXCLUDED.last_name,
           address_line = EXCLUDED.address_line,
           city = EXCLUDED.city,
           postal_code = EXCLUDED.postal_code,
           is_default = EXCLUDED.is_default,
           updated_at = now()
         RETURNING id"
    );
    $stmt->execute([
        "user_id" => $userId,
        "label" => $address["label"],
        "first_name" => $address["first_name"],
        "last_name" => $address["last_name"],
        "address_line" => $address["address_line"],
        "city" => $address["city"],
        "postal_code" => $address["postal_code"],
        "is_default" => $address["is_default"] ? "true" : "false",
    ]);

    return (int) $stmt->fetchColumn();
}

function create_order(PDO $pdo, int $userId, array $input): array
{
    $items = is_array($input["items"] ?? null) ? array_slice($input["items"], 0, 100) : [];
    if (!$items) {
        throw new DomainException("Your bag is empty.");
    }

    $addressId = save_address($pdo, $userId, $input["address"] ?? []);
    $address = sanitize_address($input["address"] ?? []);
    $shipping = min(max(money_value($input["shipping"] ?? 0), 0), 50);
    $lines = [];
    $subtotal = 0.0;

    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $line = sanitize_cart_item($pdo, $item, true);
        if (!$line) {
            continue;
        }

        $lines[] = $line;
        $subtotal += $line["price"] * $line["quantity"];
    }

    if (!$lines) {
        throw new DomainException("Your bag is empty.");
    }

    $subtotal = round($subtotal, 2);
    $tax = round($subtotal * 0.08, 2);
    $total = round($subtotal + $tax + $shipping, 2);

    $pdo->beginTransaction();
    try {
        $orderNumber = next_order_number($pdo);
        $stmt = $pdo->prepare(
            "INSERT INTO orders
             (order_number, user_id, address_id, shipping_address, subtotal, shipping, tax, total, payment_reference)
             VALUES
             (:order_number, :user_id, :address_id, CAST(:shipping_address AS jsonb), :subtotal, :shipping, :tax, :total, :payment_reference)
             RETURNING id, order_number, total, created_at"
        );
        $stmt->execute([
            "order_number" => $orderNumber,
            "user_id" => $userId,
            "address_id" => $addressId,
            "shipping_address" => json_encode($address),
            "subtotal" => $subtotal,
            "shipping" => $shipping,
            "tax" => $tax,
            "total" => $total,
            "payment_reference" => "DEMO-" . bin2hex(random_bytes(5)),
        ]);
        $order = $stmt->fetch();

        $itemStmt = $pdo->prepare(
            "INSERT INTO order_items
             (order_id, product_id, product_name, price, image_url, meta, quantity)
             VALUES
             (:order_id, :product_id, :product_name, :price, :image_url, :meta, :quantity)"
        );
        foreach ($lines as $line) {
            $itemStmt->execute([
                "order_id" => $order["id"],
                "product_id" => $line["product_id"],
                "product_name" => $line["name"],
                "price" => $line["price"],
                "image_url" => $line["image"],
                "meta" => $line["meta"],
                "quantity" => $line["quantity"],
            ]);
        }

        $pdo->prepare("DELETE FROM cart_items WHERE user_id = :user_id")->execute(["user_id" => $userId]);
        $pdo->commit();
    } catch (Throwable $error) {
        $pdo->rollBack();
        throw $error;
    }

    return [
        "_databaseId" => (int) $order["id"],
        "id" => $order["order_number"],
        "date" => date("M j, Y", strtotime((string) $order["created_at"])),
        "items" => array_map("map_line_item", $lines),
        "total" => (float) $order["total"],
    ];
}

function send_order_notifications(PDO $pdo, int $userId, string $email, array $order): array
{
    $delivery = luxe_send_order_confirmation($email, $order);
    log_notification_events($pdo, $userId, $delivery, ["email" => $email], null, (int) ($order["_databaseId"] ?? 0) ?: null);
    return $delivery;
}

function public_order(array $order): array
{
    unset($order["_databaseId"]);
    return $order;
}

function log_notification_events(PDO $pdo, int $userId, array $delivery, array $recipients = [], ?int $otpId = null, ?int $orderId = null): void
{
    $stmt = $pdo->prepare(
        "INSERT INTO notification_events
         (user_id, otp_code_id, order_id, channel, recipient, provider, status, error_message)
         VALUES
         (:user_id, :otp_code_id, :order_id, :channel, :recipient, :provider, :status, :error_message)"
    );

    foreach ($delivery as $channel => $result) {
        if (!is_array($result)) {
            continue;
        }

        $stmt->execute([
            "user_id" => $userId,
            "otp_code_id" => $otpId,
            "order_id" => $orderId,
            "channel" => clean_text($channel, 20),
            "recipient" => clean_text($recipients[$channel] ?? "", 255),
            "provider" => clean_text($result["provider"] ?? "", 80),
            "status" => clean_text($result["status"] ?? "failed", 20),
            "error_message" => clean_text($result["error"] ?? "", 500),
        ]);
    }
}

function next_order_number(PDO $pdo): string
{
    do {
        $number = "LX-" . random_int(100000, 999999);
        $stmt = $pdo->prepare("SELECT 1 FROM orders WHERE order_number = :order_number");
        $stmt->execute(["order_number" => $number]);
    } while ($stmt->fetchColumn());

    return $number;
}

function sanitize_cart_item(PDO $pdo, array $item, bool $useCatalogPrice = false): ?array
{
    $name = clean_text($item["name"] ?? "", 255);
    $price = money_value($item["price"] ?? 0);
    if (!$name || $price <= 0) {
        return null;
    }

    $productId = clean_slug($item["productId"] ?? ($item["product_id"] ?? ""));
    $catalogPrice = $useCatalogPrice && $productId ? catalog_price($pdo, $productId) : null;

    return [
        "id" => clean_text($item["id"] ?? "", 80) ?: bin2hex(random_bytes(8)),
        "product_id" => known_product_id($pdo, $productId),
        "name" => $name,
        "price" => $catalogPrice ?? $price,
        "image" => clean_url($item["image"] ?? ""),
        "meta" => clean_text($item["meta"] ?? "Qty: 1", 180) ?: "Qty: 1",
        "quantity" => max(1, min(99, (int) ($item["quantity"] ?? 1))),
    ];
}

function sanitize_address(mixed $input): array
{
    if (!is_array($input)) {
        throw new DomainException("Invalid address.");
    }

    $address = [
        "label" => clean_text($input["label"] ?? "Home", 40) ?: "Home",
        "first_name" => clean_text($input["first_name"] ?? "", 100),
        "last_name" => clean_text($input["last_name"] ?? "", 100),
        "address_line" => clean_text($input["address_line"] ?? "", 255),
        "city" => clean_text($input["city"] ?? "", 120),
        "postal_code" => clean_text($input["postal_code"] ?? "", 30),
        "is_default" => !empty($input["is_default"]),
    ];

    if (!$address["first_name"] || !$address["address_line"] || !$address["city"] || !$address["postal_code"]) {
        throw new DomainException("Complete the shipping address.");
    }

    return $address;
}

function known_product_id(PDO $pdo, string $productId): ?string
{
    if (!$productId) {
        return null;
    }

    static $cache = [];
    if (array_key_exists($productId, $cache)) {
        return $cache[$productId];
    }

    $stmt = $pdo->prepare("SELECT product_slug FROM products WHERE product_slug = :product_slug");
    $stmt->execute(["product_slug" => $productId]);
    $cache[$productId] = $stmt->fetchColumn() ? $productId : null;

    return $cache[$productId];
}

function catalog_price(PDO $pdo, string $productId): ?float
{
    $stmt = $pdo->prepare("SELECT price FROM products WHERE product_slug = :product_slug AND active = true");
    $stmt->execute(["product_slug" => $productId]);
    $price = $stmt->fetchColumn();
    return $price === false ? null : (float) $price;
}

function map_line_item(array $row): array
{
    return [
        "id" => (string) ($row["client_item_id"] ?? $row["id"] ?? ""),
        "productId" => (string) ($row["product_id"] ?? ""),
        "name" => (string) ($row["product_name"] ?? $row["name"] ?? "Item"),
        "price" => (float) ($row["price"] ?? 0),
        "image" => (string) ($row["image_url"] ?? $row["image"] ?? ""),
        "meta" => (string) ($row["meta"] ?? "Qty: 1"),
        "quantity" => (int) ($row["quantity"] ?? 1),
    ];
}

function normalized_email(mixed $value): string
{
    $email = strtolower(trim((string) $value));
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : "";
}

function clean_text(mixed $value, int $max): string
{
    $text = trim(preg_replace("/\s+/", " ", (string) $value));
    return substr($text, 0, $max);
}

function clean_slug(mixed $value): string
{
    return trim(preg_replace("/[^a-z0-9-]+/", "", strtolower((string) $value)), "-");
}

function clean_url(mixed $value): string
{
    $url = trim((string) $value);
    if ($url === "" || filter_var($url, FILTER_VALIDATE_URL)) {
        return substr($url, 0, 1200);
    }
    return "";
}

function money_value(mixed $value): float
{
    $number = is_numeric($value) ? (float) $value : (float) preg_replace("/[^0-9.]/", "", (string) $value);
    return round(max(0, $number), 2);
}
