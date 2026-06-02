<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Banner;

class BannerController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('content.manage');
        $this->view('admin/banners/index', ['title' => 'Banners', 'banners' => Banner::all()]);
    }

    public function create(): void
    {
        $this->requirePermission('content.manage');
        $this->view('admin/banners/create', ['title' => 'Create Banner', 'banner' => null]);
    }

    public function store(): void
    {
        $this->requirePermission('content.manage');
        $input = $this->inputFromPost();
        if (!$this->validate($input, [
            'title' => 'required|max:190',
            'position' => 'required|max:80',
        ])) {
            $this->back();
        }
        $imagePath = upload_file($_FILES['image'] ?? [], 'banners');
        if (!$imagePath) {
            Session::flash('error', 'Banner image is required.');
            Session::set('old_input', $_POST);
            $this->back();
        }
        Banner::create($this->bannerPayload($input, $imagePath));
        Session::flash('success', 'Banner created.');
        $this->redirect(url('/admin/banners'));
    }

    public function edit(int $id): void
    {
        $this->requirePermission('content.manage');
        $banner = Banner::find($id);
        if (!$banner) {
            Session::flash('error', 'Banner not found.');
            $this->redirect(url('/admin/banners'));
        }
        $this->view('admin/banners/edit', ['title' => 'Edit Banner', 'banner' => $banner]);
    }

    public function update(int $id): void
    {
        $this->requirePermission('content.manage');
        $existing = Banner::find($id);
        if (!$existing) {
            Session::flash('error', 'Banner not found.');
            $this->redirect(url('/admin/banners'));
        }
        $input = $this->inputFromPost();
        if (!$this->validate($input, [
            'title' => 'required|max:190',
            'position' => 'required|max:80',
        ])) {
            $this->back();
        }
        $imagePath = null;
        if (!empty($_FILES['image']['name'])) {
            $imagePath = upload_file($_FILES['image'], 'banners');
            if ($imagePath) {
                delete_upload_file($existing['image_path']);
            }
        }
        Banner::update($id, $this->bannerPayload($input, $imagePath ?? $existing['image_path']));
        Session::flash('success', 'Banner updated.');
        $this->redirect(url('/admin/banners'));
    }

    public function delete(int $id): void
    {
        $this->requirePermission('content.manage');
        $banner = Banner::find($id);
        if ($banner) {
            delete_upload_file($banner['image_path']);
            Banner::delete($id);
        }
        Session::flash('success', 'Banner deleted.');
        $this->redirect(url('/admin/banners'));
    }

    private function inputFromPost(): array
    {
        return [
            'title' => trim($_POST['title'] ?? ''),
            'subtitle' => trim($_POST['subtitle'] ?? ''),
            'button_text' => trim($_POST['button_text'] ?? ''),
            'button_url' => trim($_POST['button_url'] ?? ''),
            'position' => trim($_POST['position'] ?? 'home_hero'),
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    private function bannerPayload(array $input, string $imagePath): array
    {
        return [
            'title' => $input['title'],
            'subtitle' => $input['subtitle'] ?: null,
            'image_path' => $imagePath,
            'button_text' => $input['button_text'] ?: null,
            'button_url' => $input['button_url'] ?: null,
            'position' => $input['position'],
            'sort_order' => $input['sort_order'],
            'is_active' => $input['is_active'],
        ];
    }
}
