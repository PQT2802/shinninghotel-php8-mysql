<?php
/** @var array<string, array<string, mixed>> $translations */
/** @var array<string, string> $fields title, content, seo_title, seo_description, summary, name, subtitle, button_text */
$translations = $translations ?? ['en' => [], 'vi' => []];
$fields = $fields ?? ['title', 'content', 'seo_title', 'seo_description'];
$labels = ['en' => 'English', 'vi' => 'Tiếng Việt'];
?>
<ul class="nav nav-tabs locale-tabs" role="tablist">
    <?php foreach (['en', 'vi'] as $loc): ?>
    <li class="nav-item" role="presentation">
        <button class="nav-link<?= $loc === 'en' ? ' active' : '' ?>" type="button" data-bs-toggle="tab" data-bs-target="#tab-<?= $loc ?>" role="tab"><?= $labels[$loc] ?></button>
    </li>
    <?php endforeach; ?>
</ul>
<div class="tab-content locale-tab-panels border border-top-0 p-3 mb-3 bg-white rounded-bottom">
    <?php foreach (['en', 'vi'] as $loc): ?>
    <div class="tab-pane fade<?= $loc === 'en' ? ' show active' : '' ?>" id="tab-<?= $loc ?>" role="tabpanel">
        <?php $t = $translations[$loc] ?? []; ?>
        <?php if (in_array('title', $fields, true)): ?>
        <div class="form-group mb-3">
            <label>Title (<?= $labels[$loc] ?>) <?= $loc === 'en' ? '*' : '' ?></label>
            <input type="text" name="translations[<?= $loc ?>][title]" value="<?= e((string) ($t['title'] ?? '')) ?>" class="form-control"<?= $loc === 'en' ? ' required' : '' ?>>
        </div>
        <?php endif; ?>
        <?php if (in_array('name', $fields, true)): ?>
        <div class="form-group mb-3">
            <label>Name (<?= $labels[$loc] ?>) <?= $loc === 'en' ? '*' : '' ?></label>
            <input type="text" name="translations[<?= $loc ?>][name]" value="<?= e((string) ($t['name'] ?? '')) ?>" class="form-control"<?= $loc === 'en' ? ' required' : '' ?>>
        </div>
        <?php endif; ?>
        <?php if (in_array('summary', $fields, true)): ?>
        <div class="form-group mb-3">
            <label>Summary (<?= $labels[$loc] ?>)</label>
            <textarea name="translations[<?= $loc ?>][summary]" rows="2" class="form-control"><?= e((string) ($t['summary'] ?? '')) ?></textarea>
        </div>
        <?php endif; ?>
        <?php if (in_array('subtitle', $fields, true)): ?>
        <div class="form-group mb-3">
            <label>Subtitle (<?= $labels[$loc] ?>)</label>
            <input type="text" name="translations[<?= $loc ?>][subtitle]" value="<?= e((string) ($t['subtitle'] ?? '')) ?>" class="form-control">
        </div>
        <?php endif; ?>
        <?php if (in_array('button_text', $fields, true)): ?>
        <div class="form-group mb-3">
            <label>Button text (<?= $labels[$loc] ?>)</label>
            <input type="text" name="translations[<?= $loc ?>][button_text]" value="<?= e((string) ($t['button_text'] ?? '')) ?>" class="form-control">
        </div>
        <?php endif; ?>
        <?php if (in_array('content', $fields, true) || in_array('description', $fields, true)): ?>
        <div class="form-group mb-3">
            <label><?= in_array('description', $fields, true) ? 'Description' : 'Content' ?> (<?= $labels[$loc] ?>)</label>
            <textarea name="translations[<?= $loc ?>][<?= in_array('description', $fields, true) ? 'description' : 'content' ?>]" class="form-control rich-editor" rows="8"><?= e((string) ($t[in_array('description', $fields, true) ? 'description' : 'content'] ?? '')) ?></textarea>
        </div>
        <?php endif; ?>
        <?php if (in_array('seo_title', $fields, true)): ?>
        <div class="form-group mb-3">
            <label>SEO Title (<?= $labels[$loc] ?>)</label>
            <input type="text" name="translations[<?= $loc ?>][seo_title]" value="<?= e((string) ($t['seo_title'] ?? '')) ?>" class="form-control">
        </div>
        <?php endif; ?>
        <?php if (in_array('seo_description', $fields, true)): ?>
        <div class="form-group mb-0">
            <label>SEO Description (<?= $labels[$loc] ?>)</label>
            <textarea name="translations[<?= $loc ?>][seo_description]" rows="2" class="form-control"><?= e((string) ($t['seo_description'] ?? '')) ?></textarea>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
