<?php

declare(strict_types=1);

namespace App\Core;

class Csrf
{
    public static function token(): string
    {
        $config = require BASE_PATH . '/config/app.php';
        $name = $config['csrf_token_name'];

        if (!Session::has($name)) {
            Session::set($name, bin2hex(random_bytes(32)));
        }

        return Session::get($name);
    }

    public static function field(): string
    {
        $config = require BASE_PATH . '/config/app.php';
        $name = $config['csrf_token_name'];
        $token = self::token();

        return '<input type="hidden" name="' . e($name) . '" value="' . e($token) . '">';
    }

    public static function validate(?string $token): bool
    {
        $config = require BASE_PATH . '/config/app.php';
        $name = $config['csrf_token_name'];
        $stored = Session::get($name);

        return $stored !== null && $token !== null && hash_equals($stored, $token);
    }
}
