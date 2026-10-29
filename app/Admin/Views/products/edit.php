<div class="max-w-4xl mx-auto space-y-6"
     x-data="{
        variants: <?= htmlspecialchars(json_encode($variants), ENT_QUOTES, 'UTF-8') ?>
     }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit: <?= e($product['name']) ?></h1>
            <p class="text-xs text-slate-500 mt-1">Modify prices, stock, carousel position, and variants.</p>
        </div>
        <a href="/admin/products" class="btn-secondary text-xs py-2 px-4">Cancel</a>
    </div>

    <form action="/admin/products/<?= $product['id'] ?>/update" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6 text-xs">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Product Name *</label>
                <input type="text" name="name" value="<?= e($product['name']) ?>" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category *</label>
                <select name="category_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (int)$product['category_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?> (<?= $cat['is_active'] ? 'Active' : 'Inactive' ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Base Price (₹) *</label>
                <input type="number" step="0.5" name="price" value="<?= $product['price'] ?>" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-black focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Sale Price (₹)</label>
                <input type="number" step="0.5" name="sale_price" value="<?= $product['sale_price'] ?>"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-black focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Stock Quantity *</label>
                <input type="number" name="stock_quantity" value="<?= $product['stock_quantity'] ?>" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Short Tagline</label>
            <input type="text" name="short_description" value="<?= e($product['short_description'] ?? '') ?>"
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Description</label>
            <textarea name="description" rows="3"
                      class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:outline-none focus:ring-2 focus:ring-brand-500"><?= e($product['description'] ?? '') ?></textarea>
        </div>

        <!-- Image Management -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100 items-center">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 p-1 flex-shrink-0 flex items-center justify-center border border-slate-200">
                    <img src="<?= e($product['image_url']) ?>" alt="" class="w-full h-full object-contain">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Replace Image</label>
                    <input type="file" name="image" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                           class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:bg-brand-600 file:text-white file:text-xs">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Or Direct Asset URL</label>
                <input type="text" name="image_url" value="<?= e($product['image_url']) ?>"
                       class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:outline-none">
            </div>
        </div>

        <!-- Carousel Options -->
        <div class="pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <label class="flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer font-bold">
                <input type="checkbox" name="is_carousel" value="1" <?= $product['is_carousel'] ? 'checked' : '' ?> class="rounded text-brand-600 h-4 w-4">
                <span>Show in Hero Carousel</span>
            </label>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Carousel Order</label>
                <input type="number" name="carousel_order" value="<?= $product['carousel_order'] ?>"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
            </div>

            <label class="flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer font-bold">
                <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?> class="rounded text-brand-600 h-4 w-4">
                <span>Featured Product</span>
            </label>
        </div>

        <!-- Variants -->
        <div class="pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-slate-900 uppercase tracking-wider">Product Sizing Variants</h3>
                <button type="button" @click="variants.push({ name: '', price: '', stock_quantity: 50 })" class="text-brand-600 font-bold hover:underline">
                    + Add Variant
                </button>
            </div>

            <template x-for="(v, index) in variants" :key="index">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mb-2 p-3 bg-slate-50 border border-slate-200 rounded-xl items-center">
                    <input type="text" name="variant_names[]" x-model="v.name" placeholder="Variant name" required
                           class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg">
                    <input type="number" step="0.5" name="variant_prices[]" x-model="v.price" placeholder="Price (₹)" required
                           class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg">
                    <input type="number" name="variant_stocks[]" x-model="v.stock_quantity" placeholder="Stock" required
                           class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg">
                    <button type="button" @click="variants.splice(index, 1)" class="text-red-500 font-bold hover:underline text-right">
                        Remove
                    </button>
                </div>
            </template>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer font-bold">
                <input type="checkbox" name="is_active" value="1" <?= $product['is_active'] ? 'checked' : '' ?> class="rounded text-brand-600 h-4 w-4">
                <span>Product is Active</span>
            </label>

            <button type="submit" class="btn-primary text-xs py-3 px-8">Update Product</button>
        </div>
    </form>
</div>
