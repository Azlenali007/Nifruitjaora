<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Delivery Rules &amp; Serviceable Areas</h1>
        <p class="text-xs text-slate-500 mt-1">Configure delivery fees, minimum thresholds, and serviceable PIN codes.</p>
    </div>

    <!-- General Rules Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <h3 class="text-sm font-black text-slate-900 pb-3 border-b border-slate-100 mb-6">General Delivery Policies</h3>
        
        <form action="/admin/delivery/rules" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <?= csrf_field() ?>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Standard Delivery Charge (₹)</label>
                <input type="number" step="0.5" name="default_delivery_charge" value="<?= $settings['default_delivery_charge'] ?? 30 ?>" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Minimum Order Amount (₹)</label>
                <input type="number" step="0.5" name="min_order_amount" value="<?= $settings['min_order_amount'] ?? 80 ?>" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Free Delivery Threshold (₹)</label>
                <input type="number" step="0.5" name="free_delivery_threshold" value="<?= $settings['free_delivery_threshold'] ?? 250 ?>" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
            </div>

            <div class="sm:col-span-3 text-right pt-2 border-t border-slate-100">
                <button type="submit" class="btn-primary text-xs py-2.5 px-6">Save Delivery Rules</button>
            </div>
        </form>
    </div>

    <!-- Serviceable PIN Codes Table & Add Form -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Add PIN Code Form -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 text-xs">
            <h3 class="text-sm font-black text-slate-900 pb-3 border-b border-slate-100 mb-4">Add Serviceable PIN Code</h3>

            <form action="/admin/delivery/zones/save" method="POST" class="space-y-3">
                <?= csrf_field() ?>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">6-Digit PIN Code *</label>
                    <input type="text" name="pincode" required maxlength="6" placeholder="e.g. 400050"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold uppercase">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Area / Locality Name *</label>
                    <input type="text" name="area_name" required placeholder="e.g. Bandra West"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Delivery (₹)</label>
                        <input type="number" name="delivery_charge" value="30" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Est. Time</label>
                        <input type="text" name="estimated_time" value="30 - 45 mins" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                </div>

                <button type="submit" class="btn-primary text-xs py-2.5 w-full mt-2">Save PIN Code</button>
            </form>
        </div>

        <!-- Serviceable PIN Codes List -->
        <div class="md:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900">Configured Serviceable Areas</h3>
            </div>
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-6 font-bold">PIN Code</th>
                        <th class="py-3 px-4 font-bold">Area</th>
                        <th class="py-3 px-4 font-bold">Delivery Fee</th>
                        <th class="py-3 px-4 font-bold">ETA</th>
                        <th class="py-3 px-6 font-bold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <?php foreach ($zones as $z): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3 px-6 font-mono font-bold text-brand-700"><?= e($z['pincode']) ?></td>
                        <td class="py-3 px-4 font-bold text-slate-800"><?= e($z['area_name']) ?></td>
                        <td class="py-3 px-4 font-bold text-slate-900"><?= format_price($z['delivery_charge']) ?></td>
                        <td class="py-3 px-4 text-slate-500"><?= e($z['estimated_time']) ?></td>
                        <td class="py-3 px-6 text-right">
                            <form action="/admin/delivery/zones/<?= $z['id'] ?>/delete" method="POST" class="inline confirm-delete" data-message="Remove PIN code <?= e($z['pincode']) ?>?">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
