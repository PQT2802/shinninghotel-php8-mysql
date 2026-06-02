<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\News;
use App\Models\Translation;

class NewsController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('content.manage');
        $search = trim($_GET['q'] ?? '');
        $status = $_GET['status'] ?? '';
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $pager = News::paginate($page, 15, $search ?: null, $status ?: null);

        $this->view('admin/news/index', [
            'title' => 'News',
            'articles' => $pager['data'],
            'pager' => $pager,
            'search' => $search,
            'statusFilter' => $status,
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('content.manage');
        $this->view('admin/news/create', ['title' => 'Create News', 'article' => null, 'translations' => []]);
    }

    public function store(): void
    {
        $this->requirePermission('content.manage');
        $translations = $_POST['translations'] ?? [];
        $en = $translations['en'] ?? [];
        $input = [
            'title' => trim($en['title'] ?? ''),
            'slug' => slugify($_POST['slug'] ?? $en['title'] ?? ''),
            'summary' => trim($en['summary'] ?? ''),
            'content' => $en['content'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'seo_title' => trim($en['seo_title'] ?? ''),
            'seo_description' => trim($en['seo_description'] ?? ''),
        ];
        if (!$this->validate($input, [
            'title' => 'required|max:190',
            'slug' => 'required|max:190|unique:news,slug',
            'status' => 'required|in:draft,published',
        ])) {
            $this->back();
        }
        $id = News::create($this->articlePayload($input));
        News::saveTranslations($id, $translations);
        Session::flash('success', 'News article created.');
        $this->redirect(url('/admin/news'));
    }

    public function edit(int $id): void
    {
        $this->requirePermission('content.manage');
        $article = News::find($id);
        if (!$article) {
            Session::flash('error', 'Article not found.');
            $this->redirect(url('/admin/news'));
        }
        $this->view('admin/news/edit', [
            'title' => 'Edit News',
            'article' => $article,
            'translations' => Translation::forEntity('news', $id),
        ]);
    }

    public function update(int $id): void
    {
        $this->requirePermission('content.manage');
        $existing = News::find($id);
        if (!$existing) {
            Session::flash('error', 'Article not found.');
            $this->redirect(url('/admin/news'));
        }
        $translations = $_POST['translations'] ?? [];
        $en = $translations['en'] ?? [];
        $input = [
            'title' => trim($en['title'] ?? ''),
            'slug' => slugify($_POST['slug'] ?? $en['title'] ?? ''),
            'summary' => trim($en['summary'] ?? ''),
            'content' => $en['content'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'seo_title' => trim($en['seo_title'] ?? ''),
            'seo_description' => trim($en['seo_description'] ?? ''),
        ];
        if (!$this->validate($input, [
            'title' => 'required|max:190',
            'slug' => 'required|max:190|unique:news,slug',
            'status' => 'required|in:draft,published',
        ], $id)) {
            $this->back();
        }
        News::update($id, $this->articlePayload($input, $existing));
        News::saveTranslations($id, $translations);
        Session::flash('success', 'News article updated.');
        $this->redirect(url('/admin/news'));
    }

    public function delete(int $id): void
    {
        $this->requirePermission('content.manage');
        $article = News::find($id);
        if ($article) {
            delete_upload_file($article['thumbnail_path'] ?? null);
            News::delete($id);
        }
        Session::flash('success', 'News article deleted.');
        $this->redirect(url('/admin/news'));
    }

    private function inputFromPost(): array
    {
        return [
            'title' => trim($_POST['title'] ?? ''),
            'slug' => slugify($_POST['slug'] ?? $_POST['title'] ?? ''),
            'summary' => trim($_POST['summary'] ?? ''),
            'content' => $_POST['content'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'seo_title' => trim($_POST['seo_title'] ?? ''),
            'seo_description' => trim($_POST['seo_description'] ?? ''),
        ];
    }

    private function articlePayload(array $input, ?array $existing = null): array
    {
        $thumb = null;
        if (!empty($_FILES['thumbnail']['name'])) {
            $thumb = upload_file($_FILES['thumbnail'], 'news');
            if (!$thumb) {
                Session::flash('error', 'Thumbnail upload failed.');
                $this->back();
            }
            if ($existing && !empty($existing['thumbnail_path'])) {
                delete_upload_file($existing['thumbnail_path']);
            }
        }

        $publishedAt = $input['status'] === 'published'
            ? ($existing['published_at'] ?? date('Y-m-d H:i:s'))
            : null;

        return [
            'title' => $input['title'],
            'slug' => $input['slug'],
            'summary' => $input['summary'] ?: null,
            'content' => $input['content'],
            'thumbnail_path' => $thumb ?? ($existing['thumbnail_path'] ?? null),
            'status' => $input['status'],
            'seo_title' => $input['seo_title'] ?: null,
            'seo_description' => $input['seo_description'] ?: null,
            'published_at' => $publishedAt,
            'created_by' => $existing ? ($existing['created_by'] ?? auth_id()) : auth_id(),
            'updated_by' => auth_id(),
        ];
    }
}
