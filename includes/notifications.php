<?php

declare(strict_types=1);

function luxe_send_checkout_otp(string $email, string $phone, string $code): array
{
    $plain = "Your LUXE checkout verification code is {$code}. It expires in 10 minutes.";
    $emailBody = "Use this code to confirm your LUXE checkout:\n\n{$code}\n\nThis code expires in 10 minutes.";

    return [
        "email" => luxe_send_transactional_email($email, "Your LUXE verification code", $emailBody),
        "sms" => luxe_send_transactional_sms($phone, $plain),
    ];
}

function luxe_send_order_confirmation(string $email, array $order): array
{
    $items = array_map(static function (array $item): string {
        $name = (string) ($item["name"] ?? "Item");
        $meta = (string) ($item["meta"] ?? "Qty: 1");
        $price = number_format((float) ($item["price"] ?? 0), 2);
        return "- {$name} ({$meta}) - \${$price}";
    }, is_array($order["items"] ?? null) ? $order["items"] : []);

    $body = implode("\n", [
        "Thank you for your LUXE order.",
        "",
        "Order: " . (string) ($order["id"] ?? ""),
        "Total: $" . number_format((float) ($order["total"] ?? 0), 2),
        "",
        "Items:",
        implode("\n", $items),
        "",
        "We will email tracking details when the order ships.",
    ]);

    return [
        "email" => luxe_send_transactional_email($email, "LUXE order confirmation " . (string) ($order["id"] ?? ""), $body),
    ];
}

function luxe_send_transactional_email(string $to, string $subject, string $body): array
{
    $sendgridKey = trim((string) getenv("LUXE_SENDGRID_API_KEY"));
    if ($sendgridKey !== "") {
        return luxe_sendgrid_email($sendgridKey, $to, $subject, $body);
    }

    $transport = strtolower(trim((string) (getenv("LUXE_MAIL_TRANSPORT") ?: "log")));
    if ($transport === "mail") {
        return luxe_php_mail($to, $subject, $body);
    }

    return luxe_notification_result("sendgrid/mail", "skipped", "No email provider is configured.");
}

function luxe_send_transactional_sms(string $phone, string $body): array
{
    $sid = trim((string) getenv("LUXE_TWILIO_ACCOUNT_SID"));
    $token = trim((string) getenv("LUXE_TWILIO_AUTH_TOKEN"));
    $from = trim((string) getenv("LUXE_TWILIO_FROM"));

    if ($sid === "" || $token === "" || $from === "") {
        return luxe_notification_result("twilio", "skipped", "No SMS provider is configured.");
    }

    $url = "https://api.twilio.com/2010-04-01/Accounts/" . rawurlencode($sid) . "/Messages.json";
    $payload = http_build_query([
        "From" => $from,
        "To" => $phone,
        "Body" => $body,
    ]);

    $result = luxe_http_post($url, [
        "Authorization: Basic " . base64_encode("{$sid}:{$token}"),
        "Content-Type: application/x-www-form-urlencoded",
    ], $payload, [200, 201]);

    return $result["ok"]
        ? luxe_notification_result("twilio", "sent")
        : luxe_notification_result("twilio", "failed", $result["error"]);
}

function luxe_sendgrid_email(string $apiKey, string $to, string $subject, string $body): array
{
    $fromEmail = trim((string) getenv("LUXE_MAIL_FROM"));
    if ($fromEmail === "") {
        return luxe_notification_result("sendgrid", "skipped", "LUXE_MAIL_FROM is not configured.");
    }

    $payload = json_encode([
        "personalizations" => [[
            "to" => [["email" => $to]],
        ]],
        "from" => [
            "email" => $fromEmail,
            "name" => trim((string) (getenv("LUXE_MAIL_FROM_NAME") ?: "LUXE")),
        ],
        "subject" => $subject,
        "content" => [[
            "type" => "text/plain",
            "value" => $body,
        ]],
    ], JSON_UNESCAPED_SLASHES);

    $result = luxe_http_post("https://api.sendgrid.com/v3/mail/send", [
        "Authorization: Bearer {$apiKey}",
        "Content-Type: application/json",
    ], $payload ?: "{}", [202]);

    return $result["ok"]
        ? luxe_notification_result("sendgrid", "sent")
        : luxe_notification_result("sendgrid", "failed", $result["error"]);
}

function luxe_php_mail(string $to, string $subject, string $body): array
{
    $fromEmail = trim((string) getenv("LUXE_MAIL_FROM"));
    if ($fromEmail === "") {
        return luxe_notification_result("mail", "skipped", "LUXE_MAIL_FROM is not configured.");
    }

    $fromName = luxe_safe_header_value((string) (getenv("LUXE_MAIL_FROM_NAME") ?: "LUXE"));
    $headers = [
        "From: {$fromName} <" . luxe_safe_header_value($fromEmail) . ">",
        "Content-Type: text/plain; charset=UTF-8",
    ];

    return mail($to, luxe_safe_header_value($subject), $body, implode("\r\n", $headers))
        ? luxe_notification_result("mail", "sent")
        : luxe_notification_result("mail", "failed", "PHP mail() returned false.");
}

function luxe_http_post(string $url, array $headers, string $content, array $successStatuses): array
{
    if (function_exists("curl_init")) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $content,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 12,
        ]);
        $response = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if (in_array($status, $successStatuses, true)) {
            return ["ok" => true, "status" => $status, "error" => ""];
        }

        return [
            "ok" => false,
            "status" => $status,
            "error" => $error !== "" ? $error : substr(trim((string) $response), 0, 500),
        ];
    }

    $context = stream_context_create([
        "http" => [
            "method" => "POST",
            "header" => implode("\r\n", $headers),
            "content" => $content,
            "timeout" => 12,
            "ignore_errors" => true,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);
    $status = 0;
    foreach ($http_response_header ?? [] as $header) {
        if (preg_match("/^HTTP\/\S+\s+(\d+)/", $header, $matches)) {
            $status = (int) $matches[1];
            break;
        }
    }

    if (in_array($status, $successStatuses, true)) {
        return ["ok" => true, "status" => $status, "error" => ""];
    }

    $error = $response === false ? "HTTP request failed." : substr(trim($response), 0, 500);
    return ["ok" => false, "status" => $status, "error" => $error];
}

function luxe_notification_result(string $provider, string $status, string $error = ""): array
{
    return [
        "provider" => $provider,
        "status" => $status,
        "error" => $error,
    ];
}

function luxe_notification_was_sent(?array $result): bool
{
    return is_array($result) && ($result["status"] ?? "") === "sent";
}

function luxe_safe_header_value(string $value): string
{
    return trim(str_replace(["\r", "\n"], "", $value));
}
