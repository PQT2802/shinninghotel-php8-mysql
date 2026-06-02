<?php

declare(strict_types=1);

namespace App\Core;

class App
{
    public static function run(): void
    {
        Session::start();
        Locale::bootstrap();

        $request = new Request();
        $router = new Router();

        require BASE_PATH . '/config/routes.php';

        try {
            $router->dispatch($request);
        } catch (\Throwable $e) {
            self::handleException($e);
        }
    }

    private static function handleException(\Throwable $e): void
    {
        $config = require BASE_PATH . '/config/app.php';
        Logger::error($e);

        http_response_code(500);
        if ($config['debug']) {
            echo '<h1>Application Error</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        } else {
            View::render('web/errors/500', ['title' => 'Server Error']);
        }
    }
}
