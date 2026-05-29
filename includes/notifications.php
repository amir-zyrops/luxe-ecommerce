<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

function luxe_send_checkout_otp(string $email, string $code): array
{
    $emailBody = "Use this code to confirm your LUXE checkout:\n\n{$code}\n\nThis code expires in 10 minutes.";

    return [
        "email" => luxe_send_transactional_email($email, "Your LUXE verification code", $emailBody),
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
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return luxe_notification_result("smtp", "failed", "Invalid recipient email address.");
    }

    $autoload = dirname(__DIR__) . "/vendor/autoload.php";
    if (is_readable($autoload)) {
        require_once $autoload;
    }

    if (!class_exists(PHPMailer::class)) {
        return luxe_notification_result("smtp", "failed", "PHPMailer is not installed. Run composer install.");
    }

    // SMTP credentials are loaded from fixed environment variables here.
    $host = trim((string) getenv("SMTP_HOST"));
    $port = (int) trim((string) getenv("SMTP_PORT"));
    $username = trim((string) getenv("SMTP_USERNAME"));
    $password = (string) getenv("SMTP_PASSWORD");
    $fromEmail = trim((string) getenv("SMTP_FROM_EMAIL"));
    $fromName = trim((string) getenv("SMTP_FROM_NAME"));

    if ($host === "" || $port <= 0 || $username === "" || $password === "" || $fromEmail === "" || $fromName === "") {
        return luxe_notification_result("smtp", "skipped", "SMTP environment variables are not configured.");
    }
    if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        return luxe_notification_result("smtp", "failed", "SMTP_FROM_EMAIL is invalid.");
    }

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->Port = $port;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        if ($port === 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($port !== 25) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->CharSet = "UTF-8";
        $mail->setFrom($fromEmail, $fromName);

        // The OTP email is sent to the dynamic user-provided recipient here.
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $body;
        $mail->send();

        return luxe_notification_result("smtp", "sent");
    } catch (PHPMailerException $error) {
        return luxe_notification_result("smtp", "failed", $error->getMessage());
    } catch (Throwable $error) {
        return luxe_notification_result("smtp", "failed", $error->getMessage());
    }
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
