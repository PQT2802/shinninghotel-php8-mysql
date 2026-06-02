<?php $b = $banner ?? []; ?>
<div class="form-group"><label>Title *</label><input name="title" value="<?= e(old('title', $b['title'] ?? '')) ?>" required></div>
<div class="form-group"><label>Subtitle</label><input name="subtitle" value="<?= e(old('subtitle', $b['subtitle'] ?? '')) ?>"></div>
<div class="form-group">
    <label>Image <?= empty($b) ? '*' : '(leave empty to keep current)' ?></label>
    <?php if (!empty($b['image_path'])): ?><p><img src="<?= e(upload_url($b['image_path'])) ?>" class="thumb-preview" alt=""></p><?php endif; ?>
    <input type="file" name="image" accept="image/*" <?= empty($b) ? 'required' : '' ?>>
</div>
<div class="form-group"><label>Button text</label><input name="button_text" value="<?= e(old('button_text', $b['button_text'] ?? '')) ?>"></div>
<div class="form-group"><label>Button URL</label><input name="button_url" value="<?= e(old('button_url', $b['button_url'] ?? '')) ?>" placeholder="/book"></div>
<div class="form-group"><label>Position</label><input name="position" value="<?= e(old('position', $b['position'] ?? 'home_hero')) ?>"></div>
<div class="form-group"><label>Sort order</label><input type="number" name="sort_order" value="<?= (int) old('sort_order', $b['sort_order'] ?? 0) ?>"></div>
<div class="form-group"><label><input type="checkbox" name="is_active" value="1" <?= !isset($b['is_active']) || $b['is_active'] ? 'checked' : '' ?>> Active</label></div>
