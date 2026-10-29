<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manage Order: <?= e($order['order_number']) ?></h1>
            <p class="text-xs text-slate-500 mt-1">Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></p>
        </div>
        <a href="/admin/orders" class="btn-secondary text-xs py-2 px-4">&larr; Back to Orders</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Col 1 & 2: Order Summary & Ordered Items -->
        <div class="md:col-span-2 space-y-6">
            <!-- Items Card -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100 mb-4">Ordered Coconut Items</h3>
                <div class="divide-y divide-slate-100 text-xs">
                    <?php foreach ($order['items'] as $item): ?>
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-50 p-1 flex-shrink-0 flex items-center justify-center">
                                <img src="<?= e($item['product_image'] ?? '/assets/images/nariyal-pani.webp') ?>" alt="" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <strong class="font-bold text-slate-900 block"><?= e($item['product_name']) ?></strong>
                                <?php if ($item['variant_name']): ?>
                                <span class="text-[11px] text-slate-400"><?= e($item['variant_name']) ?></span>
                                <?php endif; ?>
                                <span class="text-slate-500 text-[11px] block"><?= $item['quantity'] ?> &times; <?= format_price($item['unit_price']) ?></span>
                            </div>
                        </div>
                        <span class="font-black text-slate-900"><?= format_price($item['total_price']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Price Breakdown -->
                <div class="mt-4 pt-4 border-t border-slate-200 space-y-1.5 text-xs text-slate-600">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-bold text-slate-900"><?= format_price($order['subtotal']) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Delivery Charge</span>
                        <span class="font-bold text-slate-900"><?= format_price($order['delivery_charge']) ?></span>
                    </div>
                    <?php if ($order['discount_amount'] > 0): ?>
                    <div class="flex justify-between text-brand-600 font-bold">
                        <span>Discount Coupon (<?= e($order['coupon_code']) ?>)</span>
                        <span>-<?= format_price($order['discount_amount']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="pt-2 border-t border-slate-200 flex justify-between text-sm font-black text-slate-900">
                        <span>Total Order Amount</span>
                        <span class="text-brand-600"><?= format_price($order['total_amount']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Customer & Delivery Information Card -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 text-xs space-y-3">
                <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">Delivery &amp; Customer Information</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">Recipient Name</span>
                        <p class="font-bold text-slate-900 text-sm mt-0.5"><?= e($order['guest_name']) ?></p>
                        <p class="text-slate-600 mt-1">Phone: <a href="tel:<?= e($order['guest_phone']) ?>" class="text-brand-600 font-bold hover:underline"><?= e($order['guest_phone']) ?></a></p>
                        <?php if ($order['guest_email']): ?>
                        <p class="text-slate-500">Email: <?= e($order['guest_email']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">Delivery Address</span>
                        <p class="text-slate-800 font-medium leading-relaxed mt-0.5"><?= nl2br(e($order['delivery_address'])) ?></p>
                        <p class="text-slate-900 font-black mt-1">PIN Code: <?= e($order['delivery_pincode']) ?></p>
                        <?php if ($order['delivery_instructions']): ?>
                        <p class="text-brand-700 bg-brand-50 p-2 rounded-xl mt-2 font-medium">Note: <?= e($order['delivery_instructions']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Payment & Transaction Information -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 text-xs space-y-3">
                <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">Razorpay Transaction Details</h3>
                <?php if ($order['payment']): ?>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Razorpay Order ID</span>
                        <span class="font-mono text-slate-800 font-bold"><?= e($order['payment']['razorpay_order_id'] ?? 'N/A') ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Payment ID</span>
                        <span class="font-mono text-brand-700 font-bold"><?= e($order['payment']['razorpay_payment_id'] ?? 'Pending') ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Gateway Status</span>
                        <span class="font-bold text-slate-900 uppercase"><?= e($order['payment']['status']) ?></span>
                    </div>
                </div>
                <?php else: ?>
                <p class="text-slate-400">No payment transaction created.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Col 3: Actions & Fulfillment Controls -->
        <div class="space-y-6">
            <!-- Fulfillment Status Form -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 text-xs">
                <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100 mb-4">Update Fulfillment</h3>

                <form action="/admin/orders/<?= $order['id'] ?>/status" method="POST" class="space-y-4">
                    <?= csrf_field() ?>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Fulfillment Stage</label>
                        <select name="fulfillment_status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                            <option value="pending" <?= $order['fulfillment_status'] === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                            <option value="confirmed" <?= $order['fulfillment_status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="preparing" <?= $order['fulfillment_status'] === 'preparing' ? 'selected' : '' ?>>Preparing &amp; Cutting</option>
                            <option value="out_for_delivery" <?= $order['fulfillment_status'] === 'out_for_delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                            <option value="delivered" <?= $order['fulfillment_status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                            <option value="cancelled" <?= $order['fulfillment_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Admin Internal Notes</label>
                        <textarea name="admin_notes" rows="3" placeholder="Rider details, delivery confirmation notes..."
                                  class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl"><?= e($order['admin_notes'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn-primary text-xs py-2.5 w-full">Update Order Status</button>
                </form>
            </div>

            <!-- Refund Management -->
            <?php if ($order['payment_status'] === 'paid'): ?>
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 text-xs space-y-3">
                <h3 class="text-sm font-bold text-red-600 pb-2 border-b border-slate-100">Process Razorpay Refund</h3>
                <p class="text-slate-500 text-[11px]">Initiate automated refund via Razorpay to the customer's original payment method.</p>

                <form action="/admin/orders/<?= $order['id'] ?>/refund" method="POST" class="space-y-3 confirm-delete" data-message="Are you sure you want to refund this order via Razorpay?">
                    <?= csrf_field() ?>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Refund Amount (₹)</label>
                        <input type="number" step="0.5" name="refund_amount" value="<?= $order['total_amount'] ?>" max="<?= $order['total_amount'] ?>" required
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Reason for Refund</label>
                        <input type="text" name="refund_reason" placeholder="e.g. Customer cancelled / damaged coconuts" required
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-md transition-colors">
                        Issue Refund
                    </button>
                </form>
            </div>
            <?php elseif ($order['payment_status'] === 'refunded'): ?>
            <div class="bg-purple-50 text-purple-800 p-4 rounded-3xl text-xs font-bold border border-purple-200 text-center">
                This order has already been refunded.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
