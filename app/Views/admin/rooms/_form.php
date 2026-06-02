<?php
$r = $room ?? [];
$val = fn (string $k, string $default = '') => e((string) old($k, $r[$k] ?? $default));
$selectedAmenities = $selectedAmenities ?? amenities_from_json($r['amenities'] ?? null);
$oldAmenities = old('amenities');
if (is_array($oldAmenities)) {
    $selectedAmenities = $oldAmenities;
}
$galleryMediaIds = [];
foreach ($gallery ?? [] as $g) {
    if (!empty($g['media_id'])) {
        $galleryMediaIds[] = (int) $g['media_id'];
    }
}
$oldGallery = old('gallery_media_ids');
if (is_array($oldGallery)) {
    $galleryMediaIds = array_map('intval', $oldGallery);
}
?>
<div class="form-row">
    <div class="form-group">
        <label>Name *</label>
        <input type="text" name="name" value="<?= $val('name') ?>" required>
        <?php if ($e = validation_error('name')): ?><p class="field-error"><?= e($e) ?></p><?php endif; ?>
    </div>
    <div class="form-group">
        <label>Slug *</label>
        <input type="text" name="slug" value="<?= $val('slug') ?>">
        <?php if ($e = validation_error('slug')): ?><p class="field-error"><?= e($e) ?></p><?php endif; ?>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Category</label>
        <select name="category_id">
            <option value="">— None —</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?= (int) $c['id'] ?>" <?= (string)old('category_id', $r['category_id'] ?? '') === (string)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <p class="hint"><a href="<?= url('/admin/room-categories') ?>">Manage categories</a></p>
    </div>
    <div class="form-group">
        <label>Status *</label>
        <select name="status">
            <option value="draft" <?= old('status', $r['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
            <option value="published" <?= old('status', $r['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Price per night (USD) *</label>
        <input type="number" step="0.01" min="0" name="price_per_night" value="<?= $val('price_per_night', '0') ?>" required>
    </div>
    <div class="form-group">
        <label>Max guests</label>
        <input type="number" min="1" max="20" name="max_guests" value="<?= (int) old('max_guests', $r['max_guests'] ?? 2) ?>">
    </div>
    <div class="form-group">
        <label>Sort order</label>
        <input type="number" name="sort_order" value="<?= (int) old('sort_order', $r['sort_order'] ?? 0) ?>">
    </div>
</div>

<div class="form-group">
    <label><input type="checkbox" name="is_featured" value="1" <?= !empty(old('is_featured', $r['is_featured'] ?? 0)) ? 'checked' : '' ?>> Featured on homepage</label>
</div>

<div class="form-group">
    <label>Description</label>
    <textarea name="description" class="rich-editor" rows="10"><?= e((string) old('description', $r['description'] ?? '')) ?></textarea>
</div>

<h3>Main image</h3>
<?php
$fieldName = 'media_image_id';
$selectedId = old('media_image_id', '');
$previewPath = $r['image_path'] ?? null;
require __DIR__ . '/../partials/media-picker.php';
?>
<div class="form-group">
    <label>Or upload new image</label>
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
</div>

<h3>Gallery (from Media)</h3>
<p class="hint">Select additional photos for the room detail page.</p>
<div class="media-picker-grid gallery-pick">
    <?php foreach ($mediaImages as $img): ?>
    <label class="media-pick-item">
        <input type="checkbox" name="gallery_media_ids[]" value="<?= (int) $img['id'] ?>" <?= in_array((int)$img['id'], $galleryMediaIds, true) ? 'checked' : '' ?>>
        <img src="<?= e(upload_url($img['file_path'])) ?>" alt="">
    </label>
    <?php endforeach; ?>
</div>

<h3>Amenities</h3>
<div class="amenity-grid">
    <?php foreach ($amenityOptions as $opt): ?>
    <label class="amenity-check">
        <input type="checkbox" name="amenities[]" value="<?= e($opt) ?>" <?= in_array($opt, $selectedAmenities, true) ? 'checked' : '' ?>>
        <?= e($opt) ?>
    </label>
    <?php endforeach; ?>
</div>
<div class="form-group">
    <label>Custom amenities (one per line)</label>
    <textarea name="amenities_custom" rows="3" placeholder="Private pool&#10;Kids bed"><?= e((string) old('amenities_custom')) ?></textarea>
</div>
