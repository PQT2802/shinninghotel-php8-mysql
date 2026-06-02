# Repair corrupted Vietnamese in CMS tables (UTF-8 safe via docker cp)
$Root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $Root

$sqlFile = Join-Path $Root "database\migrations\013_repair_vi_utf8.sql"
if (-not (Test-Path $sqlFile)) {
    Write-Error "Missing: $sqlFile"
    exit 1
}

Write-Host "Copying SQL into MySQL container (preserves UTF-8)..." -ForegroundColor Cyan
docker cp $sqlFile shinning_mysql:/tmp/013_repair_vi_utf8.sql
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

docker compose exec mysql mysql -uroot -psecret --default-character-set=utf8mb4 shinning_hotel -e "source /tmp/013_repair_vi_utf8.sql"
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "Applying rich room content..." -ForegroundColor Cyan
$roomSql = Join-Path $Root "database\migrations\014_room_rich_content.sql"
docker cp $roomSql shinning_mysql:/tmp/014_room_rich_content.sql
docker compose exec mysql mysql -uroot -psecret --default-character-set=utf8mb4 shinning_hotel -e "source /tmp/014_room_rich_content.sql"
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "Syncing EN/VI translations..." -ForegroundColor Cyan
$syncSql = Join-Path $Root "database\migrations\015_sync_en_vi_translations.sql"
docker cp $syncSql shinning_mysql:/tmp/015_sync_en_vi_translations.sql
docker compose exec mysql mysql -uroot -psecret --default-character-set=utf8mb4 shinning_hotel -e "source /tmp/015_sync_en_vi_translations.sql"
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "Done. Refresh room detail pages (Ctrl+F5)." -ForegroundColor Green
