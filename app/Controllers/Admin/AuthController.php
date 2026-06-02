<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

class AuthController extends Controller
{
    public function login(): void
    {
        $this->view('admin/auth/login', ['title' => 'Admin Login']);
    }

    public function authenticate(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = User::findByEmail($email);
        if (!$user || $user['status'] !== 'active' || !password_verify($password, $user['password_hash'])) {
            Session::flash('error', 'Invalid email or password.');
            $this->redirect(url('/admin/login'));
        }

        unset($user['password_hash']);
        Session::regenerate();
        Session::set('user', $user);
        User::updateLastLogin((int) $user['id']);

        $this->redirect(url('/admin'));
    }

    public function logout(): void
    {
        Session::forget('user');
        Session::regenerate();
        $this->redirect(url('/admin/login'));
    }
}
