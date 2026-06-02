<?php ob_start(); ?>
<section class="page-header page-header--image" style="background-image:url('<?= e(upload_url('seed/hero.jpg')) ?>')">
    <div class="container">
        <h1 class="reveal"><?= e(__('rooms.title')) ?></h1>
        <p class="reveal"><?= e(__('rooms.subtitle', ['brand' => brand_name()])) ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php require __DIR__ . '/../partials/breadcrumbs.php'; ?>
        <form class="filter-bar reveal" method="get" action="<?= url('/rooms') ?>">
            <div class="booking-field date-input-wrap">
                <label class="form-label"><i class="bi bi-calendar3"></i> <?= e(__('home.check_in')) ?></label>
                <input type="text" id="rooms-check-in" class="form-control" data-datepicker data-booking-check-in data-date-name="check_in" value="<?= e($checkIn) ?>" placeholder="<?= e(__('home.date_placeholder_check_in')) ?>">
            </div>
            <div class="booking-field date-input-wrap">
                <label class="form-label"><i class="bi bi-calendar3"></i> <?= e(__('home.check_out')) ?></label>
                <input type="text" id="rooms-check-out" class="form-control" data-datepicker data-booking-check-out data-date-name="check_out" value="<?= e($checkOut) ?>" placeholder="<?= e(__('home.date_placeholder_check_out')) ?>">
            </div>
            <div class="booking-field">
                <label class="form-label"><i class="bi bi-people"></i> <?= e(__('home.guests')) ?></label>
                <input type="number" id="rooms-guests" name="guests" class="form-control" value="<?= (int) ($guestsCount ?? 2) ?>" min="1" max="10">
            </div>
            <div class="booking-field">
                <label class="form-label"><?= e(__('rooms.category')) ?></label>
                <select id="rooms-category" name="category" class="form-select">
                    <option value=""><?= e(__('rooms.all')) ?></option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int) $cat['id'] ?>" <?= ($categoryId ?? 0) === (int) $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-shine"><?= e(__('rooms.check_availability')) ?></button>
        </form>

        <?php if (!empty($datesValid)): ?>
            <p class="availability-hint reveal"><?= e(__('rooms.nights_available', ['nights' => (int) $nights])) ?></p>
        <?php elseif ($checkIn || $checkOut): ?>
            <p class="availability-hint warn reveal"><?= e(__('rooms.invalid_dates')) ?></p>
        <?php endif; ?>

        <div class="room-grid mt-4 reveal-stagger">
            <?php if (empty($rooms)): ?>
                <p class="empty-msg"><?= e(__('rooms.no_rooms')) ?> <a href="<?= url('/book') ?>"><?= e(__('rooms.use_wizard')) ?></a>.</p>
            <?php endif; ?>
            <?php foreach ($rooms as $room):
                $amenities = amenities_from_json($room['amenities'] ?? null);
                $firstAmenity = $amenities[0] ?? null;
            ?>
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
                                <span><i class="fa-solid fa-users" aria-hidden="true"></i><?= e(__('rooms.up_to_guests', ['count' => (int) $room['max_guests']])) ?></span>
                                <?php if ($firstAmenity): ?>
                                <span><i class="fa-solid <?= e(amenity_icon($firstAmenity)) ?>" aria-hidden="true"></i><?= e($firstAmenity) ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="room-price">
                                <?= e(__('rooms.per_night', ['price' => number_format((float) $room['price_per_night'], 0)])) ?>
                            </p>
                            <?php if ($room['stay_total'] !== null): ?>
                                <p class="room-stay-total"><?= e(__('rooms.stay_total', ['total' => number_format((float) $room['stay_total'], 2)])) ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="room-card-actions">
                        <a href="<?= url('/rooms/' . $room['slug']) ?>" class="btn btn-outline-dark btn-sm"><?= e(__('rooms.details')) ?></a>
                        <?php if (!empty($datesValid)): ?>
                            <a href="<?= url('/book?room_id=' . $room['id'] . '&check_in=' . urlencode($checkIn) . '&check_out=' . urlencode($checkOut) . '&guests=' . (int)$guestsCount) ?>" class="btn btn-primary btn-sm btn-shine"><?= e(__('rooms.book')) ?></a>
                        <?php else: ?>
                            <a href="<?= url('/book') ?>" class="btn btn-primary btn-sm btn-shine"><?= e(__('rooms.book')) ?></a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../partials/cta-book.php'; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
