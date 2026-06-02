<?php

declare(strict_types=1);

function app_config(?string $key = null): mixed
{
    static $config = null;
    if ($config === null) {
        $config = require BASE_PATH . '/config/app.php';
    }
    if ($key === null) {
        return $config;
    }
    return $config[$key] ?? null;
}

function brand_name(): string
{
    return (string) app_config('brand');
}

function brand_slogan(): string
{
    return (string) app_config('slogan');
}
