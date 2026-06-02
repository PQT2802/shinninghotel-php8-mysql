<?php
$p = $page ?? [];
$translations = $translations ?? \App\Models\Translation::forEntity('page', (int) ($p['id'] ?? 0));
if (empty($translations['en']) && !empty($p)) {
    $translations['en'] = [
        'title' => $p['title'] ?? '',
        'content' => $p['content'] ?? '',
        'seo_title' => $p['seo_title'] ?? '',
        'seo_description' => $p['seo_description'] ?? '',
    ];
}
?>
<div class="form-group mb-3">
    <label>Slug *</label>
    <input type="text" name="slug" class="form-control" value="<?= e((string) old('slug', $p['slug'] ?? '')) ?>" placeholder="auto-from-english-title">
    <?php if ($e = validation_error('slug')): ?><p class="field-error"><?= e($e) ?></p><?php endif; ?>
</div>
<div class="form-group mb-3">
    <label>Status</label>
    <select name="status" class="form-select">
        <option value="draft" <?= old('status', $p['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= old('status', $p['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
    </select>
</div>
<?php require __DIR__ . '/../partials/locale-tabs.php'; ?>
