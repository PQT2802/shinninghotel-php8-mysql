<?php ob_start(); ?>
<section class="page-header page-header--image" style="background-image:url('<?= e(upload_url('seed/hero.jpg')) ?>')">
    <div class="container">
        <h1 class="reveal"><?= e(__('book.title')) ?></h1>
        <p class="reveal"><?= e(brand_slogan()) ?></p>
    </div>
</section>
<section class="section">
    <div class="container booking-wizard reveal">
        <?php require __DIR__ . '/../partials/flash.php'; ?>
        <?php require __DIR__ . '/_wizard_steps.php'; ?>

        <form method="post" action="<?= url('/book/dates') ?>" class="booking-form wizard-panel">
            <?= csrf_field() ?>
            <h2 class="h4 mb-4"><?= e(__('home.check_in')) ?> / <?= e(__('home.check_out')) ?></h2>
            <div class="row g-3 booking-dates-row">
                <div class="col-md-6 booking-field date-input-wrap">
                    <label class="form-label"><?= e(__('home.check_in')) ?> *</label>
                    <input type="text" class="form-control" data-datepicker data-booking-check-in data-date-name="check_in" value="<?= e($checkIn) ?>" required placeholder="<?= e(__('home.date_placeholder_check_in')) ?>">
                </div>
                <div class="col-md-6 booking-field date-input-wrap">
                    <label class="form-label"><?= e(__('home.check_out')) ?> *</label>
                    <input type="text" class="form-control" data-datepicker data-booking-check-out data-date-name="check_out" value="<?= e($checkOut) ?>" required placeholder="<?= e(__('home.date_placeholder_check_out')) ?>">
                </div>
                <div class="col-md-4 booking-field">
                    <label class="form-label"><?= e(__('home.guests')) ?></label>
                    <input type="number" name="guests_count" class="form-control" value="<?= (int) $guestsCount ?>" min="1" max="10">
                </div>
                <div class="col-md-8 booking-field">
                    <label class="form-label"><?= e(__('rooms.category')) ?></label>
                    <select name="category_id" class="form-select">
                        <option value=""><?= e(__('rooms.all')) ?></option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int) $cat['id'] ?>" <?= $categoryId === (int)$cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-4"><?= e(__('rooms.check_availability')) ?> →</button>
        </form>
    </div>
</section>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
