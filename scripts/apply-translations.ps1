param(
    [string]$File = "database/migrations/012_translations.sql"
)

$Root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $Root

$path = Join-Path $Root $File
if (-not (Test-Path $path)) {
    Write-Error "File not found: $path"
    exit 1
}

$containerPath = "/tmp/apply_translations.sql"
docker cp $path shinning_mysql:$containerPath
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

docker compose exec mysql mysql -uroot -psecret --default-character-set=utf8mb4 shinning_hotel -e "source $containerPath"
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
Write-Host "Applied: $File" -ForegroundColor Green
