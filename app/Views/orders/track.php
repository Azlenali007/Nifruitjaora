<div class="max-w-xl mx-auto px-4 sm:px-6 py-12">
    <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-lg border border-slate-200">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8"><use href="/assets/icons/sprite.svg#icon-search"></use></svg>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Track Your Coconut Order</h1>
            <p class="text-xs text-slate-500 mt-1">Enter your Order Reference number and phone number below.</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="mb-6 p-4 rounded-2xl bg-red-50 text-red-700 text-xs font-bold border border-red-200 text-center">
            <?= e($error) ?>
        </div>
        <?php endif; ?>

        <form action="/track" method="GET" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Order Number</label>
                <input type="text" name="order_number" value="<?= e($searchOrderNumber ?? '') ?>" required placeholder="e.g. NA-261029-A1B2C3"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 uppercase">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mobile Number</label>
                <input type="tel" name="phone" value="<?= e($searchPhone ?? '') ?>" placeholder="10-digit phone used at checkout"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <button type="submit" 
                    class="w-full py-3.5 px-6 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-sm shadow-lg shadow-brand-600/30 transition-transform active:scale-[0.98]">
                Find Order
            </button>
        </form>

        <?php if (!empty($order)): ?>
        <div class="mt-8 pt-8 border-t border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 mb-3">Order Status</h3>
            <div class="p-4 bg-brand-50 border border-brand-200 rounded-2xl flex items-center justify-between">
                <div>
                    <span class="text-xs font-mono font-bold text-brand-900 block"><?= e($order['order_number']) ?></span>
                    <span class="text-[11px] text-brand-700">Placed on <?= date('d M Y', strtotime($order['created_at'])) ?></span>
                </div>
                <a href="/orders/<?= e($order['order_number']) ?>" class="btn-primary text-xs py-2 px-4">
                    View Live Tracker
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
