<?php ob_start(); ?>
<h1>Dashboard</h1>

<h2 class="dashboard-section-title">Bookings overview</h2>
<div class="stat-grid booking-stats">
    <a href="<?= url('/admin/bookings') ?>" class="stat-card">
        <span>Total bookings</span>
        <strong><?= (int) $bookingStats['total'] ?></strong>
    </a>
    <a href="<?= url('/admin/bookings?status=pending') ?>" class="stat-card<?= ($bookingStats['pending'] ?? 0) > 0 ? ' stat-highlight' : '' ?>">
        <span>Pending</span>
        <strong><?= (int) $bookingStats['pending'] ?></strong>
    </a>
    <a href="<?= url('/admin/bookings?status=confirmed') ?>" class="stat-card">
        <span>Confirmed</span>
        <strong><?= (int) $bookingStats['confirmed'] ?></strong>
    </a>
    <a href="<?= url('/admin/bookings?status=cancelled') ?>" class="stat-card">
        <span>Cancelled</span>
        <strong><?= (int) $bookingStats['cancelled'] ?></strong>
    </a>
    <div class="stat-card stat-card-static">
        <span>Revenue (confirmed)</span>
        <strong>$<?= number_format((float) $bookingStats['revenue_confirmed'], 0) ?></strong>
    </div>
    <div class="stat-card stat-card-static">
        <span>Pending value</span>
        <strong>$<?= number_format((float) $bookingStats['revenue_pending'], 0) ?></strong>
    </div>
    <div class="stat-card stat-card-static">
        <span>New this month</span>
        <strong><?= (int) $bookingStats['this_month'] ?></strong>
    </div>
</div>

<h2 class="dashboard-section-title">Content</h2>
<div class="stat-grid">
    <div class="stat-card stat-card-static"><span>Pages</span><strong><?= (int) $stats['pages'] ?></strong></div>
    <div class="stat-card stat-card-static"><span>News</span><strong><?= (int) $stats['news'] ?></strong></div>
    <div class="stat-card stat-card-static"><span>Media</span><strong><?= (int) $stats['media'] ?></strong></div>
    <div class="stat-card stat-card-static"><span>Rooms</span><strong><?= (int) $stats['rooms'] ?></strong></div>
    <div class="stat-card stat-card-static">
        <span>Unread messages</span>
        <strong><a href="<?= url('/admin/contact-messages?status=unread') ?>"><?= (int) ($unreadMessages ?? 0) ?></a></strong>
    </div>
    <div class="stat-card stat-card-static"><span>Users</span><strong><?= (int) $userCount ?></strong></div>
</div>

<h2>Recent bookings</h2>
<table class="admin-table">
<thead><tr><th>Reference</th><th>Guest</th><th>Room</th><th>Dates</th><th>Total</th><th>Status</th><th></th></tr></thead>
<tbody>
<?php foreach ($recentBookings as $b): ?>
<tr>
<td><code><?= e(booking_reference((int) $b['id'])) ?></code></td>
<td><?= e($b['guest_name']) ?></td>
<td><?= e($b['room_name']) ?></td>
<td><?= e($b['check_in']) ?> → <?= e($b['check_out']) ?></td>
<td>$<?= number_format((float) $b['total_price'], 2) ?></td>
<td><span class="badge badge-<?= e($b['status']) ?>"><?= e($b['status']) ?></span></td>
<td><a href="<?= url('/admin/bookings/show/' . $b['id']) ?>">View</a></td>
</tr>
<?php endforeach; ?>
<?php if (empty($recentBookings)): ?><tr><td colspan="7">No bookings yet.</td></tr><?php endif; ?>
</tbody>
</table>
<p><a href="<?= url('/admin/bookings') ?>" class="btn-primary btn-sm">All bookings</a></p>

<h2>Recent contact messages</h2>
<table class="admin-table">
<thead><tr><th>Name</th><th>Subject</th><th>Status</th><th></th></tr></thead>
<tbody>
<?php foreach ($recentMessages as $m): ?>
<tr>
<td><?= e($m['name']) ?></td>
<td><?= e($m['subject'] ?? '—') ?></td>
<td><span class="badge badge-<?= e($m['status']) ?>"><?= e($m['status']) ?></span></td>
<td><a href="<?= url('/admin/contact-messages/show/' . $m['id']) ?>">View</a></td>
</tr>
<?php endforeach; ?>
<?php if (empty($recentMessages)): ?><tr><td colspan="4">No messages yet.</td></tr><?php endif; ?>
</tbody>
</table>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
