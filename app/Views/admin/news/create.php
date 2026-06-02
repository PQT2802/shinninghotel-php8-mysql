<?php ob_start(); ?>
<h1>Create News</h1>
<form method="post" action="<?= url('/admin/news') ?>" enctype="multipart/form-data"><?= csrf_field() ?>
<?php require __DIR__ . '/_form.php'; ?><button class="btn btn-primary">Save</button></form>
<?php require __DIR__ . '/../partials/tinymce.php'; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
