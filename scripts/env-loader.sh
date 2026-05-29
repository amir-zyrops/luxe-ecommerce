#!/usr/bin/env bash

luxe_trim() {
  local value="$1"
  value="${value#"${value%%[![:space:]]*}"}"
  value="${value%"${value##*[![:space:]]}"}"
  printf '%s' "$value"
}

luxe_load_env_file() {
  local env_file="$1"
  local line key value first last

  [ -f "$env_file" ] || return 0

  while IFS= read -r line || [ -n "$line" ]; do
    line="$(luxe_trim "$line")"
    [[ -z "$line" || "${line:0:1}" == "#" || "$line" != *"="* ]] && continue

    key="$(luxe_trim "${line%%=*}")"
    [[ "$key" =~ ^[A-Za-z_][A-Za-z0-9_]*$ ]] || continue
    [[ -v $key ]] && continue

    value="$(luxe_trim "${line#*=}")"
    if [ "${#value}" -ge 2 ]; then
      first="${value:0:1}"
      last="${value: -1}"
      if [[ ("$first" == "\"" && "$last" == "\"") || ("$first" == "'" && "$last" == "'") ]]; then
        value="${value:1:${#value}-2}"
      fi
    fi

    export "$key=$value"
  done < "$env_file"
}

luxe_dsn_value() {
  local key="$1"
  local dsn="$2"
  local part normalized
  local parts=()

  IFS=';' read -r -a parts <<< "$dsn"
  for part in "${parts[@]}"; do
    normalized="${part#pgsql:}"
    if [[ "$normalized" == "$key="* ]]; then
      printf '%s' "${normalized#*=}"
      return 0
    fi
  done

  return 1
}
