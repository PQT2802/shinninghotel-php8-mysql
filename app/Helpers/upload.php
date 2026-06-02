<?php

declare(strict_types=1);

function allowed_upload_extensions(): array
{
    return ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
}

function upload_file(array $file, string $subdir = ''): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $config = require BASE_PATH . '/config/app.php';
    $maxBytes = ($config['upload_max_mb'] ?? 5) * 1024 * 1024;

    if (($file['size'] ?? 0) > $maxBytes) {
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, allowed_upload_extensions(), true)) {
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowedMimes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'pdf' => 'application/pdf',
    ];
    if (!isset($allowedMimes[$ext]) || $mime !== $allowedMimes[$ext]) {
        return null;
    }

    $safeName = bin2hex(random_bytes(16)) . '.' . $ext;
    $dir = BASE_PATH . '/storage/uploads/' . trim($subdir, '/');
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $relative = ($subdir ? trim($subdir, '/') . '/' : '') . $safeName;
    $dest = BASE_PATH . '/storage/uploads/' . $relative;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }

    $publicDir = BASE_PATH . '/public/uploads/' . dirname($relative);
    if (!is_dir($publicDir)) {
        mkdir($publicDir, 0755, true);
    }
    @copy($dest, BASE_PATH . '/public/uploads/' . $relative);

    return $relative;
}

function delete_upload_file(?string $relativePath): void
{
    if ($relativePath === null || $relativePath === '') {
        return;
    }
    $paths = [
        BASE_PATH . '/storage/uploads/' . $relativePath,
        BASE_PATH . '/public/uploads/' . $relativePath,
    ];
    foreach ($paths as $path) {
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
