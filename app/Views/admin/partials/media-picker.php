<?php
/** @var array $mediaImages */
/** @var string $fieldName media_image_id */
/** @var int|string|null $selectedId */
/** @var string|null $previewPath */
$fieldName = $fieldName ?? 'media_image_id';
$selectedId = $selectedId ?? old($fieldName, '');
?>
<div class="media-picker">
    <p class="hint">Choose from <a href="<?= url('/admin/media') ?>" target="_blank">Media library</a> or upload a new file below.</p>
    <div class="media-picker-grid">
        <label class="media-pick-none">
            <input type="radio" name="<?= e($fieldName) ?>" value="" <?= $selectedId === '' ? 'checked' : '' ?>>
            <span>No selection</span>
        </label>
        <?php foreach ($mediaImages as $img): ?>
        <label class="media-pick-item">
            <input type="radio" name="<?= e($fieldName) ?>" value="<?= (int) $img['id'] ?>" <?= (string)$selectedId === (string)$img['id'] ? 'checked' : '' ?>>
            <img src="<?= e(upload_url($img['file_path'])) ?>" alt="">
            <span><?= e(mb_strimwidth($img['original_name'], 0, 20, '…')) ?></span>
        </label>
        <?php endforeach; ?>
    </div>
    <?php if ($previewPath): ?>
    <p>Current: <img src="<?= e(upload_url($previewPath)) ?>" class="thumb-preview" alt=""></p>
    <?php endif; ?>
</div>
