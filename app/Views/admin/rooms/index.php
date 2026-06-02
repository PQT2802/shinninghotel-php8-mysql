<?php ob_start(); ?>
<div class="page-actions">
    <h1>Rooms</h1>
    <div>
        <a href="<?= url('/admin/room-categories') ?>" class="btn btn-outline">Categories</a>
        <a href="<?= url('/admin/rooms/create') ?>" class="btn btn-primary">Create Room</a>
    </div>
</div>

<form method="get" action="<?= url('/admin/rooms') ?>" class="list-filters">
    <input type="search" name="q" value="<?= e($search ?? '') ?>" placeholder="Search rooms…">
    <select name="status">
        <option value="">All statuses</option>
        <option value="published" <?= ($statusFilter ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
        <option value="draft" <?= ($statusFilter ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
    </select>
    <select name="category">
        <option value="">All categories</option>
        <?php foreach ($categories as $c): ?>
        <option value="<?= (int) $c['id'] ?>" <?= ($categoryFilter ?? 0) === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary">Filter</button>
</form>

<table class="admin-table">
<thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Guests</th><th>Status</th><th></th></tr></thead>
<tbody>
<?php foreach ($rooms as $r): ?>
<tr>
<td><?php if ($r['image_path']): ?><img src="<?= e(upload_url($r['image_path'])) ?>" class="table-thumb" alt=""><?php endif; ?></td>
<td>
    <?= e($r['name']) ?>
    <?php if ($r['is_featured']): ?><span class="badge">Featured</span><?php endif; ?>
</td>
<td><?= e($r['category_name'] ?? '—') ?></td>
<td>$<?= number_format((float) $r['price_per_night'], 2) ?></td>
<td><?= (int) $r['max_guests'] ?></td>
<td><span class="badge badge-<?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
<td class="actions">
<a href="<?= url('/rooms/' . $r['slug']) ?>" target="_blank">View</a>
<a href="<?= url('/admin/rooms/edit/' . $r['id']) ?>">Edit</a>
<form method="post" action="<?= url('/admin/rooms/toggle-status/' . $r['id']) ?>" class="inline"><?= csrf_field() ?>
<button type="submit" class="btn-link"><?= $r['status'] === 'published' ? 'Unpublish' : 'Publish' ?></button>
</form>
<form method="post" action="<?= url('/admin/rooms/delete/' . $r['id']) ?>" class="inline" onsubmit="return confirm('Delete this room?')"><?= csrf_field() ?>
<button type="submit" class="btn-link danger">Delete</button>
</form>
</td>
</tr>
<?php endforeach; ?>
<?php if (empty($rooms)): ?><tr><td colspan="7">No rooms found.</td></tr><?php endif; ?>
</tbody>
</table>
<?= pagination_links($pager, url('/admin/rooms') . '?' . http_build_query(array_filter(['q' => $search ?? '', 'status' => $statusFilter ?? '', 'category' => $categoryFilter ?? '']))) ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
