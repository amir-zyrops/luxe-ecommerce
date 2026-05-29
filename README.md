# LUXE E-commerce Storefront

🛍️ A responsive PHP and PostgreSQL storefront with product browsing, cart, wishlist, OTP checkout, saved addresses, and order history.

## ✨ Features

- Responsive storefront UI
- Collection filters and product detail pages
- Anonymous cart and wishlist stored in browser `localStorage`
- PostgreSQL storage after OTP verification
- Email and phone based user creation during checkout
- Hashed OTP verification
- Saved addresses and order history
- PHPMailer SMTP email OTP delivery

## 🧱 Tech Stack

- PHP 8
- PostgreSQL
- Composer
- PHPMailer
- Vanilla JavaScript
- Tailwind CDN
- CSS

## 🚀 Setup

```bash
cp .env.example .env
composer install
scripts/setup-db.sh
LUXE_PORT=8080 scripts/serve.sh
```

Open:

```text
http://127.0.0.1:8080
```

Use `scripts/serve.sh` instead of plain `php -S` so PostgreSQL extensions are loaded correctly.

## 🔐 Environment

For development OTP display:

```env
LUXE_DEBUG_OTP=1
LUXE_REQUIRE_OTP_DELIVERY=0
```

For real email OTP delivery, add SMTP credentials in `.env`.

```env
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USERNAME=your_smtp_username
SMTP_PASSWORD=your_smtp_password
SMTP_FROM_EMAIL=orders@example.com
SMTP_FROM_NAME=LUXE
```

## ✅ Checks

```bash
for file in api.php includes/database.php includes/notifications.php index.php collections.php product.php checkout.php; do
  php -l "$file"
done

node --check assets/js/site.js
bash -n scripts/env-loader.sh scripts/setup-db.sh scripts/serve.sh
php -r 'require "vendor/autoload.php"; echo class_exists("PHPMailer\\PHPMailer\\PHPMailer") ? "phpmailer=ok\n" : "phpmailer=missing\n";'
git diff --check
```

## 📝 Documentation

When setup, database, OTP, or runtime behavior changes, update both `README.md` and `PROJECT_OVERVIEW.md` in the same change.
