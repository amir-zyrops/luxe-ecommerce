#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd -- "$SCRIPT_DIR/.." && pwd)"

# shellcheck source=scripts/env-loader.sh
. "$SCRIPT_DIR/env-loader.sh"

luxe_load_env_file "$PROJECT_DIR/.env"

HOST="${LUXE_HOST:-127.0.0.1}"
PORT="${LUXE_PORT:-8000}"

cd "$PROJECT_DIR"

echo "LUXE storefront: http://$HOST:$PORT/"
echo "Retailer portal: http://$HOST:$PORT/retailer/login.php"

exec php \
  -d extension=pgsql \
  -d extension=pdo_pgsql \
  -S "$HOST:$PORT"
