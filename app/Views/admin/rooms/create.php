<?php ob_start(); ?>
<h1><?= e($title) ?></h1>
<form method="post" action="<?= url('/admin/rooms') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php require __DIR__ . '/_form.php'; ?>
    <button type="submit" class="btn btn-primary">Create Room</button>
    <a href="<?= url('/admin/rooms') ?>">Cancel</a>
</form>
<?php require __DIR__ . '/../partials/tinymce.php'; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
