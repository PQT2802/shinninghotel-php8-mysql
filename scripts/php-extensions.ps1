# Bat extension PHP (chay 1 lan sau khi cai PHP qua winget)
$phpDir = Split-Path (Get-Command php -ErrorAction Stop).Source -Parent
$ini = Join-Path $phpDir "php.ini"
if (-not (Test-Path $ini)) {
    Copy-Item (Join-Path $phpDir "php.ini-development") $ini
}
$content = Get-Content $ini -Raw
$enable = @('pdo_mysql', 'mysqli', 'mbstring', 'openssl', 'fileinfo', 'curl', 'zip')
foreach ($ext in $enable) {
    $content = $content -replace ";extension=$ext", "extension=$ext"
}
if ($content -notmatch 'extension_dir\s*=') {
    $content = $content -replace ';extension_dir = "ext"', 'extension_dir = "ext"'
}
Set-Content $ini $content -NoNewline
Write-Host "Updated: $ini"
php -m | Select-String "pdo_mysql|zip|fileinfo"
