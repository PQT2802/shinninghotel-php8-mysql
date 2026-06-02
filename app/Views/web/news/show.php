<?php ob_start(); ?>
<section class="page-header page-header--image" <?php if ($article['thumbnail_path']): ?>style="background-image:url('<?= e(upload_url($article['thumbnail_path'])) ?>')"<?php endif; ?>>
    <div class="container">
        <?php if (!empty($article['published_at'])): ?>
            <time class="article-date" datetime="<?= e($article['published_at']) ?>"><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?= e(date('F j, Y', strtotime($article['published_at']))) ?></time>
        <?php endif; ?>
        <h1 class="reveal"><?= e($article['title']) ?></h1>
        <?php if (!empty($article['summary'])): ?>
            <p class="article-lead text-white opacity-75"><?= e($article['summary']) ?></p>
        <?php endif; ?>
    </div>
</section>
<section class="section article-section">
    <div class="container">
        <?php require __DIR__ . '/../partials/breadcrumbs.php'; ?>
        <?php if ($article['thumbnail_path']): ?>
            <img class="article-hero reveal" src="<?= e(upload_url($article['thumbnail_path'])) ?>" alt="<?= e($article['title']) ?>" loading="lazy" width="1200" height="480">
        <?php endif; ?>
        <div class="prose article-body reveal"><?= $article['content'] ?? '' ?></div>
        <p class="article-back reveal"><a href="<?= url('/news') ?>" class="link-arrow"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> <?= e(__('news.all_news')) ?></a></p>
    </div>
</section>
<script type="application/ld+json"><?= seo_json_ld_article($article, $settings ?? []) ?></script>
<?php require __DIR__ . '/../partials/cta-book.php'; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
