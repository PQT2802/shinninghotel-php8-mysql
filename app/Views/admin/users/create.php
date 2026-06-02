<?php ob_start(); ?>
<h1>Create User</h1>
<form method="post" action="<?= url('/admin/users') ?>"><?= csrf_field() ?>
<?php require __DIR__ . '/_form.php'; ?><button class="btn btn-primary">Save</button></form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
