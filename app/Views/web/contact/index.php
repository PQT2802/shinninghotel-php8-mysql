<?php
$mapQuery = urlencode($settings['address'] ?? '');
?>
<?php ob_start(); ?>
<section class="page-header page-header--image" style="background-image:url('<?= e(upload_url('seed/contact.jpg')) ?>')">
    <div class="container">
        <h1 class="reveal"><?= e(__('contact.title')) ?></h1>
        <p class="reveal"><?= e(__('contact.subtitle')) ?></p>
    </div>
</section>
<section class="section">
    <div class="container">
        <?php require __DIR__ . '/../partials/breadcrumbs.php'; ?>
        <div class="contact-grid">
            <div class="contact-info-card reveal">
                <div class="contact-visual">
                    <img src="<?= e(upload_url('seed/contact.jpg')) ?>" alt="<?= e(brand_name()) ?>" loading="lazy" width="600" height="220">
                </div>
                <h2><?= e(__('contact.get_in_touch')) ?></h2>
                <?php if (!empty($settings['address'])): ?>
                    <p class="contact-line">
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        <strong><?= e(__('contact.address')) ?></strong>
                        <?= e($settings['address']) ?>
                    </p>
                    <?php if ($mapQuery): ?>
                    <p><a href="https://www.google.com/maps/search/?api=1&amp;query=<?= $mapQuery ?>" class="link-arrow" target="_blank" rel="noopener"><i class="fa-solid fa-map" aria-hidden="true"></i> <?= e(__('contact.open_maps')) ?> →</a></p>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (!empty($settings['contact_phone'])): ?>
                    <p class="contact-line">
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                        <strong><?= e(__('contact.phone')) ?></strong>
                        <a href="tel:<?= e(preg_replace('/\s+/', '', $settings['contact_phone'])) ?>"><?= e($settings['contact_phone']) ?></a>
                    </p>
                <?php endif; ?>
                <?php if (!empty($settings['contact_email'])): ?>
                    <p class="contact-line">
                        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                        <strong><?= e(__('contact.email')) ?></strong>
                        <a href="mailto:<?= e($settings['contact_email']) ?>"><?= e($settings['contact_email']) ?></a>
                    </p>
                <?php endif; ?>
                <p class="contact-hours muted"><i class="fa-regular fa-clock" aria-hidden="true"></i> <?= e(__('contact.hours')) ?></p>
            </div>
            <form method="post" action="<?= url('/contact') ?>" class="contact-form wizard-panel reveal">
                <?= csrf_field() ?>
                <?php require __DIR__ . '/../partials/flash.php'; ?>
                <?php $old = $old ?? []; ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="contact-name"><?= e(__('contact.name')) ?> *</label>
                        <input type="text" id="contact-name" name="name" class="form-control" value="<?= e($old['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="contact-email"><?= e(__('contact.email')) ?> *</label>
                        <input type="email" id="contact-email" name="email" class="form-control" value="<?= e($old['email'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="contact-phone"><?= e(__('contact.phone')) ?></label>
                    <input type="tel" id="contact-phone" name="phone" class="form-control" value="<?= e($old['phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="contact-subject"><?= e(__('contact.subject')) ?></label>
                    <input type="text" id="contact-subject" name="subject" class="form-control" value="<?= e($old['subject'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="contact-message"><?= e(__('contact.message')) ?> *</label>
                    <textarea id="contact-message" name="message" class="form-control" rows="5" required><?= e($old['message'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-shine"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i> <?= e(__('contact.send')) ?></button>
            </form>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
