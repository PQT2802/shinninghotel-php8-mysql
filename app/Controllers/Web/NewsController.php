<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Controller;
use App\Models\News;
use App\Models\Setting;

class NewsController extends Controller
{
    public function index(): void
    {
        $settings = Setting::allKeyed();
        $this->view('web/news/index', [
            'title' => 'News & Events',
            'metaDescription' => 'Latest news, offers, and events from ' . brand_name() . ' Hotel.',
            'breadcrumbs' => [['label' => 'News & Events', 'url' => url('/news')]],
            'articles' => News::published(20),
            'settings' => $settings,
        ]);
    }

    public function show(string $slug): void
    {
        $article = News::findPublishedBySlug($slug);
        if (!$article) {
            http_response_code(404);
            $this->view('web/errors/404', ['title' => 'Article Not Found', 'robots' => 'noindex, nofollow']);
            return;
        }
        $settings = Setting::allKeyed();
        $this->view('web/news/show', [
            'title' => $article['seo_title'] ?? $article['title'],
            'metaDescription' => $article['seo_description'] ?? $article['summary'] ?? '',
            'ogType' => 'article',
            'ogImagePath' => $article['thumbnail_path'] ?? null,
            'breadcrumbs' => [
                ['label' => 'News', 'url' => url('/news')],
                ['label' => $article['title'], 'url' => url('/news/' . $article['slug'])],
            ],
            'article' => $article,
            'settings' => $settings,
        ]);
    }
}
