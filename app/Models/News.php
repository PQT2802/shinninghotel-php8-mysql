<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Locale;
use App\Core\Model;

class News extends Model
{
    private static function localeParams(): array
    {
        return ['loc' => locale(), 'fb' => Locale::fallback()];
    }
    public static function paginate(int $page = 1, int $perPage = 15, ?string $search = null, ?string $status = null): array
    {
        $where = ['1=1'];
        $params = [];
        if ($search) {
            $where[] = '(title LIKE :q OR slug LIKE :q)';
            $params['q'] = '%' . $search . '%';
        }
        if ($status && in_array($status, ['draft', 'published'], true)) {
            $where[] = 'status = :status';
            $params['status'] = $status;
        }
        $whereSql = implode(' AND ', $where);

        $countStmt = self::db()->prepare("SELECT COUNT(*) FROM news WHERE {$whereSql}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);
        $stmt = self::db()->prepare(
            "SELECT * FROM news WHERE {$whereSql} ORDER BY id DESC LIMIT :limit OFFSET :offset"
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

    public static function published(int $limit = 20, int $offset = 0): array
    {
        $lp = self::localeParams();
        $stmt = self::db()->prepare(
            'SELECT n.*,
                COALESCE(nt.title, ntf.title, n.title) AS title,
                COALESCE(nt.summary, ntf.summary, n.summary) AS summary,
                COALESCE(nt.content, ntf.content, n.content) AS content
             FROM news n
             LEFT JOIN news_translations nt ON nt.news_id = n.id AND nt.locale = :loc
             LEFT JOIN news_translations ntf ON ntf.news_id = n.id AND ntf.locale = :fb
             WHERE n.status = :status ORDER BY n.published_at DESC LIMIT :limit OFFSET :offset'
        );
        foreach ($lp as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue('status', 'published');
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findPublishedBySlug(string $slug): ?array
    {
        $lp = self::localeParams();
        $stmt = self::db()->prepare(
            'SELECT n.*,
                COALESCE(nt.title, ntf.title, n.title) AS title,
                COALESCE(nt.summary, ntf.summary, n.summary) AS summary,
                COALESCE(nt.content, ntf.content, n.content) AS content,
                COALESCE(nt.seo_title, ntf.seo_title, n.seo_title) AS seo_title,
                COALESCE(nt.seo_description, ntf.seo_description, n.seo_description) AS seo_description
             FROM news n
             LEFT JOIN news_translations nt ON nt.news_id = n.id AND nt.locale = :loc
             LEFT JOIN news_translations ntf ON ntf.news_id = n.id AND ntf.locale = :fb
             WHERE n.slug = :slug AND n.status = :status LIMIT 1'
        );
        $stmt->execute(array_merge(['slug' => $slug, 'status' => 'published'], $lp));
        return $stmt->fetch() ?: null;
    }

    public static function saveTranslations(int $newsId, array $byLocale): void
    {
        foreach ($byLocale as $loc => $fields) {
            if (empty($fields['title'])) {
                continue;
            }
            Translation::save('news', $newsId, $loc, [
                'title' => $fields['title'],
                'summary' => $fields['summary'] ?? '',
                'content' => $fields['content'] ?? '',
                'seo_title' => $fields['seo_title'] ?? '',
                'seo_description' => $fields['seo_description'] ?? '',
            ]);
        }
    }

    public static function all(): array
    {
        return self::db()->query('SELECT * FROM news ORDER BY id DESC')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM news WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO news (title, slug, summary, content, thumbnail_path, status, seo_title, seo_description, published_at, created_by)
             VALUES (:title, :slug, :summary, :content, :thumbnail_path, :status, :seo_title, :seo_description, :published_at, :created_by)'
        );
        $stmt->execute($data);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = self::db()->prepare(
            'UPDATE news SET title=:title, slug=:slug, summary=:summary, content=:content, thumbnail_path=:thumbnail_path,
             status=:status, seo_title=:seo_title, seo_description=:seo_description, published_at=:published_at, updated_by=:updated_by
             WHERE id=:id'
        );
        $data['id'] = $id;
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        self::db()->prepare('DELETE FROM news WHERE id = :id')->execute(['id' => $id]);
    }

    public static function count(): int
    {
        return (int) self::db()->query('SELECT COUNT(*) FROM news')->fetchColumn();
    }
}
