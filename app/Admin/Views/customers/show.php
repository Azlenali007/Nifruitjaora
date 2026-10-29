<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Customer: <?= e($user['name']) ?></h1>
            <p class="text-xs text-slate-500 mt-1">Customer ID: #<?= $user['id'] ?> &bull; Joined <?= date('d M Y', strtotime($user['created_at'])) ?></p>
        </div>
        <a href="/admin/customers" class="btn-secondary text-xs py-2 px-4">&larr; Back to Customers</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Customer Info Card -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 text-xs space-y-4">
            <h3 class="font-bold text-slate-900 text-sm pb-2 border-b border-slate-100">Contact Details</h3>
            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Mobile Phone</span>
                <p class="font-bold text-brand-700 text-sm mt-0.5"><?= e($user['phone']) ?></p>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Email Address</span>
                <p class="font-medium text-slate-800 mt-0.5"><?= e($user['email'] ?? 'Not provided') ?></p>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Account Status</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-brand-100 text-brand-800 mt-1 inline-block">
                    <?= e($user['status']) ?>
                </span>
            </div>
        </div>

        <!-- Order History -->
        <div class="md:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
            <h3 class="font-bold text-slate-900 text-sm pb-2 border-b border-slate-100 mb-4">Customer Order History</h3>
            <?php if (empty($orders)): ?>
            <p class="text-xs text-slate-400 py-6 text-center">No orders found for this customer.</p>
            <?php else: ?>
            <div class="divide-y divide-slate-100 text-xs">
                <?php foreach ($orders as $o): ?>
                <div class="py-3 flex items-center justify-between">
                    <div>
                        <a href="/admin/orders/<?= $o['id'] ?>" class="font-mono font-bold text-brand-700 hover:underline"><?= e($o['order_number']) ?></a>
                        <span class="text-[11px] text-slate-400 block"><?= date('d M Y, h:i A', strtotime($o['created_at'])) ?></span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-slate-900 block"><?= format_price($o['total_amount']) ?></span>
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full <?= $o['payment_status'] === 'paid' ? 'bg-brand-50 text-brand-700' : 'bg-amber-50 text-amber-700' ?>">
                            <?= e($o['payment_status']) ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
