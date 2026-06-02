<?php
use App\Models\Menu;

$footerMenu = Menu::findByLocation('footer');
$footerItems = $footerMenu ? Menu::itemsForMenu((int) $footerMenu['id']) : [];
$mapQuery = urlencode($settings['address'] ?? '');
?>
<footer class="site-footer">
    <div class="container">
        <div class="row g-4 g-lg-5">
            <div class="col-md-4">
                <strong class="footer-brand"><?= e($settings['site_name'] ?? brand_name() . ' Hotel') ?></strong>
                <p class="footer-tagline"><?= e(brand_slogan()) ?></p>
                <a href="<?= url('/book') ?>" class="btn btn-primary btn-sm"><?= e(__('footer.reserve')) ?></a>
            </div>
            <div class="col-md-4">
                <h3 class="footer-heading"><?= e(__('footer.explore')) ?></h3>
                <nav class="footer-nav" aria-label="Footer">
                    <?php if (!empty($footerItems)): ?>
                        <?php foreach ($footerItems as $item): ?>
                            <a href="<?= e(menu_item_url($item)) ?>"><?= e($item['title']) ?></a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="<?= url('/rooms') ?>"><?= e(__('nav.rooms')) ?></a>
                        <a href="<?= url('/about') ?>"><?= e(__('nav.about')) ?></a>
                        <a href="<?= url('/news') ?>"><?= e(__('nav.news')) ?></a>
                        <a href="<?= url('/contact') ?>"><?= e(__('nav.contact')) ?></a>
                    <?php endif; ?>
                </nav>
            </div>
            <div class="col-md-4">
                <h3 class="footer-heading"><?= e(__('footer.contact')) ?></h3>
                <?php if (!empty($settings['address'])): ?>
                    <p class="footer-contact-line"><?= e($settings['address']) ?></p>
                    <?php if ($mapQuery): ?>
                    <a href="https://www.google.com/maps/search/?api=1&amp;query=<?= $mapQuery ?>" class="footer-map-link" target="_blank" rel="noopener"><?= e(__('footer.view_map')) ?></a>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (!empty($settings['contact_phone'])): ?>
                    <p class="mt-3"><a href="tel:<?= e(preg_replace('/\s+/', '', $settings['contact_phone'])) ?>"><?= e($settings['contact_phone']) ?></a></p>
                <?php endif; ?>
                <?php if (!empty($settings['contact_email'])): ?>
                    <p><a href="mailto:<?= e($settings['contact_email']) ?>"><?= e($settings['contact_email']) ?></a></p>
                <?php endif; ?>
                <div class="social-links mt-3">
                    <?php if (!empty($settings['facebook_url'])): ?>
                        <a href="<?= e($settings['facebook_url']) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($settings['instagram_url'])): ?>
                        <a href="<?= e($settings['instagram_url']) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= e(brand_name()) ?> Hotel. <?= e(__('footer.rights')) ?></p>
        </div>
    </div>
</footer>
