<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
     x-data="{
        basePrice: <?= (float)$product['price'] ?>,
        selectedPrice: <?= (float)$product['price'] ?>,
        quantity: 1,
        selectedVariantId: <?= !empty($variants) ? $variants[0]['id'] : 'null' ?>,
        updateVariant(variant) {
            this.selectedVariantId = variant.id;
            this.selectedPrice = parseFloat(variant.price);
        }
     }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8">
        <a href="/" class="hover:text-brand-600">Home</a>
        <span>/</span>
        <a href="/products" class="hover:text-brand-600">Catalogue</a>
        <span>/</span>
        <span class="text-slate-700"><?= e($product['name']) ?></span>
    </nav>

    <!-- Product Layout Grid -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-lg border border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-10 lg:gap-14">
        <!-- Col 1: Visual Image Stage -->
        <div class="flex flex-col items-center justify-center bg-gradient-to-b from-brand-50/50 to-slate-50 rounded-3xl p-6 sm:p-10 relative overflow-visible">
            <div class="w-full max-w-sm h-72 sm:h-96 flex items-center justify-center overflow-visible">
                <img src="<?= e($product['image_url']) ?>" 
                     alt="<?= e($product['name']) ?>" 
                     onerror="this.onerror=null;this.src='/assets/images/nariyal-pani.webp'"
                     class="max-w-full max-h-full w-auto h-auto object-contain filter drop-shadow-2xl">
            </div>

            <!-- Natural Stamp Badge -->
            <div class="absolute top-4 left-4 inline-flex items-center gap-1.5 bg-brand-600 text-white text-[11px] font-extrabold px-3 py-1 rounded-full shadow-md">
                <svg class="w-3.5 h-3.5"><use href="/assets/icons/sprite.svg#icon-leaf"></use></svg>
                <span>100% Natural</span>
            </div>
        </div>

        <!-- Col 2: Product Information & Purchase Controls -->
        <div class="flex flex-col justify-between">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600 block mb-2">
                    <?= e($product['category_name']) ?>
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    <?= e($product['name']) ?>
                </h1>

                <?php if (!empty($product['short_description'])): ?>
                <p class="text-xs sm:text-sm font-semibold text-slate-400 mt-1">
                    <?= e($product['short_description']) ?>
                </p>
                <?php endif; ?>

                <!-- Price Display -->
                <div class="mt-4 flex items-baseline gap-3">
                    <span class="text-3xl sm:text-4xl font-extrabold text-brand-600" x-text="'₹' + Math.round(selectedPrice)">
                        <?= format_price($product['price']) ?>
                    </span>
                    <?php if (!empty($product['sale_price'])): ?>
                        <span class="text-sm text-slate-400 line-through"><?= format_price($product['sale_price']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Size / Packaging Variants -->
                <?php if (!empty($variants)): ?>
                <div class="mt-6">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Select Variant</label>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($variants as $idx => $v): ?>
                        <button type="button" 
                                @click="updateVariant(<?= htmlspecialchars(json_encode($v), ENT_QUOTES, 'UTF-8') ?>)"
                                :class="selectedVariantId === <?= $v['id'] ?> ? 'border-brand-600 bg-brand-50 text-brand-900 ring-2 ring-brand-500/30' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
                                class="px-4 py-2.5 rounded-xl border text-xs font-bold transition-all">
                            <span><?= e($v['name']) ?></span>
                            <span class="text-brand-600 ml-1.5">(<?= format_price($v['price']) ?>)</span>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Description -->
                <div class="mt-6">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Description</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        <?= nl2br(e($product['description'] ?? 'Fresh tender coconut harvested daily from organic groves.')) ?>
                    </p>
                </div>

                <!-- Health Benefits Row -->
                <div class="mt-6 pt-6 border-t border-slate-100 grid grid-cols-2 gap-3 text-xs text-slate-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600"><use href="/assets/icons/sprite.svg#icon-circle-check"></use></svg>
                        <span>Zero artificial flavors</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600"><use href="/assets/icons/sprite.svg#icon-circle-check"></use></svg>
                        <span>Abundant potassium &amp; electrolytes</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600"><use href="/assets/icons/sprite.svg#icon-circle-check"></use></svg>
                        <span>Hygienically pre-cut packaging</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600"><use href="/assets/icons/sprite.svg#icon-circle-check"></use></svg>
                        <span>Fast local cold delivery</span>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 pt-6 border-t border-slate-100">
                <?php if ($product['is_in_stock']): ?>
                <form action="/cart/add" method="POST" class="space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="variant_id" :value="selectedVariantId">

                    <!-- Quantity Control & Buttons -->
                    <div class="flex items-center gap-4">
                        <div class="inline-flex items-center bg-slate-100 border border-slate-200 rounded-full p-1 shadow-inner">
                            <button type="button" @click="if (quantity > 1) quantity--" class="w-8 h-8 flex items-center justify-center font-bold text-slate-600 hover:text-brand-600">&minus;</button>
                            <input type="number" name="quantity" x-model="quantity" readonly class="w-10 text-center bg-transparent font-extrabold text-sm focus:outline-none">
                            <button type="button" @click="quantity++" class="w-8 h-8 flex items-center justify-center font-bold text-slate-600 hover:text-brand-600">&#43;</button>
                        </div>

                        <!-- Add to Cart -->
                        <button type="submit" 
                                class="flex-1 py-3.5 px-6 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm flex items-center justify-center gap-2 transition-transform active:scale-95 shadow-md">
                            <svg class="w-4 h-4"><use href="/assets/icons/sprite.svg#icon-cart"></use></svg>
                            <span>Add to Cart</span>
                        </button>
                    </div>

                    <!-- Direct Order Now -->
                    <button type="submit" name="order_now" value="1"
                            class="w-full py-4 px-6 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-base flex items-center justify-center gap-2 shadow-lg shadow-brand-600/30 transition-all active:scale-[0.98]">
                        <svg class="w-5 h-5"><use href="/assets/icons/sprite.svg#icon-bag"></use></svg>
                        <span>Instant Order Now</span>
                    </button>
                </form>
                <?php else: ?>
                <div class="bg-red-50 text-red-700 p-4 rounded-2xl text-center text-sm font-bold border border-red-200">
                    This item is temporarily sold out for today. Fresh morning batch arrives tomorrow at 7 AM!
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
