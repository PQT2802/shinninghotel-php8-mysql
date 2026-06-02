<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Menu extends Model
{
    public static function findByLocation(string $location): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM menus WHERE location = :location LIMIT 1');
        $stmt->execute(['location' => $location]);
        return $stmt->fetch() ?: null;
    }

    public static function itemsForMenu(int $menuId): array
    {
        return MenuItem::allForMenu($menuId, true);
    }

    public static function all(): array
    {
        return self::db()->query('SELECT * FROM menus ORDER BY id')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM menus WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }
}
