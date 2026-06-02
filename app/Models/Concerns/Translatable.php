<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Core\Locale;

trait Translatable
{
    protected static function translationJoin(string $table, string $fkColumn, string $alias = 't'): string
    {
        $loc = locale();
        $fallback = Locale::fallback();

        return "LEFT JOIN {$table} {$alias} ON {$alias}.{$fkColumn} = e.id AND {$alias}.locale = " . self::db()->quote($loc)
            . " LEFT JOIN {$table} {$alias}f ON {$alias}f.{$fkColumn} = e.id AND {$alias}f.locale = " . self::db()->quote($fallback);
    }

    protected static function coalesceField(string $field, string $alias = 't', string $fallbackAlias = 'tf'): string
    {
        return "COALESCE(NULLIF({$alias}.{$field}, ''), {$fallbackAlias}.{$field})";
    }
}
