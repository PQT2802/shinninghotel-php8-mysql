<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class RoomImage extends Model
{
    public static function forRoom(int $roomId): array
    {
        $stmt = self::db()->prepare(
            'SELECT * FROM room_images WHERE room_id = :room_id ORDER BY sort_order, id'
        );
        $stmt->execute(['room_id' => $roomId]);
        return $stmt->fetchAll();
    }

    /**
     * @param array<int, array{file_path: string, media_id?: int|null}> $images
     */
    public static function syncForRoom(int $roomId, array $images): void
    {
        self::db()->prepare('DELETE FROM room_images WHERE room_id = :room_id')
            ->execute(['room_id' => $roomId]);

        $order = 0;
        foreach ($images as $img) {
            $path = trim($img['file_path'] ?? '');
            if ($path === '') {
                continue;
            }
            $stmt = self::db()->prepare(
                'INSERT INTO room_images (room_id, file_path, media_id, sort_order)
                 VALUES (:room_id, :file_path, :media_id, :sort_order)'
            );
            $stmt->execute([
                'room_id' => $roomId,
                'file_path' => $path,
                'media_id' => !empty($img['media_id']) ? (int) $img['media_id'] : null,
                'sort_order' => $order++,
            ]);
        }
    }

    public static function pathsForRoom(int $roomId): array
    {
        return array_column(self::forRoom($roomId), 'file_path');
    }
}
