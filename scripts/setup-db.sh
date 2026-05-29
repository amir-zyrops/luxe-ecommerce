#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd -- "$SCRIPT_DIR/.." && pwd)"

# shellcheck source=scripts/env-loader.sh
. "$SCRIPT_DIR/env-loader.sh"

luxe_load_env_file "$PROJECT_DIR/.env"

DB_NAME="${LUXE_DB_NAME:-}"
if [[ -z "$DB_NAME" && -n "${LUXE_DB_DSN:-}" ]]; then
  DB_NAME="$(luxe_dsn_value dbname "$LUXE_DB_DSN" || true)"
fi
DB_NAME="${DB_NAME:-luxe_ecommerce}"
DB_USER="${LUXE_DB_USER:-postgres}"
DB_HOST="${LUXE_DB_HOST:-}"
DB_PORT="${LUXE_DB_PORT:-}"

if [[ -z "$DB_HOST" && -n "${LUXE_DB_DSN:-}" ]]; then
  DB_HOST="$(luxe_dsn_value host "$LUXE_DB_DSN" || true)"
fi
if [[ -z "$DB_PORT" && -n "${LUXE_DB_DSN:-}" ]]; then
  DB_PORT="$(luxe_dsn_value port "$LUXE_DB_DSN" || true)"
fi

if [[ -v LUXE_DB_PASS ]]; then
  export PGPASSWORD="$LUXE_DB_PASS"
fi

PSQL_ARGS=(-U "$DB_USER")
CREATEDB_ARGS=(-U "$DB_USER")
if [[ -n "$DB_HOST" ]]; then
  PSQL_ARGS+=(-h "$DB_HOST")
  CREATEDB_ARGS+=(-h "$DB_HOST")
fi
if [[ -n "$DB_PORT" ]]; then
  PSQL_ARGS+=(-p "$DB_PORT")
  CREATEDB_ARGS+=(-p "$DB_PORT")
fi

DB_NAME_SQL="${DB_NAME//\'/\'\'}"
if ! psql "${PSQL_ARGS[@]}" -d postgres -tAc "SELECT 1 FROM pg_database WHERE datname = '$DB_NAME_SQL'" | grep -q 1; then
  createdb "${CREATEDB_ARGS[@]}" "$DB_NAME"
fi

psql "${PSQL_ARGS[@]}" -v ON_ERROR_STOP=1 -d "$DB_NAME" -f "$PROJECT_DIR/db.sql"
