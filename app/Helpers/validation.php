<?php

declare(strict_types=1);

use App\Core\Session;

function old(string $key, mixed $default = ''): mixed
{
    $input = Session::get('old_input') ?? [];
    return $input[$key] ?? $default;
}

function validation_error(string $field): ?string
{
    $errors = Session::get('validation_errors') ?? [];
    return $errors[$field][0] ?? null;
}

function clear_validation_state(): void
{
    Session::forget('old_input');
    Session::forget('validation_errors');
}
