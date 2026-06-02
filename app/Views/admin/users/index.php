<?php ob_start(); ?>
<div class="page-actions"><h1>Users</h1><a href="<?= url('/admin/users/create') ?>" class="btn btn-primary">Create</a></div>
<table class="admin-table">
<thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last login</th><th></th></tr></thead>
<tbody>
<?php foreach ($users as $u): ?>
<tr>
<td><?= e($u['name']) ?></td>
<td><?= e($u['email']) ?></td>
<td><?= e($u['role']) ?></td>
<td><span class="badge"><?= e($u['status']) ?></span></td>
<td><?= e($u['last_login_at'] ?? '—') ?></td>
<td class="actions">
<a href="<?= url('/admin/users/edit/' . $u['id']) ?>">Edit</a>
<?php if ((int)$u['id'] !== auth_id()): ?>
<form method="post" action="<?= url('/admin/users/delete/' . $u['id']) ?>" class="inline" onsubmit="return confirm('Delete user?')"><?= csrf_field() ?><button type="submit" class="btn-link danger">Delete</button></form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
