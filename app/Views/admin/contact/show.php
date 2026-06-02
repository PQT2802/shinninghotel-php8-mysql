<?php ob_start(); $m = $message; ?>
<div class="page-actions">
    <h1>Message #<?= (int) $m['id'] ?></h1>
    <div>
        <?php if ($m['status'] === 'read'): ?>
        <form method="post" action="<?= url('/admin/contact-messages/unread/' . $m['id']) ?>" class="inline"><?= csrf_field() ?><button class="btn btn-outline">Mark unread</button></form>
        <?php endif; ?>
        <form method="post" action="<?= url('/admin/contact-messages/delete/' . $m['id']) ?>" class="inline" onsubmit="return confirm('Delete message?')"><?= csrf_field() ?><button class="btn-link danger">Delete</button></form>
    </div>
</div>
<div class="message-detail">
    <p><strong>From:</strong> <?= e($m['name']) ?> &lt;<?= e($m['email']) ?>&gt;</p>
    <?php if ($m['phone']): ?><p><strong>Phone:</strong> <?= e($m['phone']) ?></p><?php endif; ?>
    <p><strong>Subject:</strong> <?= e($m['subject'] ?? '—') ?></p>
    <p><strong>Received:</strong> <?= e($m['created_at']) ?></p>
    <p><strong>Status:</strong> <?= e($m['status']) ?></p>
    <hr>
    <div class="message-body"><?= nl2br(e($m['message'])) ?></div>
</div>
<p><a href="<?= url('/admin/contact-messages') ?>">← Back to inbox</a></p>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
