# Import schema + seed + migrations 012-015 vào MySQL trên VPS
# Sửa 3 biến bên dưới rồi chạy từ thư mục gốc repo:
#   powershell -ExecutionPolicy Bypass -File deploy\scripts\import-db.ps1

$ErrorActionPreference = "Stop"

# ========== SỬA TẠI ĐÂY ==========
$VpsSsh       = "root@123.456.789.0"   # user@IP
$DbPassword   = "CHANGE_ME_SHINNINGHOTEL_PASSWORD"
$DbName       = "shinninghotel_db"
$DbUser       = "shinninghotel_user"
$MysqlContainer = "shinninghotel_mysql"
# ==================================

$RepoRoot = Resolve-Path (Join-Path $PSScriptRoot "..\..")
Set-Location $RepoRoot

$mysqlCmd = "docker exec -i $MysqlContainer mysql -u$DbUser -p$DbPassword $DbName"

function Invoke-SqlFile {
    param(
        [string]$Label,
        [scriptblock]$GetContent
    )
    Write-Host "`n==> $Label" -ForegroundColor Cyan
    & $GetContent | ssh $VpsSsh $mysqlCmd
    if ($LASTEXITCODE -ne 0) {
        throw "Failed: $Label (exit $LASTEXITCODE)"
    }
    Write-Host "OK: $Label" -ForegroundColor Green
}

Write-Host "Repo: $RepoRoot"
Write-Host "Target: $VpsSsh -> $DbName"
$confirm = Read-Host "Continue? (y/N)"
if ($confirm -notmatch '^[yY]') { exit 0 }

Invoke-SqlFile "schema.sql" {
    Get-Content "database\schema.sql" | Where-Object { $_ -notmatch '^(CREATE DATABASE|USE )' }
}

Invoke-SqlFile "seed.sql" {
    (Get-Content "database\seeders\seed.sql") -replace 'USE shinning_hotel', "USE $DbName"
}

$migrations = @(
    '012_translations.sql',
    '013_repair_vi_utf8.sql',
    '014_room_rich_content.sql',
    '015_sync_en_vi_translations.sql'
)

foreach ($f in $migrations) {
    $path = "database\migrations\$f"
    if (-not (Test-Path $path)) {
        throw "Missing file: $path"
    }
    Invoke-SqlFile $f { Get-Content $path }
}

Write-Host "`nDone. Test: http://YOUR_VPS_IP:8081/en/ and /vi/" -ForegroundColor Green
Write-Host "Admin: admin@shinning.com / password (change immediately)" -ForegroundColor Yellow
