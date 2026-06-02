<?php

declare(strict_types=1);

namespace App\Core;

class Locale
{
    private static string $current = 'en';
    private static string $originalUri = '/';

    public static function bootstrap(): void
    {
        $config = require BASE_PATH . '/config/locale.php';
        $supported = $config['supported'];
        $default = $config['default'];

        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        self::$originalUri = $uri;

        if (preg_match('#^/(en|vi)(/.*)?$#', $uri, $m) && in_array($m[1], $supported, true)) {
            self::$current = $m[1];
            Session::set('locale', $m[1]);
            $stripped = $m[2] ?? '/';
            $_SERVER['REQUEST_URI'] = self::rebuildUri($stripped === '' ? '/' : $stripped);
            return;
        }

        if (str_starts_with($uri, '/admin') || str_starts_with($uri, '/switch-locale')) {
            self::$current = Session::get('locale') ?? $default;
            if (!in_array(self::$current, $supported, true)) {
                self::$current = $default;
            }
            return;
        }

        self::$current = self::detectPreferred($config);
        Session::set('locale', self::$current);
    }

    public static function shouldRedirectRoot(): bool
    {
        return self::$originalUri === '/';
    }

    public static function handleRootRedirect(): void
    {
        $config = require BASE_PATH . '/config/locale.php';
        $locale = self::detectPreferred($config);
        header('Location: ' . locale_url('/', $locale), true, 302);
        exit;
    }

    public static function current(): string
    {
        return self::$current;
    }

    public static function set(string $locale): void
    {
        $config = require BASE_PATH . '/config/locale.php';
        if (in_array($locale, $config['supported'], true)) {
            self::$current = $locale;
            Session::set('locale', $locale);
        }
    }

    public static function fallback(): string
    {
        $config = require BASE_PATH . '/config/locale.php';

        return $config['fallback'];
    }

    public static function supported(): array
    {
        $config = require BASE_PATH . '/config/locale.php';

        return $config['supported'];
    }

    private static function detectPreferred(array $config): string
    {
        $session = Session::get('locale');
        if ($session && in_array($session, $config['supported'], true)) {
            return $session;
        }

        $accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        if (stripos($accept, 'vi') !== false) {
            return 'vi';
        }

        return $config['default'];
    }

    private static function rebuildUri(string $path): string
    {
        $query = $_SERVER['QUERY_STRING'] ?? '';

        return $path . ($query !== '' ? '?' . $query : '');
    }
}
