<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $path, array $data = []): void
    {
        View::render($path, $data);
    }

    protected function requirePermission(string $permission): void
    {
        if (!can_access($permission)) {
            http_response_code(403);
            echo '403 Forbidden';
            exit;
        }
    }

    protected function validate(array $data, array $rules, ?int $excludeId = null): bool
    {
        $validator = new Validator($data, $rules, ['exclude_id' => $excludeId]);
        if ($validator->fails()) {
            Session::flash('error', $validator->firstError() ?? 'Validation failed.');
            Session::set('validation_errors', $validator->errors());
            Session::set('old_input', $data);
            return false;
        }
        clear_validation_state();
        return true;
    }

    protected function redirect(string $url): never
    {
        Response::redirect($url);
    }

    protected function back(): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        Response::redirect($referer);
    }

    protected function withSuccess(string $message): never
    {
        Session::flash('success', $message);
        $this->back();
    }

    protected function withError(string $message): never
    {
        Session::flash('error', $message);
        $this->back();
    }
}
