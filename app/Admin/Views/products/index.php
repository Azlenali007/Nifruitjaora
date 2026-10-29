<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Coconuts &amp; Product Management</h1>
            <p class="text-xs text-slate-500 mt-1">Manage pricing, variants, stock, and carousel visibility.</p>
        </div>
        <a href="/admin/products/create" class="btn-primary text-xs py-2.5 px-5">
            + Add New Coconut
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <form action="/admin/products" method="GET" class="flex flex-wrap items-center gap-3 w-full">
            <div class="relative flex-grow max-w-xs">
                <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search product or SKU..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"><use href="/assets/icons/sprite.svg#icon-search"></use></svg>
            </div>

            <select name="status" onchange="this.form.submit()" 
                    class="text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none">
                <option value="">All Statuses</option>
                <option value="1" <?= $status === 1 ? 'selected' : '' ?>>Active Only</option>
                <option value="0" <?= $status === 0 ? 'selected' : '' ?>>Inactive Only</option>
            </select>

            <button type="submit" class="btn-secondary text-xs py-2 px-4">Filter</button>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-6 font-bold">Product</th>
                        <th class="py-3 px-4 font-bold">Category</th>
                        <th class="py-3 px-4 font-bold">Price</th>
                        <th class="py-3 px-4 font-bold">Stock</th>
                        <th class="py-3 px-4 font-bold">Carousel</th>
                        <th class="py-3 px-4 font-bold">Status</th>
                        <th class="py-3 px-6 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <?php foreach ($products as $p): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-6 flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 p-1 flex items-center justify-center flex-shrink-0">
                                <img src="<?= e($p['image_url']) ?>" alt="" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <strong class="font-bold text-slate-900 block"><?= e($p['name']) ?></strong>
                                <span class="text-[10px] text-slate-400 font-mono"><?= e($p['slug']) ?></span>
                            </div>
                        </td>

                        <td class="py-3.5 px-4 text-slate-600 font-bold"><?= e($p['category_name']) ?></td>

                        <td class="py-3.5 px-4 font-black text-slate-900">
                            <?= format_price($p['price']) ?>
                            <?php if (!empty($p['sale_price'])): ?>
                                <span class="text-[10px] text-slate-400 line-through block"><?= format_price($p['sale_price']) ?></span>
                            <?php endif; ?>
                        </td>

                        <td class="py-3.5 px-4">
                            <?php if ($p['stock_quantity'] > 10): ?>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-700">In Stock (<?= $p['stock_quantity'] ?>)</span>
                            <?php elseif ($p['stock_quantity'] > 0): ?>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700">Low (<?= $p['stock_quantity'] ?>)</span>
                            <?php else: ?>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700">Sold Out</span>
                            <?php endif; ?>
                        </td>

                        <td class="py-3.5 px-4">
                            <?php if ($p['is_carousel']): ?>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-600 text-white">#<?= $p['carousel_order'] ?> Carousel</span>
                            <?php else: ?>
                                <span class="text-slate-400 text-[11px]">&minus;</span>
                            <?php endif; ?>
                        </td>

                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                <?= $p['is_active'] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' ?>">
                                <?= $p['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>

                        <td class="py-3.5 px-6 text-right space-x-2">
                            <a href="/admin/products/<?= $p['id'] ?>/edit" class="text-brand-600 hover:text-brand-800 font-bold">Edit</a>
                            <form action="/admin/products/<?= $p['id'] ?>/delete" method="POST" class="inline confirm-delete" data-message="Are you sure you want to delete '<?= e($p['name']) ?>'?">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
