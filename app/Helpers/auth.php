<?php

declare(strict_types=1);

use App\Core\Session;

function auth_user(): ?array
{
    return Session::get('user');
}

function auth_check(): bool
{
    return auth_user() !== null;
}

function auth_id(): ?int
{
    $user = auth_user();
    return $user ? (int) $user['id'] : null;
}

function auth_role(): ?string
{
    $user = auth_user();
    return $user['role'] ?? null;
}

function can_access(string $permission): bool
{
    $role = auth_role();
    if ($role === 'super_admin') {
        return true;
    }
    $permissions = require BASE_PATH . '/config/permissions.php';
    $allowed = $permissions[$permission] ?? [];
    return in_array($role, $allowed, true);
}
