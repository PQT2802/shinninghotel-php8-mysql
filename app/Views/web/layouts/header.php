<?php
use App\Models\Menu;
use App\Models\Setting;

$headerMenu = Menu::findByLocation('header');
$menuItems = $headerMenu ? Menu::itemsForMenu((int) $headerMenu['id']) : [];
$siteSettings = $settings ?? Setting::allKeyed();
$logoPath = $siteSettings['logo_path'] ?? '';
$currentPath = current_localized_path();
$loc = locale();
?>
<header class="site-header" id="site-header">
    <div class="container header-inner">
        <a href="<?= url('/') ?>" class="logo" aria-label="<?= e(brand_name()) ?>">
            <?php if ($logoPath): ?>
                <img src="<?= e(upload_url($logoPath)) ?>" alt="<?= e(brand_name()) ?>" class="logo-img">
            <?php else: ?>
                <span class="logo-brand"><?= e(brand_name()) ?></span>
            <?php endif; ?>
            <span class="logo-tagline"><?= e(brand_slogan()) ?></span>
        </a>

        <div class="header-actions">
            <nav class="lang-switcher" aria-label="Language">
                <a href="<?= e(switch_locale_url('en')) ?>" class="lang-link<?= $loc === 'en' ? ' is-active' : '' ?>" hreflang="en">EN</a>
                <span class="lang-sep">|</span>
                <a href="<?= e(switch_locale_url('vi')) ?>" class="lang-link<?= $loc === 'vi' ? ' is-active' : '' ?>" hreflang="vi">VI</a>
            </nav>

            <button type="button" class="nav-toggle" id="nav-toggle" aria-expanded="false" aria-controls="main-nav" aria-label="<?= e(__('nav.menu')) ?>">
                <span class="nav-toggle-bar"></span>
                <span class="nav-toggle-bar"></span>
                <span class="nav-toggle-bar"></span>
            </button>
        </div>

        <nav class="main-nav" id="main-nav" aria-label="Main">
            <?php foreach ($menuItems as $item):
                $itemUrl = menu_item_url($item);
                $itemPath = parse_url($itemUrl, PHP_URL_PATH) ?: '/';
                $isActive = rtrim($itemPath, '/') === rtrim($currentPath, '/') || ($itemPath !== '/' && str_starts_with($currentPath, parse_url($itemPath, PHP_URL_PATH) ?: ''));
            ?>
                <a href="<?= e($itemUrl) ?>" target="<?= e($item['target'] ?? '_self') ?>"<?= $isActive ? ' class="is-active"' : '' ?>><?= e($item['title']) ?></a>
            <?php endforeach; ?>
            <a href="<?= url('/book') ?>" class="btn btn-primary btn-nav-cta btn-shine"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i> <?= e(__('nav.book_now')) ?></a>
        </nav>
    </div>
</header>
