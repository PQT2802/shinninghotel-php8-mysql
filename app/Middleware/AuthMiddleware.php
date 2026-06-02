<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Response;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(callable $next): mixed
    {
        if (!Session::has('user')) {
            Response::redirect(url('/admin/login'));
        }
        return $next();
    }
}
