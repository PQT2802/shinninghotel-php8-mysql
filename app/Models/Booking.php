<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Booking extends Model
{
    public static function create(array $data): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO bookings (room_id, check_in, check_out, guest_name, guest_email, guest_phone, guests_count, total_price, status, notes, locale)
             VALUES (:room_id, :check_in, :check_out, :guest_name, :guest_email, :guest_phone, :guests_count, :total_price, :status, :notes, :locale)'
        );
        $stmt->execute($data);
        return (int) self::db()->lastInsertId();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare(
            'SELECT b.*, r.name AS room_name, r.slug AS room_slug, r.image_path AS room_image,
                    c.name AS category_name
             FROM bookings b
             JOIN rooms r ON r.id = b.room_id
             LEFT JOIN room_categories c ON c.id = r.category_id
             WHERE b.id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function paginate(int $page = 1, int $perPage = 20, ?string $status = null): array
    {
        $where = '1=1';
        $params = [];
        if ($status && in_array($status, ['pending', 'confirmed', 'cancelled'], true)) {
            $where = 'b.status = :status';
            $params['status'] = $status;
        }

        $countStmt = self::db()->prepare("SELECT COUNT(*) FROM bookings b WHERE {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);
        $stmt = self::db()->prepare(
            "SELECT b.*, r.name AS room_name FROM bookings b
             JOIN rooms r ON r.id = b.room_id
             WHERE {$where} ORDER BY b.created_at DESC LIMIT :limit OFFSET :offset"
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

    public static function allRecent(int $limit = 20): array
    {
        $stmt = self::db()->prepare(
            'SELECT b.*, r.name AS room_name FROM bookings b
             JOIN rooms r ON r.id = b.room_id
             ORDER BY b.created_at DESC LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function updateStatus(int $id, string $status): void
    {
        self::db()->prepare('UPDATE bookings SET status = :status WHERE id = :id')
            ->execute(['id' => $id, 'status' => $status]);
    }

    public static function countPending(): int
    {
        return (int) self::db()->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
    }

    public static function countByStatus(string $status): int
    {
        $stmt = self::db()->prepare('SELECT COUNT(*) FROM bookings WHERE status = :status');
        $stmt->execute(['status' => $status]);
        return (int) $stmt->fetchColumn();
    }

    public static function countAll(): int
    {
        return (int) self::db()->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
    }

    public static function sumRevenueByStatuses(array $statuses): float
    {
        if ($statuses === []) {
            return 0.0;
        }
        $placeholders = implode(',', array_fill(0, count($statuses), '?'));
        $stmt = self::db()->prepare(
            "SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE status IN ({$placeholders})"
        );
        $stmt->execute(array_values($statuses));

        return (float) $stmt->fetchColumn();
    }

    public static function countCreatedSince(string $date): int
    {
        $stmt = self::db()->prepare('SELECT COUNT(*) FROM bookings WHERE created_at >= :since');
        $stmt->execute(['since' => $date]);

        return (int) $stmt->fetchColumn();
    }

    /** @return array<string, int|float> */
    public static function dashboardStats(): array
    {
        $monthStart = date('Y-m-01 00:00:00');

        return [
            'total' => self::countAll(),
            'pending' => self::countByStatus('pending'),
            'confirmed' => self::countByStatus('confirmed'),
            'cancelled' => self::countByStatus('cancelled'),
            'revenue_confirmed' => self::sumRevenueByStatuses(['confirmed']),
            'revenue_pending' => self::sumRevenueByStatuses(['pending']),
            'this_month' => self::countCreatedSince($monthStart),
        ];
    }
}
