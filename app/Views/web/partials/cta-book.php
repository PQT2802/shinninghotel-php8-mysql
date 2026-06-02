<section class="cta-strip">
    <div class="container cta-strip-inner">
        <div class="cta-strip-text">
            <h2><?= e($ctaTitle ?? __('cta.title')) ?></h2>
            <p><?= e($ctaSubtitle ?? __('cta.subtitle', ['brand' => brand_name()])) ?></p>
        </div>
        <a href="<?= url('/book') ?>" class="btn btn-primary btn-lg btn-shine reveal"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i> <?= e($ctaButton ?? __('cta.button')) ?></a>
    </div>
</section>
