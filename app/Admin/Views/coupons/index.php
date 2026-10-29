<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Coupons &amp; Discounts</h1>
            <p class="text-xs text-slate-500 mt-1">Configure percentage or flat discounts, usage limits, and validity.</p>
        </div>
        <a href="/admin/coupons/create" class="btn-primary text-xs py-2.5 px-5">
            + Create Coupon
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider border-b border-slate-200">
                    <th class="py-3 px-6 font-bold">Code</th>
                    <th class="py-3 px-4 font-bold">Discount Value</th>
                    <th class="py-3 px-4 font-bold">Min. Order</th>
                    <th class="py-3 px-4 font-bold">Max. Discount</th>
                    <th class="py-3 px-4 font-bold">Redemptions</th>
                    <th class="py-3 px-4 font-bold">Status</th>
                    <th class="py-3 px-6 font-bold text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                <?php foreach ($coupons as $c): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3.5 px-6 font-mono font-black text-brand-700 text-sm"><?= e($c['code']) ?></td>
                    <td class="py-3.5 px-4 font-bold text-slate-900">
                        <?= $c['discount_type'] === 'percentage' ? (float)$c['discount_value'] . '%' : format_price($c['discount_value']) ?>
                    </td>
                    <td class="py-3.5 px-4 text-slate-600"><?= format_price($c['min_order_amount']) ?></td>
                    <td class="py-3.5 px-4 text-slate-600"><?= $c['max_discount_amount'] ? format_price($c['max_discount_amount']) : '&minus;' ?></td>
                    <td class="py-3.5 px-4 text-slate-600"><?= $c['used_count'] ?> / <?= $c['usage_limit'] ?? '&infin;' ?></td>
                    <td class="py-3.5 px-4">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase <?= $c['is_active'] ? 'bg-brand-100 text-brand-800' : 'bg-slate-100 text-slate-500' ?>">
                            <?= $c['is_active'] ? 'Active' : 'Disabled' ?>
                        </span>
                    </td>
                    <td class="py-3.5 px-6 text-right space-x-2">
                        <a href="/admin/coupons/<?= $c['id'] ?>/edit" class="text-brand-600 hover:text-brand-800 font-bold">Edit</a>
                        <form action="/admin/coupons/<?= $c['id'] ?>/delete" method="POST" class="inline confirm-delete" data-message="Delete coupon '<?= e($c['code']) ?>'?">
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
