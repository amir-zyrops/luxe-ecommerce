<?php

declare(strict_types=1);

function luxe_tracking_next_number(PDO $pdo): string
{
    for ($attempt = 0; $attempt < 10; $attempt++) {
        $number = "LXTRK-" . strtoupper(bin2hex(random_bytes(6)));
        $stmt = $pdo->prepare("SELECT 1 FROM orders WHERE tracking_number = :tracking_number");
        $stmt->execute(["tracking_number" => $number]);
        if (!$stmt->fetchColumn()) {
            return $number;
        }
    }

    throw new RuntimeException("Could not generate a tracking number.");
}

function luxe_tracking_clean_number(mixed $value): string
{
    $number = strtoupper(trim((string) $value));
    $number = preg_replace("/[^A-Z0-9-]/", "", $number) ?? "";
    return substr($number, 0, 40);
}

function luxe_tracking_estimated_delivery(float $shippingCost): string
{
    $days = $shippingCost > 0 ? 1 : 5;
    return (new DateTimeImmutable("today"))->modify("+{$days} days")->format("Y-m-d");
}

function luxe_tracking_public_payload(array $order): array
{
    $number = luxe_tracking_clean_number($order["tracking_number"] ?? "");
    $status = luxe_tracking_current_status($order);

    return [
        "number" => $number,
        "status" => $status,
        "statusLabel" => luxe_tracking_status_label($status),
        "carrier" => trim((string) ($order["carrier"] ?? "")) ?: "LUXE Delivery",
        "estimatedDelivery" => luxe_tracking_format_date($order["estimated_delivery"] ?? null),
        "orderNumber" => (string) ($order["order_number"] ?? ""),
        "placedDate" => luxe_tracking_format_date($order["created_at"] ?? null),
        "destinationCity" => (string) ($order["destination_city"] ?? ""),
        "steps" => luxe_tracking_steps($status),
    ];
}

function luxe_tracking_fetch(PDO $pdo, string $trackingNumber): ?array
{
    $trackingNumber = luxe_tracking_clean_number($trackingNumber);
    if ($trackingNumber === "") {
        return null;
    }

    $stmt = $pdo->prepare(
        "SELECT order_number, tracking_number, tracking_status, carrier, estimated_delivery, created_at,
                shipping_address->>'city' AS destination_city
         FROM orders
         WHERE tracking_number = :tracking_number
         LIMIT 1"
    );
    $stmt->execute(["tracking_number" => $trackingNumber]);
    $row = $stmt->fetch();

    return is_array($row) ? $row : null;
}

function luxe_tracking_current_status(array $order): string
{
    $stored = luxe_tracking_clean_status($order["tracking_status"] ?? "");
    if ($stored !== "" && $stored !== "processing") {
        return $stored;
    }

    $createdAt = strtotime((string) ($order["created_at"] ?? ""));
    $estimatedAt = strtotime((string) ($order["estimated_delivery"] ?? ""));
    $now = time();

    if ($estimatedAt && $now >= $estimatedAt + 86400) {
        return "delivered";
    }
    if ($estimatedAt && date("Y-m-d", $now) >= date("Y-m-d", $estimatedAt)) {
        return "out_for_delivery";
    }
    if ($createdAt && $now - $createdAt >= 172800) {
        return "in_transit";
    }
    if ($createdAt && $now - $createdAt >= 86400) {
        return "packed";
    }

    return "processing";
}

function luxe_tracking_clean_status(mixed $value): string
{
    $status = strtolower(trim((string) $value));
    return array_key_exists($status, luxe_tracking_status_labels()) ? $status : "";
}

function luxe_tracking_status_label(string $status): string
{
    return luxe_tracking_status_labels()[$status] ?? "Processing";
}

function luxe_tracking_status_labels(): array
{
    return [
        "processing" => "Processing",
        "packed" => "Packed",
        "in_transit" => "In transit",
        "out_for_delivery" => "Out for delivery",
        "delivered" => "Delivered",
    ];
}

function luxe_tracking_steps(string $currentStatus): array
{
    $statuses = array_keys(luxe_tracking_status_labels());
    $currentIndex = array_search($currentStatus, $statuses, true);
    if ($currentIndex === false) {
        $currentIndex = 0;
    }

    return array_map(static function (string $status, int $index) use ($currentIndex): array {
        return [
            "status" => $status,
            "label" => luxe_tracking_status_label($status),
            "state" => $index < $currentIndex ? "complete" : ($index === $currentIndex ? "current" : "pending"),
        ];
    }, $statuses, array_keys($statuses));
}

function luxe_tracking_format_date(mixed $value): string
{
    $timestamp = strtotime((string) $value);
    return $timestamp ? date("M j, Y", $timestamp) : "";
}
