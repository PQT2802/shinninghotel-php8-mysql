<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Media;

class MediaController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('media.manage');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $pager = Media::paginate($page, 24);
        $this->view('admin/media/index', [
            'title' => 'Media',
            'files' => $pager['data'],
            'pager' => $pager,
        ]);
    }

    public function upload(): void
    {
        $this->requirePermission('media.manage');
        if (empty($_FILES['file']['name'])) {
            Session::flash('error', 'No file selected.');
            $this->redirect(url('/admin/media'));
        }
        $path = upload_file($_FILES['file'], 'media');
        if (!$path) {
            Session::flash('error', 'Upload failed. Allowed: jpg, png, webp, pdf. Max size from .env.');
            $this->redirect(url('/admin/media'));
        }
        Media::create([
            'original_name' => $_FILES['file']['name'],
            'file_name' => basename($path),
            'file_path' => $path,
            'mime_type' => $_FILES['file']['type'] ?? 'application/octet-stream',
            'file_size' => (int) $_FILES['file']['size'],
            'uploaded_by' => auth_id(),
        ]);
        Session::flash('success', 'File uploaded.');
        $this->redirect(url('/admin/media'));
    }

    public function delete(int $id): void
    {
        $this->requirePermission('media.manage');
        $file = Media::find($id);
        if ($file) {
            delete_upload_file($file['file_path']);
            Media::delete($id);
        }
        Session::flash('success', 'Media deleted.');
        $this->redirect(url('/admin/media'));
    }
}
