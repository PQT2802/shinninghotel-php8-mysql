<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$navItems = [
    ['url' => '/admin', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'exact' => true],
    ['url' => '/admin/pages', 'label' => 'Pages', 'icon' => 'bi-file-text'],
    ['url' => '/admin/news', 'label' => 'News', 'icon' => 'bi-newspaper'],
    ['url' => '/admin/room-categories', 'label' => 'Room Categories', 'icon' => 'bi-tags'],
    ['url' => '/admin/rooms', 'label' => 'Rooms', 'icon' => 'bi-door-open'],
    ['url' => '/admin/bookings', 'label' => 'Bookings', 'icon' => 'bi-calendar-check', 'badge' => 'pending'],
    ['url' => '/admin/banners', 'label' => 'Banners', 'icon' => 'bi-image'],
    ['url' => '/admin/media', 'label' => 'Media', 'icon' => 'bi-images'],
    ['url' => '/admin/menus', 'label' => 'Menus', 'icon' => 'bi-list'],
];
if (can_access('settings.manage')) {
    $navItems[] = ['url' => '/admin/settings', 'label' => 'Settings', 'icon' => 'bi-gear'];
}
if (can_access('users.manage')) {
    $navItems[] = ['url' => '/admin/users', 'label' => 'Users', 'icon' => 'bi-people'];
}
$navItems[] = ['url' => '/admin/contact-messages', 'label' => 'Messages', 'icon' => 'bi-envelope', 'badge' => 'unread'];
?>
<aside class="admin-sidebar" id="admin-sidebar">
    <div class="sidebar-brand">
        <span class="sidebar-brand-name"><?= e(brand_name()) ?></span>
        <span class="sidebar-brand-sub">Content Management</span>
    </div>
    <nav aria-label="Admin navigation">
        <?php foreach ($navItems as $item):
            $path = $item['url'];
            $normalized = rtrim($currentPath, '/');
            $itemPath = rtrim($path, '/');
            $isActive = !empty($item['exact'])
                ? $normalized === $itemPath
                : ($normalized === $itemPath || str_starts_with($normalized, $itemPath . '/'));
            $badge = '';
            if (($item['badge'] ?? '') === 'pending') {
                try {
                    $n = \App\Models\Booking::countPending();
                    if ($n > 0) {
                        $badge = ' (' . $n . ')';
                    }
                } catch (\Throwable) {}
            }
            if (($item['badge'] ?? '') === 'unread') {
                try {
                    $n = \App\Models\ContactMessage::countUnread();
                    if ($n > 0) {
                        $badge = ' (' . $n . ')';
                    }
                } catch (\Throwable) {}
            }
        ?>
        <a href="<?= url($path) ?>" class="<?= $isActive ? 'is-active' : '' ?>">
            <i class="bi <?= e($item['icon']) ?> nav-icon" aria-hidden="true"></i>
            <?= e($item['label']) ?><?= $badge ?>
        </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer-link">
        <a href="<?= url('/') ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View Site</a>
    </div>
</aside>
