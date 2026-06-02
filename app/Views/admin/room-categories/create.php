<?php ob_start(); ?>
<h1>Create Room Category</h1>
<form method="post" action="<?= url('/admin/room-categories') ?>">
    <?= csrf_field() ?>
    <?php require __DIR__ . '/_form.php'; ?>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="<?= url('/admin/room-categories') ?>">Cancel</a>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
