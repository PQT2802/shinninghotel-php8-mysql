<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Locale;
use App\Core\Model;

class MenuItem extends Model
{
    public static function allForMenu(int $menuId, bool $activeOnly = false): array
    {
        $lp = ['loc' => locale(), 'fb' => Locale::fallback()];
        $sql = 'SELECT mi.*,
                COALESCE(mit.title, mitf.title, mi.title) AS title
                FROM menu_items mi
                LEFT JOIN menu_item_translations mit ON mit.menu_item_id = mi.id AND mit.locale = :loc
                LEFT JOIN menu_item_translations mitf ON mitf.menu_item_id = mi.id AND mitf.locale = :fb
                WHERE mi.menu_id = :menu_id';
        if ($activeOnly) {
            $sql .= ' AND mi.is_active = 1';
        }
        $sql .= ' ORDER BY mi.sort_order, mi.id';
        $stmt = self::db()->prepare($sql);
        $stmt->execute(array_merge(['menu_id' => $menuId], $lp));
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM menu_items WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO menu_items (menu_id, parent_id, title, url, page_id, target, sort_order, is_active)
             VALUES (:menu_id, :parent_id, :title, :url, :page_id, :target, :sort_order, :is_active)'
        );
        $stmt->execute([
            'menu_id' => $data['menu_id'],
            'parent_id' => $data['parent_id'] ?? null,
            'title' => $data['title'],
            'url' => $data['url'] ?? null,
            'page_id' => $data['page_id'] ?? null,
            'target' => $data['target'] ?? '_self',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (int) ($data['is_active'] ?? 1),
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = self::db()->prepare(
            'UPDATE menu_items SET title=:title, url=:url, page_id=:page_id, target=:target,
             sort_order=:sort_order, is_active=:is_active WHERE id=:id AND menu_id=:menu_id'
        );
        $stmt->execute([
            'id' => $id,
            'menu_id' => $data['menu_id'],
            'title' => $data['title'],
            'url' => $data['url'] ?? null,
            'page_id' => $data['page_id'] ?? null,
            'target' => $data['target'] ?? '_self',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (int) ($data['is_active'] ?? 1),
        ]);
    }

    public static function delete(int $id): void
    {
        self::db()->prepare('DELETE FROM menu_items WHERE id = :id')->execute(['id' => $id]);
    }

    /**
     * Replace all items for a menu from admin form rows.
     *
     * @param array<int, array<string, mixed>> $rows
     */
    public static function syncMenu(int $menuId, array $rows): void
    {
        $keptIds = [];
        $order = 0;
        foreach ($rows as $row) {
            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $pageId = !empty($row['page_id']) ? (int) $row['page_id'] : null;
            $url = trim((string) ($row['url'] ?? ''));
            if ($pageId) {
                $page = Page::find($pageId);
                if ($page) {
                    $url = match ($page['slug']) {
                        'about-us' => '/about',
                        'location' => '/location',
                        default => '/page/' . $page['slug'],
                    };
                }
            }

            $data = [
                'menu_id' => $menuId,
                'title' => $title,
                'url' => $url ?: null,
                'page_id' => $pageId,
                'target' => ($row['target'] ?? '_self') === '_blank' ? '_blank' : '_self',
                'sort_order' => $order++,
                'is_active' => !empty($row['is_active']) ? 1 : 0,
            ];
            $itemId = !empty($row['id']) ? (int) $row['id'] : 0;
            if ($itemId > 0 && self::find($itemId)) {
                self::update($itemId, $data);
                $keptIds[] = $itemId;
            } else {
                $keptIds[] = self::create($data);
            }
        }

        if ($keptIds === []) {
            self::db()->prepare('DELETE FROM menu_items WHERE menu_id = :menu_id')
                ->execute(['menu_id' => $menuId]);
            return;
        }

        $placeholders = implode(',', array_fill(0, count($keptIds), '?'));
        $stmt = self::db()->prepare(
            "DELETE FROM menu_items WHERE menu_id = ? AND id NOT IN ({$placeholders})"
        );
        $stmt->execute(array_merge([$menuId], $keptIds));
    }
}
