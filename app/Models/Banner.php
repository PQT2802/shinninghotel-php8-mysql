<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Locale;
use App\Core\Model;

class Banner extends Model
{
    public static function activeByPosition(string $position): array
    {
        $lp = ['loc' => locale(), 'fb' => Locale::fallback()];
        $stmt = self::db()->prepare(
            'SELECT b.*,
                COALESCE(bt.title, btf.title, b.title) AS title,
                COALESCE(bt.subtitle, btf.subtitle, b.subtitle) AS subtitle,
                COALESCE(bt.button_text, btf.button_text, b.button_text) AS button_text
             FROM banners b
             LEFT JOIN banner_translations bt ON bt.banner_id = b.id AND bt.locale = :loc
             LEFT JOIN banner_translations btf ON btf.banner_id = b.id AND btf.locale = :fb
             WHERE b.position = :position AND b.is_active = 1 ORDER BY b.sort_order'
        );
        $stmt->execute(array_merge(['position' => $position], $lp));
        return $stmt->fetchAll();
    }

    public static function saveTranslations(int $bannerId, array $byLocale): void
    {
        foreach ($byLocale as $loc => $fields) {
            if (empty($fields['title'])) {
                continue;
            }
            Translation::save('banner', $bannerId, $loc, [
                'title' => $fields['title'],
                'subtitle' => $fields['subtitle'] ?? '',
                'button_text' => $fields['button_text'] ?? '',
            ]);
        }
    }

    public static function all(): array
    {
        return self::db()->query('SELECT * FROM banners ORDER BY sort_order')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM banners WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO banners (title, subtitle, image_path, button_text, button_url, position, sort_order, is_active)
             VALUES (:title, :subtitle, :image_path, :button_text, :button_url, :position, :sort_order, :is_active)'
        );
        $stmt->execute($data);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $data['id'] = $id;
        self::db()->prepare(
            'UPDATE banners SET title=:title, subtitle=:subtitle, image_path=:image_path, button_text=:button_text,
             button_url=:button_url, position=:position, sort_order=:sort_order, is_active=:is_active WHERE id=:id'
        )->execute($data);
    }

    public static function delete(int $id): void
    {
        self::db()->prepare('DELETE FROM banners WHERE id = :id')->execute(['id' => $id]);
    }
}
