<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Controller;
use App\Models\Page;
use App\Models\Setting;

class PageController extends Controller
{
    public function about(): void
    {
        $page = Page::findPublishedBySlug('about-us');
        $settings = Setting::allKeyed();
        $this->renderPage($page, 'About Us', $settings);
    }

    public function location(): void
    {
        $page = Page::findPublishedBySlug('location');
        $settings = Setting::allKeyed();
        $this->renderPage($page, 'Location', $settings);
    }

    public function show(string $slug): void
    {
        $page = Page::findPublishedBySlug($slug);
        if (!$page) {
            http_response_code(404);
            $this->view('web/errors/404', ['title' => 'Page Not Found', 'robots' => 'noindex, nofollow']);
            return;
        }
        $settings = Setting::allKeyed();
        $this->renderPage($page, $page['title'], $settings);
    }

    private function renderPage(?array $page, string $fallbackTitle, array $settings): void
    {
        $displayTitle = $page['title'] ?? $fallbackTitle;
        $title = $page['seo_title'] ?? $displayTitle;
        $metaDescription = $page['seo_description'] ?? ($page ? mb_substr(strip_tags($page['content'] ?? ''), 0, 160) : '');
        $this->view('web/pages/show', [
            'page' => $page,
            'title' => $title,
            'metaDescription' => $metaDescription,
            'breadcrumbs' => [['label' => $displayTitle, 'url' => '']],
            'settings' => $settings,
        ]);
    }
}
