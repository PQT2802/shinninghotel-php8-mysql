# Copy docker-compose + .env template lên VPS
# Sửa $VpsSsh rồi chạy từ repo root:
#   powershell -ExecutionPolicy Bypass -File deploy\scripts\copy-config-to-vps.ps1

$ErrorActionPreference = "Stop"

# ========== SỬA TẠI ĐÂY ==========
$VpsSsh = "root@123.456.789.0"
# ==================================

$RepoRoot = Resolve-Path (Join-Path $PSScriptRoot "..\..")
$RemoteDir = "/opt/apps/shinninghotel"

Write-Host "Copy to ${VpsSsh}:${RemoteDir}"

ssh $VpsSsh "mkdir -p $RemoteDir"

scp (Join-Path $RepoRoot "deploy\vps\docker-compose.yml.example") "${VpsSsh}:${RemoteDir}/docker-compose.yml"
scp (Join-Path $RepoRoot "deploy\vps\.env.example") "${VpsSsh}:${RemoteDir}/.env"

Write-Host "Done. SSH vào VPS và chạy: nano ${RemoteDir}/docker-compose.yml && nano ${RemoteDir}/.env"
