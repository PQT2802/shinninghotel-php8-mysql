<?php

declare(strict_types=1);

namespace App\Core;

class Logger
{
    private const MAX_BYTES = 5_242_880; // 5 MB

    public static function error(\Throwable $e): void
    {
        self::write('ERROR', $e->getMessage() . "\n" . $e->getTraceAsString());
    }

    public static function warning(string $message, array $context = []): void
    {
        $line = $message;
        if ($context !== []) {
            $line .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        }
        self::write('WARNING', $line);
    }

    public static function info(string $message): void
    {
        self::write('INFO', $message);
    }

    private static function write(string $level, string $message): void
    {
        $dir = BASE_PATH . '/storage/logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file = $dir . '/app.log';
        self::rotateIfNeeded($file);

        $entry = sprintf("[%s] %s: %s\n", date('Y-m-d H:i:s'), $level, trim($message));
        @file_put_contents($file, $entry, FILE_APPEND | LOCK_EX);
    }

    private static function rotateIfNeeded(string $file): void
    {
        if (!is_file($file) || filesize($file) < self::MAX_BYTES) {
            return;
        }

        $rotated = $file . '.' . date('Y-m-d_His');
        @rename($file, $rotated);

        $archives = glob($file . '.*') ?: [];
        usort($archives, fn ($a, $b) => filemtime($b) <=> filemtime($a));
        foreach (array_slice($archives, 5) as $old) {
            @unlink($old);
        }
    }
}
