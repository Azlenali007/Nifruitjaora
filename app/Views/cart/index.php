<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
     x-data="cartManager(<?= htmlspecialchars(json_encode($cartSummary), ENT_QUOTES, 'UTF-8') ?>)">
    
    <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-8">Shopping Cart</h1>

    <template x-if="summary.total_items === 0">
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 max-w-md mx-auto my-8">
            <div class="w-20 h-20 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10"><use href="/assets/icons/sprite.svg#icon-cart"></use></svg>
            </div>
            <h2 class="text-xl font-bold text-slate-900 mb-2">Your Cart is Empty</h2>
            <p class="text-xs text-slate-500 mb-6">Explore our daily batch of fresh tender coconuts and coconut water!</p>
            <a href="/products" class="btn-primary text-sm py-3 px-8">Browse Coconuts</a>
        </div>
    </template>

    <template x-if="summary.total_items > 0">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Items Table / List -->
            <div class="lg:col-span-2 space-y-4">
                
                <!-- Free Delivery Progress Bar -->
                <div class="bg-brand-50 border border-brand-200 rounded-2xl p-4">
                    <div class="flex items-center justify-between text-xs font-bold text-brand-900 mb-1.5">
                        <span x-show="summary.subtotal >= summary.free_delivery_threshold">🎉 Congratulations! You have unlocked FREE Delivery!</span>
                        <span x-show="summary.subtotal < summary.free_delivery_threshold"
                              x-text="'Add ₹' + Math.max(0, summary.free_delivery_threshold - summary.subtotal) + ' more for FREE delivery!'"></span>
                        <span x-text="Math.round(Math.min(100, (summary.subtotal / summary.free_delivery_threshold) * 100)) + '%'"></span>
                    </div>
                    <div class="w-full bg-brand-200/60 rounded-full h-2 overflow-hidden">
                        <div class="bg-brand-600 h-2 rounded-full transition-all duration-500"
                             :style="'width: ' + Math.min(100, (summary.subtotal / summary.free_delivery_threshold) * 100) + '%'"></div>
                    </div>
                </div>

                <!-- Product Cards in Cart -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 divide-y divide-slate-100">
                    <template x-for="item in summary.items" :key="item.id">
                        <div class="py-4 first:pt-0 last:pb-0 flex items-center gap-4">
                            <!-- Image -->
                            <div class="w-20 h-20 rounded-2xl bg-slate-50 p-2 flex-shrink-0 flex items-center justify-center">
                                <img :src="item.image_url" :alt="item.name" class="w-full h-full object-contain">
                            </div>

                            <!-- Details -->
                            <div class="flex-grow">
                                <h3 class="text-sm font-bold text-slate-900" x-text="item.name"></h3>
                                <p class="text-xs text-slate-400" x-show="item.variant_name" x-text="item.variant_name"></p>
                                <div class="text-sm font-black text-brand-600 mt-1" x-text="'₹' + Math.round(item.unit_price)"></div>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="inline-flex items-center bg-slate-100 border border-slate-200 rounded-full p-1">
                                <button type="button" @click="updateQty(item.id, item.quantity - 1)" 
                                        class="w-7 h-7 flex items-center justify-center text-slate-600 hover:text-brand-600 font-bold">&minus;</button>
                                <span class="w-7 text-center font-extrabold text-xs text-slate-800" x-text="item.quantity"></span>
                                <button type="button" @click="updateQty(item.id, item.quantity + 1)" 
                                        class="w-7 h-7 flex items-center justify-center text-slate-600 hover:text-brand-600 font-bold">&#43;</button>
                            </div>

                            <!-- Total & Delete -->
                            <div class="text-right flex flex-col items-end gap-1 min-w-[70px]">
                                <span class="text-sm font-black text-slate-900" x-text="'₹' + Math.round(item.subtotal)"></span>
                                <button type="button" @click="removeItem(item.id)" aria-label="Remove item" 
                                        class="text-xs text-slate-400 hover:text-red-500 transition-colors">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl p-6 shadow-md border border-slate-200 sticky top-28 space-y-6">
                    <h2 class="text-lg font-black text-slate-900">Order Summary</h2>

                    <!-- Price Calculations -->
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal (<span x-text="summary.total_items"></span> items)</span>
                            <span class="font-bold text-slate-900" x-text="'₹' + summary.subtotal"></span>
                        </div>

                        <div class="flex justify-between text-slate-600">
                            <span>Estimated Delivery</span>
                            <span class="font-bold" :class="summary.delivery_charge === 0 ? 'text-brand-600' : 'text-slate-900'" 
                                  x-text="summary.delivery_charge === 0 ? 'FREE' : '₹' + summary.delivery_charge"></span>
                        </div>

                        <template x-if="summary.discount_amount > 0">
                            <div class="flex justify-between text-brand-600 font-bold bg-brand-50 p-2 rounded-xl">
                                <span>Coupon (<span x-text="summary.applied_coupon.code"></span>)</span>
                                <span x-text="'-₹' + summary.discount_amount"></span>
                            </div>
                        </template>

                        <div class="pt-3 border-t border-slate-200 flex justify-between text-base font-black text-slate-900">
                            <span>Total Amount</span>
                            <span class="text-brand-600" x-text="'₹' + summary.total_amount"></span>
                        </div>
                    </div>

                    <!-- Coupon Code Input -->
                    <div>
                        <div class="flex gap-2">
                            <input type="text" x-model="couponCode" placeholder="Promo code (e.g. FRESH10)"
                                   class="flex-1 text-xs uppercase px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 font-bold">
                            <button type="button" @click="applyCoupon()" 
                                    class="px-4 py-2.5 bg-slate-900 text-white font-bold text-xs rounded-xl hover:bg-slate-800 transition-colors">
                                Apply
                            </button>
                        </div>
                        <template x-if="summary.applied_coupon">
                            <div class="mt-2 flex items-center justify-between text-xs text-brand-700 bg-brand-50 px-3 py-1.5 rounded-lg">
                                <span>Coupon <strong><span x-text="summary.applied_coupon.code"></span></strong> Applied</span>
                                <button type="button" @click="removeCoupon()" class="text-red-500 font-bold hover:underline">Remove</button>
                            </div>
                        </template>
                    </div>

                    <!-- Checkout Button -->
                    <a href="/checkout" 
                       class="w-full py-4 px-6 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-sm flex items-center justify-center gap-2 shadow-lg shadow-brand-600/30 transition-transform active:scale-[0.98]">
                        <span>Proceed to Checkout</span>
                        <svg class="w-4 h-4"><use href="/assets/icons/sprite.svg#icon-chevron-right"></use></svg>
                    </a>

                    <p class="text-[11px] text-center text-slate-400">
                        🔒 Secure Checkout with Razorpay UPI, Cards &amp; NetBanking.
                    </p>
                </div>
            </div>
        </div>
    </template>
</div>
