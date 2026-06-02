<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Media extends Model
{
    public static function paginate(int $page = 1, int $perPage = 24): array
    {
        $total = self::count();
        $offset = max(0, ($page - 1) * $perPage);
        $stmt = self::db()->prepare('SELECT * FROM media ORDER BY id DESC LIMIT :limit OFFSET :offset');
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

    public static function all(): array
    {
        return self::db()->query('SELECT * FROM media ORDER BY id DESC')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM media WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO media (original_name, file_name, file_path, mime_type, file_size, uploaded_by)
             VALUES (:original_name, :file_name, :file_path, :mime_type, :file_size, :uploaded_by)'
        );
        $stmt->execute($data);
        return (int) self::db()->lastInsertId();
    }

    public static function delete(int $id): void
    {
        self::db()->prepare('DELETE FROM media WHERE id = :id')->execute(['id' => $id]);
    }

    public static function count(): int
    {
        return (int) self::db()->query('SELECT COUNT(*) FROM media')->fetchColumn();
    }

    /** @return array<int, array<string, mixed>> */
    public static function images(int $limit = 100): array
    {
        $stmt = self::db()->prepare(
            "SELECT * FROM media WHERE mime_type LIKE 'image/%' ORDER BY id DESC LIMIT :limit"
        );
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
