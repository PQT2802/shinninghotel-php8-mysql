<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

abstract class Model
{
    protected static function db(): PDO
    {
        return Database::connection();
    }

    protected static function table(): string
    {
        throw new \RuntimeException('Model must define table()');
    }
}
