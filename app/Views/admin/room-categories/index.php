<?php ob_start(); ?>
<div class="page-actions">
    <h1>Room Categories</h1>
    <a href="<?= url('/admin/room-categories/create') ?>" class="btn btn-primary">Create Category</a>
</div>
<table class="admin-table">
<thead><tr><th>Name</th><th>Slug</th><th>Rooms</th><th>Order</th><th>Active</th><th></th></tr></thead>
<tbody>
<?php foreach ($categories as $c): ?>
<tr>
<td><?= e($c['name']) ?></td>
<td><code><?= e($c['slug']) ?></code></td>
<td><?= (int) ($c['room_count'] ?? 0) ?></td>
<td><?= (int) $c['sort_order'] ?></td>
<td><?= $c['is_active'] ? 'Yes' : 'No' ?></td>
<td class="actions">
<a href="<?= url('/admin/rooms?category=' . $c['id']) ?>">View rooms</a>
<a href="<?= url('/admin/room-categories/edit/' . $c['id']) ?>">Edit</a>
<?php if ((int)($c['room_count'] ?? 0) === 0): ?>
<form method="post" action="<?= url('/admin/room-categories/delete/' . $c['id']) ?>" class="inline" onsubmit="return confirm('Delete category?')"><?= csrf_field() ?><button type="submit" class="btn-link danger">Delete</button></form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
