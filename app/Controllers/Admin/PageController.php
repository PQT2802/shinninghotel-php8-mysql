<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Page;
use App\Models\Translation;

class PageController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('content.manage');
        $search = trim($_GET['q'] ?? '');
        $status = $_GET['status'] ?? '';
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $pager = Page::paginate($page, 15, $search ?: null, $status ?: null);

        $this->view('admin/pages/index', [
            'title' => 'Pages',
            'pages' => $pager['data'],
            'pager' => $pager,
            'search' => $search,
            'statusFilter' => $status,
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('content.manage');
        clear_validation_state();
        $this->view('admin/pages/create', ['title' => 'Create Page', 'page' => null, 'translations' => []]);
    }

    public function store(): void
    {
        $this->requirePermission('content.manage');
        $translations = $_POST['translations'] ?? [];
        $en = $translations['en'] ?? [];
        $input = [
            'title' => trim($en['title'] ?? ''),
            'slug' => slugify($_POST['slug'] ?? $en['title'] ?? ''),
            'content' => $en['content'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'seo_title' => trim($en['seo_title'] ?? ''),
            'seo_description' => trim($en['seo_description'] ?? ''),
        ];
        if (!$this->validate($input, [
            'title' => 'required|max:190',
            'slug' => 'required|max:190|unique:pages,slug',
            'status' => 'required|in:draft,published',
        ])) {
            $this->back();
        }
        $id = Page::create($this->pagePayload($input));
        Page::saveTranslations($id, $translations);
        Session::flash('success', 'Page created.');
        $this->redirect(url('/admin/pages'));
    }

    public function edit(int $id): void
    {
        $this->requirePermission('content.manage');
        $page = Page::find($id);
        if (!$page) {
            Session::flash('error', 'Page not found.');
            $this->redirect(url('/admin/pages'));
        }
        $this->view('admin/pages/edit', [
            'title' => 'Edit Page',
            'page' => $page,
            'translations' => Translation::forEntity('page', $id),
        ]);
    }

    public function update(int $id): void
    {
        $this->requirePermission('content.manage');
        if (!Page::find($id)) {
            Session::flash('error', 'Page not found.');
            $this->redirect(url('/admin/pages'));
        }
        $translations = $_POST['translations'] ?? [];
        $en = $translations['en'] ?? [];
        $input = [
            'title' => trim($en['title'] ?? ''),
            'slug' => slugify($_POST['slug'] ?? $en['title'] ?? ''),
            'content' => $en['content'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'seo_title' => trim($en['seo_title'] ?? ''),
            'seo_description' => trim($en['seo_description'] ?? ''),
        ];
        if (!$this->validate($input, [
            'title' => 'required|max:190',
            'slug' => 'required|max:190|unique:pages,slug',
            'status' => 'required|in:draft,published',
        ], $id)) {
            $this->back();
        }
        Page::update($id, $this->pagePayload($input, $id));
        Page::saveTranslations($id, $translations);
        Session::flash('success', 'Page updated.');
        $this->redirect(url('/admin/pages'));
    }

    public function delete(int $id): void
    {
        $this->requirePermission('content.manage');
        Page::delete($id);
        Session::flash('success', 'Page deleted.');
        $this->redirect(url('/admin/pages'));
    }

    private function pagePayload(array $input, ?int $id = null): array
    {
        $existing = $id ? Page::find($id) : null;
        $publishedAt = $input['status'] === 'published'
            ? ($existing['published_at'] ?? date('Y-m-d H:i:s'))
            : null;

        return [
            'title' => $input['title'],
            'slug' => $input['slug'],
            'content' => $input['content'],
            'status' => $input['status'],
            'seo_title' => $input['seo_title'] ?: null,
            'seo_description' => $input['seo_description'] ?: null,
            'created_by' => $id ? ($existing['created_by'] ?? auth_id()) : auth_id(),
            'updated_by' => auth_id(),
            'published_at' => $publishedAt,
        ];
    }
}
