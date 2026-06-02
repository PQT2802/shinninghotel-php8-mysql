<?php ob_start(); ?>
<section class="error-page">
    <div class="container">
        <p class="error-code">404</p>
        <h1><?= e(__('errors.page_not_found')) ?></h1>
        <p class="error-lead"><?= e(__('errors.page_not_found_lead')) ?></p>
        <div class="error-actions">
            <a href="<?= url('/') ?>" class="btn btn-primary"><?= e(__('book.back_home')) ?></a>
            <a href="<?= url('/rooms') ?>" class="btn btn-outline-dark"><?= e(__('home.view_all_rooms')) ?></a>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
$robots = $robots ?? 'noindex, nofollow';
require __DIR__ . '/../layouts/main.php';
