<?php ob_start(); $s = $settings; ?>
<h1>Settings</h1>
<form method="post" action="<?= url('/admin/settings') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <h2>General</h2>
    <div class="form-group"><label>Site name</label><input name="site_name" value="<?= e($s['site_name'] ?? '') ?>"></div>

    <h2>Branding</h2>
    <div class="form-group">
        <label>Logo</label>
        <?php if (!empty($s['logo_path'])): ?><p><img src="<?= e(upload_url($s['logo_path'])) ?>" alt="Logo" class="thumb-preview"></p><?php endif; ?>
        <input type="file" name="logo" accept="image/jpeg,image/png,image/webp">
    </div>
    <div class="form-group">
        <label>Favicon</label>
        <?php if (!empty($s['favicon_path'])): ?><p><img src="<?= e(upload_url($s['favicon_path'])) ?>" alt="Favicon" class="favicon-preview"></p><?php endif; ?>
        <input type="file" name="favicon" accept="image/jpeg,image/png,image/webp">
    </div>

    <h2>Contact</h2>
    <div class="form-group"><label>Email</label><input type="email" name="contact_email" value="<?= e($s['contact_email'] ?? '') ?>"></div>
    <div class="form-group"><label>Phone</label><input name="contact_phone" value="<?= e($s['contact_phone'] ?? '') ?>"></div>
    <div class="form-group"><label>Address</label><textarea name="address" rows="3"><?= e($s['address'] ?? '') ?></textarea></div>

    <h2>Social</h2>
    <div class="form-group"><label>Facebook URL</label><input name="facebook_url" value="<?= e($s['facebook_url'] ?? '') ?>"></div>
    <div class="form-group"><label>Instagram URL</label><input name="instagram_url" value="<?= e($s['instagram_url'] ?? '') ?>"></div>
    <div class="form-group"><label>Twitter / X URL</label><input name="twitter_url" value="<?= e($s['twitter_url'] ?? '') ?>"></div>

    <h2>SEO defaults</h2>
    <div class="form-group"><label>Default title</label><input name="seo_default_title" value="<?= e($s['seo_default_title'] ?? '') ?>"></div>
    <div class="form-group"><label>Default description</label><textarea name="seo_default_description" rows="2"><?= e($s['seo_default_description'] ?? '') ?></textarea></div>

    <button type="submit" class="btn btn-primary">Save Settings</button>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
