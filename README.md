# LUXE E-commerce Storefront

LUXE is a PHP and PostgreSQL e-commerce storefront with product browsing, cart, wishlist, OTP checkout, saved addresses, order history, and notification hooks for real email/SMS delivery.

## Features

- Responsive luxury storefront UI
- Collection filtering by category, size, color, price, segment, and sort order
- Product detail page with size guide, wishlist, and add-to-bag behavior
- Browser-only anonymous cart and wishlist through `localStorage`
- PostgreSQL persistence after OTP login
- Email/phone user creation during checkout
- Hashed OTP storage with expiry and attempt limits
- Saved addresses and order history per verified user
- SendGrid email and Twilio SMS integration points
- Notification audit records in PostgreSQL

## Tech Stack

- PHP 8
- PostgreSQL
- Vanilla JavaScript
- Tailwind CDN
- CSS in `assets/css/site.css`

## Project Structure

```text
.
├── api.php                    # JSON backend API
├── checkout.php               # checkout page and OTP modal
├── collections.php            # product listing page
├── db.sql                     # PostgreSQL schema and seed products
├── index.php                  # homepage
├── product.php                # product detail page
├── PROJECT_OVERVIEW.md        # detailed file map and workflow notes
├── assets/
│   ├── css/site.css           # shared responsive styling
│   ├── icons/*.svg            # homepage value icons
│   ├── js/site.js             # frontend behavior and API sync
│   └── luxe-mark.svg          # favicon/brand mark
├── includes/
│   ├── database.php           # environment loading and PDO connection
│   └── notifications.php      # SendGrid, PHP mail, and Twilio helpers
└── scripts/
    ├── serve.sh               # PHP dev server with PostgreSQL extensions
    └── setup-db.sh            # database creation and schema application
```

## Environment Files

Use `.env.example` as the safe template for GitHub. It contains variable names but no real secrets.

Create your local `.env`:

```bash
cp .env.example .env
```

Do not commit `.env`. It is ignored because it can contain database passwords and mail/SMS provider keys.

## Local Setup

From the project folder:

```bash
scripts/setup-db.sh
LUXE_PORT=8080 scripts/serve.sh
```

Open:

```text
http://127.0.0.1:8080
```

Use `scripts/serve.sh` instead of plain `php -S`, because this environment needs PostgreSQL extensions loaded explicitly.

## Checkout And User Flow

1. Anonymous users browse products and use cart/wishlist in browser `localStorage`.
2. No anonymous `guest_sessions` data is stored in PostgreSQL.
3. During checkout, the user enters email and phone.
4. `api.php?action=request_otp` creates or updates that user in `users`.
5. The OTP is hashed and stored in `otp_codes`.
6. Email/SMS delivery is attempted through configured providers.
7. `api.php?action=verify_otp` verifies the code and creates the PHP user session.
8. `api.php?action=orders/create` saves the address, order, and order items for that verified user.
9. Different email addresses create separate users and separate order histories.

## Real Email And SMS

For development, the OTP can appear on screen:

```env
LUXE_DEBUG_OTP=1
LUXE_REQUIRE_OTP_DELIVERY=0
```

For production-style delivery, set:

```env
LUXE_DEBUG_OTP=0
LUXE_REQUIRE_OTP_DELIVERY=1
LUXE_MAIL_FROM=orders@yourdomain.com
LUXE_MAIL_FROM_NAME=LUXE
LUXE_SENDGRID_API_KEY=your_sendgrid_key
LUXE_TWILIO_ACCOUNT_SID=your_twilio_sid
LUXE_TWILIO_AUTH_TOKEN=your_twilio_token
LUXE_TWILIO_FROM=+1234567890
```

SendGrid requires a verified sender or authenticated domain. Twilio requires a valid sender number and E.164 phone numbers such as `+15551234567`.

## Verification

Run these checks before pushing:

```bash
for file in api.php includes/database.php includes/notifications.php index.php collections.php product.php checkout.php; do
  php -l "$file"
done

node --check assets/js/site.js
bash -n scripts/setup-db.sh scripts/serve.sh
git diff --check
scripts/setup-db.sh
```

With the server running, confirm the API:

```bash
curl -sS "http://127.0.0.1:8080/api.php?action=state"
curl -sS "http://127.0.0.1:8080/api.php?action=products"
```

## Git Notes

Commit these:

- source PHP files
- `assets/`
- `includes/`
- `scripts/`
- `db.sql`
- `.env.example`
- `.gitignore`
- `README.md`
- `PROJECT_OVERVIEW.md`

Do not commit:

- `.env`
- `.idea/`
- logs
- OS metadata such as `.DS_Store`
