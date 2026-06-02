<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('settings.manage');
        $this->view('admin/settings/index', [
            'title' => 'Settings',
            'settings' => Setting::allKeyed(),
        ]);
    }

    public function update(): void
    {
        $this->requirePermission('settings.manage');

        $textKeys = [
            'site_name', 'contact_email', 'contact_phone', 'address',
            'facebook_url', 'instagram_url', 'twitter_url',
            'seo_default_title', 'seo_default_description',
        ];
        foreach ($textKeys as $key) {
            if (isset($_POST[$key])) {
                Setting::set($key, trim((string) $_POST[$key]));
            }
        }

        if (!empty($_FILES['logo']['name'])) {
            $path = upload_file($_FILES['logo'], 'settings');
            if ($path) {
                $old = Setting::get('logo_path');
                delete_upload_file($old);
                Setting::set('logo_path', $path, 'image', 'branding');
            }
        }

        if (!empty($_FILES['favicon']['name'])) {
            $path = upload_file($_FILES['favicon'], 'settings');
            if ($path) {
                $old = Setting::get('favicon_path');
                delete_upload_file($old);
                Setting::set('favicon_path', $path, 'image', 'branding');
            }
        }

        Session::flash('success', 'Settings saved.');
        $this->redirect(url('/admin/settings'));
    }
}
