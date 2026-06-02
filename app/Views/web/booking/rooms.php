<?php ob_start(); $w = $wizard; ?>
<section class="page-header">
    <div class="container">
        <h1><?= e(__('book.choose_room')) ?></h1>
        <p><?= e(booking_format_dates($w['check_in'], $w['check_out'])) ?> · <?= (int) $nights ?> <?= e(__('book.nights')) ?> · <?= (int) $w['guests_count'] ?> <?= e(__('home.guests')) ?></p>
    </div>
</section>
<section class="section">
    <div class="container booking-wizard">
        <?php require __DIR__ . '/../partials/flash.php'; ?>
        <?php require __DIR__ . '/_wizard_steps.php'; ?>

        <?php if (empty($rooms)): ?>
            <div class="wizard-panel empty-state">
                <p><?= e(__('rooms.no_rooms')) ?></p>
                <div class="wizard-actions justify-content-center">
                    <a href="<?= url('/book') ?>" class="btn btn-outline-dark"><?= e(__('book.back')) ?></a>
                    <a href="<?= url('/rooms?check_in=' . urlencode($w['check_in']) . '&check_out=' . urlencode($w['check_out'])) ?>" class="btn btn-primary"><?= e(__('home.view_all_rooms')) ?></a>
                </div>
            </div>
        <?php else: ?>
            <form method="post" action="<?= url('/book/room') ?>" id="room-select-form">
                <?= csrf_field() ?>
                <input type="hidden" name="room_id" id="selected-room-id" value="">
                <div class="room-select-grid">
                    <?php foreach ($rooms as $room): ?>
                    <label class="room-select-card">
                        <input type="radio" name="room_id_radio" value="<?= (int) $room['id'] ?>" required>
                        <img src="<?= e(upload_url($room['image_path'] ?? '')) ?>" alt="<?= e($room['name']) ?>">
                        <div class="room-select-body">
                            <span class="room-cat"><?= e($room['category_name'] ?? '') ?></span>
                            <h3><?= e($room['name']) ?></h3>
                            <p><?= e(__('rooms.up_to_guests', ['count' => (int) $room['max_guests']])) ?></p>
                            <p class="room-price"><?= e(__('rooms.per_night', ['price' => number_format((float) $room['price_per_night'], 0)])) ?></p>
                            <p class="room-total"><strong>$<?= number_format((float) $room['stay_total'], 2) ?></strong></p>
                            <a href="<?= url('/rooms/' . $room['slug']) ?>" class="room-detail-link" target="_blank" rel="noopener" onclick="event.stopPropagation()"><?= e(__('rooms.details')) ?></a>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
                <div class="wizard-actions">
                    <a href="<?= url('/book') ?>" class="btn btn-outline-dark">← <?= e(__('book.back')) ?></a>
                    <button type="submit" class="btn btn-primary" id="continue-room-btn" disabled><?= e(__('book.continue')) ?> →</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
