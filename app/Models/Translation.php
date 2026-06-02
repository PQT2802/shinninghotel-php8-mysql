<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Translation extends Model
{
    private const MAP = [
        'page' => ['table' => 'page_translations', 'fk' => 'page_id'],
        'news' => ['table' => 'news_translations', 'fk' => 'news_id'],
        'room' => ['table' => 'room_translations', 'fk' => 'room_id'],
        'room_category' => ['table' => 'room_category_translations', 'fk' => 'category_id'],
        'banner' => ['table' => 'banner_translations', 'fk' => 'banner_id'],
        'menu_item' => ['table' => 'menu_item_translations', 'fk' => 'menu_item_id'],
    ];

    public static function save(string $entity, int $entityId, string $locale, array $fields): void
    {
        if (!isset(self::MAP[$entity])) {
            return;
        }
        $meta = self::MAP[$entity];
        $table = $meta['table'];
        $fk = $meta['fk'];

        $existing = self::findRow($table, $fk, $entityId, $locale);
        if ($existing) {
            $sets = [];
            $params = ['id' => $existing['id']];
            foreach ($fields as $col => $val) {
                $sets[] = "{$col} = :{$col}";
                $params[$col] = $val;
            }
            $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE id = :id';
            self::db()->prepare($sql)->execute($params);
            return;
        }

        $cols = array_merge([$fk, 'locale'], array_keys($fields));
        $placeholders = array_map(fn ($c) => ':' . $c, $cols);
        $params = array_merge([$fk => $entityId, 'locale' => $locale], $fields);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $cols),
            implode(', ', $placeholders)
        );
        self::db()->prepare($sql)->execute($params);
    }

    public static function forEntity(string $entity, int $entityId): array
    {
        if (!isset(self::MAP[$entity])) {
            return [];
        }
        $meta = self::MAP[$entity];
        $stmt = self::db()->prepare(
            'SELECT * FROM ' . $meta['table'] . ' WHERE ' . $meta['fk'] . ' = :id'
        );
        $stmt->execute(['id' => $entityId]);
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $out[$row['locale']] = $row;
        }

        return $out;
    }

    public static function translationStatus(string $entity, int $entityId): array
    {
        $all = self::forEntity($entity, $entityId);
        $status = [];
        foreach (['en', 'vi'] as $loc) {
            $status[$loc] = isset($all[$loc]) && !empty($all[$loc]['title'] ?? $all[$loc]['name'] ?? '');
        }

        return $status;
    }

    private static function findRow(string $table, string $fk, int $entityId, string $locale): ?array
    {
        $stmt = self::db()->prepare(
            "SELECT * FROM {$table} WHERE {$fk} = :id AND locale = :locale LIMIT 1"
        );
        $stmt->execute(['id' => $entityId, 'locale' => $locale]);

        return $stmt->fetch() ?: null;
    }
}
