# Shinning Hotel — setup: Docker MySQL + PHP local
# Chạy: powershell -ExecutionPolicy Bypass -File scripts\setup.ps1

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $Root

Write-Host "==> Shinning Hotel setup" -ForegroundColor Cyan

# 1. Docker MySQL
if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Host "ERROR: Docker chua cai. Cai Docker Desktop: https://www.docker.com/products/docker-desktop/" -ForegroundColor Red
    exit 1
}

Write-Host "==> Starting MySQL (Docker)..." -ForegroundColor Yellow
docker compose up -d

Write-Host "==> Waiting for MySQL healthy..." -ForegroundColor Yellow
$max = 60
for ($i = 0; $i -lt $max; $i++) {
    $health = docker inspect --format='{{.State.Health.Status}}' shinning_mysql 2>$null
    if ($health -eq "healthy") { break }
    Start-Sleep -Seconds 2
}
if ($health -ne "healthy") {
    Write-Host "WARN: MySQL chua healthy — doi them hoac xem: docker compose logs mysql" -ForegroundColor Yellow
}

# 2. .env (Docker MySQL credentials)
if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "==> Created .env from .env.example" -ForegroundColor Green
} else {
    $envContent = Get-Content ".env" -Raw
    if ($envContent -match 'DB_USERNAME=root' -and $envContent -notmatch 'DB_PASSWORD=shinning') {
        Write-Host "==> Tip: cap nhat .env — DB_USERNAME=shinning, DB_PASSWORD=shinning (Docker)" -ForegroundColor Yellow
    }
}

# 3. PHP + Composer
$php = Get-Command php -ErrorAction SilentlyContinue
if (-not $php) {
    Write-Host ""
    Write-Host "PHP chua co trong PATH. Cai PHP 8 (chon mot):" -ForegroundColor Yellow
    Write-Host "  winget install PHP.PHP.8.3"
    Write-Host "  winget install PHP.PHP.8.2"
    Write-Host "Sau do bat lai terminal va chay lai script nay." -ForegroundColor Yellow
    Write-Host ""
    Write-Host "MySQL Docker da chay. DB: shinning_hotel / user: shinning / pass: shinning" -ForegroundColor Green
    exit 0
}

Write-Host "==> PHP: $(php -r 'echo PHP_VERSION;')" -ForegroundColor Green

if (-not (Test-Path "vendor\autoload.php")) {
  if (Get-Command composer -ErrorAction SilentlyContinue) {
    composer install --no-interaction
  } elseif (Test-Path "composer.phar") {
    php composer.phar install --no-interaction
  } else {
    Write-Host "==> Downloading composer.phar..." -ForegroundColor Yellow
    Invoke-WebRequest -Uri "https://getcomposer.org/download/latest-stable/composer.phar" -OutFile "composer.phar"
    php composer.phar install --no-interaction
  }
}

Write-Host "==> Download seed images..." -ForegroundColor Yellow
php scripts\download_seed_images.php

Write-Host ""
Write-Host "==> Done!" -ForegroundColor Green
Write-Host "  MySQL: 127.0.0.1:3306 (shinning / shinning)"
Write-Host "  App:   php -S localhost:8000 -t public"
Write-Host "  Web:   http://localhost:8000"
Write-Host "  Admin: http://localhost:8000/admin/login (admin@shinning.com / password)"
