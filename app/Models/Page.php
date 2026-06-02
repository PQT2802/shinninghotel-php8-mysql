<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Locale;
use App\Core\Model;

class Page extends Model
{
    public static function paginate(int $page = 1, int $perPage = 15, ?string $search = null, ?string $status = null): array
    {
        $where = ['1=1'];
        $params = [];
        if ($search) {
            $where[] = '(p.title LIKE :q OR p.slug LIKE :q)';
            $params['q'] = '%' . $search . '%';
        }
        if ($status && in_array($status, ['draft', 'published'], true)) {
            $where[] = 'p.status = :status';
            $params['status'] = $status;
        }
        $whereSql = implode(' AND ', $where);

        $countStmt = self::db()->prepare("SELECT COUNT(*) FROM pages p WHERE {$whereSql}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);
        $stmt = self::db()->prepare(
            "SELECT p.* FROM pages p WHERE {$whereSql} ORDER BY p.updated_at DESC, p.id DESC LIMIT :limit OFFSET :offset"
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

    public static function all(): array
    {
        return self::db()->query('SELECT * FROM pages ORDER BY updated_at DESC, id DESC')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM pages WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findPublishedBySlug(string $slug, ?string $locale = null): ?array
    {
        $loc = $locale ?? locale();
        $fb = Locale::fallback();
        $stmt = self::db()->prepare(
            'SELECT p.*,
                COALESCE(pt.title, ptf.title, p.title) AS title,
                COALESCE(pt.content, ptf.content, p.content) AS content,
                COALESCE(pt.seo_title, ptf.seo_title, p.seo_title) AS seo_title,
                COALESCE(pt.seo_description, ptf.seo_description, p.seo_description) AS seo_description
             FROM pages p
             LEFT JOIN page_translations pt ON pt.page_id = p.id AND pt.locale = :loc
             LEFT JOIN page_translations ptf ON ptf.page_id = p.id AND ptf.locale = :fb
             WHERE p.slug = :slug AND p.status = :status LIMIT 1'
        );
        $stmt->execute(['slug' => $slug, 'status' => 'published', 'loc' => $loc, 'fb' => $fb]);

        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO pages (title, slug, content, status, seo_title, seo_description, created_by, published_at)
             VALUES (:title, :slug, :content, :status, :seo_title, :seo_description, :created_by, :published_at)'
        );
        $stmt->execute($data);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = self::db()->prepare(
            'UPDATE pages SET title=:title, slug=:slug, content=:content, status=:status,
             seo_title=:seo_title, seo_description=:seo_description, updated_by=:updated_by, published_at=:published_at
             WHERE id=:id'
        );
        $data['id'] = $id;
        $stmt->execute($data);
    }

    public static function saveTranslations(int $pageId, array $byLocale): void
    {
        foreach ($byLocale as $loc => $fields) {
            if (empty($fields['title'])) {
                continue;
            }
            Translation::save('page', $pageId, $loc, [
                'title' => $fields['title'],
                'content' => $fields['content'] ?? '',
                'seo_title' => $fields['seo_title'] ?? '',
                'seo_description' => $fields['seo_description'] ?? '',
            ]);
        }
    }

    public static function delete(int $id): void
    {
        self::db()->prepare('DELETE FROM pages WHERE id = :id')->execute(['id' => $id]);
    }

    public static function count(): int
    {
        return (int) self::db()->query('SELECT COUNT(*) FROM pages')->fetchColumn();
    }

    public static function publishedList(): array
    {
        return self::db()->query("SELECT id, title, slug FROM pages WHERE status = 'published' ORDER BY title")->fetchAll();
    }
}
