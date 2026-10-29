<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Order Management</h1>
            <p class="text-xs text-slate-500 mt-1">Track incoming deliveries, payment verifications, and fulfillments.</p>
        </div>
        <div class="text-xs font-bold text-slate-500 bg-white px-4 py-2 rounded-2xl border border-slate-200 shadow-xs">
            Total Orders Found: <strong class="text-brand-600"><?= $totalOrders ?></strong>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200">
        <form action="/admin/orders" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-500 mb-1">Search</label>
                <input type="text" name="search" value="<?= e($filters['search'] ?? '') ?>" placeholder="Order #, name, phone..."
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none">
            </div>

            <div>
                <label class="block font-bold text-slate-500 mb-1">Payment Status</label>
                <select name="payment_status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none">
                    <option value="">All Payments</option>
                    <option value="paid" <?= ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="pending" <?= ($filters['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending Payment</option>
                    <option value="failed" <?= ($filters['payment_status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
                    <option value="refunded" <?= ($filters['payment_status'] ?? '') === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-500 mb-1">Fulfillment Status</label>
                <select name="fulfillment_status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none">
                    <option value="">All Fulfillments</option>
                    <option value="pending" <?= ($filters['fulfillment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="confirmed" <?= ($filters['fulfillment_status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="preparing" <?= ($filters['fulfillment_status'] ?? '') === 'preparing' ? 'selected' : '' ?>>Preparing</option>
                    <option value="out_for_delivery" <?= ($filters['fulfillment_status'] ?? '') === 'out_for_delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                    <option value="delivered" <?= ($filters['fulfillment_status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                    <option value="cancelled" <?= ($filters['fulfillment_status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-500 mb-1">Date Range</label>
                <input type="date" name="date_from" value="<?= e($filters['date_from'] ?? '') ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none text-[11px]">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary text-xs py-2 px-4 flex-1">Filter</button>
                <a href="/admin/orders" class="btn-secondary text-xs py-2 px-3">Reset</a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-6 font-bold">Order Reference</th>
                        <th class="py-3 px-4 font-bold">Customer Details</th>
                        <th class="py-3 px-4 font-bold">Payment</th>
                        <th class="py-3 px-4 font-bold">Fulfillment</th>
                        <th class="py-3 px-4 font-bold">Total Amount</th>
                        <th class="py-3 px-4 font-bold">Order Date</th>
                        <th class="py-3 px-6 font-bold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">No matching orders found.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-6 font-mono font-bold text-brand-700">
                            <?= e($o['order_number']) ?>
                        </td>

                        <td class="py-3.5 px-4">
                            <strong class="font-bold text-slate-900 block"><?= e($o['guest_name']) ?></strong>
                            <span class="text-slate-500 text-[11px]"><?= e($o['guest_phone']) ?></span>
                        </td>

                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                <?= $o['payment_status'] === 'paid' ? 'bg-brand-100 text-brand-800' : ($o['payment_status'] === 'refunded' ? 'bg-purple-100 text-purple-800' : 'bg-amber-100 text-amber-800') ?>">
                                <?= e($o['payment_status']) ?>
                            </span>
                        </td>

                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                <?= e(str_replace('_', ' ', $o['fulfillment_status'])) ?>
                            </span>
                        </td>

                        <td class="py-3.5 px-4 font-black text-slate-900">
                            <?= format_price($o['total_amount']) ?>
                        </td>

                        <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                            <?= date('d M Y, h:i A', strtotime($o['created_at'])) ?>
                        </td>

                        <td class="py-3.5 px-6 text-right">
                            <a href="/admin/orders/<?= $o['id'] ?>" class="btn-secondary text-[11px] py-1.5 px-3">
                                Manage
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
