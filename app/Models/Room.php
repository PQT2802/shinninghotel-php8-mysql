<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Locale;
use App\Core\Model;

class Room extends Model
{
    private static function localeParams(): array
    {
        $loc = locale();
        $fb = Locale::fallback();

        return [
            'loc_rt' => $loc,
            'fb_rt' => $fb,
            'loc_ct' => $loc,
            'fb_ct' => $fb,
        ];
    }

    private static string $roomSelect = 'r.*,
                COALESCE(rt.name, rtf.name, r.name) AS name,
                COALESCE(rt.description, rtf.description, r.description) AS description,
                COALESCE(ct.name, ctf.name, c.name) AS category_name,
                c.slug AS category_slug';
    public static function paginate(int $page = 1, int $perPage = 15, ?string $search = null, ?string $status = null, ?int $categoryId = null): array
    {
        $where = ['1=1'];
        $params = [];
        if ($search) {
            $where[] = '(r.name LIKE :q OR r.slug LIKE :q)';
            $params['q'] = '%' . $search . '%';
        }
        if ($status && in_array($status, ['draft', 'published'], true)) {
            $where[] = 'r.status = :status';
            $params['status'] = $status;
        }
        if ($categoryId) {
            $where[] = 'r.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }
        $whereSql = implode(' AND ', $where);

        $countStmt = self::db()->prepare(
            "SELECT COUNT(*) FROM rooms r WHERE {$whereSql}"
        );
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);
        $stmt = self::db()->prepare(
            "SELECT r.*, c.name AS category_name, c.slug AS category_slug
             FROM rooms r LEFT JOIN room_categories c ON c.id = r.category_id
             WHERE {$whereSql} ORDER BY r.sort_order, r.name LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => (int) max(1, ceil($total / $perPage)),
        ];
    }

    public static function published(array $filters = []): array
    {
        $lp = self::localeParams();
        $sql = 'SELECT ' . self::$roomSelect . '
                FROM rooms r
                LEFT JOIN room_translations rt ON rt.room_id = r.id AND rt.locale = :loc_rt
                LEFT JOIN room_translations rtf ON rtf.room_id = r.id AND rtf.locale = :fb_rt
                LEFT JOIN room_categories c ON c.id = r.category_id
                LEFT JOIN room_category_translations ct ON ct.category_id = c.id AND ct.locale = :loc_ct
                LEFT JOIN room_category_translations ctf ON ctf.category_id = c.id AND ctf.locale = :fb_ct
                WHERE r.status = :status AND (c.is_active = 1 OR c.id IS NULL)';
        $params = array_merge(['status' => 'published'], $lp);

        if (!empty($filters['category_id'])) {
            $sql .= ' AND r.category_id = :category_id';
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['guests_count'])) {
            $sql .= ' AND r.max_guests >= :guests_count';
            $params['guests_count'] = (int) $filters['guests_count'];
        }

        if (!empty($filters['check_in']) && !empty($filters['check_out'])) {
            $sql .= ' AND r.id NOT IN (
                SELECT room_id FROM bookings
                WHERE status IN (\'pending\', \'confirmed\')
                AND check_in < :check_out AND check_out > :check_in
            )';
            $params['check_in'] = $filters['check_in'];
            $params['check_out'] = $filters['check_out'];
        }

        $sql .= ' ORDER BY r.sort_order, r.name';
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function findPublishedBySlug(string $slug): ?array
    {
        $lp = self::localeParams();
        $stmt = self::db()->prepare(
            'SELECT ' . self::$roomSelect . ' FROM rooms r
             LEFT JOIN room_translations rt ON rt.room_id = r.id AND rt.locale = :loc_rt
             LEFT JOIN room_translations rtf ON rtf.room_id = r.id AND rtf.locale = :fb_rt
             LEFT JOIN room_categories c ON c.id = r.category_id
             LEFT JOIN room_category_translations ct ON ct.category_id = c.id AND ct.locale = :loc_ct
             LEFT JOIN room_category_translations ctf ON ctf.category_id = c.id AND ctf.locale = :fb_ct
             WHERE r.slug = :slug AND r.status = :status LIMIT 1'
        );
        $stmt->execute(array_merge(['slug' => $slug, 'status' => 'published'], $lp));
        $room = $stmt->fetch() ?: null;
        if ($room) {
            $room['gallery'] = RoomImage::pathsForRoom((int) $room['id']);
        }
        return $room;
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare(
            'SELECT r.*, c.name AS category_name FROM rooms r
             LEFT JOIN room_categories c ON c.id = r.category_id WHERE r.id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function featured(int $limit = 3): array
    {
        $lp = self::localeParams();
        $stmt = self::db()->prepare(
            'SELECT ' . self::$roomSelect . ' FROM rooms r
             LEFT JOIN room_translations rt ON rt.room_id = r.id AND rt.locale = :loc_rt
             LEFT JOIN room_translations rtf ON rtf.room_id = r.id AND rtf.locale = :fb_rt
             LEFT JOIN room_categories c ON c.id = r.category_id
             LEFT JOIN room_category_translations ct ON ct.category_id = c.id AND ct.locale = :loc_ct
             LEFT JOIN room_category_translations ctf ON ctf.category_id = c.id AND ctf.locale = :fb_ct
             WHERE r.status = :status AND r.is_featured = 1
             ORDER BY r.sort_order LIMIT :limit'
        );
        $stmt->execute(array_merge($lp, ['status' => 'published', 'limit' => $limit]));
        return $stmt->fetchAll();
    }

    public static function saveTranslations(int $roomId, array $byLocale): void
    {
        foreach ($byLocale as $loc => $fields) {
            if (empty($fields['name'])) {
                continue;
            }
            Translation::save('room', $roomId, $loc, [
                'name' => $fields['name'],
                'description' => $fields['description'] ?? '',
            ]);
        }
    }

    public static function isAvailable(int $roomId, string $checkIn, string $checkOut): bool
    {
        $stmt = self::db()->prepare(
            'SELECT COUNT(*) FROM bookings
             WHERE room_id = :room_id AND status IN (\'pending\', \'confirmed\')
             AND check_in < :check_out AND check_out > :check_in'
        );
        $stmt->execute([
            'room_id' => $roomId,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
        ]);
        return (int) $stmt->fetchColumn() === 0;
    }

    public static function all(): array
    {
        return self::db()->query(
            'SELECT r.*, c.name AS category_name FROM rooms r
             LEFT JOIN room_categories c ON c.id = r.category_id ORDER BY r.sort_order, r.name'
        )->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO rooms (category_id, name, slug, description, amenities, price_per_night, max_guests, image_path, status, is_featured, sort_order)
             VALUES (:category_id, :name, :slug, :description, :amenities, :price_per_night, :max_guests, :image_path, :status, :is_featured, :sort_order)'
        );
        $stmt->execute($data);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $stmt = self::db()->prepare(
            'UPDATE rooms SET category_id=:category_id, name=:name, slug=:slug, description=:description,
             amenities=:amenities, price_per_night=:price_per_night, max_guests=:max_guests, image_path=:image_path,
             status=:status, is_featured=:is_featured, sort_order=:sort_order WHERE id=:id'
        );
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        self::db()->prepare('DELETE FROM rooms WHERE id = :id')->execute(['id' => $id]);
    }

    public static function count(): int
    {
        return (int) self::db()->query('SELECT COUNT(*) FROM rooms')->fetchColumn();
    }
}
