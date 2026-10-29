<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Breadcrumb & Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Coconut Store Catalogue</h1>
        <p class="text-sm text-slate-500 mt-1">Farm fresh tender coconuts, chilled water, and coconut malai treats.</p>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200 mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Category Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
            <a href="/products" 
               class="px-4 py-2 rounded-full text-xs font-bold transition-all <?= empty($currentCategory) ? 'bg-brand-600 text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                All Coconuts
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="/products?category=<?= e($cat['slug']) ?>" 
               class="px-4 py-2 rounded-full text-xs font-bold transition-all <?= $currentCategory === $cat['slug'] ? 'bg-brand-600 text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                <?= e($cat['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Search & Sort Controls -->
        <form action="/products" method="GET" class="flex items-center gap-3 w-full md:w-auto">
            <?php if (!empty($currentCategory)): ?>
                <input type="hidden" name="category" value="<?= e($currentCategory) ?>">
            <?php endif; ?>
            <div class="relative flex-grow md:w-64">
                <input type="text" name="search" value="<?= e($currentSearch) ?>" placeholder="Search..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"><use href="/assets/icons/sprite.svg#icon-search"></use></svg>
            </div>
            <select name="sort" onchange="this.form.submit()" 
                    class="text-xs font-semibold py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
                <option value="featured" <?= $currentSort === 'featured' ? 'selected' : '' ?>>Featured</option>
                <option value="price_asc" <?= $currentSort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price_desc" <?= $currentSort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                <option value="name_asc" <?= $currentSort === 'name_asc' ? 'selected' : '' ?>>Name: A - Z</option>
            </select>
        </form>
    </div>

    <!-- Product Grid -->
    <?php if (empty($products)): ?>
    <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 max-w-md mx-auto my-12">
        <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8"><use href="/assets/icons/sprite.svg#icon-search"></use></svg>
        </div>
        <h3 class="text-lg font-bold text-slate-900">No Coconuts Found</h3>
        <p class="text-xs text-slate-500 mt-1 mb-6">No active products match your search criteria.</p>
        <a href="/products" class="btn-primary text-xs py-2.5 px-6">View All Products</a>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($products as $product): ?>
        <div class="bg-white rounded-3xl p-5 shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-200 flex flex-col justify-between group">
            <div>
                <a href="/products/<?= e($product['slug']) ?>" class="block relative w-full h-52 sm:h-56 bg-gradient-to-b from-slate-50 to-brand-50/20 rounded-2xl overflow-visible mb-3 p-3 flex items-center justify-center group-hover:scale-[1.02] transition-transform">
                    <img src="<?= e($product['image_url']) ?>" 
                         alt="<?= e($product['name']) ?>" 
                         onerror="this.onerror=null;this.src='/assets/images/nariyal-pani.webp'"
                         class="max-w-full max-h-full w-auto h-auto object-contain filter drop-shadow">
                    <?php if (!$product['is_in_stock']): ?>
                        <span class="absolute top-3 left-3 bg-red-100 text-red-700 text-[10px] font-bold px-2.5 py-0.5 rounded-full shadow-sm">Out of Stock</span>
                    <?php endif; ?>
                </a>

                <span class="text-[10px] font-extrabold uppercase tracking-wider text-brand-600 block mb-1">
                    <?= e($product['category_name']) ?>
                </span>
                <h3 class="text-base font-bold text-slate-900 leading-snug group-hover:text-brand-600 transition-colors">
                    <a href="/products/<?= e($product['slug']) ?>"><?= e($product['name']) ?></a>
                </h3>
                <p class="text-xs text-slate-400 line-clamp-2 mt-1">
                    <?= e($product['short_description'] ?? $product['description']) ?>
                </p>
            </div>

            <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-lg font-black text-slate-900">
                        <?= format_price($product['price']) ?>
                    </span>
                    <?php if (!empty($product['sale_price'])): ?>
                        <span class="text-xs text-slate-400 line-through ml-1"><?= format_price($product['sale_price']) ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($product['is_in_stock']): ?>
                <form action="/cart/add" method="POST" class="inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" 
                            class="p-2.5 rounded-full bg-brand-500 hover:bg-brand-600 text-white flex items-center justify-center shadow-md active:scale-95 transition-transform">
                        <svg class="w-4 h-4"><use href="/assets/icons/sprite.svg#icon-cart"></use></svg>
                    </button>
                </form>
                <?php else: ?>
                <span class="text-xs font-bold text-slate-400">Sold Out</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
