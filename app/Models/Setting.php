<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Setting extends Model
{
    public static function allKeyed(): array
    {
        $rows = self::db()->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $out[$row['setting_key']] = $row['setting_value'];
        }
        return $out;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $stmt = self::db()->prepare('SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1');
        $stmt->execute(['key' => $key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? (string) $val : $default;
    }

    public static function set(string $key, ?string $value, string $type = 'text', string $group = 'general'): void
    {
        $stmt = self::db()->prepare(
            'INSERT INTO settings (setting_key, setting_value, setting_type, group_name)
             VALUES (:key, :value, :type, :group)
             ON DUPLICATE KEY UPDATE setting_value = :value2, setting_type = :type2, group_name = :group2'
        );
        $stmt->execute([
            'key' => $key,
            'value' => $value,
            'type' => $type,
            'group' => $group,
            'value2' => $value,
            'type2' => $type,
            'group2' => $group,
        ]);
    }
}
