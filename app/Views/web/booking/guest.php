<?php ob_start(); $w = $wizard; ?>
<section class="page-header">
    <div class="container">
        <h1><?= e(__('book.guest_info')) ?></h1>
        <p><?= e($room['name']) ?> · <?= e(booking_format_dates($w['check_in'], $w['check_out'])) ?></p>
    </div>
</section>
<section class="section">
    <div class="container booking-wizard">
        <?php require __DIR__ . '/../partials/flash.php'; ?>
        <?php require __DIR__ . '/_wizard_steps.php'; ?>

        <div class="wizard-summary-bar">
            <img src="<?= e(upload_url($room['image_path'] ?? '')) ?>" alt="">
            <div>
                <strong><?= e($room['name']) ?></strong>
                <p><?= (int) $nights ?> <?= e(__('book.nights')) ?> · $<?= number_format($totalPrice, 2) ?></p>
            </div>
        </div>

        <form method="post" action="<?= url('/book/guest') ?>" class="booking-form wizard-panel">
            <?= csrf_field() ?>
            <h2 class="h5 mb-4"><?= e(__('book.guest_info')) ?></h2>
            <div class="form-row">
                <div class="form-group">
                    <label><?= e(__('book.full_name')) ?> *</label>
                    <input type="text" name="guest_name" class="form-control" value="<?= e((string) old('guest_name', $w['guest_name'] ?? '')) ?>" required>
                </div>
                <div class="form-group">
                    <label><?= e(__('book.email')) ?> *</label>
                    <input type="email" name="guest_email" class="form-control" value="<?= e((string) old('guest_email', $w['guest_email'] ?? '')) ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label><?= e(__('book.phone')) ?></label>
                <input type="tel" name="guest_phone" class="form-control" value="<?= e((string) old('guest_phone', $w['guest_phone'] ?? '')) ?>">
            </div>
            <div class="form-group">
                <label><?= e(__('book.notes')) ?></label>
                <textarea name="notes" class="form-control" rows="3"><?= e((string) old('notes', $w['notes'] ?? '')) ?></textarea>
            </div>
            <div class="wizard-actions">
                <a href="<?= url('/book/rooms') ?>" class="btn btn-outline-dark">← <?= e(__('book.back')) ?></a>
                <button type="submit" class="btn btn-primary"><?= e(__('book.review')) ?> →</button>
            </div>
        </form>
    </div>
</section>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
