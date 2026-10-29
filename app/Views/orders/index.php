<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Order History</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Review all your past and active coconut orders.</p>
        </div>
        <a href="/products" class="btn-primary text-xs py-2.5 px-5">Order Again</a>
    </div>

    <?php if (empty($orders)): ?>
    <div class="bg-white rounded-3xl p-12 text-center border border-slate-200">
        <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8"><use href="/assets/icons/sprite.svg#icon-bag"></use></svg>
        </div>
        <h3 class="text-base font-bold text-slate-900">No Orders Yet</h3>
        <p class="text-xs text-slate-500 mt-1 mb-6">You have not placed any coconut orders with this account.</p>
        <a href="/products" class="btn-primary text-xs py-2.5 px-6">Explore Catalogue</a>
    </div>
    <?php else: ?>
    <div class="space-y-4">
        <?php foreach ($orders as $o): ?>
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
                <div>
                    <span class="text-xs font-mono font-black text-brand-700">Order #<?= e($o['order_number']) ?></span>
                    <span class="text-xs text-slate-400 ml-2 block sm:inline"><?= date('d M Y, h:i A', strtotime($o['created_at'])) ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase 
                        <?= $o['payment_status'] === 'paid' ? 'bg-brand-100 text-brand-800' : 'bg-amber-100 text-amber-800' ?>">
                        <?= e($o['payment_status']) ?>
                    </span>
                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-slate-100 text-slate-700">
                        <?= e(str_replace('_', ' ', $o['fulfillment_status'])) ?>
                    </span>
                </div>
            </div>

            <!-- Items summary -->
            <div class="py-4 space-y-2 text-xs">
                <?php foreach ($o['items'] as $item): ?>
                <div class="flex items-center justify-between">
                    <span class="text-slate-700"><?= $item['quantity'] ?> &times; <?= e($item['product_name']) ?> <?= $item['variant_name'] ? '(' . e($item['variant_name']) . ')' : '' ?></span>
                    <span class="font-bold text-slate-900"><?= format_price($item['total_price']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400 font-semibold">Total Paid</span>
                    <span class="text-base font-black text-slate-900 block"><?= format_price($o['total_amount']) ?></span>
                </div>
                <a href="/orders/<?= e($o['order_number']) ?>" class="btn-secondary text-xs py-2 px-5">
                    Track &amp; View Details
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
