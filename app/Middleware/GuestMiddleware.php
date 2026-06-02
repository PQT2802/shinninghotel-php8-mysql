<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Response;
use App\Core\Session;

class GuestMiddleware
{
    public function handle(callable $next): mixed
    {
        if (Session::has('user')) {
            Response::redirect(url('/admin'));
        }
        return $next();
    }
}
