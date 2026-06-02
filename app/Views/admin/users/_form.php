<?php
$u = $user ?? [];
$val = fn (string $k, string $default = '') => e((string) old($k, $u[$k] ?? $default));
$role = old('role', $u['role'] ?? 'editor');
$status = old('status', $u['status'] ?? 'active');
?>
<div class="form-group"><label>Name *</label><input name="name" value="<?= $val('name') ?>" required></div>
<div class="form-group">
    <label>Email *</label>
    <input type="email" name="email" value="<?= $val('email') ?>" required>
    <?php if ($e = validation_error('email')): ?><p class="field-error"><?= e($e) ?></p><?php endif; ?>
</div>
<div class="form-group">
    <label>Password <?= $u ? '(leave blank to keep)' : '*' ?></label>
    <input type="password" name="password" <?= $u ? '' : 'required' ?>>
    <?php if ($e = validation_error('password')): ?><p class="field-error"><?= e($e) ?></p><?php endif; ?>
</div>
<div class="form-group">
    <label>Role</label>
    <select name="role">
        <?php if (auth_role() === 'super_admin'): ?>
        <option value="super_admin" <?= $role === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
        <?php endif; ?>
        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="editor" <?= $role === 'editor' ? 'selected' : '' ?>>Editor</option>
    </select>
</div>
<div class="form-group">
    <label>Status</label>
    <select name="status">
        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
        <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
    </select>
</div>
