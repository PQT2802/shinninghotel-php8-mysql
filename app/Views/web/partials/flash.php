<?php if ($s = \App\Core\Session::flash('success')): ?>
<div class="alert alert-success" role="alert"><?= e($s) ?></div>
<?php endif; ?>
<?php if ($e = \App\Core\Session::flash('error')): ?>
<div class="alert alert-error" role="alert"><?= e($e) ?></div>
<?php endif; ?>
