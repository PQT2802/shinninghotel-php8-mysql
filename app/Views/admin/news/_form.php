<?php
$a = $article ?? [];
$translations = $translations ?? \App\Models\Translation::forEntity('news', (int) ($a['id'] ?? 0));
if (empty($translations['en']) && !empty($a)) {
    $translations['en'] = [
        'title' => $a['title'] ?? '',
        'summary' => $a['summary'] ?? '',
        'content' => $a['content'] ?? '',
        'seo_title' => $a['seo_title'] ?? '',
        'seo_description' => $a['seo_description'] ?? '',
    ];
}
?>
<div class="form-group mb-3">
    <label>Slug *</label>
    <input type="text" name="slug" class="form-control" value="<?= e((string) old('slug', $a['slug'] ?? '')) ?>">
</div>
<div class="form-group mb-3">
    <label>Status</label>
    <select name="status" class="form-select">
        <option value="draft" <?= old('status', $a['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= old('status', $a['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
    </select>
</div>
<div class="form-group mb-3">
    <label>Thumbnail</label>
    <?php if (!empty($a['thumbnail_path'])): ?>
        <p><img src="<?= e(upload_url($a['thumbnail_path'])) ?>" class="thumb-preview" alt=""></p>
    <?php endif; ?>
    <input type="file" name="thumbnail" accept="image/*" class="form-control">
</div>
<?php
$fields = ['title', 'summary', 'content', 'seo_title', 'seo_description'];
require __DIR__ . '/../partials/locale-tabs.php';
?>
