<?php

declare(strict_types=1);

function url(string $path = ''): string
{
    $path = $path === '' ? '' : '/' . ltrim($path, '/');

    if ($path !== '' && (str_starts_with($path, '/admin') || str_starts_with($path, '/switch-locale') || str_starts_with($path, '/assets') || str_starts_with($path, '/uploads'))) {
        $config = require BASE_PATH . '/config/app.php';

        return rtrim($config['url'], '/') . $path;
    }

    if (function_exists('locale_url')) {
        return locale_url($path === '' ? '/' : $path);
    }

    $config = require BASE_PATH . '/config/app.php';

    return rtrim($config['url'], '/') . $path;
}

function asset(string $path): string
{
    return url('/assets/' . ltrim($path, '/'));
}

function upload_url(string $path): string
{
    if ($path === '') {
        return '';
    }
    if (str_starts_with($path, 'http')) {
        return $path;
    }
    return url('/uploads/' . ltrim($path, '/'));
}

function menu_item_url(array $item): string
{
    $path = trim((string) ($item['url'] ?? '/'));
    if ($path === '' || $path === '/') {
        return url('/');
    }

    return url($path);
}

function current_localized_path(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    if (preg_match('#^/(en|vi)(/.*)?$#', $uri, $m)) {
        return $m[2] ?? '/';
    }

    return $uri;
}
