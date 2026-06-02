<?php ob_start(); ?>
<h1>Edit News</h1>
<form method="post" action="<?= url('/admin/news/update/' . $article['id']) ?>" enctype="multipart/form-data"><?= csrf_field() ?>
<?php require __DIR__ . '/_form.php'; ?><button class="btn btn-primary">Update</button></form>
<?php require __DIR__ . '/../partials/tinymce.php'; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
