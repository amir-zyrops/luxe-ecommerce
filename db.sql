-- PostgreSQL schema for the LUXE storefront.
-- Create the database first, then run this file with psql:
--   createdb luxe_ecommerce
--   psql -d luxe_ecommerce -f db.sql

CREATE EXTENSION IF NOT EXISTS pgcrypto;

CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS trigger AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  email TEXT NOT NULL UNIQUE,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  CHECK (position('@' IN email) > 1)
);

ALTER TABLE users DROP COLUMN IF EXISTS phone CASCADE;

DROP TRIGGER IF EXISTS trg_users_updated_at ON users;
CREATE TRIGGER trg_users_updated_at
BEFORE UPDATE ON users
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

CREATE TABLE IF NOT EXISTS otp_codes (
  id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  email TEXT NOT NULL,
  purpose TEXT NOT NULL DEFAULT 'checkout',
  code_hash TEXT NOT NULL,
  attempts INTEGER NOT NULL DEFAULT 0,
  verified BOOLEAN NOT NULL DEFAULT false,
  verified_at TIMESTAMPTZ,
  expires_at TIMESTAMPTZ NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

ALTER TABLE otp_codes ADD COLUMN IF NOT EXISTS purpose TEXT NOT NULL DEFAULT 'checkout';
ALTER TABLE otp_codes ADD COLUMN IF NOT EXISTS verified BOOLEAN NOT NULL DEFAULT false;
ALTER TABLE otp_codes DROP COLUMN IF EXISTS phone CASCADE;
UPDATE otp_codes SET verified = true WHERE verified_at IS NOT NULL;

DROP INDEX IF EXISTS idx_otp_codes_lookup;
CREATE INDEX idx_otp_codes_lookup
ON otp_codes (email, purpose, created_at DESC)
WHERE verified = false;

CREATE TABLE IF NOT EXISTS retailer_accounts (
  id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  email TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  display_name TEXT NOT NULL,
  business_name TEXT NOT NULL DEFAULT '',
  role TEXT NOT NULL DEFAULT 'retailer' CHECK (role IN ('retailer', 'admin', 'system')),
  status TEXT NOT NULL DEFAULT 'active' CHECK (status IN ('active', 'disabled')),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  CHECK (position('@' IN email) > 1)
);

ALTER TABLE retailer_accounts DROP COLUMN IF EXISTS phone CASCADE;
ALTER TABLE retailer_accounts ADD COLUMN IF NOT EXISTS business_name TEXT NOT NULL DEFAULT '';

INSERT INTO retailer_accounts (email, password_hash, display_name, business_name, role, status)
VALUES ('system@luxe.local', '', 'LUXE Catalog', 'LUXE Catalog', 'system', 'active')
ON CONFLICT (email) DO UPDATE SET
  display_name = EXCLUDED.display_name,
  business_name = EXCLUDED.business_name,
  role = EXCLUDED.role,
  status = EXCLUDED.status,
  updated_at = now();

DROP TRIGGER IF EXISTS trg_retailer_accounts_updated_at ON retailer_accounts;
CREATE TRIGGER trg_retailer_accounts_updated_at
BEFORE UPDATE ON retailer_accounts
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

CREATE TABLE IF NOT EXISTS products (
  product_slug TEXT PRIMARY KEY,
  name TEXT NOT NULL,
  description TEXT NOT NULL DEFAULT '',
  category TEXT NOT NULL,
  segment TEXT NOT NULL CHECK (segment IN ('men', 'women', 'accessories')),
  price NUMERIC(10, 2) NOT NULL CHECK (price >= 0),
  stock_quantity INTEGER NOT NULL DEFAULT 0 CHECK (stock_quantity >= 0),
  image_url TEXT NOT NULL DEFAULT '',
  default_color TEXT NOT NULL DEFAULT '',
  available_colors JSONB NOT NULL DEFAULT '[]'::jsonb,
  available_sizes JSONB NOT NULL DEFAULT '[]'::jsonb,
  vendor_id BIGINT REFERENCES retailer_accounts(id),
  approval_status TEXT NOT NULL DEFAULT 'approved',
  rejection_reason TEXT NOT NULL DEFAULT '',
  archived_at TIMESTAMPTZ,
  is_new_arrival BOOLEAN NOT NULL DEFAULT false,
  popularity INTEGER NOT NULL DEFAULT 0,
  active BOOLEAN NOT NULL DEFAULT true,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

ALTER TABLE products ADD COLUMN IF NOT EXISTS description TEXT NOT NULL DEFAULT '';
ALTER TABLE products ADD COLUMN IF NOT EXISTS stock_quantity INTEGER NOT NULL DEFAULT 0;
ALTER TABLE products ADD COLUMN IF NOT EXISTS vendor_id BIGINT REFERENCES retailer_accounts(id);
ALTER TABLE products ADD COLUMN IF NOT EXISTS approval_status TEXT NOT NULL DEFAULT 'approved';
ALTER TABLE products ADD COLUMN IF NOT EXISTS rejection_reason TEXT NOT NULL DEFAULT '';
ALTER TABLE products ADD COLUMN IF NOT EXISTS archived_at TIMESTAMPTZ;

DO $$
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_constraint
    WHERE conname = 'products_stock_quantity_check'
  ) THEN
    ALTER TABLE products
    ADD CONSTRAINT products_stock_quantity_check
    CHECK (stock_quantity >= 0);
  END IF;
END;
$$;

DO $$
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_constraint
    WHERE conname = 'products_approval_status_check'
  ) THEN
    ALTER TABLE products
    ADD CONSTRAINT products_approval_status_check
    CHECK (approval_status IN ('pending', 'approved', 'rejected'));
  END IF;
END;
$$;

UPDATE products
SET vendor_id = (SELECT id FROM retailer_accounts WHERE email = 'system@luxe.local')
WHERE vendor_id IS NULL;

UPDATE products
SET active = true
WHERE approval_status = 'approved'
  AND archived_at IS NULL;

UPDATE products
SET is_new_arrival = true
WHERE approval_status = 'approved'
  AND archived_at IS NULL
  AND vendor_id IS NOT NULL
  AND vendor_id <> (SELECT id FROM retailer_accounts WHERE email = 'system@luxe.local');

ALTER TABLE products ALTER COLUMN vendor_id DROP NOT NULL;

DROP INDEX IF EXISTS idx_products_listing;
CREATE INDEX idx_products_listing
ON products (active, approval_status, archived_at, segment, category, price);

CREATE INDEX IF NOT EXISTS idx_products_vendor_id
ON products (vendor_id, updated_at DESC);

DROP TRIGGER IF EXISTS trg_products_updated_at ON products;
CREATE TRIGGER trg_products_updated_at
BEFORE UPDATE ON products
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

CREATE TABLE IF NOT EXISTS retailer_messages (
  id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  retailer_id BIGINT NOT NULL REFERENCES retailer_accounts(id) ON DELETE CASCADE,
  sender_id BIGINT NOT NULL REFERENCES retailer_accounts(id) ON DELETE CASCADE,
  sender_role TEXT NOT NULL CHECK (sender_role IN ('retailer', 'admin')),
  body TEXT NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  CHECK (length(trim(body)) > 0)
);

CREATE INDEX IF NOT EXISTS idx_retailer_messages_retailer_created_at
ON retailer_messages (retailer_id, created_at DESC);

CREATE INDEX IF NOT EXISTS idx_retailer_messages_sender_created_at
ON retailer_messages (sender_id, created_at DESC);

CREATE TABLE IF NOT EXISTS addresses (
  id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  label TEXT NOT NULL DEFAULT 'Home',
  first_name TEXT NOT NULL,
  last_name TEXT NOT NULL DEFAULT '',
  address_line TEXT NOT NULL,
  city TEXT NOT NULL,
  postal_code TEXT NOT NULL,
  is_default BOOLEAN NOT NULL DEFAULT false,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  UNIQUE (user_id, label)
);

CREATE INDEX IF NOT EXISTS idx_addresses_user_id ON addresses (user_id);

DROP TRIGGER IF EXISTS trg_addresses_updated_at ON addresses;
CREATE TRIGGER trg_addresses_updated_at
BEFORE UPDATE ON addresses
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

CREATE TABLE IF NOT EXISTS cart_items (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  client_item_id TEXT NOT NULL,
  user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  product_id TEXT REFERENCES products(product_slug) ON DELETE SET NULL,
  product_name TEXT NOT NULL,
  price NUMERIC(10, 2) NOT NULL CHECK (price >= 0),
  image_url TEXT NOT NULL DEFAULT '',
  meta TEXT NOT NULL DEFAULT 'Qty: 1',
  quantity INTEGER NOT NULL DEFAULT 1 CHECK (quantity > 0),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

DELETE FROM cart_items WHERE user_id IS NULL;
DROP INDEX IF EXISTS idx_cart_items_guest_session_id;
ALTER TABLE cart_items DROP COLUMN IF EXISTS guest_session_id CASCADE;
ALTER TABLE cart_items ALTER COLUMN user_id SET NOT NULL;

CREATE INDEX IF NOT EXISTS idx_cart_items_user_id ON cart_items (user_id);

DROP TRIGGER IF EXISTS trg_cart_items_updated_at ON cart_items;
CREATE TRIGGER trg_cart_items_updated_at
BEFORE UPDATE ON cart_items
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

CREATE TABLE IF NOT EXISTS wishlist_items (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  product_id TEXT NOT NULL,
  product_name TEXT NOT NULL,
  price NUMERIC(10, 2) NOT NULL CHECK (price >= 0),
  image_url TEXT NOT NULL DEFAULT '',
  color TEXT NOT NULL DEFAULT '',
  size_label TEXT NOT NULL DEFAULT '',
  metadata JSONB NOT NULL DEFAULT '{}'::jsonb,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

DELETE FROM wishlist_items WHERE user_id IS NULL;
DROP INDEX IF EXISTS uniq_wishlist_items_guest_product;
ALTER TABLE wishlist_items DROP COLUMN IF EXISTS guest_session_id CASCADE;
ALTER TABLE wishlist_items ALTER COLUMN user_id SET NOT NULL;

CREATE UNIQUE INDEX IF NOT EXISTS uniq_wishlist_items_user_product
ON wishlist_items (user_id, product_id)
WHERE user_id IS NOT NULL;

DROP TABLE IF EXISTS guest_sessions CASCADE;

DROP TRIGGER IF EXISTS trg_wishlist_items_updated_at ON wishlist_items;
CREATE TRIGGER trg_wishlist_items_updated_at
BEFORE UPDATE ON wishlist_items
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

CREATE TABLE IF NOT EXISTS orders (
  id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  order_number TEXT NOT NULL UNIQUE,
  user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  address_id BIGINT REFERENCES addresses(id) ON DELETE SET NULL,
  shipping_address JSONB NOT NULL,
  subtotal NUMERIC(10, 2) NOT NULL CHECK (subtotal >= 0),
  shipping NUMERIC(10, 2) NOT NULL DEFAULT 0 CHECK (shipping >= 0),
  tax NUMERIC(10, 2) NOT NULL DEFAULT 0 CHECK (tax >= 0),
  total NUMERIC(10, 2) NOT NULL CHECK (total >= 0),
  status TEXT NOT NULL DEFAULT 'paid',
  payment_reference TEXT NOT NULL DEFAULT '',
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_orders_user_id_created_at
ON orders (user_id, created_at DESC);

CREATE TABLE IF NOT EXISTS notification_events (
  id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  otp_code_id BIGINT REFERENCES otp_codes(id) ON DELETE SET NULL,
  order_id BIGINT REFERENCES orders(id) ON DELETE SET NULL,
  channel TEXT NOT NULL CHECK (channel = 'email'),
  recipient TEXT NOT NULL,
  provider TEXT NOT NULL DEFAULT '',
  status TEXT NOT NULL CHECK (status IN ('sent', 'skipped', 'failed')),
  error_message TEXT NOT NULL DEFAULT '',
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

DELETE FROM notification_events WHERE channel <> 'email';
ALTER TABLE notification_events DROP CONSTRAINT IF EXISTS notification_events_channel_check;
ALTER TABLE notification_events ADD CONSTRAINT notification_events_channel_check CHECK (channel = 'email');

CREATE INDEX IF NOT EXISTS idx_notification_events_user_id
ON notification_events (user_id, created_at DESC);

CREATE TABLE IF NOT EXISTS order_items (
  id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  order_id BIGINT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
  product_id TEXT REFERENCES products(product_slug) ON DELETE SET NULL,
  product_name TEXT NOT NULL,
  price NUMERIC(10, 2) NOT NULL CHECK (price >= 0),
  image_url TEXT NOT NULL DEFAULT '',
  meta TEXT NOT NULL DEFAULT 'Qty: 1',
  quantity INTEGER NOT NULL DEFAULT 1 CHECK (quantity > 0),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_order_items_order_id ON order_items (order_id);

INSERT INTO products (
  product_slug,
  name,
  description,
  category,
  segment,
  price,
  stock_quantity,
  image_url,
  default_color,
  available_colors,
  available_sizes,
  is_new_arrival,
  popularity
) VALUES
  ('shirt', 'Structured Poplin Shirt', 'Cotton poplin shirt with a sharp collar and clean tailored fit.', 'shirts-tops', 'men', 49.00, 25, 'https://lh3.googleusercontent.com/aida-public/AB6AXuCrgWwcqbnGCbjTzZ_L6va3PhP4cf4IkTCLovTQVVQ21uNl8Z-iAYJCbtNUwdrpW52ANcPOZwSBpeAkbpFZnb0-vmkLpg0C_vW6vRxtMZj6gBIA2DX3yK8ePkauxAxka-V-BfxLiFYxmc6Mx5JHfFpx3WxafijUCHffhouLljkrx8EiJup7kTSg2xicdPlld859d7hAUJTEWheTEcKB3y6rjnpquQM6TXl1jopT4NOnhBwGC5VSLaG6XmWgnXyjf0PVwYVyU0-uFaM', 'Optic White', '["Optic White"]'::jsonb, '["S","M","L"]'::jsonb, false, 82),
  ('moto-jacket', 'Asymmetric Moto Jacket', 'Premium leather moto jacket with asymmetric hardware.', 'outerwear', 'men', 189.00, 12, 'https://lh3.googleusercontent.com/aida-public/AB6AXuCTQq3n3DCDOOp4uvO55s47vNfLVHaHL4RRpmQqKOoCNfugHrUSjaTDkAo5K3YISIbA7a6jPcUShZsJ2Kg4juASweAYhtFdUprZXKWLmzBEBqEGrcYqOd5n_2XbRfHpRdAIXuQ_gktPbMPRiK3WoC9oUbfmD9gEkBh2mvSxQF4TL5GvJomBfX-CHRr8oP2nWafsoDLJB-Z1Ip4V9yuEAU8yFbdTe2Vn1fUWblpq2vamVyaLdv6edctwOp1ntp-15r6hJZVsMpz5A6U', 'Midnight Black', '["Midnight Black"]'::jsonb, '["S","M","L"]'::jsonb, false, 95),
  ('cashmere', 'Cashmere Crewneck', 'Soft cashmere crewneck with a relaxed everyday silhouette.', 'knitwear', 'women', 79.00, 18, 'https://lh3.googleusercontent.com/aida-public/AB6AXuBKRsTPHqnzmiLlQT6UJpkPFNyO_MC4TNYb8SLuuMT6eYVM26cT9KAJMjVQ1qZWuiqfIJzs66s0pT0Kd283kZj60MPLYqtTndM0cpkUqRHwhU7wS3BBgv6mWNsd1ufC4mvnDY_lBSgkqZOWwcnoMLrVFit4gZ4t7j3erTI0U5TjES76gjQHKOoL4Pyp_EcWeesh6GetZ1KXeMvU5n8YwQzEs_heAJKyYt2P3wc_hLNamSKoddmgUVxUJG1Ek392j3LSNcbQVF-imaw', 'Sand Cashmere', '["Sand Cashmere"]'::jsonb, '["XS","S","M","L"]'::jsonb, false, 88),
  ('double-coat', 'Double-Breasted Coat', 'Structured wool coat with a double-breasted front.', 'outerwear', 'women', 149.00, 10, 'https://lh3.googleusercontent.com/aida-public/AB6AXuBcfj6mRnoWAMit-uw8PQAtxQvdlt-iqN_hTRSSJrdUS9RKiKInc0kGnFBpnmILN-0f7085ep0PsOJAyJvcjGQC0AZV2KDseDTQhXIaTE-tlE9PDpl_W1Kv-UkVY5CX9ErctWGn33f-niKBOXx6nxueMslAmGGb8tuaNbO0Qrfd4RvLWNdFAVLw0UQgLLlaiUx3sfstE9KfQ3B8zYjtRhP83jmvpfPPrcIaadZsR6ApcWrRZFI3epE9kosgUMpHTjmgyQrEcdQE2rE', 'Deep Navy', '["Deep Navy"]'::jsonb, '["M","L"]'::jsonb, false, 90),
  ('sneaker', 'Leather Court Sneaker', 'Minimal full-grain leather court sneaker.', 'footwear', 'accessories', 69.00, 30, 'https://lh3.googleusercontent.com/aida-public/AB6AXuAUUNay2PR3AwHLDUHjx5Rujo1UCiEsk-gFH6OzLEieQu0HBGKsjxnRrFB5OZsc9q7B5o67a2InJi-Tdd3qIOWFJq2Todw4uuvtMZHAi5Cbb1uGXJ2gchjbwXGMDIdDwnmlo3L8KdqDZgb5A0Qu_u9e7ub53CAEpBZQTvyypWWQWnUUmr678MIqY9wQ-sv4MxG_ZRTmCD0MHg1aCUsq0ed1nrSrjQKUfem5AQoxDdWafvYL29gZ9NLCvNCfLF1cHxUNhPawinAQQ3c', 'Optic White', '["Optic White","Electric Blue"]'::jsonb, '["EU 40","EU 41","EU 42","EU 43","EU 44","EU 45"]'::jsonb, false, 86),
  ('trousers', 'Pleated Wide Trousers', 'High-waisted wide-leg trousers in Italian wool.', 'trousers', 'women', 59.00, 22, 'https://lh3.googleusercontent.com/aida-public/AB6AXuDuE_74HpXg2xZnPmAtU1hwrFAGeUUQLliTQ98uEl4VGiqN3aWycidkTB9OjKPu8hN1UHEG7s1uuA-UExUb0ah0ExP54zzeiWKm1wphy34Jgbk9dJLA87Jx_dvpE7M7bebLb3gIK9A5kb_8oBjTvBaU9fTn6G4Pe35kkg4htY0qyX2bn5Dw_CMLN5YewQj2YFp-A-wnTrwWeORhL54bfahvB8uF9zyiI4qq17CAILQv3ZB8Jn2-TGmQsjtUqheDI91WHuoytYiCkE0', 'Ash Gray', '["Ash Gray"]'::jsonb, '["XS","S","M","L"]'::jsonb, false, 80),
  ('coat', 'Wool Tailored Coat', 'Tailored wool coat with a refined navy finish.', 'outerwear', 'women', 129.00, 14, 'https://lh3.googleusercontent.com/aida-public/AB6AXuBchUcD0EUx2j2iuPQpWTJiIV8_zw6fVXkrrcoW0NNOHi1SxdQu66MH8cT3G92PJljVOKIKgAIxyBfrzWWenQqSPhvb1LWVR7mApfsOtw2uz6Y0KuD0iE38tFoufpB9nbsctHFEUVTIfLvBiOPst0XZ0luxheHlGNr5fdlKJaBuMALpmJsNioBdRUkPlV9y0lbkOIJe3LmZLgRYbVi9X9cNiyeudwa6R7G7FYt8ukQkzQCN-Lq6ASZTwC8kocRV1qzT1TLfE4Ck0ws', 'Deep Navy', '["Deep Navy"]'::jsonb, '["S","M","L"]'::jsonb, true, 86),
  ('heels', 'Leather Artisan Pump', 'Leather pump with a precise artisan finish.', 'footwear', 'women', 79.00, 16, 'https://lh3.googleusercontent.com/aida-public/AB6AXuCJnqionFY9y6EYbY_k4evRIJvyAUyoVg6PwXuwkjSxd9qMHcQnc8LWwRtP425gPIACwYi4BS5rEn6a0KGYC0FNUJvGW5DD0_kSsfAwo0JEcqudDivOu-ipKeVOJq6AGfrgwkWDX--L6eQ64NWowPC_RH-NzHnWgcRyYnqAasPBj1Kkzbs7jslOeGddj7tOwH78WBLt_Aj94R2TFl-YneG96_bN-w1tvvuaSkU-DBLuCJN2XQosE_KpP5P8hSYeH9aEujzdyJQhAKA', 'Black Leather', '["Black Leather"]'::jsonb, '["EU 36","EU 37","EU 38","EU 39","EU 40"]'::jsonb, true, 84),
  ('tote', 'Structural Leather Tote', 'Structured leather tote sized for daily carry.', 'bags', 'accessories', 89.00, 12, 'https://lh3.googleusercontent.com/aida-public/AB6AXuCQPtWo0O_h25ZD-uqlJNUOw_CMG6SjaIqwAdS7fF4-9zejORMZ_NS2z4p6KnILnMjKol4ArgZXQDMligjx1c4OZfzlnKZ0QCj3wVXujPKAC1OmvFlLDAwsIRshZBPok04K30_grPaCoCynebD3yteoWChW4NTJTnO3_ms3Qj_z61YdTuKy8wujCaIbt_2-hq2cf1tvvkCCTGkhrSYwJaECUQpkz1Nc7H3rbZ475nBm59md7IDO_JiPOxvWsjB_tlGvr5_oX2AaDRs', 'Cream Leather', '["Cream Leather"]'::jsonb, '["One Size"]'::jsonb, true, 83),
  ('gown', 'Silk Evening Gown', 'Silk evening gown with a clean formal drape.', 'dresses', 'women', 149.00, 8, 'https://lh3.googleusercontent.com/aida-public/AB6AXuDyT9BhCFk-MjJkEIVTVNYLmb1QxR0SY6MzQjKyVpH6ALokWVkyTHfXcL2ePj0kD36BSmDQ2m1UX1l9b4U2Tdu6Ux_Jwi1oNQSi08hC2M2BgKgNtq5cjSrCZV5A51TrT7tgWxx1dIIwS_fPxiRfi1HDyR9tLER9Vb2k5gL75JToaQ-7z0vD5JDn-D6NuRPHrHGIS4-DIAHKY57ZRJ0l3agSH1MbIGmaB2agVwG8Mxy37aKCRpprkhttpXXdfeoWXyvTErZQS8kAyv4', 'Ivory Silk', '["Ivory Silk"]'::jsonb, '["S","M","L"]'::jsonb, true, 87),
  ('cloud-runner', 'Cloud Runner', 'Lightweight runner with soft grey mesh.', 'footwear', 'men', 59.00, 28, 'https://lh3.googleusercontent.com/aida-public/AB6AXuDC93J-Mi_uyRyUJEzeo2VUt3gCnx1KweKP_DA251be5VmSSPWzQiE3EhjkhxBwd55Gdn3J9N_12Bg5RS3V-OcMAmCzGyfEn-qdSteSk2Sm2QxMbfFQaTV2PYuxS-hkbx1W8FzSC8WNHMBlUvT6u9qRYmqskiYnAom81LJ-H0Fo81sIZgmqT2a5PI1sSigLrSXPZT1NqMpPwhbGMQ7KSvY-jFjHsUWYYIzRGNgt1QR5JJXBwnUKEJSA31X41LiJjnESmcWWo8QyvTc', 'Soft Gray', '["Soft Gray","Optic White"]'::jsonb, '["EU 40","EU 41","EU 42","EU 43","EU 44"]'::jsonb, false, 91),
  ('downtown-high-top', 'Downtown High-Top', 'High-top leather sneaker with mahogany finish.', 'footwear', 'men', 79.00, 20, 'https://lh3.googleusercontent.com/aida-public/AB6AXuAjpeUssbT7HMtTqW4qyakzgJS1FiU-SwjRpIy1Tw9BZ6aW8dg1ia9mBEQTsXehCdnQ38ZNuPh-KwVqfaeAzAieT8_pSEHP2-gnuITMw24A-7ga_cJJtTAdWKtIKtSQB42G6Lq4ASqeKiDYxXOXIJ_svraHjMSC6L9xtrDwdUFYZMV5tu9rBzDSf-JQj_B9GQqtOtgotp4hmyshrGNnQ18q8fvkriRBUhxAH8NCNZuoL9oEZk47FOljzcP6gzOFg5XhTuuE5NEFFuA', 'Mahogany Leather', '["Mahogany Leather"]'::jsonb, '["EU 40","EU 41","EU 42","EU 43","EU 44"]'::jsonb, false, 93),
  ('essential-tote', 'Essential Tote', 'Sand leather tote with a fixed carry size.', 'bags', 'accessories', 69.00, 18, 'https://lh3.googleusercontent.com/aida-public/AB6AXuCZA8QsI1z-4yH3tVTcLmNVZSvq3xbj2ReUE6tr_ldz3pUm4x6nLz0K8UtTdzYAXvt7OIQ4lGi_6bYNDOyqL9I_QJVDOF48pDIH-Hl8svspIO25hllQki_zXnMSkeUILE1UWGtW97p85dErHOd_YKll0yV9E6Bw0WrAMHHHh88cflfziP3AfRvQ8HCNboloyIkI7rub2yxdcXDNddCggX3FjoxliU_zAL_I9FGwVFlgZKL2z8nMK2kpTSHbaQalI_DPVzFDIE-mzos', 'Sand Leather', '["Sand Leather"]'::jsonb, '["One Size"]'::jsonb, false, 89),
  ('mono-watch', 'Mono-Chrome Watch', 'Minimal steel watch with a monochrome face.', 'accessories', 'accessories', 99.00, 24, 'https://lh3.googleusercontent.com/aida-public/AB6AXuC4Z1PFbN5d5iX569B8_T5_giVQDfygycpDU7KE5ZeWXlQyLIJmRezROLUNVXqri-ol3nabfGSMPEJ4iCQRQ3T6ieAeILwdCX24kcjlMHyXEspuvHJa7qUNV4Ty59HPXyJstJCij6Din9uuEUaPdOqaoCdpTSzOijKAqnOB29zI02kZbQC29RivviKhLtFIO0S6vHSxpncfEwf_k_0GPHYo0IEKYDIqk8vh4-4e-BGwzb7smuC_FkCeST4f3apB7htMF-P6Mo4ECbE', 'Brushed Silver', '["Brushed Silver"]'::jsonb, '["38 mm","42 mm"]'::jsonb, false, 85)
ON CONFLICT (product_slug) DO UPDATE SET
  name = EXCLUDED.name,
  description = EXCLUDED.description,
  category = EXCLUDED.category,
  segment = EXCLUDED.segment,
  price = EXCLUDED.price,
  stock_quantity = EXCLUDED.stock_quantity,
  image_url = EXCLUDED.image_url,
  default_color = EXCLUDED.default_color,
  available_colors = EXCLUDED.available_colors,
  available_sizes = EXCLUDED.available_sizes,
  is_new_arrival = EXCLUDED.is_new_arrival,
  popularity = EXCLUDED.popularity,
  active = true,
  updated_at = now();

UPDATE products
SET vendor_id = (SELECT id FROM retailer_accounts WHERE email = 'system@luxe.local'),
    approval_status = 'approved',
    rejection_reason = '',
    archived_at = NULL,
    active = true
WHERE product_slug IN (
  'shirt',
  'moto-jacket',
  'cashmere',
  'double-coat',
  'sneaker',
  'trousers',
  'coat',
  'heels',
  'tote',
  'gown',
  'cloud-runner',
  'downtown-high-top',
  'essential-tote',
  'mono-watch'
);

UPDATE products
SET vendor_id = (SELECT id FROM retailer_accounts WHERE email = 'system@luxe.local')
WHERE vendor_id IS NULL;

ALTER TABLE products ALTER COLUMN vendor_id SET NOT NULL;
