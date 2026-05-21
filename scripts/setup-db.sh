#!/usr/bin/env bash
set -euo pipefail

if [ -f .env ]; then
  set -a
  # shellcheck disable=SC1091
  . ./.env
  set +a
fi

DB_NAME="${LUXE_DB_NAME:-luxe_ecommerce}"
DB_USER="${LUXE_DB_USER:-postgres}"

if ! psql -U "$DB_USER" -tAc "SELECT 1 FROM pg_database WHERE datname = '$DB_NAME'" | grep -q 1; then
  createdb -U "$DB_USER" "$DB_NAME"
fi

psql -U "$DB_USER" -v ON_ERROR_STOP=1 -d "$DB_NAME" -f db.sql
