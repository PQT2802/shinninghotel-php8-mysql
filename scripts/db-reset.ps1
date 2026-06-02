# Xoa volume MySQL va import lai schema + seed
$Root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $Root

Write-Host "Stopping MySQL and removing data volume..." -ForegroundColor Yellow
docker compose down -v
docker compose up -d
Write-Host "Wait ~30s for init scripts, then check: docker compose logs mysql" -ForegroundColor Cyan
