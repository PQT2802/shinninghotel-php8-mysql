<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin') ?> | <?= e(brand_name()) ?> CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Lora:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/theme-luxury.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin-body" id="admin-body">
<div class="sidebar-overlay" id="sidebar-overlay" aria-hidden="true"></div>
<?php require __DIR__ . '/sidebar.php'; ?>
<div class="admin-main">
    <?php require __DIR__ . '/topbar.php'; ?>
    <div class="admin-content">
        <?php if ($s = \App\Core\Session::flash('success')): ?><div class="alert alert-success"><?= e($s) ?></div><?php endif; ?>
        <?php if ($e = \App\Core\Session::flash('error')): ?><div class="alert alert-error"><?= e($e) ?></div><?php endif; ?>
        <?= $content ?? '' ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
