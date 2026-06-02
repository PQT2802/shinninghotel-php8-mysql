<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\RoomCategory;

class RoomCategoryController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('content.manage');
        $this->view('admin/room-categories/index', [
            'title' => 'Room Categories',
            'categories' => RoomCategory::all(),
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('content.manage');
        clear_validation_state();
        $this->view('admin/room-categories/create', [
            'title' => 'Create Category',
            'category' => null,
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('content.manage');
        $input = $this->inputFromPost();
        if (!$this->validate($input, [
            'name' => 'required|max:120',
            'slug' => 'required|max:120|unique:room_categories,slug',
        ])) {
            $this->back();
        }
        RoomCategory::create($this->payload($input));
        Session::flash('success', 'Category created.');
        $this->redirect(url('/admin/room-categories'));
    }

    public function edit(int $id): void
    {
        $this->requirePermission('content.manage');
        $category = RoomCategory::find($id);
        if (!$category) {
            Session::flash('error', 'Category not found.');
            $this->redirect(url('/admin/room-categories'));
        }
        $this->view('admin/room-categories/edit', [
            'title' => 'Edit Category',
            'category' => $category,
            'roomCount' => RoomCategory::countRooms($id),
        ]);
    }

    public function update(int $id): void
    {
        $this->requirePermission('content.manage');
        if (!RoomCategory::find($id)) {
            Session::flash('error', 'Category not found.');
            $this->redirect(url('/admin/room-categories'));
        }
        $input = $this->inputFromPost();
        if (!$this->validate($input, [
            'name' => 'required|max:120',
            'slug' => 'required|max:120|unique:room_categories,slug',
        ], $id)) {
            $this->back();
        }
        RoomCategory::update($id, $this->payload($input));
        Session::flash('success', 'Category updated.');
        $this->redirect(url('/admin/room-categories'));
    }

    public function delete(int $id): void
    {
        $this->requirePermission('content.manage');
        $count = RoomCategory::countRooms($id);
        if ($count > 0) {
            Session::flash('error', "Cannot delete: {$count} room(s) still use this category. Reassign or delete them first.");
            $this->redirect(url('/admin/room-categories'));
        }
        RoomCategory::delete($id);
        Session::flash('success', 'Category deleted.');
        $this->redirect(url('/admin/room-categories'));
    }

    private function inputFromPost(): array
    {
        return [
            'name' => trim($_POST['name'] ?? ''),
            'slug' => slugify($_POST['slug'] ?? $_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    private function payload(array $input): array
    {
        return [
            'name' => $input['name'],
            'slug' => $input['slug'],
            'description' => $input['description'] ?: null,
            'sort_order' => $input['sort_order'],
            'is_active' => $input['is_active'],
        ];
    }
}
