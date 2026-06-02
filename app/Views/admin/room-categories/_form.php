<?php
$c = $category ?? [];
$val = fn (string $k, string $default = '') => e((string) old($k, $c[$k] ?? $default));
?>
<div class="form-group">
    <label>Name *</label>
    <input type="text" name="name" value="<?= $val('name') ?>" required>
    <?php if ($e = validation_error('name')): ?><p class="field-error"><?= e($e) ?></p><?php endif; ?>
</div>
<div class="form-group">
    <label>Slug *</label>
    <input type="text" name="slug" value="<?= $val('slug') ?>" placeholder="e.g. deluxe">
    <?php if ($e = validation_error('slug')): ?><p class="field-error"><?= e($e) ?></p><?php endif; ?>
</div>
<div class="form-group">
    <label>Description</label>
    <textarea name="description" rows="3"><?= e((string) old('description', $c['description'] ?? '')) ?></textarea>
</div>
<div class="form-group">
    <label>Sort order</label>
    <input type="number" name="sort_order" value="<?= (int) old('sort_order', $c['sort_order'] ?? 0) ?>">
</div>
<div class="form-group">
    <label><input type="checkbox" name="is_active" value="1" <?= !isset($c['is_active']) || $c['is_active'] ? 'checked' : '' ?>> Active (show on website)</label>
</div>
