<?php
/** @var array $carouselProducts */
/** @var array $featuredProducts */
/** @var array $categories */
/** @var array $settings */
?>

<div class="relative overflow-hidden bg-slate-100"
     x-data="coconutCarousel(<?= htmlspecialchars(json_encode($carouselProducts), ENT_QUOTES, 'UTF-8') ?>)"
     @touchstart="handleTouchStart($event)"
     @touchmove="handleTouchMove($event)"
     @touchend="handleTouchEnd($event)"
     @mousedown="handleMouseDown($event)"
     @mousemove="handleMouseMove($event)"
     @mouseup="handleMouseUp($event)"
     @mouseleave="handleMouseUp($event)">

    <!-- TROPICAL COCONUT HERO SECTION (Exact Reference Mockup Reproduction) -->
    <section class="relative pt-4 sm:pt-6 pb-12 sm:pb-20 overflow-x-clip bg-gradient-to-b from-[#06180e] via-[#0d2a19] to-slate-100 text-slate-800">
        <!-- Natural palm leaves overlay pattern -->
        <div class="absolute inset-0 opacity-25 pointer-events-none mix-blend-overlay bg-cover bg-center"
             style="background-image: url('/assets/images/hero-bg.webp');"></div>

        <div class="relative max-w-6xl mx-auto px-3 sm:px-6">

            <!-- MAIN COCONUT STAGE WITH VISIBLE LEFT & RIGHT PEEK CARDS -->
            <div class="relative flex items-center justify-center min-h-[440px] sm:min-h-[500px] select-none pt-2 overflow-visible">

                <!-- LEFT PEEK CARD (Previous Product - Visible on Mobile, Tablet & Desktop) -->
                <template x-if="prevProduct">
                    <div @click="prev()" 
                         class="absolute -left-12 sm:-left-4 md:left-4 lg:left-8 top-12 sm:top-16 z-10 w-36 sm:w-44 lg:w-48 bg-white/90 backdrop-blur-md rounded-3xl p-3 sm:p-4 shadow-xl border border-white/70 flex flex-col items-center text-center cursor-pointer transition-all duration-300 transform scale-85 sm:scale-95 hover:scale-100 hover:bg-white opacity-80 hover:opacity-100 group select-none">
                        
                        <!-- Product Thumbnail with full visibility -->
                        <div class="w-20 h-20 sm:w-24 sm:h-24 mb-2 flex items-center justify-center overflow-visible rounded-2xl p-1">
                            <img :src="prevProduct.image_url" 
                                 :alt="prevProduct.name" 
                                 onerror="this.onerror=null;this.src='/assets/images/nariyal-pani.webp'"
                                 class="max-w-full max-h-full w-auto h-auto object-contain filter drop-shadow-md transition-transform group-hover:scale-105">
                        </div>

                        <!-- Top green accent notch -->
                        <div class="w-6 h-1 bg-emerald-500 rounded-full mb-1"></div>
                        <h4 class="text-[11px] sm:text-xs font-black text-slate-900 leading-tight mb-1 line-clamp-2" x-text="prevProduct.name"></h4>
                        <div class="text-emerald-700 font-black text-sm sm:text-base mb-2 sm:mb-3" x-text="'₹' + Math.round(prevProduct.price)"></div>

                        <!-- Circular Quick Cart Button -->
                        <button @click.stop="addQuickProduct(prevProduct.id)" type="button" aria-label="Add to cart"
                                class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-brand-500 hover:bg-brand-600 text-white flex items-center justify-center shadow-md transition-transform active:scale-90">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5"><use href="/assets/icons/sprite.svg#icon-cart"></use></svg>
                        </button>

                        <!-- Left Arrow Trigger Button -->
                        <div class="absolute -left-3 sm:-left-4 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white text-slate-700 shadow-md flex items-center justify-center border border-slate-100 group-hover:bg-brand-50 group-hover:text-brand-700 transition-colors">
                            <svg class="w-4 h-4"><use href="/assets/icons/sprite.svg#icon-chevron-left"></use></svg>
                        </div>
                    </div>
                </template>

                <!-- CENTER ACTIVE PRODUCT (Tender Coconut Hero) -->
                <div class="z-20 flex flex-col items-center w-full max-w-sm sm:max-w-md mx-auto transition-transform duration-300"
                     :style="dragOffset !== 0 ? 'transform: translateX(' + dragOffset + 'px)' : ''">
                    
                    <!-- Center Product Visual: Hero Tender Coconut with Branding Sticker and Straw -->
                    <!-- Generous clearance ensuring 100% of the straw, husk, ice cubes and base are visible -->
                    <div class="relative w-full h-64 sm:h-80 md:h-88 flex items-center justify-center overflow-visible py-2">
                        <img :src="currentProduct.image_url" 
                             :alt="currentProduct.name"
                             onerror="this.onerror=null;this.src='/assets/images/hero-coconut.webp'"
                             class="max-w-full max-h-full w-auto h-auto object-contain filter drop-shadow-2xl transition-all duration-300 transform hover:scale-105 pointer-events-none select-none">

                        <!-- Circular Navigation Arrows positioned beside the coconut -->
                        <button @click="prev()" type="button" aria-label="Previous Coconut"
                                class="absolute -left-2 sm:left-1 md:left-2 top-1/2 -translate-y-1/2 w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-white/90 text-slate-800 shadow-xl flex items-center justify-center border border-white hover:bg-white active:scale-90 transition-all z-30">
                            <svg class="w-5 h-5"><use href="/assets/icons/sprite.svg#icon-chevron-left"></use></svg>
                        </button>
                        <button @click="next()" type="button" aria-label="Next Coconut"
                                class="absolute -right-2 sm:right-1 md:right-2 top-1/2 -translate-y-1/2 w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-white/90 text-slate-800 shadow-xl flex items-center justify-center border border-white hover:bg-white active:scale-90 transition-all z-30">
                            <svg class="w-5 h-5"><use href="/assets/icons/sprite.svg#icon-chevron-right"></use></svg>
                        </button>
                    </div>

                    <!-- ACTIVE PRODUCT CARD (Matches attached visual specification) -->
                    <div class="relative w-full bg-white rounded-[2.5rem] p-5 sm:p-7 shadow-2xl border border-white/80 text-center mt-2 z-20">
                        
                        <!-- Top Leaf Accent Badge Notch -->
                        <div class="absolute -top-5 left-1/2 -translate-x-1/2 w-10 h-10 rounded-full bg-white border border-brand-100 shadow-md flex items-center justify-center text-brand-500">
                            <svg class="w-5 h-5"><use href="/assets/icons/sprite.svg#icon-leaf"></use></svg>
                        </div>

                        <!-- Product Name -->
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-black text-slate-900 tracking-tight pt-2" x-text="currentProduct.name">
                            Fresh Nariyal Pani
                        </h2>

                        <!-- Natural Tagline / Subtitle (High Contrast) -->
                        <p class="text-xs sm:text-sm font-bold text-slate-700 mt-1" x-text="currentProduct.short_description || '100% Natural | No Added Sugar | Fresh & Healthy'">
                            100% Natural | No Added Sugar | Fresh &amp; Healthy
                        </p>

                        <!-- Price -->
                        <div class="mt-2 sm:mt-3">
                            <span class="text-3xl sm:text-4xl font-black text-emerald-700 tracking-tight" x-text="'₹' + Math.round(currentProduct.price)">
                                ₹40
                            </span>
                        </div>

                        <!-- Quantity Selector Pill [- 1 +] -->
                        <div class="inline-flex items-center justify-center bg-slate-100 border border-slate-300 rounded-full py-1.5 px-4 mt-3 sm:mt-4 shadow-inner">
                            <button @click="decrementQty()" type="button" aria-label="Decrease quantity"
                                    class="w-7 h-7 flex items-center justify-center text-slate-800 hover:text-emerald-700 font-black text-lg select-none">
                                &minus;
                            </button>
                            <span class="w-8 text-center font-black text-base text-slate-900" x-text="quantity">1</span>
                            <button @click="incrementQty()" type="button" aria-label="Increase quantity"
                                    class="w-7 h-7 flex items-center justify-center text-slate-800 hover:text-emerald-700 font-black text-lg select-none">
                                &#43;
                            </button>
                        </div>

                        <!-- Action Buttons: Order Now & Add to Cart -->
                        <div class="mt-4 sm:mt-5 flex items-center gap-3">
                            <button @click="addActiveToCart(true)" type="button"
                                    class="flex-1 py-3.5 sm:py-4 px-6 rounded-full bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-black text-sm sm:text-base tracking-wide flex items-center justify-center gap-2 shadow-xl shadow-emerald-700/25 transition-all">
                                <svg class="w-5 h-5 text-white"><use href="/assets/icons/sprite.svg#icon-bag"></use></svg>
                                <span class="text-white">Order Now</span>
                            </button>
                            <button @click="addActiveToCart(false)" type="button" aria-label="Add to cart"
                                    class="p-3.5 sm:p-4 rounded-full bg-slate-100 hover:bg-emerald-50 text-slate-800 hover:text-emerald-700 border border-slate-300 transition-colors shadow-sm" title="Add to Cart">
                                <svg class="w-5 h-5"><use href="/assets/icons/sprite.svg#icon-cart"></use></svg>
                            </button>
                        </div>

                    </div>
                </div>

                <!-- RIGHT PEEK CARD (Next Product - Visible on Mobile, Tablet & Desktop) -->
                <template x-if="nextProduct">
                    <div @click="next()" 
                         class="absolute -right-12 sm:-right-4 md:right-4 lg:right-8 top-12 sm:top-16 z-10 w-36 sm:w-44 lg:w-48 bg-white/90 backdrop-blur-md rounded-3xl p-3 sm:p-4 shadow-xl border border-white/70 flex flex-col items-center text-center cursor-pointer transition-all duration-300 transform scale-85 sm:scale-95 hover:scale-100 hover:bg-white opacity-80 hover:opacity-100 group select-none">
                        
                        <!-- Product Thumbnail with full visibility -->
                        <div class="w-20 h-20 sm:w-24 sm:h-24 mb-2 flex items-center justify-center overflow-visible rounded-2xl p-1">
                            <img :src="nextProduct.image_url" 
                                 :alt="nextProduct.name" 
                                 onerror="this.onerror=null;this.src='/assets/images/coconut-malai-bar.webp'"
                                 class="max-w-full max-h-full w-auto h-auto object-contain filter drop-shadow-md transition-transform group-hover:scale-105">
                        </div>

                        <!-- Top green accent notch -->
                        <div class="w-6 h-1 bg-emerald-500 rounded-full mb-1"></div>
                        <h4 class="text-[11px] sm:text-xs font-black text-slate-900 leading-tight mb-1 line-clamp-2" x-text="nextProduct.name"></h4>
                        <div class="text-emerald-700 font-black text-sm sm:text-base mb-2 sm:mb-3" x-text="'₹' + Math.round(nextProduct.price)"></div>

                        <!-- Circular Quick Cart Button -->
                        <button @click.stop="addQuickProduct(nextProduct.id)" type="button" aria-label="Add to cart"
                                class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-brand-500 hover:bg-brand-600 text-white flex items-center justify-center shadow-md transition-transform active:scale-90">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5"><use href="/assets/icons/sprite.svg#icon-cart"></use></svg>
                        </button>

                        <!-- Right Arrow Trigger Button -->
                        <div class="absolute -right-3 sm:-right-4 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white text-slate-700 shadow-md flex items-center justify-center border border-slate-100 group-hover:bg-brand-50 group-hover:text-brand-700 transition-colors">
                            <svg class="w-4 h-4"><use href="/assets/icons/sprite.svg#icon-chevron-right"></use></svg>
                        </div>
                    </div>
                </template>

            </div>

            <!-- 4 BENEFIT BADGES ROW (Matches Reference Design) -->
            <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 max-w-3xl mx-auto">
                <!-- Badge 1: 100% Natural -->
                <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 text-center shadow-md border border-white/70 flex flex-col items-center justify-center transition-transform hover:-translate-y-0.5">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2">
                        <svg class="w-5 h-5"><use href="/assets/icons/sprite.svg#icon-leaf"></use></svg>
                    </div>
                    <span class="text-xs font-black text-slate-900">100% Natural</span>
                </div>

                <!-- Badge 2: No Preservatives -->
                <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 text-center shadow-md border border-white/70 flex flex-col items-center justify-center transition-transform hover:-translate-y-0.5">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2">
                        <svg class="w-5 h-5"><use href="/assets/icons/sprite.svg#icon-heart"></use></svg>
                    </div>
                    <span class="text-xs font-black text-slate-900">No Preservatives</span>
                </div>

                <!-- Badge 3: Fresh Daily Coconuts -->
                <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 text-center shadow-md border border-white/70 flex flex-col items-center justify-center transition-transform hover:-translate-y-0.5">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2">
                        <svg class="w-5 h-5"><use href="/assets/icons/sprite.svg#icon-circle-check"></use></svg>
                    </div>
                    <span class="text-xs font-black text-slate-900">Fresh Daily Coconuts</span>
                </div>

                <!-- Badge 4: Healthy & Refreshing -->
                <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 text-center shadow-md border border-white/70 flex flex-col items-center justify-center transition-transform hover:-translate-y-0.5">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2">
                        <svg class="w-5 h-5"><use href="/assets/icons/sprite.svg#icon-zap"></use></svg>
                    </div>
                    <span class="text-xs font-black text-slate-900">Healthy &amp; Refreshing</span>
                </div>
            </div>

            <!-- CAROUSEL PAGINATION DOTS (Matches 3-dot design in mockup) -->
            <div class="flex items-center justify-center gap-2.5 mt-6">
                <template x-for="(prod, idx) in products.slice(0, 3)" :key="prod.id">
                    <button @click="goTo(idx)" 
                            :aria-label="'Go to item ' + (idx + 1)"
                            :class="currentIndex === idx ? 'w-6 bg-brand-500 shadow-sm' : 'w-2 bg-emerald-700/40 hover:bg-emerald-600/60'"
                            class="h-2 rounded-full transition-all duration-300 focus:outline-none"></button>
                </template>
            </div>

            <!-- BOTTOM QUICK ITEM / CATEGORY HORIZONTAL SELECTOR (Matches reference design) -->
            <div class="mt-8 bg-white/95 backdrop-blur-md rounded-3xl p-3 shadow-xl border border-white max-w-4xl mx-auto overflow-x-auto scrollbar-none">
                <div class="flex items-center justify-around min-w-[500px] gap-2">
                    <template x-for="(prod, idx) in products" :key="prod.id">
                        <button @click="goTo(idx)" type="button"
                                :class="currentIndex === idx ? 'bg-brand-100/90 border-brand-300 ring-2 ring-brand-500/30 shadow-sm' : 'hover:bg-slate-50 border-transparent'"
                                class="flex-1 flex flex-col items-center py-2 px-3 rounded-2xl border transition-all text-center">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center overflow-visible mb-1 p-0.5">
                                <img :src="prod.image_url" 
                                     :alt="prod.name" 
                                     onerror="this.onerror=null;this.src='/assets/images/nariyal-pani.webp'"
                                     class="max-w-full max-h-full w-auto h-auto object-contain">
                            </div>
                            <span class="text-[11px] font-bold leading-tight" 
                                  :class="currentIndex === idx ? 'text-brand-900 font-extrabold' : 'text-slate-600'"
                                  x-text="prod.name"></span>
                        </button>
                    </template>
                </div>
            </div>

        </div>
    </section>

    <!-- FEATURED COCONUT DELICACIES & CATALOGUE SECTION -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600">Pure &amp; Farm Sourced</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 tracking-tight">Our Farm Fresh Selection</h2>
                <p class="text-sm text-slate-500 mt-2">Delivered chilled with zero preservatives, straight to your doorstep.</p>
            </div>
            <a href="/products" class="mt-4 md:mt-0 inline-flex items-center gap-1.5 text-sm font-bold text-brand-600 hover:text-brand-700">
                <span>View Full Catalogue</span>
                <svg class="w-4 h-4"><use href="/assets/icons/sprite.svg#icon-chevron-right"></use></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="bg-white rounded-3xl p-6 shadow-md hover:shadow-xl transition-all duration-300 border border-slate-100 flex flex-col justify-between group">
                <div>
                    <!-- Product Image with 100% full visibility (no cutoffs) -->
                    <a href="/products/<?= e($product['slug']) ?>" class="block relative w-full h-52 sm:h-56 bg-gradient-to-b from-slate-50 to-brand-50/20 rounded-2xl overflow-visible mb-4 p-4 flex items-center justify-center group-hover:scale-[1.02] transition-transform">
                        <img src="<?= e($product['image_url']) ?>" 
                             alt="<?= e($product['name']) ?>" 
                             onerror="this.onerror=null;this.src='/assets/images/nariyal-pani.webp'"
                             class="max-w-full max-h-full w-auto h-auto object-contain filter drop-shadow">
                        <?php if ($product['is_in_stock']): ?>
                            <span class="absolute top-3 left-3 bg-brand-50 text-brand-700 text-[10px] font-extrabold px-2.5 py-1 rounded-full border border-brand-200">
                                Fresh Harvest
                            </span>
                        <?php else: ?>
                            <span class="absolute top-3 left-3 bg-red-50 text-red-700 text-[10px] font-extrabold px-2.5 py-1 rounded-full border border-red-200">
                                Sold Out
                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- Title & Details -->
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-brand-600 block mb-1">
                        <?= e($product['category_name']) ?>
                    </span>
                    <h3 class="text-lg font-bold text-slate-900 group-hover:text-brand-600 transition-colors">
                        <a href="/products/<?= e($product['slug']) ?>"><?= e($product['name']) ?></a>
                    </h3>
                    <p class="text-xs text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                        <?= e($product['short_description'] ?? $product['description']) ?>
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-2xl font-black text-slate-900"><?= format_price($product['price']) ?></span>
                        <span class="text-[11px] font-medium text-slate-400 block">Per unit</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button onclick="Cart.add(<?= (int)$product['id'] ?>, null, 1)" type="button" aria-label="Add to cart"
                                class="p-3 rounded-full bg-slate-100 hover:bg-brand-50 text-slate-700 hover:text-brand-600 border border-slate-200 transition-colors shadow-sm" title="Add to cart">
                            <svg class="w-4 h-4"><use href="/assets/icons/sprite.svg#icon-cart"></use></svg>
                        </button>
                        <a href="/products/<?= e($product['slug']) ?>" 
                           class="py-2.5 px-4 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs tracking-wide shadow-md shadow-brand-600/20 transition-all">
                            Order Now
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- STORE PROMISE / HYDRATION QUALITY BANNER -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="bg-gradient-to-r from-brand-900 via-brand-800 to-emerald-900 rounded-[2.5rem] p-8 sm:p-12 text-white relative overflow-hidden shadow-2xl">
            <div class="relative z-10 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 bg-brand-500/20 text-brand-300 text-xs font-extrabold px-3.5 py-1.5 rounded-full border border-brand-500/30 mb-4">
                    <span>🌴</span> Farm Fresh Guarantee
                </span>
                <h3 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight">
                    Harvested at Sunrise.<br>Delivered Ice Cold to Your Door.
                </h3>
                <p class="text-sm sm:text-base text-brand-100/90 mt-3 leading-relaxed">
                    Our tender coconuts are picked every morning from certified organic coastal groves, kept naturally chilled, and delivered with express temperature control in Bandra, Khar, Santacruz, and Andheri.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <a href="/products" class="py-3.5 px-8 rounded-full bg-white hover:bg-brand-50 text-brand-950 font-black text-sm tracking-wide shadow-xl transition-all">
                        Explore Coconut Menu
                    </a>
                    <a href="/track" class="py-3.5 px-6 rounded-full bg-brand-800/80 hover:bg-brand-700 text-white font-bold text-sm border border-brand-700/60 transition-all">
                        Track Delivery
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>
