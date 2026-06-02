<?php if (!empty($breadcrumbs) && is_array($breadcrumbs)): ?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
    <ol>
        <li><a href="<?= url('/') ?>"><?= e(__('breadcrumb.home')) ?></a></li>
        <?php foreach ($breadcrumbs as $i => $crumb): ?>
            <?php if ($i === array_key_last($breadcrumbs)): ?>
                <li aria-current="page"><?= e($crumb['label']) ?></li>
            <?php else: ?>
                <li><a href="<?= e($crumb['url']) ?>"><?= e($crumb['label']) ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>
