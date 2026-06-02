<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\Response;

class CsrfMiddleware
{
    public function handle(callable $next): mixed
    {
        $config = require BASE_PATH . '/config/app.php';
        $token = $_POST[$config['csrf_token_name']] ?? null;

        if (!Csrf::validate($token)) {
            http_response_code(419);
            echo 'Invalid CSRF token.';
            exit;
        }

        return $next();
    }
}
