<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?= e(brand_name()) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Lora:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/theme-luxury.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="login-page">
    <form method="post" action="<?= url('/admin/login') ?>" class="login-box">
        <h1><?= e(brand_name()) ?></h1>
        <p class="login-sub"><?= e(brand_slogan()) ?></p>
        <?php if ($e = \App\Core\Session::flash('error')): ?><div class="alert alert-error"><?= e($e) ?></div><?php endif; ?>
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="login-email">Email</label>
            <input type="email" id="login-email" name="email" class="form-control" required autocomplete="username">
        </div>
        <div class="form-group">
            <label for="login-password">Password</label>
            <input type="password" id="login-password" name="password" class="form-control" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn btn-primary w-100">Sign In</button>
        <p class="login-hint">Default: admin@shinning.com / password</p>
    </form>
</body>
</html>
