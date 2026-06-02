<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static function table(): string
    {
        return 'users';
    }

    public static function findByEmail(string $email, ?int $excludeId = null): ?array
    {
        $sql = 'SELECT * FROM users WHERE email = :email';
        $params = ['email' => $email];
        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }
        $sql .= ' LIMIT 1';
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function all(): array
    {
        return self::db()->query('SELECT id, name, email, role, status, last_login_at, created_at FROM users ORDER BY id DESC')->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO users (name, email, password_hash, role, status) VALUES (:name, :email, :password_hash, :role, :status)'
        );
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => $data['password_hash'],
            'role' => $data['role'],
            'status' => $data['status'] ?? 'active',
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $fields = ['name = :name', 'email = :email', 'role = :role', 'status = :status'];
        $params = [
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => $data['status'],
        ];
        if (!empty($data['password_hash'])) {
            $fields[] = 'password_hash = :password_hash';
            $params['password_hash'] = $data['password_hash'];
        }
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
        self::db()->prepare($sql)->execute($params);
    }

    public static function updateLastLogin(int $id): void
    {
        self::db()->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id')->execute(['id' => $id]);
    }

    public static function count(): int
    {
        return (int) self::db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public static function delete(int $id): void
    {
        self::db()->prepare('DELETE FROM users WHERE id = :id')->execute(['id' => $id]);
    }
}
