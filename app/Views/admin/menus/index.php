<?php ob_start(); ?>
<h1>Menus</h1>
<ul><?php foreach ($menus as $m): ?>
<li><a href="<?= url('/admin/menus/edit/' . $m['id']) ?>"><?= e($m['name']) ?> (<?= e($m['location']) ?>)</a></li>
<?php endforeach; ?></ul>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
