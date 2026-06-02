<?php ob_start(); ?>
<h1>Create Page</h1>
<form method="post" action="<?= url('/admin/pages') ?>"><?= csrf_field() ?>
<?php require __DIR__ . '/_form.php'; ?>
<button type="submit" class="btn btn-primary">Save</button>
</form>
<?php require __DIR__ . '/../partials/tinymce.php'; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
