<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">My Customer Profile</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Manage your account information and saved addresses.</p>
        </div>
        <form action="/logout" method="POST">
            <?= csrf_field() ?>
            <button type="submit" class="btn-secondary text-xs py-2 px-4 text-red-600 hover:bg-red-50">Sign Out</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-4">
            <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100">Personal Details</h2>
            
            <form action="/profile/update" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Name</label>
                    <input type="text" name="name" value="<?= e($user['name']) ?>" required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mobile Number</label>
                    <input type="text" value="<?= e($user['phone']) ?>" disabled
                           class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-medium text-slate-500 cursor-not-allowed">
                    <span class="text-[10px] text-slate-400 mt-1 block">Phone number cannot be changed</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address</label>
                    <input type="email" name="email" value="<?= e($user['email'] ?? '') ?>"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <button type="submit" class="btn-primary text-xs py-2.5 w-full">Save Profile</button>
            </form>
        </div>

        <!-- Quick Links & Recent Activity -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Recent Orders</h2>
                    <a href="/my-orders" class="text-xs font-bold text-brand-600 hover:underline">View All</a>
                </div>

                <?php if (empty($recentOrders)): ?>
                <p class="text-xs text-slate-400 py-6 text-center">No orders placed yet.</p>
                <?php else: ?>
                <div class="divide-y divide-slate-100 text-xs">
                    <?php foreach ($recentOrders as $ro): ?>
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <span class="font-mono font-bold text-brand-700 block"><?= e($ro['order_number']) ?></span>
                            <span class="text-[11px] text-slate-400"><?= date('d M Y', strtotime($ro['created_at'])) ?> &bull; <?= format_price($ro['total_amount']) ?></span>
                        </div>
                        <a href="/orders/<?= e($ro['order_number']) ?>" class="btn-secondary text-[11px] py-1.5 px-3">
                            Track
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Saved Addresses Summary -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Delivery Addresses</h2>
                    <a href="/addresses" class="text-xs font-bold text-brand-600 hover:underline">Manage Addresses</a>
                </div>
                <div class="pt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <?php foreach (array_slice($addresses, 0, 2) as $ad): ?>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl">
                        <strong class="font-bold text-slate-800 block"><?= e($ad['recipient_name']) ?></strong>
                        <p class="text-slate-600 line-clamp-2 mt-1"><?= e($ad['address_line1']) ?></p>
                        <span class="text-brand-600 font-bold block mt-1"><?= e($ad['pincode']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
