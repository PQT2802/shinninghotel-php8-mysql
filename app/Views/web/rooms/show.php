<?php
$amenities = amenities_from_json($room['amenities'] ?? null);
$allImages = room_gallery_paths($room);
$fallbackImg = room_image_url('seed/room-standard.jpg');
$bookQuery = '';
if (!empty($_GET['check_in'])) {
    $bookQuery .= '&check_in=' . urlencode($_GET['check_in']);
}
if (!empty($_GET['check_out'])) {
    $bookQuery .= '&check_out=' . urlencode($_GET['check_out']);
}
$roomMeta = [
    ['icon' => 'fa-ruler-combined', 'label' => __('rooms.size_label'), 'value' => $room['size'] ?? '—'],
    ['icon' => 'fa-bed', 'label' => __('rooms.bed_label'), 'value' => $room['bed'] ?? '—'],
    ['icon' => 'fa-eye', 'label' => __('rooms.view_label'), 'value' => $room['view'] ?? '—'],
];
$roomSpecs = [
    'standard-king' => ['size' => '25 m²', 'bed' => 'King', 'view' => locale() === 'vi' ? 'Thành phố' : 'City'],
    'standard-twin' => ['size' => '28 m²', 'bed' => locale() === 'vi' ? '2 giường đơn' : 'Twin', 'view' => locale() === 'vi' ? 'Thành phố' : 'City'],
    'deluxe-ocean-view' => ['size' => '40 m²', 'bed' => 'King', 'view' => locale() === 'vi' ? 'Biển' : 'Ocean'],
    'deluxe-garden-view' => ['size' => '38 m²', 'bed' => 'King', 'view' => locale() === 'vi' ? 'Vườn' : 'Garden'],
    'presidential-suite' => ['size' => '120 m²', 'bed' => 'King', 'view' => locale() === 'vi' ? 'Panorama' : 'Panorama'],
    'family-connecting' => ['size' => '65 m²', 'bed' => locale() === 'vi' ? 'Linh hoạt' : 'Flexible', 'view' => locale() === 'vi' ? 'Thành phố' : 'City'],
    'executive-club-king' => ['size' => '42 m²', 'bed' => 'King', 'view' => locale() === 'vi' ? 'Thành phố' : 'City'],
    'penthouse-sky' => ['size' => '150 m²', 'bed' => 'King', 'view' => locale() === 'vi' ? 'Skyline' : 'Skyline'],
];
$spec = $roomSpecs[$room['slug'] ?? ''] ?? ['size' => '—', 'bed' => '—', 'view' => '—'];
$roomMeta[0]['value'] = $spec['size'];
$roomMeta[1]['value'] = $spec['bed'];
$roomMeta[2]['value'] = $spec['view'];
?>
<?php ob_start(); ?>
<section class="page-header page-header--compact">
    <div class="container">
        <span class="room-cat reveal"><?= e($room['category_name'] ?? '') ?></span>
        <h1 class="reveal"><?= e($room['name']) ?></h1>
    </div>
</section>
<section class="section room-detail-section">
    <div class="container">
        <?php require __DIR__ . '/../partials/breadcrumbs.php'; ?>

        <div class="room-detail-layout">
            <div class="room-detail-media">
                <?php
                $primaryPath = $allImages[0] ?? '';
                $primaryUrl = room_image_url($primaryPath ?: null);
                ?>
                <p class="gallery-hint muted"><i class="fa-regular fa-images" aria-hidden="true"></i> <?= e(__('rooms.gallery_hint')) ?></p>
                <div class="room-gallery-viewer" data-room-gallery>
                    <div class="room-gallery-main-wrap">
                        <img id="room-gallery-main"
                             class="room-main-image js-lightbox-trigger"
                             src="<?= e($primaryUrl) ?>"
                             alt="<?= e($room['name']) ?>"
                             data-full="<?= e($primaryUrl) ?>"
                             width="800"
                             height="600"
                             loading="eager"
                             decoding="async"
                             onerror="this.onerror=null;this.src='<?= e($fallbackImg) ?>';this.dataset.full='<?= e($fallbackImg) ?>';">
                    </div>
                    <?php if (count($allImages) > 1): ?>
                    <div class="room-gallery-thumbs-row" role="tablist" aria-label="<?= e(__('rooms.gallery_hint')) ?>">
                        <?php foreach ($allImages as $i => $path): ?>
                        <?php $thumbUrl = room_image_url($path); ?>
                        <button type="button"
                                class="room-gallery-thumb<?= $i === 0 ? ' is-active' : '' ?>"
                                role="tab"
                                aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                                data-src="<?= e($thumbUrl) ?>"
                                data-full="<?= e($thumbUrl) ?>">
                            <img src="<?= e($thumbUrl) ?>" alt="" width="120" height="80" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallbackImg) ?>';">
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="room-spec-grid reveal">
                    <?php foreach ($roomMeta as $meta): ?>
                    <div class="room-spec-card">
                        <i class="fa-solid <?= e($meta['icon']) ?>" aria-hidden="true"></i>
                        <span class="room-spec-label"><?= e($meta['label']) ?></span>
                        <strong><?= e($meta['value']) ?></strong>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="room-detail-prose prose mt-4 d-none d-lg-block">
                    <?= $room['description'] ?? '' ?>
                </div>
            </div>

            <aside class="room-booking-panel reveal">
                <span class="room-cat"><?= e($room['category_name'] ?? '') ?></span>
                <h2 class="h3"><?= e($room['name']) ?></h2>
                <p class="room-price-lg"><i class="fa-solid fa-tag" aria-hidden="true"></i> $<?= number_format((float) $room['price_per_night'], 0) ?> <small><?= e(__('rooms.per_night_label')) ?></small></p>
                <p class="room-meta"><i class="fa-solid fa-users" aria-hidden="true"></i> <?= e(__('rooms.up_to_guests', ['count' => (int) $room['max_guests']])) ?></p>

                <?php if ($amenities): ?>
                <div class="room-amenities-block">
                    <h3 class="room-amenities-title"><?= e(__('rooms.included_title')) ?></h3>
                    <ul class="amenity-list">
                        <?php foreach ($amenities as $a): ?>
                        <li><i class="fa-solid <?= e(amenity_icon($a)) ?>" aria-hidden="true"></i><?= e($a) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <a href="<?= url('/book?room_id=' . $room['id'] . $bookQuery) ?>" class="btn btn-primary btn-lg w-100 btn-shine"><?= e(__('rooms.reserve')) ?></a>
                <a href="<?= url('/rooms') ?>" class="btn btn-outline-dark w-100 mt-3"><?= e(__('home.view_all_rooms')) ?></a>
            </aside>
        </div>

        <div class="room-detail-prose prose mt-5 d-lg-none reveal">
            <?= $room['description'] ?? '' ?>
        </div>
    </div>
</section>
<script type="application/ld+json"><?= seo_json_ld_room($room, $settings ?? []) ?></script>
<div id="lightbox" class="lightbox" hidden aria-hidden="true">
    <button type="button" class="lightbox-close" aria-label="Close">&times;</button>
    <img src="" alt="" class="lightbox-img">
</div>
<?php require __DIR__ . '/../partials/cta-book.php'; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
