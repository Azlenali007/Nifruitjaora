<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Store Profile &amp; General Settings</h1>
        <p class="text-xs text-slate-500 mt-1">Configure business name, operating hours, and customer contact channels.</p>
    </div>

    <form action="/admin/settings/update" method="POST" class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6 text-xs">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Business Name</label>
                <input type="text" name="store_name" value="<?= e($settings['store_name'] ?? 'N.A Fresh Fruits & Coconuts') ?>" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Header Tagline</label>
                <input type="text" name="store_tagline" value="<?= e($settings['store_tagline'] ?? 'FRESH • HEALTHY • NATURAL') ?>" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Customer Support Phone</label>
                <input type="text" name="contact_phone" value="<?= e($settings['contact_phone'] ?? '+91 98765 43210') ?>" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Support Email</label>
                <input type="email" name="contact_email" value="<?= e($settings['contact_email'] ?? 'support@nafreshfruits.com') ?>" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium">
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Outlet Address</label>
            <textarea name="store_address" rows="2" required
                      class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium"><?= e($settings['store_address'] ?? 'Shop 4, Green Palm Arcade, Hill Road, Bandra West, Mumbai 400050') ?></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Daily Store Hours</label>
                <input type="text" name="store_hours" value="<?= e($settings['store_hours'] ?? '7:00 AM - 10:00 PM (All 7 Days)') ?>" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Store Currency</label>
                <input type="text" name="currency" value="<?= e($settings['currency'] ?? 'INR') ?>" readonly
                       class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl font-bold cursor-not-allowed">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Currency Symbol</label>
                <input type="text" name="currency_symbol" value="<?= e($settings['currency_symbol'] ?? '₹') ?>" readonly
                       class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl font-bold cursor-not-allowed">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 text-right">
            <button type="submit" class="btn-primary text-xs py-3 px-8">Save Profile Settings</button>
        </div>
    </form>
</div>
