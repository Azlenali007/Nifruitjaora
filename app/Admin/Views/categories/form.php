<?php
$isEdit = !empty($category);
$action = $isEdit ? "/admin/categories/{$category['id']}/update" : "/admin/categories/store";
?>

<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight"><?= $isEdit ? 'Edit Category' : 'Create Category' ?></h1>
            <p class="text-xs text-slate-500 mt-1">Configure category visibility for store customers.</p>
        </div>
        <a href="/admin/categories" class="btn-secondary text-xs py-2 px-4">Cancel</a>
    </div>

    <form action="<?= $action ?>" method="POST" class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-4 text-xs">
        <?= csrf_field() ?>

        <div>
            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category Name *</label>
            <input type="text" name="name" value="<?= e($category['name'] ?? '') ?>" required placeholder="e.g. Fresh Coconuts"
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Description</label>
            <textarea name="description" rows="3" placeholder="Category brief..."
                      class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:outline-none focus:ring-2 focus:ring-brand-500"><?= e($category['description'] ?? '') ?></textarea>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Display Sort Order</label>
            <input type="number" name="sort_order" value="<?= $category['sort_order'] ?? 0 ?>"
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium">
        </div>

        <div class="pt-2">
            <label class="flex items-center gap-2 cursor-pointer font-bold">
                <input type="checkbox" name="is_active" value="1" <?= (!isset($category['is_active']) || $category['is_active']) ? 'checked' : '' ?> class="rounded text-brand-600 h-4 w-4">
                <span>Category is Active (Visible on Customer Store)</span>
            </label>
            <p class="text-[11px] text-slate-400 mt-1 pl-6">
                Note: Unchecked categories (like future Juices or Kulfi) will remain strictly hidden from customers.
            </p>
        </div>

        <div class="pt-4 border-t border-slate-100 text-right">
            <button type="submit" class="btn-primary text-xs py-3 px-8">Save Category</button>
        </div>
    </form>
</div>
