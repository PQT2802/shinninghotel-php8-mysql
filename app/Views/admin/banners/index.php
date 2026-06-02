<?php ob_start(); ?>
<div class="page-actions"><h1>Banners</h1><a href="<?= url('/admin/banners/create') ?>" class="btn btn-primary">Create</a></div>
<table class="admin-table">
<thead><tr><th>Preview</th><th>Title</th><th>Position</th><th>Active</th><th></th></tr></thead>
<tbody>
<?php foreach ($banners as $b): ?>
<tr>
<td><?php if ($b['image_path']): ?><img src="<?= e(upload_url($b['image_path'])) ?>" alt="" class="table-thumb"><?php endif; ?></td>
<td><?= e($b['title']) ?></td>
<td><?= e($b['position']) ?></td>
<td><?= $b['is_active'] ? 'Yes' : 'No' ?></td>
<td class="actions">
<a href="<?= url('/admin/banners/edit/' . $b['id']) ?>">Edit</a>
<form method="post" action="<?= url('/admin/banners/delete/' . $b['id']) ?>" class="inline" onsubmit="return confirm('Delete banner?')"><?= csrf_field() ?><button class="btn-link danger">Delete</button></form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
