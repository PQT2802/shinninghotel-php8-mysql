<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class ContactMessage extends Model
{
    public static function create(array $data): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (:name, :email, :phone, :subject, :message)'
        );
        $stmt->execute($data);
        return (int) self::db()->lastInsertId();
    }

    public static function paginate(int $page = 1, int $perPage = 20, ?string $status = null): array
    {
        $where = '1=1';
        $params = [];
        if ($status && in_array($status, ['unread', 'read'], true)) {
            $where = 'status = :status';
            $params['status'] = $status;
        }

        $countStmt = self::db()->prepare("SELECT COUNT(*) FROM contact_messages WHERE {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);
        $stmt = self::db()->prepare(
            "SELECT * FROM contact_messages WHERE {$where} ORDER BY created_at DESC LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => (int) max(1, ceil($total / $perPage)),
        ];
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM contact_messages WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function recent(int $limit = 10): array
    {
        $stmt = self::db()->prepare('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT :limit');
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function all(): array
    {
        return self::db()->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
    }

    public static function markRead(int $id): void
    {
        self::db()->prepare("UPDATE contact_messages SET status = 'read' WHERE id = :id")->execute(['id' => $id]);
    }

    public static function markUnread(int $id): void
    {
        self::db()->prepare("UPDATE contact_messages SET status = 'unread' WHERE id = :id")->execute(['id' => $id]);
    }

    public static function delete(int $id): void
    {
        self::db()->prepare('DELETE FROM contact_messages WHERE id = :id')->execute(['id' => $id]);
    }

    public static function countUnread(): int
    {
        return (int) self::db()->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'")->fetchColumn();
    }
}
