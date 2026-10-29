<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
     x-data="checkoutHandler({
        pincode: '400050',
        name: '<?= auth() ? e(auth()['name']) : '' ?>',
        phone: '<?= auth() ? e(auth()['phone']) : '' ?>',
        email: '<?= auth() ? e(auth()['email'] ?? '') : '' ?>',
        address: ''
     })">

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Express Checkout</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Local express delivery within 30-45 minutes.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Delivery Details Form -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Saved Addresses (if customer logged in) -->
            <?php if (!empty($savedAddresses)): ?>
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600"><use href="/assets/icons/sprite.svg#icon-circle-check"></use></svg>
                    Saved Delivery Addresses
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php foreach ($savedAddresses as $addr): ?>
                    <div @click="selectSavedAddress(<?= htmlspecialchars(json_encode($addr), ENT_QUOTES, 'UTF-8') ?>)"
                         :class="selectedAddressId === <?= $addr['id'] ?> ? 'border-brand-600 bg-brand-50/50 ring-2 ring-brand-500/20' : 'border-slate-200 hover:border-slate-300'"
                         class="p-4 rounded-2xl border cursor-pointer transition-all text-xs">
                        <strong class="block font-bold text-slate-900"><?= e($addr['recipient_name']) ?></strong>
                        <p class="text-slate-600 mt-1"><?= e($addr['address_line1']) ?>, PIN: <?= e($addr['pincode']) ?></p>
                        <span class="text-brand-600 font-bold block mt-2"><?= e($addr['phone']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Delivery Address Form -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
                <h3 class="text-base font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-600"><use href="/assets/icons/sprite.svg#icon-user"></use></svg>
                    Delivery &amp; Contact Details
                </h3>

                <form id="checkoutForm" @submit.prevent="submitOrder()" class="space-y-4">
                    <?= csrf_field() ?>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Full Name -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Your Full Name *</label>
                            <input type="text" name="name" x-model="name" required placeholder="e.g. Rahul Sharma"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>

                        <!-- Mobile Number -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mobile Number *</label>
                            <input type="tel" name="phone" x-model="phone" required placeholder="10-digit mobile (e.g. 9876543210)"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address (Optional)</label>
                        <input type="email" name="email" x-model="email" placeholder="For payment receipt &amp; order updates"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>

                    <!-- Complete Delivery Address -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Delivery Address *</label>
                        <textarea name="address" x-model="address" required rows="3" placeholder="Flat / House No, Building, Street, Landmark..."
                                  class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- PIN Code -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">PIN Code *</label>
                            <input type="text" name="pincode" x-model="pincode" required maxlength="6" placeholder="e.g. 400050"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold tracking-wider focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <span class="text-[11px] text-slate-400 mt-1 block">Supported: 400050, 400051, 400052, 400053, 400054, 400055, 400056</span>
                        </div>

                        <!-- Special Instructions -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Delivery Instructions</label>
                            <input type="text" name="instructions" x-model="instructions" placeholder="e.g. Leave with security / Ring bell"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>

                    <?php if (auth()): ?>
                    <div class="pt-2">
                        <label class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
                            <input type="checkbox" name="save_address" value="1" x-model="saveAddress" class="rounded text-brand-600 focus:ring-brand-500 h-4 w-4">
                            <span>Save this address to my profile for future orders</span>
                        </label>
                    </div>
                    <?php endif; ?>

                </form>
            </div>

            <!-- Payment Gateway Notice -->
            <div class="bg-brand-50/70 border border-brand-200 rounded-3xl p-5 flex items-start gap-3 text-xs text-brand-900">
                <svg class="w-6 h-6 text-brand-600 flex-shrink-0 mt-0.5"><use href="/assets/icons/sprite.svg#icon-shield"></use></svg>
                <div>
                    <strong class="font-bold block text-sm mb-1">Razorpay Secure Online Checkout</strong>
                    <p class="leading-relaxed">
                        Pay safely with UPI (Google Pay, PhonePe, Paytm), Credit/Debit Cards, or NetBanking. Orders are immediately dispatched once payment signature is verified.
                    </p>
                </div>
            </div>

        </div>

        <!-- Order Summary Side Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl p-6 shadow-md border border-slate-200 sticky top-28 space-y-6">
                <h3 class="text-base font-black text-slate-900 pb-3 border-b border-slate-100">Review Items</h3>

                <!-- Line Items Mini List -->
                <div class="max-h-60 overflow-y-auto divide-y divide-slate-100 pr-1 text-xs">
                    <?php foreach ($cartSummary['items'] as $item): ?>
                    <div class="py-2.5 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-lg bg-slate-50 p-1 flex-shrink-0 flex items-center justify-center">
                                <img src="<?= e($item['image_url']) ?>" alt="" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <span class="font-bold text-slate-800 block line-clamp-1"><?= e($item['name']) ?></span>
                                <span class="text-slate-400 text-[11px]"><?= $item['quantity'] ?> &times; <?= format_price($item['unit_price']) ?></span>
                            </div>
                        </div>
                        <span class="font-bold text-slate-900"><?= format_price($item['subtotal']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Price Breakdown -->
                <div class="space-y-2.5 text-xs pt-3 border-t border-slate-100">
                    <div class="flex justify-between text-slate-600">
                        <span>Items Subtotal</span>
                        <span class="font-bold text-slate-900"><?= format_price($cartSummary['subtotal']) ?></span>
                    </div>

                    <div class="flex justify-between text-slate-600">
                        <span>Delivery Fee</span>
                        <span class="font-bold <?= $cartSummary['delivery_charge'] === 0.0 ? 'text-brand-600' : 'text-slate-900' ?>">
                            <?= $cartSummary['delivery_charge'] === 0.0 ? 'FREE' : format_price($cartSummary['delivery_charge']) ?>
                        </span>
                    </div>

                    <?php if ($cartSummary['discount_amount'] > 0): ?>
                    <div class="flex justify-between text-brand-600 font-bold bg-brand-50 p-2 rounded-xl">
                        <span>Discount (<?= e($cartSummary['applied_coupon']['code']) ?>)</span>
                        <span>-<?= format_price($cartSummary['discount_amount']) ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="pt-3 border-t border-slate-200 flex justify-between text-base font-black text-slate-900">
                        <span>Total Payable</span>
                        <span class="text-brand-600"><?= format_price($cartSummary['total_amount']) ?></span>
                    </div>
                </div>

                <!-- Checkout Trigger Button -->
                <?php if ($razorpayEnabled): ?>
                <button type="button" @click="submitOrder()" :disabled="isSubmitting"
                        class="w-full py-4 px-6 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-base flex items-center justify-center gap-2 shadow-lg shadow-brand-600/30 transition-all active:scale-[0.98] disabled:opacity-50">
                    <template x-if="!isSubmitting">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5"><use href="/assets/icons/sprite.svg#icon-shield"></use></svg>
                            <span>Pay <?= format_price($cartSummary['total_amount']) ?> with Razorpay</span>
                        </span>
                    </template>
                    <template x-if="isSubmitting">
                        <span>Connecting to Gateway...</span>
                    </template>
                </button>
                <?php else: ?>
                <div class="bg-amber-50 text-amber-800 p-4 rounded-2xl text-xs font-bold text-center border border-amber-200">
                    Online payment gateway is temporarily unavailable. Please check back shortly.
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
