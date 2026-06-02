<?php

declare(strict_types=1);

use App\Core\App;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
if (file_exists(BASE_PATH . '/.env')) {
    $dotenv->load();
} else {
    $dotenv->safeLoad();
}

$appConfig = require BASE_PATH . '/config/app.php';

if ($appConfig['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

App::run();
