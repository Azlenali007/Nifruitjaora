<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Saved Delivery Addresses</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Manage addresses for fast 1-click checkout.</p>
        </div>
        <a href="/profile" class="btn-secondary text-xs py-2 px-4">Back to Profile</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- New Address Form -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100 mb-4">Add New Address</h3>

            <form action="/addresses/store" method="POST" class="space-y-3 text-xs">
                <?= csrf_field() ?>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Recipient Name *</label>
                    <input type="text" name="recipient_name" required placeholder="Name"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Mobile Phone *</label>
                    <input type="tel" name="phone" required placeholder="10-digit mobile"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Address Line 1 *</label>
                    <input type="text" name="address_line1" required placeholder="Flat, Building, Street"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Landmark</label>
                    <input type="text" name="landmark" placeholder="Near garden / bank"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">City</label>
                        <input type="text" name="city" value="Mumbai" required
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">PIN Code *</label>
                        <input type="text" name="pincode" required maxlength="6" placeholder="400050"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer font-bold text-slate-700">
                        <input type="checkbox" name="is_default" value="1" class="rounded text-brand-600 focus:ring-brand-500 h-4 w-4">
                        <span>Set as Default</span>
                    </label>
                </div>

                <button type="submit" class="btn-primary text-xs py-2.5 w-full mt-2">Save Address</button>
            </form>
        </div>

        <!-- Address List -->
        <div class="md:col-span-2 space-y-4">
            <?php if (empty($addresses)): ?>
            <div class="bg-white rounded-3xl p-10 text-center border border-slate-200">
                <p class="text-xs text-slate-500">No saved addresses yet. Add your first address using the form.</p>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php foreach ($addresses as $a): ?>
                <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200 flex flex-col justify-between text-xs">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <strong class="font-bold text-slate-900 text-sm"><?= e($a['recipient_name']) ?></strong>
                            <?php if ($a['is_default']): ?>
                            <span class="bg-brand-50 text-brand-700 font-extrabold px-2 py-0.5 rounded-full text-[10px]">Default</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-slate-600 leading-relaxed"><?= e($a['address_line1']) ?></p>
                        <?php if ($a['landmark']): ?>
                        <p class="text-slate-400 mt-0.5">Landmark: <?= e($a['landmark']) ?></p>
                        <?php endif; ?>
                        <p class="text-slate-800 font-bold mt-2">PIN: <?= e($a['pincode']) ?> &bull; <?= e($a['city']) ?></p>
                        <p class="text-brand-600 font-bold mt-1"><?= e($a['phone']) ?></p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 text-right">
                        <form action="/addresses/<?= $a['id'] ?>/delete" method="POST" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-bold">Delete</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
