<?php ob_start(); ?>
<section class="page-header page-header--image" style="background-image:url('<?= e(upload_url('seed/news-1.jpg')) ?>')">
    <div class="container">
        <h1 class="reveal"><?= e(__('news.title')) ?></h1>
        <p class="reveal"><?= e(__('news.subtitle', ['brand' => brand_name()])) ?></p>
    </div>
</section>
<section class="section">
    <div class="container">
        <?php require __DIR__ . '/../partials/breadcrumbs.php'; ?>
        <?php if (empty($articles)): ?>
            <p class="empty-msg reveal"><?= e(__('news.empty')) ?></p>
        <?php else: ?>
        <div class="news-grid reveal-stagger">
            <?php foreach ($articles as $article): ?>
            <article class="news-card hover-lift card-glow reveal">
                <a href="<?= url('/news/' . $article['slug']) ?>">
                    <?php if ($article['thumbnail_path']): ?>
                    <div class="img-zoom-wrap">
                        <img src="<?= e(upload_url($article['thumbnail_path'])) ?>" alt="<?= e($article['title']) ?>" loading="lazy" width="400" height="260">
                    </div>
                    <?php endif; ?>
                    <div class="news-card-body">
                        <?php if (!empty($article['published_at'])): ?>
                            <time class="news-card-date" datetime="<?= e(date('Y-m-d', strtotime($article['published_at']))) ?>">
                                <i class="fa-regular fa-calendar" aria-hidden="true"></i><?= e(date('M j, Y', strtotime($article['published_at']))) ?>
                            </time>
                        <?php endif; ?>
                        <h3><?= e($article['title']) ?></h3>
                        <p class="muted"><?= e($article['summary'] ?? '') ?></p>
                        <span class="link-arrow"><?= e(__('home.read_more')) ?> →</span>
                    </div>
                </a>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/../partials/cta-book.php'; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
