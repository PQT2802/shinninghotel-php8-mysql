<?php ob_start(); ?>
<section class="page-header page-header--image" style="background-image:url('<?= e(upload_url('seed/about.jpg')) ?>')">
    <div class="container">
        <h1 class="reveal"><?= e($page['title'] ?? 'Page') ?></h1>
    </div>
</section>
<section class="section">
    <div class="container">
        <?php require __DIR__ . '/../partials/breadcrumbs.php'; ?>
        <div class="prose page-prose reveal">
            <?php if ($page): ?>
                <?= $page['content'] ?? '<p>Content coming soon.</p>' ?>
            <?php else: ?>
                <p>Content coming soon.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../partials/cta-book.php'; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
