<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;

class MenuController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('content.manage');
        $this->view('admin/menus/index', ['title' => 'Menus', 'menus' => Menu::all()]);
    }

    public function edit(int $id): void
    {
        $this->requirePermission('content.manage');
        $menu = Menu::find($id);
        if (!$menu) {
            Session::flash('error', 'Menu not found.');
            $this->redirect(url('/admin/menus'));
        }
        $this->view('admin/menus/edit', [
            'title' => 'Edit Menu: ' . $menu['name'],
            'menu' => $menu,
            'items' => MenuItem::allForMenu($id, false),
            'pages' => Page::publishedList(),
        ]);
    }

    public function update(int $id): void
    {
        $this->requirePermission('content.manage');
        if (!Menu::find($id)) {
            Session::flash('error', 'Menu not found.');
            $this->redirect(url('/admin/menus'));
        }

        $rows = $_POST['items'] ?? [];
        if (!is_array($rows)) {
            $rows = [];
        }

        MenuItem::syncMenu($id, $rows);
        Session::flash('success', 'Menu saved.');
        $this->redirect(url('/admin/menus/edit/' . $id));
    }
}
