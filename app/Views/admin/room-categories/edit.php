<?php ob_start(); ?>
<h1>Edit Category</h1>
<?php if (($roomCount ?? 0) > 0): ?>
<p class="hint"><?= (int) $roomCount ?> room(s) in this category. <a href="<?= url('/admin/rooms?category=' . $category['id']) ?>">Manage rooms</a></p>
<?php endif; ?>
<form method="post" action="<?= url('/admin/room-categories/update/' . $category['id']) ?>">
    <?= csrf_field() ?>
    <?php require __DIR__ . '/_form.php'; ?>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="<?= url('/admin/room-categories') ?>">Cancel</a>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
