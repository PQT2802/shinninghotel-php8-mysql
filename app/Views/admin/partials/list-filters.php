<?php
/** @var string $action */
/** @var string|null $search */
/** @var string|null $statusFilter */
?>
<form method="get" action="<?= e($action) ?>" class="list-filters">
    <input type="search" name="q" value="<?= e($search ?? '') ?>" placeholder="Search…">
    <?php if (isset($statusFilter)): ?>
    <select name="status">
        <option value="">All statuses</option>
        <option value="published" <?= ($statusFilter ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
        <option value="draft" <?= ($statusFilter ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
    </select>
    <?php endif; ?>
    <?php if (isset($statusOptions) && $statusOptions === 'contact'): ?>
    <select name="status">
        <option value="">All</option>
        <option value="unread" <?= ($statusFilter ?? '') === 'unread' ? 'selected' : '' ?>>Unread</option>
        <option value="read" <?= ($statusFilter ?? '') === 'read' ? 'selected' : '' ?>>Read</option>
    </select>
    <?php endif; ?>
    <button type="submit" class="btn btn-primary">Filter</button>
</form>
