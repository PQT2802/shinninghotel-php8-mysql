<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$_ENV['APP_ENV'] = 'testing';
$_ENV['APP_DEBUG'] = 'true';
$_ENV['HOTEL_BRAND'] = 'Shinning';
$_ENV['HOTEL_SLOGAN'] = 'Where Every Stay Shines';
$_ENV['APP_URL'] = 'http://localhost:8000';
