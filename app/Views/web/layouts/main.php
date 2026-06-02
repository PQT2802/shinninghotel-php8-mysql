<?php
if (!isset($settings)) {
    $settings = \App\Models\Setting::allKeyed();
}
$siteName = $settings['site_name'] ?? brand_name() . ' Hotel';
$pageHeading = $title ?? brand_name();
$pageTitle = ($metaTitle ?? null) ?: ($pageHeading . ' | ' . $siteName);
$isVi = locale() === 'vi';
$metaDesc = $metaDescription ?? ($settings[$isVi ? 'seo_default_description_vi' : 'seo_default_description'] ?? brand_slogan());
$ogImage = seo_og_image($ogImagePath ?? null, $settings);
$ogType = $ogType ?? 'website';
$canonical = $canonicalUrl ?? seo_canonical_path();
$ogLocale = locale() === 'vi' ? 'vi_VN' : 'en_US';
?>
<!DOCTYPE html>
<html lang="<?= e(locale()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($metaDesc) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">
    <meta name="robots" content="<?= e($robots ?? 'index, follow') ?>">
    <link rel="alternate" hreflang="en" href="<?= e(locale_url(current_localized_path(), 'en')) ?>">
    <link rel="alternate" hreflang="vi" href="<?= e(locale_url(current_localized_path(), 'vi')) ?>">

    <meta property="og:type" content="<?= e($ogType) ?>">
    <meta property="og:site_name" content="<?= e($siteName) ?>">
    <meta property="og:title" content="<?= e($pageHeading) ?>">
    <meta property="og:description" content="<?= e($metaDesc) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta property="og:locale" content="<?= e($ogLocale) ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($pageHeading) ?>">
    <meta name="twitter:description" content="<?= e($metaDesc) ?>">
    <meta name="twitter:image" content="<?= e($ogImage) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php if ($isVi): ?>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <?php else: ?>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Lora:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJt/AlPkAZ0zT8t5nMcaR90+1fQKRGmJvH69USiuaHa9ahJAV9weSNLlJ1Sohw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
    <?php if (!empty($heroPreloadUrl)): ?>
    <link rel="preload" as="image" href="<?= e($heroPreloadUrl) ?>">
    <?php endif; ?>
    <?php if (!empty($settings['favicon_path'])): ?>
    <link rel="icon" href="<?= e(upload_url($settings['favicon_path'])) ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= asset('css/theme-luxury.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/web.css') ?>">
    <script type="application/ld+json"><?= seo_json_ld_hotel($settings) ?></script>
</head>
<body data-locale="<?= e(locale()) ?>">
<a href="#main-content" class="skip-link"><?= e(__('common.skip_content')) ?></a>
<?php require __DIR__ . '/header.php'; ?>
<main id="main-content" class="site-main">
    <?= $content ?? '' ?>
</main>
<?php require __DIR__ . '/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/vi.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
<script src="<?= asset('js/datepicker.js') ?>" defer></script>
<script src="<?= asset('js/web.js') ?>" defer></script>
</body>
</html>
