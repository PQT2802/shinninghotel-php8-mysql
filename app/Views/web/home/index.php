<?php ob_start(); ?>
<?php
$hero = $banners[0] ?? null;
$galleryRooms = $featuredRooms ?? [];
$experiences = [
    ['title' => __('home.exp_dining'), 'text' => __('home.exp_dining_text'), 'img' => 'seed/exp-dining.jpg', 'icon' => 'fa-utensils'],
    ['title' => __('home.exp_spa'), 'text' => __('home.exp_spa_text'), 'img' => 'seed/exp-spa.jpg', 'icon' => 'fa-spa'],
    ['title' => __('home.exp_wellness'), 'text' => __('home.exp_wellness_text'), 'img' => 'seed/exp-wellness.jpg', 'icon' => 'fa-person-swimming'],
];
$testimonials = [
    ['quote' => __('home.testimonial_1'), 'author' => __('home.testimonial_1_author'), 'role' => __('home.testimonial_1_role'), 'avatar' => 'seed/avatar-1.jpg'],
    ['quote' => __('home.testimonial_2'), 'author' => __('home.testimonial_2_author'), 'role' => __('home.testimonial_2_role'), 'avatar' => 'seed/avatar-2.jpg'],
    ['quote' => __('home.testimonial_3'), 'author' => __('home.testimonial_3_author'), 'role' => __('home.testimonial_3_role'), 'avatar' => 'seed/avatar-3.jpg'],
];
$perkIcons = ['fa-location-dot', 'fa-gem', 'fa-calendar-check'];
?>
<section class="hero hero-animated" <?php if ($hero): ?>style="background-image:url('<?= e(upload_url($hero['image_path'])) ?>')"<?php endif; ?>>
    <div class="hero-overlay">
        <div class="container hero-content">
            <p class="hero-eyebrow" data-aos="fade-up"><i class="fa-solid fa-star" aria-hidden="true"></i><?= e(brand_name()) ?> Hotel</p>
            <h1 data-aos="fade-up" data-aos-delay="100"><?= e($hero['title'] ?? __('home.hero_title')) ?></h1>
            <p class="hero-sub" data-aos="fade-up" data-aos-delay="150"><?= e($hero['subtitle'] ?? __('home.hero_sub')) ?></p>
            <form class="booking-bar" action="<?= url('/book/dates') ?>" method="post" data-aos="fade-up" data-aos-delay="200">
                <?= csrf_field() ?>
                <div class="booking-field date-input-wrap">
                    <label for="hero-check-in"><i class="bi bi-calendar3"></i> <?= e(__('home.check_in')) ?></label>
                    <input type="text" id="hero-check-in" class="form-control" data-datepicker data-booking-check-in data-date-name="check_in" required placeholder="<?= e(__('home.date_placeholder_check_in')) ?>">
                </div>
                <div class="booking-field date-input-wrap">
                    <label for="hero-check-out"><i class="bi bi-calendar3"></i> <?= e(__('home.check_out')) ?></label>
                    <input type="text" id="hero-check-out" class="form-control" data-datepicker data-booking-check-out data-date-name="check_out" required placeholder="<?= e(__('home.date_placeholder_check_out')) ?>">
                </div>
                <div class="booking-field">
                    <label for="hero-guests"><i class="bi bi-people"></i> <?= e(__('home.guests')) ?></label>
                    <input type="number" id="hero-guests" name="guests_count" class="form-control" value="2" min="1" max="10">
                </div>
                <button type="submit" class="btn btn-primary btn-shine"><?= e(__('home.book_stay')) ?></button>
            </form>
        </div>
    </div>
    <a href="#intro" class="hero-scroll" aria-label="<?= e(__('home.scroll_down')) ?>">
        <span><?= e(__('home.scroll_down')) ?></span>
        <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
    </a>
</section>

<section class="intro-section" id="intro">
    <div class="container intro-grid">
        <div class="intro-text reveal">
            <p class="section-eyebrow"><?= e(__('home.eyebrow')) ?></p>
            <h2><?= e(__('home.intro_title')) ?></h2>
            <div class="gold-divider" aria-hidden="true"><i class="fa-solid fa-diamond"></i></div>
            <p><?= e(__('home.intro_text', ['brand' => brand_name()])) ?></p>
            <a href="<?= url('/about') ?>" class="link-arrow"><?= e(__('home.discover')) ?> →</a>
        </div>
        <div class="perks-grid reveal-stagger">
            <?php
            $perks = [
                ['title' => __('home.perk_location'), 'text' => __('home.perk_location_text')],
                ['title' => __('home.perk_comfort'), 'text' => __('home.perk_comfort_text')],
                ['title' => __('home.perk_direct'), 'text' => __('home.perk_direct_text')],
            ];
            foreach ($perks as $i => $perk):
            ?>
            <div class="perk-card card-glow reveal">
                <span class="perk-icon-fa" aria-hidden="true"><i class="fa-solid <?= e($perkIcons[$i]) ?>"></i></span>
                <h3><?= e($perk['title']) ?></h3>
                <p><?= e($perk['text']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="stats-section">
    <div class="container">
        <p class="section-eyebrow text-center" style="color: var(--gold-light);"><?= e(__('home.stats_eyebrow')) ?></p>
        <h2 class="section-title" style="color: var(--white); margin-bottom: var(--space-10);"><?= e(__('home.stats_title')) ?></h2>
        <div class="stats-grid">
            <div class="stat-item reveal">
                <i class="fa-solid fa-bed stat-icon" aria-hidden="true"></i>
                <div class="stat-value"><span class="count-up" data-target="48" data-decimals="0">0</span></div>
                <div class="stat-label"><?= e(__('home.stat_rooms')) ?></div>
            </div>
            <div class="stat-item reveal">
                <i class="fa-solid fa-award stat-icon" aria-hidden="true"></i>
                <div class="stat-value"><span class="count-up" data-target="15" data-decimals="0" data-suffix="+">0</span></div>
                <div class="stat-label"><?= e(__('home.stat_years')) ?></div>
            </div>
            <div class="stat-item reveal">
                <i class="fa-solid fa-heart stat-icon" aria-hidden="true"></i>
                <div class="stat-value"><span class="count-up" data-target="12" data-decimals="0" data-suffix="K+">0</span></div>
                <div class="stat-label"><?= e(__('home.stat_guests')) ?></div>
            </div>
            <div class="stat-item reveal">
                <i class="fa-solid fa-star stat-icon" aria-hidden="true"></i>
                <div class="stat-value"><span class="count-up" data-target="4.9" data-decimals="1">0</span></div>
                <div class="stat-label"><?= e(__('home.stat_rating')) ?></div>
            </div>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <p class="section-eyebrow text-center reveal"><?= e(__('home.experiences_eyebrow')) ?></p>
        <h2 class="section-title reveal"><?= e(__('home.experiences_title')) ?></h2>
        <p class="section-lead reveal"><?= e(__('home.experiences_lead')) ?></p>
        <div class="experience-grid reveal-stagger">
            <?php foreach ($experiences as $i => $exp): ?>
            <a href="<?= url('/contact') ?>" class="experience-card hover-lift card-glow reveal">
                <span class="exp-icon" aria-hidden="true"><i class="fa-solid <?= e($exp['icon']) ?>"></i></span>
                <img src="<?= e(upload_url($exp['img'])) ?>" alt="<?= e($exp['title']) ?>" loading="lazy" width="400" height="380">
                <div class="experience-card-body">
                    <h3><?= e($exp['title']) ?></h3>
                    <p><?= e($exp['text']) ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <p class="section-eyebrow text-center reveal"><?= e(__('home.accommodations')) ?></p>
        <h2 class="section-title reveal"><?= e(__('home.featured_rooms')) ?></h2>
        <div class="room-grid reveal-stagger">
            <?php foreach ($featuredRooms as $i => $room): ?>
                <article class="room-card hover-lift card-glow reveal">
                    <a href="<?= url('/rooms/' . $room['slug']) ?>">
                        <div class="room-card-media img-zoom-wrap">
                            <?php if (!empty($room['is_featured'])): ?>
                            <span class="room-badge-featured"><?= e(__('rooms.featured_badge')) ?></span>
                            <?php endif; ?>
                            <img src="<?= e(upload_url($room['image_path'] ?? '')) ?>" alt="<?= e($room['name']) ?>" loading="lazy" width="400" height="260">
                        </div>
                        <div class="room-card-body">
                            <span class="room-cat"><?= e($room['category_name'] ?? '') ?></span>
                            <h3><?= e($room['name']) ?></h3>
                            <div class="room-meta-icons">
                                <span><i class="fa-solid fa-users" aria-hidden="true"></i><?= (int) $room['max_guests'] ?></span>
                                <span><i class="fa-solid fa-wifi" aria-hidden="true"></i> WiFi</span>
                            </div>
                            <p class="room-price"><?= e(__('home.from_price', ['price' => number_format((float) $room['price_per_night'], 0)])) ?></p>
                        </div>
                    </a>
                    <div class="room-card-actions">
                        <a href="<?= url('/rooms/' . $room['slug']) ?>" class="btn btn-outline-dark btn-sm"><?= e(__('rooms.details')) ?></a>
                        <a href="<?= url('/book?room_id=' . $room['id']) ?>" class="btn btn-primary btn-sm btn-shine"><?= e(__('rooms.book')) ?></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="section-cta reveal"><a href="<?= url('/rooms') ?>" class="btn btn-outline-dark"><?= e(__('home.view_all_rooms')) ?></a></p>
    </div>
</section>

<?php if (!empty($galleryRooms)): ?>
<section class="section section-alt">
    <div class="container">
        <p class="section-eyebrow text-center reveal"><?= e(__('home.gallery_eyebrow')) ?></p>
        <h2 class="section-title reveal"><?= e(__('home.gallery_title')) ?></h2>
        <div class="gallery-grid reveal">
            <?php
            $galleryPaths = [];
            foreach ($galleryRooms as $r) {
                if (!empty($r['image_path'])) {
                    $galleryPaths[] = $r['image_path'];
                }
                if (!empty($r['gallery'])) {
                    foreach ($r['gallery'] as $gp) {
                        $galleryPaths[] = $gp;
                    }
                }
            }
            $galleryPaths = array_slice(array_unique($galleryPaths), 0, 6);
            foreach ($galleryPaths as $path):
            ?>
            <div class="gallery-item">
                <img class="js-lightbox-trigger" src="<?= e(upload_url($path)) ?>" alt="<?= e(brand_name()) ?>" loading="lazy" width="400" height="300" data-full="<?= e(upload_url($path)) ?>">
            </div>
            <?php endforeach; ?>
        </div>
        <p class="section-cta reveal"><a href="<?= url('/rooms') ?>" class="link-arrow"><?= e(__('home.gallery_cta')) ?> →</a></p>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="container">
        <p class="section-eyebrow text-center reveal"><?= e(__('home.testimonials_eyebrow')) ?></p>
        <h2 class="section-title reveal"><?= e(__('home.testimonials_title')) ?></h2>
        <div class="swiper testimonials-swiper reveal">
            <div class="swiper-wrapper">
                <?php foreach ($testimonials as $t): ?>
                <div class="swiper-slide">
                    <blockquote class="testimonial-card">
                        <img class="testimonial-avatar" src="<?= e(upload_url($t['avatar'])) ?>" alt="" loading="lazy" width="56" height="56">
                        <div class="testimonial-stars" aria-hidden="true">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p class="testimonial-quote"><?= e($t['quote']) ?></p>
                        <footer>
                            <cite class="testimonial-author"><?= e($t['author']) ?></cite>
                            <span class="testimonial-role"><?= e($t['role']) ?></span>
                        </footer>
                    </blockquote>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
        <div class="awards-row reveal">
            <div class="award-badge"><strong><?= e(__('home.award_1_title')) ?></strong><?= e(__('home.award_1_sub')) ?></div>
            <div class="award-badge"><strong><?= e(__('home.award_2_title')) ?></strong><?= e(__('home.award_2_sub')) ?></div>
            <div class="award-badge"><strong><?= e(__('home.award_3_title')) ?></strong><?= e(__('home.award_3_sub')) ?></div>
        </div>
    </div>
</section>

<?php if (!empty($latestNews)): ?>
<section class="section section-alt">
    <div class="container">
        <p class="section-eyebrow text-center reveal"><?= e(__('home.journal')) ?></p>
        <h2 class="section-title reveal"><?= e(__('home.news_events')) ?></h2>
        <div class="news-grid reveal-stagger">
            <?php foreach ($latestNews as $article): ?>
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
        <p class="section-cta reveal"><a href="<?= url('/news') ?>" class="btn btn-outline-dark"><?= e(__('home.all_news')) ?></a></p>
    </div>
</section>
<?php endif; ?>

<div id="lightbox" class="lightbox" hidden aria-hidden="true">
    <button type="button" class="lightbox-close" aria-label="Close">&times;</button>
    <img src="" alt="" class="lightbox-img">
</div>

<?php require __DIR__ . '/../partials/cta-book.php'; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
