<?php ob_start(); $w = $wizard; ?>
<section class="page-header">
    <div class="container">
        <h1><?= e(__('book.review')) ?></h1>
        <p><?= e(brand_slogan()) ?></p>
    </div>
</section>
<section class="section">
    <div class="container booking-wizard">
        <?php require __DIR__ . '/../partials/flash.php'; ?>
        <?php require __DIR__ . '/_wizard_steps.php'; ?>

        <div class="booking-review-card wizard-panel">
            <div class="review-room">
                <img src="<?= e(upload_url($room['image_path'] ?? '')) ?>" alt="">
                <div>
                    <h2 class="h4"><?= e($room['name']) ?></h2>
                    <p class="room-cat"><?= e($room['category_name'] ?? '') ?></p>
                </div>
            </div>
            <dl class="review-details">
                <dt><?= e(__('home.check_in')) ?></dt>
                <dd><?= e(booking_format_dates($w['check_in'], $w['check_out'])) ?> (<?= (int) $nights ?> <?= e(__('book.nights')) ?>)</dd>
                <dt><?= e(__('home.guests')) ?></dt>
                <dd><?= (int) $w['guests_count'] ?></dd>
                <dt><?= e(__('book.full_name')) ?></dt>
                <dd><?= e($w['guest_name']) ?></dd>
                <dt><?= e(__('book.email')) ?></dt>
                <dd><?= e($w['guest_email']) ?></dd>
                <?php if (!empty($w['guest_phone'])): ?>
                <dt><?= e(__('book.phone')) ?></dt>
                <dd><?= e($w['guest_phone']) ?></dd>
                <?php endif; ?>
                <?php if (!empty($w['notes'])): ?>
                <dt><?= e(__('book.notes')) ?></dt>
                <dd><?= e($w['notes']) ?></dd>
                <?php endif; ?>
                <dt><?= e(__('book.total')) ?></dt>
                <dd class="review-total">$<?= number_format($totalPrice, 2) ?></dd>
            </dl>
            <p class="review-note"><?= e(__('book.status_pending')) ?></p>

            <form method="post" action="<?= url('/book/confirm') ?>" class="wizard-actions">
                <?= csrf_field() ?>
                <a href="<?= url('/book/guest') ?>" class="btn btn-outline-dark">← <?= e(__('book.back')) ?></a>
                <button type="submit" class="btn btn-primary"><?= e(__('book.confirm')) ?></button>
            </form>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
