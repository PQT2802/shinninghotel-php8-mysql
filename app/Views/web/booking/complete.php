<?php ob_start(); ?>
<section class="section booking-complete">
    <div class="container text-center">
        <div class="complete-icon" aria-hidden="true">✓</div>
        <h1><?= e(__('book.complete_title')) ?></h1>
        <p class="complete-lead"><?= e(__('book.complete_lead')) ?></p>
        <p class="booking-ref"><?= e(__('book.reference')) ?>: <strong><?= e($reference) ?></strong></p>

        <div class="booking-review-card wizard-panel" style="max-width:520px;margin:2rem auto;text-align:left">
            <dl class="review-details">
                <dt><?= e(__('book.step_room')) ?></dt>
                <dd><?= e($booking['room_name']) ?></dd>
                <dt><?= e(__('home.check_in')) ?></dt>
                <dd><?= e(booking_format_dates($booking['check_in'], $booking['check_out'])) ?> (<?= (int) $nights ?> <?= e(__('book.nights')) ?>)</dd>
                <dt><?= e(__('book.total')) ?></dt>
                <dd>$<?= number_format((float) $booking['total_price'], 2) ?></dd>
                <dt>Status</dt>
                <dd><span class="status-pending"><?= e(__('book.status_pending')) ?></span></dd>
            </dl>
            <?php $emailStatus = \App\Core\Session::flash('email_status'); ?>
            <?php if ($emailStatus === 'sent'): ?>
            <p class="review-note"><?= e(__('book.email_sent', ['email' => $booking['guest_email']])) ?></p>
            <?php elseif ($emailStatus === 'failed'): ?>
            <p class="review-note"><?= e(__('book.email_failed', ['ref' => $reference])) ?></p>
            <?php else: ?>
            <p class="review-note"><?= e(__('book.email_later', ['email' => $booking['guest_email']])) ?></p>
            <?php endif; ?>
        </div>

        <div class="error-actions">
            <a href="<?= url('/') ?>" class="btn btn-outline-dark"><?= e(__('book.back_home')) ?></a>
            <a href="<?= url('/rooms') ?>" class="btn btn-primary"><?= e(__('book.browse_rooms')) ?></a>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
