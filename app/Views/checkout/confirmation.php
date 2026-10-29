<div class="max-w-3xl mx-auto px-4 sm:px-6 py-16">
    <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-xl border border-slate-200 text-center">
        <!-- Success Icon -->
        <div class="w-20 h-20 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-6 shadow-inner">
            <svg class="w-10 h-10"><use href="/assets/icons/sprite.svg#icon-circle-check"></use></svg>
        </div>

        <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600">Order Confirmed</span>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight mt-1">Thank You for Your Order!</h1>
        <p class="text-sm text-slate-500 mt-2">
            Your fresh coconuts are being prepared for delivery.
        </p>

        <!-- Order Reference Badge -->
        <div class="inline-block bg-slate-50 border border-slate-200 rounded-2xl px-6 py-3 my-6">
            <span class="text-xs text-slate-400 block font-semibold">Order Reference Number</span>
            <span class="text-xl font-mono font-black text-brand-700"><?= e($order['order_number']) ?></span>
        </div>

        <!-- Order Details Card -->
        <div class="text-left bg-slate-50 rounded-2xl p-6 border border-slate-100 my-6 space-y-4 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <span class="font-bold text-slate-700">Payment Status</span>
                <span class="px-3 py-1 rounded-full font-bold uppercase text-[10px] 
                    <?= $order['payment_status'] === 'paid' ? 'bg-brand-100 text-brand-800' : 'bg-amber-100 text-amber-800' ?>">
                    <?= e($order['payment_status']) ?>
                </span>
            </div>

            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <span class="font-bold text-slate-700">Delivery Address</span>
                <span class="text-slate-900 font-medium text-right max-w-xs"><?= e($order['delivery_address']) ?> (PIN: <?= e($order['delivery_pincode']) ?>)</span>
            </div>

            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <span class="font-bold text-slate-700">Recipient Contact</span>
                <span class="text-slate-900 font-bold"><?= e($order['guest_name']) ?> &bull; <?= e($order['guest_phone']) ?></span>
            </div>

            <!-- Items -->
            <div class="pt-2">
                <strong class="font-bold text-slate-800 block mb-2">Items Ordered:</strong>
                <div class="space-y-2">
                    <?php foreach ($order['items'] as $item): ?>
                    <div class="flex justify-between items-center text-slate-600">
                        <span><?= $item['quantity'] ?> &times; <?= e($item['product_name']) ?> <?= $item['variant_name'] ? '(' . e($item['variant_name']) . ')' : '' ?></span>
                        <span class="font-bold text-slate-900"><?= format_price($item['total_price']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-200 flex justify-between text-sm font-black text-slate-900">
                <span>Total Amount Paid</span>
                <span class="text-brand-600"><?= format_price($order['total_amount']) ?></span>
            </div>
        </div>

        <!-- Action Links -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
            <a href="/orders/<?= e($order['order_number']) ?>" class="btn-primary text-xs py-3 px-8 w-full sm:w-auto">
                Live Order Tracking
            </a>
            <a href="/" class="btn-secondary text-xs py-3 px-8 w-full sm:w-auto">
                Return to Store
            </a>
        </div>
    </div>
</div>
