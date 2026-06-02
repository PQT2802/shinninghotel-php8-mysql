<?php ob_start(); ?>
<h1>Create Banner</h1>
<form method="post" action="<?= url('/admin/banners') ?>" enctype="multipart/form-data"><?= csrf_field() ?>
<?php $banner = null; require __DIR__ . '/_form.php'; ?>
<button class="btn btn-primary">Save</button></form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
