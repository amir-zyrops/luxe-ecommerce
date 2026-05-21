#!/usr/bin/env bash
set -euo pipefail

if [ -f .env ]; then
  set -a
  # shellcheck disable=SC1091
  . ./.env
  set +a
fi

HOST="${LUXE_HOST:-127.0.0.1}"
PORT="${LUXE_PORT:-8000}"

exec php \
  -d extension=pgsql \
  -d extension=pdo_pgsql \
  -S "$HOST:$PORT"
