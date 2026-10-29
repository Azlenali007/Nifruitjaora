<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Homepage &amp; Carousel Management</h1>
        <p class="text-xs text-slate-500 mt-1">Control hero banners, announcement bar, and carousel product lineup.</p>
    </div>

    <form action="/admin/homepage/update" method="POST" class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6 text-xs">
        <?= csrf_field() ?>

        <!-- Announcement Banner -->
        <div>
            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Top Notice Announcement Bar</label>
            <input type="text" name="notice_banner" value="<?= e($settings['notice_banner'] ?? '') ?>"
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
            <span class="text-[10px] text-slate-400 mt-1 block">Displayed at the very top of all customer pages.</span>
        </div>

        <!-- Hero Headings -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Hero Main Heading</label>
                <input type="text" name="hero_heading" value="<?= e($settings['hero_heading'] ?? '') ?>"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Hero Subtitle</label>
                <input type="text" name="hero_subheading" value="<?= e($settings['hero_subheading'] ?? '') ?>"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <!-- Carousel Lineup Selection -->
        <div class="pt-4 border-t border-slate-100">
            <h3 class="text-sm font-black text-slate-900 mb-2">Featured Products in 3D Stage Carousel</h3>
            <p class="text-slate-500 text-[11px] mb-4">Select coconut products to display in the central interactive carousel.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <?php foreach ($allProducts as $p): ?>
                <label class="p-3 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between cursor-pointer hover:bg-slate-100/60 transition-colors">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="carousel_products[]" value="<?= $p['id'] ?>" 
                               <?= $p['is_carousel'] ? 'checked' : '' ?> class="rounded text-brand-600 h-4 w-4">
                        <div>
                            <strong class="font-bold text-slate-900 block"><?= e($p['name']) ?></strong>
                            <span class="text-slate-400 text-[10px]"><?= format_price($p['price']) ?></span>
                        </div>
                    </div>
                    <?php if ($p['is_carousel']): ?>
                        <span class="bg-brand-100 text-brand-800 text-[10px] font-bold px-2 py-0.5 rounded-full">#<?= $p['carousel_order'] ?> Carousel</span>
                    <?php endif; ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer font-bold">
                <input type="checkbox" name="is_store_open" value="1" <?= (!isset($settings['is_store_open']) || $settings['is_store_open']) ? 'checked' : '' ?> class="rounded text-brand-600 h-4 w-4">
                <span>Store is Open to Accept Deliveries Today</span>
            </label>

            <button type="submit" class="btn-primary text-xs py-3 px-8">Save Homepage Settings</button>
        </div>
    </form>
</div>
