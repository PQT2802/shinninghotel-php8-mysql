<?php ob_start(); ?>
<div class="page-actions">
    <h1>Pages</h1>
    <a href="<?= url('/admin/pages/create') ?>" class="btn btn-primary">Create</a>
</div>
<?php
$action = url('/admin/pages');
$search = $search ?? '';
$statusFilter = $statusFilter ?? '';
require __DIR__ . '/../partials/list-filters.php';
?>
<table class="admin-table">
<thead><tr><th>Title</th><th>Slug</th><th>Status</th><th>Updated</th><th></th></tr></thead>
<tbody>
<?php foreach ($pages as $p): ?>
<tr>
<td><?= e($p['title']) ?></td>
<td><code><?= e($p['slug']) ?></code></td>
<td><span class="badge badge-<?= e($p['status']) ?>"><?= e($p['status']) ?></span></td>
<td><?= e($p['updated_at'] ?? $p['created_at'] ?? '') ?></td>
<td class="actions">
<a href="<?= url('/page/' . $p['slug']) ?>" target="_blank">View</a>
<a href="<?= url('/admin/pages/edit/' . $p['id']) ?>">Edit</a>
<form method="post" action="<?= url('/admin/pages/delete/' . $p['id']) ?>" class="inline" onsubmit="return confirm('Delete this page?')"><?= csrf_field() ?><button type="submit" class="btn-link danger">Delete</button></form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?= pagination_links($pager, url('/admin/pages') . '?' . http_build_query(array_filter(['q' => $search, 'status' => $statusFilter]))) ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
