# LUXE E-commerce Storefront

Responsive PHP and PostgreSQL e-commerce storefront with anonymous browsing, email OTP checkout, saved carts/wishlists for verified customers, and a separate retailer portal for seller-managed products.

## Features

- Public storefront with product browsing, collections, product detail pages, cart, and wishlist
- Customers browse without login and authenticate only during checkout
- Email-only checkout OTP using PHPMailer SMTP
- OTP codes are generated on the backend and stored hashed in PostgreSQL
- Stripe Checkout payment redirect after email verification
- Saved addresses, cart items, wishlist items, orders, and notification logs
- Separate retailer authentication and dashboard
- Retailers can add, edit, and delete only their own products from the dashboard
- Product images can be provided by URL or uploaded locally
- Admin approval/rejection for retailer-submitted products
- Admins can review approved products, open public product pages, and delete any product
- Admins can view retailer account details and product totals
- Retailer/admin messages inside the retailer portal
- Public product listings show only approved, active products that have not been deleted

## Tech Stack

- PHP
- PostgreSQL
- Composer
- PHPMailer
- Stripe Checkout API
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
│   ├── uploads/        # runtime product uploads, ignored by Git
│   ├── luxe-favicon.svg
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
LUXE_ALLOW_DEMO_ORDERS=0
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

Stripe Checkout settings:

```env
STRIPE_SECRET_KEY=sk_test_your_key_here
STRIPE_CURRENCY=usd
STRIPE_SUCCESS_URL=
STRIPE_CANCEL_URL=
```

If `STRIPE_SUCCESS_URL` and `STRIPE_CANCEL_URL` are blank, the backend sends Stripe back to `/checkout.php` on the current local server host.

Optional retailer admin approval login:

```env
RETAILER_ADMIN_EMAIL=admin@example.com
RETAILER_ADMIN_PASSWORD=change-this-password
```

Do not commit `.env`; use `.env.example` for safe defaults.

## Customer Flow

Customers can browse the storefront without logging in. Cart and wishlist data stay in browser `localStorage` until checkout verification.

At checkout, the customer enters an email address. The backend generates a 6-digit OTP with `random_int()`, stores only `password_hash()` output in PostgreSQL, and sends the OTP to the entered email using PHPMailer SMTP.

After OTP verification, the checkout page redirects the customer to Stripe Checkout. Card details are entered only on Stripe's hosted checkout page. When Stripe redirects back with a paid session, the backend creates the order, clears the saved cart, and sends the order confirmation email to the verified checkout email.

The old direct demo order endpoint is disabled by default. Set `LUXE_ALLOW_DEMO_ORDERS=1` only for local testing without Stripe.

## Retailer Flow

The storefront shows one `Become a Retailer` link. It opens `retailer/login.php`.

New sellers use the `Create a retailer account` link below the login form. Retailer sign-up collects:

- Name
- Store or business name
- Email
- Password
- Confirm password

Retailers manage products directly from the dashboard. The top retailer navigation stays focused on `Storefront`, `Collections`, and `Dashboard`; product actions live inside the dashboard table and the blue add-product button.

Product images can be added in either of two ways:

- Paste an image URL
- Upload a JPG, PNG, WebP, or GIF file up to 5 MB

Uploaded product images are stored under `assets/uploads/products/` at runtime and are ignored by Git. New and edited retailer products are saved as `pending` until an admin approves them.

After admin approval, retailer products are published as active New Arrivals and are returned by the public products API for the Collections page.

Retailers can also send messages to the admin team from the dashboard.

## Admin Approval

Set `RETAILER_ADMIN_EMAIL` and `RETAILER_ADMIN_PASSWORD` in `.env`, then log in through:

```text
http://127.0.0.1:8080/retailer/login.php
```

After login, admins land on the same retailer dashboard. There is no public navbar tab for admin tools. From the dashboard, admins can open:

- `Admin Approval` for pending product review
- `Approved Products` for live approved listings and product deletion
- `Retailers` for retailer account details and product totals

Approving a product sets it to active, approved, and New Arrival so it can appear publicly. Admins can also reply to retailer messages from the dashboard. Public storefront APIs return only products where:

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
- `retailer_messages`
- `addresses`
- `cart_items`
- `wishlist_items`
- `orders`
- `order_items`
- `notification_events`

Existing seeded products are assigned to a system owner and remain visible as approved products.

Retailer support is part of `db.sql`. Running `scripts/setup-db.sh` creates `retailer_accounts`, `retailer_messages`, and product ownership/approval fields including `vendor_id`, `approval_status`, `description`, `stock_quantity`, and `archived_at`, which is used internally as the product deletion marker.

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
