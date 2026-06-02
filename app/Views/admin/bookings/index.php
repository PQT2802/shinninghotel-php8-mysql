<?php ob_start(); ?>
<div class="page-actions">
    <h1>Bookings</h1>
</div>

<div class="stat-grid booking-stats">
    <a href="<?= url('/admin/bookings?status=pending') ?>" class="stat-card <?= ($statusFilter ?? '') === 'pending' ? 'stat-active' : '' ?>">
        <span>Pending</span><strong><?= (int) $stats['pending'] ?></strong>
    </a>
    <a href="<?= url('/admin/bookings?status=confirmed') ?>" class="stat-card <?= ($statusFilter ?? '') === 'confirmed' ? 'stat-active' : '' ?>">
        <span>Confirmed</span><strong><?= (int) $stats['confirmed'] ?></strong>
    </a>
    <a href="<?= url('/admin/bookings?status=cancelled') ?>" class="stat-card <?= ($statusFilter ?? '') === 'cancelled' ? 'stat-active' : '' ?>">
        <span>Cancelled</span><strong><?= (int) $stats['cancelled'] ?></strong>
    </a>
    <a href="<?= url('/admin/bookings') ?>" class="stat-card <?= ($statusFilter ?? '') === '' ? 'stat-active' : '' ?>">
        <span>All</span><strong><?= (int) ($stats['pending'] + $stats['confirmed'] + $stats['cancelled']) ?></strong>
    </a>
</div>

<table class="admin-table">
<thead>
<tr>
    <th>Reference</th>
    <th>Guest</th>
    <th>Room</th>
    <th>Check-in</th>
    <th>Check-out</th>
    <th>Total</th>
    <th>Status</th>
    <th></th>
</tr>
</thead>
<tbody>
<?php foreach ($bookings as $b): ?>
<tr class="<?= $b['status'] === 'pending' ? 'row-unread' : '' ?>">
<td><a href="<?= url('/admin/bookings/show/' . $b['id']) ?>"><code><?= e(booking_reference((int)$b['id'])) ?></code></a></td>
<td><?= e($b['guest_name']) ?><br><small><?= e($b['guest_email']) ?></small></td>
<td><?= e($b['room_name']) ?></td>
<td><?= e($b['check_in']) ?></td>
<td><?= e($b['check_out']) ?></td>
<td>$<?= number_format((float) $b['total_price'], 2) ?></td>
<td><span class="badge badge-<?= e($b['status']) ?>"><?= e($b['status']) ?></span></td>
<td class="actions">
<a href="<?= url('/admin/bookings/show/' . $b['id']) ?>">View</a>
<form method="post" action="<?= url('/admin/bookings/status/' . $b['id']) ?>" class="inline"><?= csrf_field() ?>
<select name="status" onchange="this.form.submit()">
<option value="pending" <?= $b['status']==='pending'?'selected':'' ?>>Pending</option>
<option value="confirmed" <?= $b['status']==='confirmed'?'selected':'' ?>>Confirmed</option>
<option value="cancelled" <?= $b['status']==='cancelled'?'selected':'' ?>>Cancelled</option>
</select>
</form>
</td>
</tr>
<?php endforeach; ?>
<?php if (empty($bookings)): ?><tr><td colspan="8">No bookings found.</td></tr><?php endif; ?>
</tbody>
</table>
<?= pagination_links($pager, url('/admin/bookings') . ($statusFilter ? '?status=' . urlencode($statusFilter) : '')) ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
