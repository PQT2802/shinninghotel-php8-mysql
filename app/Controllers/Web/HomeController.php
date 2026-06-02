<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Controller;
use App\Core\Locale;
use App\Models\Banner;
use App\Models\News;
use App\Models\Room;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index(): void
    {
        if (Locale::shouldRedirectRoot()) {
            Locale::handleRootRedirect();
        }

        $settings = Setting::allKeyed();
        $banners = Banner::activeByPosition('home_hero');
        $heroImage = $banners[0]['image_path'] ?? null;
        $isVi = locale() === 'vi';
        $this->view('web/home/index', [
            'heroPreloadUrl' => $heroImage ? upload_url($heroImage) : null,
            'title' => $settings[$isVi ? 'seo_default_title_vi' : 'seo_default_title'] ?? brand_name() . ' Hotel',
            'metaTitle' => $settings[$isVi ? 'seo_default_title_vi' : 'seo_default_title'] ?? null,
            'metaDescription' => $settings[$isVi ? 'seo_default_description_vi' : 'seo_default_description'] ?? brand_slogan(),
            'ogImagePath' => $banners[0]['image_path'] ?? $settings['logo_path'] ?? null,
            'banners' => $banners,
            'featuredRooms' => Room::featured(3),
            'latestNews' => News::published(3),
            'settings' => $settings,
        ]);
    }
}
