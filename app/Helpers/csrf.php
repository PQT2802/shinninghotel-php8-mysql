<?php

declare(strict_types=1);

use App\Core\Csrf;

function csrf_field(): string
{
    return Csrf::field();
}

function csrf_token(): string
{
    return Csrf::token();
}
