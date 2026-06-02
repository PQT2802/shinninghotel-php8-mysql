<?php ob_start(); ?>
<div class="page-actions">
    <h1>Contact Messages <?php if (($unreadCount ?? 0) > 0): ?><span class="badge badge-unread"><?= (int) $unreadCount ?> unread</span><?php endif; ?></h1>
</div>
<?php
$action = url('/admin/contact-messages');
$statusFilter = $statusFilter ?? '';
$statusOptions = 'contact';
require __DIR__ . '/../partials/list-filters.php';
?>
<table class="admin-table">
<thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Date</th><th></th></tr></thead>
<tbody>
<?php foreach ($messages as $m): ?>
<tr class="<?= $m['status'] === 'unread' ? 'row-unread' : '' ?>">
<td><?= e($m['name']) ?></td>
<td><?= e($m['email']) ?></td>
<td><?= e($m['subject'] ?? '—') ?></td>
<td><span class="badge badge-<?= e($m['status']) ?>"><?= e($m['status']) ?></span></td>
<td><?= e($m['created_at']) ?></td>
<td class="actions">
<a href="<?= url('/admin/contact-messages/show/' . $m['id']) ?>">View</a>
<?php if ($m['status'] === 'unread'): ?>
<form method="post" action="<?= url('/admin/contact-messages/read/' . $m['id']) ?>" class="inline"><?= csrf_field() ?><button type="submit" class="btn-link">Mark read</button></form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?= pagination_links($pager, url('/admin/contact-messages') . '?' . http_build_query(array_filter(['status' => $statusFilter]))) ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
