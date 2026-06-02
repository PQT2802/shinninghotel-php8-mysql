<?php ob_start(); ?>
<h1>Edit User</h1>
<form method="post" action="<?= url('/admin/users/update/' . $user['id']) ?>"><?= csrf_field() ?>
<?php require __DIR__ . '/_form.php'; ?><button class="btn btn-primary">Update</button></form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
