<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Locale;
use App\Core\Model;

class RoomCategory extends Model
{
    public static function allActive(): array
    {
        $lp = ['loc' => locale(), 'fb' => Locale::fallback()];
        $stmt = self::db()->prepare(
            'SELECT c.*, COALESCE(ct.name, ctf.name, c.name) AS name,
                COALESCE(ct.description, ctf.description, c.description) AS description
             FROM room_categories c
             LEFT JOIN room_category_translations ct ON ct.category_id = c.id AND ct.locale = :loc
             LEFT JOIN room_category_translations ctf ON ctf.category_id = c.id AND ctf.locale = :fb
             WHERE c.is_active = 1 ORDER BY c.sort_order, name'
        );
        $stmt->execute($lp);

        return $stmt->fetchAll();
    }

    public static function saveTranslations(int $categoryId, array $byLocale): void
    {
        foreach ($byLocale as $loc => $fields) {
            if (empty($fields['name'])) {
                continue;
            }
            Translation::save('room_category', $categoryId, $loc, [
                'name' => $fields['name'],
                'description' => $fields['description'] ?? '',
            ]);
        }
    }

    public static function all(): array
    {
        return self::db()->query(
            'SELECT c.*, (SELECT COUNT(*) FROM rooms r WHERE r.category_id = c.id) AS room_count
             FROM room_categories c ORDER BY c.sort_order, c.name'
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM room_categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO room_categories (name, slug, description, sort_order, is_active)
             VALUES (:name, :slug, :description, :sort_order, :is_active)'
        );
        $stmt->execute($data);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $data['id'] = $id;
        self::db()->prepare(
            'UPDATE room_categories SET name=:name, slug=:slug, description=:description,
             sort_order=:sort_order, is_active=:is_active WHERE id=:id'
        )->execute($data);
    }

    public static function delete(int $id): void
    {
        self::db()->prepare('DELETE FROM room_categories WHERE id = :id')->execute(['id' => $id]);
    }

    public static function countRooms(int $categoryId): int
    {
        $stmt = self::db()->prepare('SELECT COUNT(*) FROM rooms WHERE category_id = :id');
        $stmt->execute(['id' => $categoryId]);
        return (int) $stmt->fetchColumn();
    }
}
