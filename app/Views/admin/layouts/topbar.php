<header class="admin-topbar">
    <div class="d-flex align-items-center gap-3">
        <button type="button" class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="admin-sidebar">
            <i class="bi bi-list"></i>
        </button>
        <h1><?= e($title ?? 'Dashboard') ?></h1>
    </div>
    <div class="admin-topbar-actions">
        <span class="user-meta"><?= e(auth_user()['name'] ?? '') ?> · <?= e(auth_role() ?? '') ?></span>
        <form method="post" action="<?= url('/admin/logout') ?>" class="d-inline"><?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-dark btn-sm">Logout</button>
        </form>
    </div>
</header>
