<?php

declare(strict_types=1);

/** @return list<string> */
function room_gallery_paths(array $room): array
{
    $paths = [];
    if (!empty($room['image_path'])) {
        $paths[] = (string) $room['image_path'];
    }
    foreach ($room['gallery'] ?? [] as $path) {
        $path = (string) $path;
        if ($path !== '' && !in_array($path, $paths, true)) {
            $paths[] = $path;
        }
    }

    return $paths;
}

function room_image_file_exists(string $path): bool
{
    if (str_starts_with($path, 'http')) {
        return true;
    }

    $public = defined('BASE_PATH')
        ? BASE_PATH . '/public/uploads/' . ltrim($path, '/')
        : '';

    return $public !== '' && is_file($public);
}

function room_image_url(?string $path, string $fallback = 'seed/room-standard.jpg'): string
{
    $path = trim((string) $path);
    if ($path !== '' && room_image_file_exists($path)) {
        return upload_url($path);
    }

    if (room_image_file_exists($fallback)) {
        return upload_url($fallback);
    }

    return upload_url($path !== '' ? $path : $fallback);
}
