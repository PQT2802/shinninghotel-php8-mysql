<?php ob_start(); ?>
<h1>Edit: <?= e($menu['name']) ?> <small>(<?= e($menu['location']) ?>)</small></h1>
<p class="hint">Link to a custom URL or pick a published page. Drag order via sort numbers.</p>

<form method="post" action="<?= url('/admin/menus/update/' . $menu['id']) ?>" id="menu-form">
    <?= csrf_field() ?>
    <table class="admin-table" id="menu-items-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>URL</th>
                <th>Page</th>
                <th>Target</th>
                <th>Order</th>
                <th>Active</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php
        $rows = $items;
        if ($rows === []) {
            $rows = [['id' => '', 'title' => '', 'url' => '', 'page_id' => '', 'target' => '_self', 'sort_order' => 0, 'is_active' => 1]];
        }
        foreach ($rows as $i => $item):
        ?>
            <tr class="menu-item-row">
                <td>
                    <input type="hidden" name="items[<?= $i ?>][id]" value="<?= (int) ($item['id'] ?? 0) ?>">
                    <input type="text" name="items[<?= $i ?>][title]" value="<?= e($item['title'] ?? '') ?>" required>
                </td>
                <td><input type="text" name="items[<?= $i ?>][url]" value="<?= e($item['url'] ?? '') ?>" placeholder="/rooms"></td>
                <td>
                    <select name="items[<?= $i ?>][page_id]">
                        <option value="">— Custom URL —</option>
                        <?php foreach ($pages as $pg): ?>
                        <option value="<?= (int) $pg['id'] ?>" <?= (int)($item['page_id'] ?? 0) === (int)$pg['id'] ? 'selected' : '' ?>><?= e($pg['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <select name="items[<?= $i ?>][target]">
                        <option value="_self" <?= ($item['target'] ?? '') === '_self' ? 'selected' : '' ?>>Same tab</option>
                        <option value="_blank" <?= ($item['target'] ?? '') === '_blank' ? 'selected' : '' ?>>New tab</option>
                    </select>
                </td>
                <td><input type="number" name="items[<?= $i ?>][sort_order]" value="<?= (int) ($item['sort_order'] ?? $i) ?>" class="input-sm"></td>
                <td><input type="checkbox" name="items[<?= $i ?>][is_active]" value="1" <?= !empty($item['is_active']) ? 'checked' : '' ?>></td>
                <td><button type="button" class="btn-link danger remove-row">Remove</button></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p>
        <button type="button" class="btn btn-outline" id="add-menu-row">+ Add item</button>
        <button type="submit" class="btn btn-primary">Save Menu</button>
        <a href="<?= url('/admin/menus') ?>">Back</a>
    </p>
</form>

<template id="menu-row-template">
<tr class="menu-item-row">
    <td><input type="hidden" name="items[__INDEX__][id]" value="0"><input type="text" name="items[__INDEX__][title]" required></td>
    <td><input type="text" name="items[__INDEX__][url]" placeholder="/path"></td>
    <td><select name="items[__INDEX__][page_id]"><option value="">— Custom URL —</option><?php foreach ($pages as $pg): ?><option value="<?= (int)$pg['id'] ?>"><?= e($pg['title']) ?></option><?php endforeach; ?></select></td>
    <td><select name="items[__INDEX__][target]"><option value="_self">Same tab</option><option value="_blank">New tab</option></select></td>
    <td><input type="number" name="items[__INDEX__][sort_order]" value="0" class="input-sm"></td>
    <td><input type="checkbox" name="items[__INDEX__][is_active]" value="1" checked></td>
    <td><button type="button" class="btn-link danger remove-row">Remove</button></td>
</tr>
</template>

<script>
(function() {
    let idx = document.querySelectorAll('.menu-item-row').length;
    document.getElementById('add-menu-row')?.addEventListener('click', function() {
        const tpl = document.getElementById('menu-row-template').innerHTML.replace(/__INDEX__/g, idx++);
        document.querySelector('#menu-items-table tbody').insertAdjacentHTML('beforeend', tpl);
    });
    document.getElementById('menu-form')?.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr')?.remove();
        }
    });
    document.querySelectorAll('select[name*="[page_id]"]').forEach(function(sel) {
        sel.addEventListener('change', function() {
            if (this.value) {
                const slug = this.options[this.selectedIndex].text;
                const row = this.closest('tr');
                const urlInput = row.querySelector('input[name*="[url]"]');
                if (urlInput && !urlInput.value) {
                    urlInput.placeholder = 'Or leave empty to use /page/{slug}';
                }
            }
        });
    });
})();
</script>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
