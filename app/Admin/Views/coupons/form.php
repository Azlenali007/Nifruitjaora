<?php
$isEdit = !empty($coupon);
$action = $isEdit ? "/admin/coupons/{$coupon['id']}/update" : "/admin/coupons/store";
?>

<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight"><?= $isEdit ? 'Edit Coupon' : 'Create New Coupon' ?></h1>
            <p class="text-xs text-slate-500 mt-1">Configure discount rules and validity constraints.</p>
        </div>
        <a href="/admin/coupons" class="btn-secondary text-xs py-2 px-4">Cancel</a>
    </div>

    <form action="<?= $action ?>" method="POST" class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-4 text-xs">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Coupon Code *</label>
                <input type="text" name="code" value="<?= e($coupon['code'] ?? '') ?>" required placeholder="e.g. SUMMER25"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono uppercase font-black focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Discount Type</label>
                <select name="discount_type" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                    <option value="percentage" <?= ($coupon['discount_type'] ?? '') === 'percentage' ? 'selected' : '' ?>>Percentage (%)</option>
                    <option value="fixed" <?= ($coupon['discount_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Flat Amount (₹)</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Discount Value *</label>
                <input type="number" step="0.5" name="discount_value" value="<?= $coupon['discount_value'] ?? '' ?>" required placeholder="10 or 25"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Min. Order (₹)</label>
                <input type="number" step="0.5" name="min_order_amount" value="<?= $coupon['min_order_amount'] ?? '0' ?>"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Max. Discount (₹)</label>
                <input type="number" step="0.5" name="max_discount_amount" value="<?= $coupon['max_discount_amount'] ?? '' ?>" placeholder="Optional cap"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Total Redemptions Limit</label>
            <input type="number" name="usage_limit" value="<?= $coupon['usage_limit'] ?? '' ?>" placeholder="e.g. 500 (blank for unlimited)"
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
        </div>

        <div class="pt-2">
            <label class="flex items-center gap-2 cursor-pointer font-bold">
                <input type="checkbox" name="is_active" value="1" <?= (!isset($coupon['is_active']) || $coupon['is_active']) ? 'checked' : '' ?> class="rounded text-brand-600 h-4 w-4">
                <span>Coupon is Active</span>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 text-right">
            <button type="submit" class="btn-primary text-xs py-3 px-8">Save Coupon</button>
        </div>
    </form>
</div>
