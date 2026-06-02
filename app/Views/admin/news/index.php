<?php ob_start(); ?>
<div class="page-actions">
    <h1>News</h1>
    <a href="<?= url('/admin/news/create') ?>" class="btn btn-primary">Create</a>
</div>
<?php
$action = url('/admin/news');
$search = $search ?? '';
$statusFilter = $statusFilter ?? '';
require __DIR__ . '/../partials/list-filters.php';
?>
<table class="admin-table">
<thead><tr><th>Title</th><th>Status</th><th>Published</th><th></th></tr></thead>
<tbody>
<?php foreach ($articles as $a): ?>
<tr>
<td><?= e($a['title']) ?></td>
<td><span class="badge badge-<?= e($a['status']) ?>"><?= e($a['status']) ?></span></td>
<td><?= e($a['published_at'] ?? '—') ?></td>
<td class="actions">
<a href="<?= url('/news/' . $a['slug']) ?>" target="_blank">View</a>
<a href="<?= url('/admin/news/edit/' . $a['id']) ?>">Edit</a>
<form method="post" action="<?= url('/admin/news/delete/' . $a['id']) ?>" class="inline" onsubmit="return confirm('Delete?')"><?= csrf_field() ?><button type="submit" class="btn-link danger">Delete</button></form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?= pagination_links($pager, url('/admin/news') . '?' . http_build_query(array_filter(['q' => $search, 'status' => $statusFilter]))) ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
