<?php

declare(strict_types=1);

use App\Core\Locale;

function locale(): string
{
    return Locale::current();
}

function __(string $key, array $replace = []): string
{
    static $lines = [];

    $loc = locale();
    if (!isset($lines[$loc])) {
        $file = BASE_PATH . '/lang/' . $loc . '.php';
        $lines[$loc] = is_file($file) ? require $file : [];
    }

    $value = array_get($lines[$loc], $key);
    if ($value === null) {
        $fallback = Locale::fallback();
        if ($fallback !== $loc) {
            if (!isset($lines[$fallback])) {
                $file = BASE_PATH . '/lang/' . $fallback . '.php';
                $lines[$fallback] = is_file($file) ? require $file : [];
            }
            $value = array_get($lines[$fallback], $key);
        }
    }

    $text = (string) ($value ?? $key);
    foreach ($replace as $k => $v) {
        $text = str_replace(':' . $k, (string) $v, $text);
    }

    return $text;
}

function array_get(array $array, string $key, mixed $default = null): mixed
{
    if (isset($array[$key])) {
        return $array[$key];
    }
    foreach (explode('.', $key) as $segment) {
        if (!is_array($array) || !array_key_exists($segment, $array)) {
            return $default;
        }
        $array = $array[$segment];
    }

    return $array;
}

function localized_path(string $path = ''): string
{
    $path = $path === '' ? '' : '/' . ltrim($path, '/');
    if ($path === '' || $path === '/') {
        return '/' . locale();
    }

    return '/' . locale() . $path;
}

function locale_url(string $path = '', ?string $forLocale = null): string
{
    $config = require BASE_PATH . '/config/app.php';
    $loc = $forLocale ?? locale();
    $path = $path === '' ? '' : '/' . ltrim($path, '/');

    if ($path === '' || $path === '/') {
        return rtrim($config['url'], '/') . '/' . $loc;
    }

    return rtrim($config['url'], '/') . '/' . $loc . $path;
}

function switch_locale_url(string $targetLocale): string
{
    return locale_url(current_localized_path(), $targetLocale);
}
