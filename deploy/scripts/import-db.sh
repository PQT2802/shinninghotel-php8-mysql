#!/usr/bin/env bash
# Import schema + seed + migrations 012-015 (chạy từ máy có repo + ssh tới VPS)
# Usage: bash deploy/scripts/import-db.sh

set -euo pipefail

# ========== SỬA TẠI ĐÂY ==========
VPS_SSH="root@123.456.789.0"
DB_PASSWORD="CHANGE_ME_SHINNINGHOTEL_PASSWORD"
DB_NAME="shinninghotel_db"
DB_USER="shinninghotel_user"
MYSQL_CONTAINER="shinninghotel_mysql"
# ==================================

REPO_ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
cd "$REPO_ROOT"

run_sql() {
  local label="$1"
  echo "==> $label"
  ssh "$VPS_SSH" "docker exec -i $MYSQL_CONTAINER mysql -u${DB_USER} -p${DB_PASSWORD} ${DB_NAME}" < "$2"
  echo "OK: $label"
}

read -r -p "Import to $VPS_SSH / $DB_NAME — continue? [y/N] " ans
[[ "$ans" =~ ^[yY]$ ]] || exit 0

TMP_SCHEMA="$(mktemp)"
grep -v -E '^(CREATE DATABASE|USE )' database/schema.sql > "$TMP_SCHEMA"
run_sql "schema.sql" "$TMP_SCHEMA"
rm -f "$TMP_SCHEMA"

TMP_SEED="$(mktemp)"
sed 's/USE shinning_hotel/USE '"$DB_NAME"'/' database/seeders/seed.sql > "$TMP_SEED"
run_sql "seed.sql" "$TMP_SEED"
rm -f "$TMP_SEED"

for f in 012_translations.sql 013_repair_vi_utf8.sql 014_room_rich_content.sql 015_sync_en_vi_translations.sql; do
  run_sql "$f" "database/migrations/$f"
done

echo "Done. Admin: admin@shinning.com / password"
