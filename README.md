# LUXE E-commerce Storefront

Responsive PHP and PostgreSQL e-commerce storefront with anonymous browsing, email OTP checkout, saved carts/wishlists for verified customers, and a separate retailer portal for seller-managed products.

## Features

- Public storefront with product browsing, collections, product detail pages, cart, and wishlist
- Customers browse without login and authenticate only during checkout
- Email-only checkout OTP using PHPMailer SMTP
- OTP codes are generated on the backend and stored hashed in PostgreSQL
- Saved addresses, cart items, wishlist items, orders, and notification logs
- Separate retailer authentication and dashboard
- Retailers can add, edit, and archive only their own products
- Admin approval/rejection for retailer-submitted products
- Public product listings show only approved, active, non-archived products

## Tech Stack

- PHP
- PostgreSQL
- Composer
- PHPMailer
- Vanilla JavaScript
- Tailwind CDN
- CSS

## Project Structure

```text
.
├── api.php
├── db.sql
├── index.php
├── collections.php
├── product.php
├── checkout.php
├── retailer/
│   ├── index.php
│   ├── login.php
│   ├── signup.php
│   └── dashboard.php
├── includes/
│   ├── database.php
│   └── notifications.php
├── assets/
│   ├── css/
│   ├── icons/
│   ├── js/
│   └── luxe-mark.svg
├── scripts/
│   ├── env-loader.sh
│   ├── setup-db.sh
│   └── serve.sh
├── composer.json
├── composer.lock
└── .env.example
```

The main storefront pages are kept at the project root so the PHP development server can serve them directly without a custom router.

## Requirements

- PHP with PostgreSQL extensions available
- PostgreSQL running locally or reachable through `LUXE_DB_DSN`
- Composer

## Setup

```bash
cp .env.example .env
composer install
scripts/setup-db.sh
LUXE_PORT=8080 scripts/serve.sh
```

Open the storefront:

```text
http://127.0.0.1:8080/
```

Retailer pages:

```text
http://127.0.0.1:8080/retailer/login.php
http://127.0.0.1:8080/retailer/signup.php
http://127.0.0.1:8080/retailer/dashboard.php
```

Use `scripts/serve.sh` instead of plain `php -S` so the project `.env` file is loaded and PostgreSQL extensions are passed correctly.

## Environment

Database settings:

```env
LUXE_DB_DSN=pgsql:host=127.0.0.1;port=5432;dbname=luxe_ecommerce
LUXE_DB_USER=postgres
LUXE_DB_PASS=
```

Development OTP settings:

```env
LUXE_DEBUG_OTP=1
LUXE_DEBUG_API=0
LUXE_REQUIRE_OTP_DELIVERY=0
```

SMTP settings for real email OTP delivery:

```env
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USERNAME=your_smtp_username
SMTP_PASSWORD=your_smtp_password
SMTP_FROM_EMAIL=orders@example.com
SMTP_FROM_NAME=LUXE
```

Optional retailer admin approval login:

```env
RETAILER_ADMIN_EMAIL=admin@example.com
RETAILER_ADMIN_PASSWORD=change-this-password
```

Do not commit `.env`; use `.env.example` for safe defaults.

## Customer Flow

Customers can browse the storefront without logging in. Cart and wishlist data stay in browser `localStorage` until checkout verification.

At checkout, the customer enters an email address. The backend generates a 6-digit OTP with `random_int()`, stores only `password_hash()` output in PostgreSQL, and sends the OTP to the entered email using PHPMailer SMTP.

## Retailer Flow

The storefront shows one `Become a Retailer` link. It opens `retailer/login.php`.

New sellers use the `Create a retailer account` link below the login form. Retailer sign-up collects:

- Name
- Store or business name
- Email
- Password
- Confirm password

Retailers can manage only products owned by their retailer account. New and edited retailer products are saved as `pending` until an admin approves them.

## Admin Approval

Set `RETAILER_ADMIN_EMAIL` and `RETAILER_ADMIN_PASSWORD` in `.env`, then log in through:

```text
http://127.0.0.1:8080/retailer/login.php
```

Admins can approve or reject pending retailer products. Public storefront APIs return only products where:

```text
active = true
approval_status = approved
archived_at IS NULL
```

## Database

Run the schema and seed data:

```bash
scripts/setup-db.sh
```

The schema creates:

- `users`
- `otp_codes`
- `retailer_accounts`
- `products`
- `addresses`
- `cart_items`
- `wishlist_items`
- `orders`
- `order_items`
- `notification_events`

Existing seeded products are assigned to a system owner and remain visible as approved products.

Retailer support is part of `db.sql`. Running `scripts/setup-db.sh` creates `retailer_accounts` and adds product ownership/approval fields including `vendor_id`, `approval_status`, `description`, `stock_quantity`, and `archived_at`.

Retailer account approval is not required for dashboard access; product approval only controls whether submitted products appear publicly.

## Verification

```bash
for file in api.php includes/database.php includes/notifications.php index.php collections.php product.php checkout.php retailer/index.php retailer/login.php retailer/signup.php retailer/dashboard.php; do
  php -l "$file"
done

node --check assets/js/site.js
bash -n scripts/env-loader.sh scripts/setup-db.sh scripts/serve.sh
php -r 'require "vendor/autoload.php"; echo class_exists("PHPMailer\\PHPMailer\\PHPMailer") ? "phpmailer=ok\n" : "phpmailer=missing\n";'
git diff --check
```

With the dev server running, check:

```bash
curl -sS "http://127.0.0.1:8080/api.php?action=products"
```
