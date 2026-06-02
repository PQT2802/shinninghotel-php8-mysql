<?php ob_start(); $b = $booking; ?>
<div class="page-actions">
    <h1><?= e($reference) ?></h1>
    <span class="badge badge-<?= e($b['status']) ?>"><?= e($b['status']) ?></span>
</div>

<div class="booking-detail-grid">
    <div class="booking-detail-card">
        <h2>Guest</h2>
        <p><strong><?= e($b['guest_name']) ?></strong></p>
        <p><a href="mailto:<?= e($b['guest_email']) ?>"><?= e($b['guest_email']) ?></a></p>
        <?php if ($b['guest_phone']): ?><p><?= e($b['guest_phone']) ?></p><?php endif; ?>
        <p><?= (int) $b['guests_count'] ?> guest(s)</p>
    </div>
    <div class="booking-detail-card">
        <h2>Stay</h2>
        <?php if ($b['room_image']): ?>
        <img src="<?= e(upload_url($b['room_image'])) ?>" class="thumb-preview" alt="">
        <?php endif; ?>
        <p><strong><?= e($b['room_name']) ?></strong></p>
        <p><?= e($b['category_name'] ?? '') ?></p>
        <p><?= e(booking_format_dates($b['check_in'], $b['check_out'])) ?></p>
        <p><?= (int) $nights ?> nights</p>
        <p class="review-total">$<?= number_format((float) $b['total_price'], 2) ?></p>
    </div>
</div>

<?php if (!empty($b['notes'])): ?>
<div class="booking-detail-card">
    <h2>Special requests</h2>
    <p><?= nl2br(e($b['notes'])) ?></p>
</div>
<?php endif; ?>

<div class="booking-detail-card">
    <h2>Update status</h2>
    <form method="post" action="<?= url('/admin/bookings/status/' . $b['id']) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="redirect" value="<?= e(url('/admin/bookings/show/' . $b['id'])) ?>">
        <select name="status">
            <option value="pending" <?= $b['status']==='pending'?'selected':'' ?>>Pending</option>
            <option value="confirmed" <?= $b['status']==='confirmed'?'selected':'' ?>>Confirmed</option>
            <option value="cancelled" <?= $b['status']==='cancelled'?'selected':'' ?>>Cancelled</option>
        </select>
        <button type="submit" class="btn btn-primary">Save status</button>
    </form>
    <p class="hint">Created: <?= e($b['created_at']) ?></p>
</div>

<p><a href="<?= url('/admin/bookings') ?>">← All bookings</a></p>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
