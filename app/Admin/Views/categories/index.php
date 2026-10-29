<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Category Hierarchy</h1>
            <p class="text-xs text-slate-500 mt-1">Manage current coconut categories and future categories (e.g. Juices &amp; Kulfi).</p>
        </div>
        <a href="/admin/categories/create" class="btn-primary text-xs py-2.5 px-5">
            + New Category
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider border-b border-slate-200">
                    <th class="py-3 px-6 font-bold">Category Name</th>
                    <th class="py-3 px-4 font-bold">Slug</th>
                    <th class="py-3 px-4 font-bold">Products Count</th>
                    <th class="py-3 px-4 font-bold">Store Visibility</th>
                    <th class="py-3 px-6 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                <?php foreach ($categories as $c): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3.5 px-6 font-bold text-slate-900"><?= e($c['name']) ?></td>
                    <td class="py-3.5 px-4 font-mono text-slate-400"><?= e($c['slug']) ?></td>
                    <td class="py-3.5 px-4 font-bold text-brand-700"><?= $c['product_count'] ?> items</td>
                    <td class="py-3.5 px-4">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                            <?= $c['is_active'] ? 'bg-brand-100 text-brand-800' : 'bg-amber-100 text-amber-800' ?>">
                            <?= $c['is_active'] ? 'Live in Store' : 'Inactive (Hidden)' ?>
                        </span>
                    </td>
                    <td class="py-3.5 px-6 text-right space-x-2">
                        <a href="/admin/categories/<?= $c['id'] ?>/edit" class="text-brand-600 hover:text-brand-800 font-bold">Edit</a>
                        <?php if ($c['product_count'] == 0): ?>
                        <form action="/admin/categories/<?= $c['id'] ?>/delete" method="POST" class="inline confirm-delete" data-message="Delete category '<?= e($c['name']) ?>'?">
                            <?= csrf_field() ?>
                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold">Delete</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
