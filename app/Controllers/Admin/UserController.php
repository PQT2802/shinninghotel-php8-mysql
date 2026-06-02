<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

class UserController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('users.manage');
        $this->view('admin/users/index', ['title' => 'Users', 'users' => User::all()]);
    }

    public function create(): void
    {
        $this->requirePermission('users.manage');
        $this->view('admin/users/create', ['title' => 'Create User', 'user' => null]);
    }

    public function store(): void
    {
        $this->requirePermission('users.manage');
        $input = $this->inputFromPost();
        if (!$this->validate($input, [
            'name' => 'required|max:120',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:super_admin,admin,editor',
            'status' => 'required|in:active,inactive',
        ])) {
            $this->back();
        }
        if (!$this->allowedRole($input['role'])) {
            Session::flash('error', 'You cannot assign that role.');
            $this->back();
        }
        User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password_hash' => password_hash($input['password'], PASSWORD_DEFAULT),
            'role' => $input['role'],
            'status' => $input['status'],
        ]);
        Session::flash('success', 'User created.');
        $this->redirect(url('/admin/users'));
    }

    public function edit(int $id): void
    {
        $this->requirePermission('users.manage');
        $user = User::find($id);
        if (!$user) {
            Session::flash('error', 'User not found.');
            $this->redirect(url('/admin/users'));
        }
        unset($user['password_hash']);
        $this->view('admin/users/edit', ['title' => 'Edit User', 'user' => $user]);
    }

    public function update(int $id): void
    {
        $this->requirePermission('users.manage');
        $existing = User::find($id);
        if (!$existing) {
            Session::flash('error', 'User not found.');
            $this->redirect(url('/admin/users'));
        }
        $input = $this->inputFromPost();
        $rules = [
            'name' => 'required|max:120',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:super_admin,admin,editor',
            'status' => 'required|in:active,inactive',
        ];
        if (!empty($input['password'])) {
            $rules['password'] = 'min:6';
        }
        if (!$this->validate($input, $rules, $id)) {
            $this->back();
        }
        if (!$this->allowedRole($input['role'])) {
            Session::flash('error', 'You cannot assign that role.');
            $this->back();
        }
        $data = [
            'name' => $input['name'],
            'email' => $input['email'],
            'role' => $input['role'],
            'status' => $input['status'],
            'password_hash' => '',
        ];
        if (!empty($input['password'])) {
            $data['password_hash'] = password_hash($input['password'], PASSWORD_DEFAULT);
        }
        User::update($id, $data);
        Session::flash('success', 'User updated.');
        $this->redirect(url('/admin/users'));
    }

    public function delete(int $id): void
    {
        $this->requirePermission('users.manage');
        if ($id === auth_id()) {
            Session::flash('error', 'You cannot delete your own account.');
            $this->redirect(url('/admin/users'));
        }
        User::delete($id);
        Session::flash('success', 'User deleted.');
        $this->redirect(url('/admin/users'));
    }

    private function inputFromPost(): array
    {
        return [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? 'editor',
            'status' => $_POST['status'] ?? 'active',
        ];
    }

    private function allowedRole(string $role): bool
    {
        if (auth_role() === 'super_admin') {
            return true;
        }
        return in_array($role, ['admin', 'editor'], true);
    }
}
