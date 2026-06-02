<?php
$current = $step ?? 1;
$steps = $wizardSteps ?? [];
$stepIcons = [1 => 'fa-calendar-days', 2 => 'fa-bed', 3 => 'fa-user', 4 => 'fa-check'];
?>
<nav class="wizard-steps reveal" aria-label="Booking progress">
    <?php foreach ($steps as $num => $info): ?>
        <?php
        $class = 'wizard-step';
        if ($num < $current) {
            $class .= ' done';
        } elseif ($num === $current) {
            $class .= ' active';
        }
        $canLink = $num < $current;
        $icon = $stepIcons[$num] ?? 'fa-circle';
        ?>
        <?php if ($canLink): ?>
            <a href="<?= e($info['url']) ?>" class="<?= e($class) ?>">
                <span class="wizard-num" data-icon="<?= e($icon) ?>"><?= $num ?></span>
                <span class="wizard-label"><i class="fa-solid <?= e($icon) ?>" aria-hidden="true"></i> <?= e($info['label']) ?></span>
            </a>
        <?php else: ?>
            <span class="<?= e($class) ?>">
                <span class="wizard-num" data-icon="<?= e($icon) ?>"><?= $num ?></span>
                <span class="wizard-label"><i class="fa-solid <?= e($icon) ?>" aria-hidden="true"></i> <?= e($info['label']) ?></span>
            </span>
        <?php endif; ?>
        <?php if ($num < count($steps)): ?><span class="wizard-connector"></span><?php endif; ?>
    <?php endforeach; ?>
</nav>
