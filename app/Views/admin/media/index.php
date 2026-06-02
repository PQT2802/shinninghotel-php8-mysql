<?php ob_start(); ?>
<h1>Media Library</h1>
<form method="post" action="<?= url('/admin/media/upload') ?>" enctype="multipart/form-data" class="upload-form">
    <?= csrf_field() ?>
    <input type="file" name="file" accept=".jpg,.jpeg,.png,.webp,.pdf" required>
    <button class="btn btn-primary">Upload</button>
</form>

<div class="media-grid">
<?php foreach ($files as $f): ?>
    <div class="media-card">
        <?php if (str_starts_with($f['mime_type'], 'image/')): ?>
            <img src="<?= e(upload_url($f['file_path'])) ?>" alt="">
        <?php else: ?>
            <div class="media-file-icon">PDF</div>
        <?php endif; ?>
        <p class="media-name" title="<?= e($f['original_name']) ?>"><?= e(mb_strimwidth($f['original_name'], 0, 24, '…')) ?></p>
        <input type="text" readonly class="media-url" value="<?= e(upload_url($f['file_path'])) ?>" id="url-<?= (int)$f['id'] ?>">
        <div class="media-actions">
            <button type="button" class="btn btn-sm btn-outline copy-url" data-target="url-<?= (int)$f['id'] ?>">Copy URL</button>
            <form method="post" action="<?= url('/admin/media/delete/' . $f['id']) ?>" onsubmit="return confirm('Delete file?')"><?= csrf_field() ?><button class="btn-link danger">Delete</button></form>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?= pagination_links($pager, url('/admin/media')) ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
