<?php
$steps = ['pending' => 1, 'confirmed' => 2, 'preparing' => 3, 'out_for_delivery' => 4, 'delivered' => 5];
$currentStep = $steps[$order['fulfillment_status']] ?? 1;
if ($order['fulfillment_status'] === 'cancelled') {
    $currentStep = -1;
}
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Back to Orders -->
    <a href="/my-orders" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-brand-600 mb-6">
        <svg class="w-4 h-4"><use href="/assets/icons/sprite.svg#icon-chevron-left"></use></svg>
        <span>Back to Orders</span>
    </a>

    <!-- Order Header Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Order Reference</span>
                <h1 class="text-2xl font-mono font-black text-slate-900 mt-1"><?= e($order['order_number']) ?></h1>
                <span class="text-xs text-slate-500 mt-1 block">Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider
                    <?= $order['payment_status'] === 'paid' ? 'bg-brand-100 text-brand-800' : 'bg-amber-100 text-amber-800' ?>">
                    Payment: <?= e($order['payment_status']) ?>
                </span>
            </div>
        </div>

        <!-- Order Fulfillment Progress Tracker -->
        <div class="py-8">
            <h3 class="text-xs font-extrabold uppercase tracking-widest text-slate-400 mb-6">Delivery Progress</h3>
            
            <?php if ($order['fulfillment_status'] === 'cancelled'): ?>
                <div class="bg-red-50 text-red-700 p-4 rounded-2xl text-xs font-bold border border-red-200 text-center">
                    This order has been cancelled.
                </div>
            <?php else: ?>
                <div class="relative flex items-center justify-between max-w-2xl mx-auto">
                    <!-- Progress line -->
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-slate-100 w-full z-0"></div>
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-brand-500 z-0 transition-all duration-500"
                         style="width: <?= min(100, (($currentStep - 1) / 4) * 100) ?>%;"></div>

                    <!-- Step 1: Confirmed -->
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs 
                            <?= $currentStep >= 2 ? 'bg-brand-600 text-white' : 'bg-slate-200 text-slate-500' ?>">
                            ✓
                        </div>
                        <span class="text-[11px] font-bold text-slate-700 mt-2">Confirmed</span>
                    </div>

                    <!-- Step 2: Preparing -->
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs 
                            <?= $currentStep >= 3 ? 'bg-brand-600 text-white' : 'bg-slate-200 text-slate-500' ?>">
                            ✓
                        </div>
                        <span class="text-[11px] font-bold text-slate-700 mt-2">Preparing</span>
                    </div>

                    <!-- Step 3: Out for Delivery -->
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs 
                            <?= $currentStep >= 4 ? 'bg-brand-600 text-white' : 'bg-slate-200 text-slate-500' ?>">
                            ✓
                        </div>
                        <span class="text-[11px] font-bold text-slate-700 mt-2">On the Way</span>
                    </div>

                    <!-- Step 4: Delivered -->
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs 
                            <?= $currentStep >= 5 ? 'bg-brand-600 text-white' : 'bg-slate-200 text-slate-500' ?>">
                            ✓
                        </div>
                        <span class="text-[11px] font-bold text-slate-700 mt-2">Delivered</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Delivery & Items Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-100 text-xs">
            <div>
                <strong class="text-xs font-bold text-slate-900 uppercase tracking-wider block mb-2">Delivery Address</strong>
                <p class="text-slate-600 leading-relaxed"><?= nl2br(e($order['delivery_address'])) ?></p>
                <p class="text-slate-800 font-bold mt-1">PIN: <?= e($order['delivery_pincode']) ?></p>
                <p class="text-slate-600 mt-1">Contact: <?= e($order['guest_name']) ?> (<?= e($order['guest_phone']) ?>)</p>
            </div>

            <div>
                <strong class="text-xs font-bold text-slate-900 uppercase tracking-wider block mb-2">Payment Breakdown</strong>
                <div class="space-y-1.5 text-slate-600">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-bold text-slate-900"><?= format_price($order['subtotal']) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Delivery</span>
                        <span class="font-bold text-slate-900"><?= format_price($order['delivery_charge']) ?></span>
                    </div>
                    <?php if ($order['discount_amount'] > 0): ?>
                    <div class="flex justify-between text-brand-600 font-bold">
                        <span>Discount</span>
                        <span>-<?= format_price($order['discount_amount']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="pt-2 border-t border-slate-200 flex justify-between text-sm font-black text-slate-900">
                        <span>Total Paid</span>
                        <span class="text-brand-600"><?= format_price($order['total_amount']) ?></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
