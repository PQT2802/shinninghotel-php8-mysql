<?php if ($err = validation_error('title') ?? validation_error('email') ?? validation_error('slug')): ?>
<p class="field-error"><?= e($err) ?></p>
<?php endif; ?>
