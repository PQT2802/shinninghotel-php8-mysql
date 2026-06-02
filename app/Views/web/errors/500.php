<?php ob_start(); ?>
<section class="error-page">
    <div class="container">
        <p class="error-code">500</p>
        <h1>Something went wrong</h1>
        <p class="error-lead">Please try again later.</p>
        <div class="error-actions">
            <a href="<?= url('/') ?>" class="btn btn-primary"><?= e(__('book.back_home')) ?></a>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); $settings = []; $robots = 'noindex, nofollow'; require __DIR__ . '/../layouts/main.php'; ?>
