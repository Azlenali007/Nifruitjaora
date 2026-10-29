<div>
    <div class="mb-6">
        <h2 class="text-2xl font-black text-white tracking-tight">Store &amp; Environment Setup</h2>
        <p class="text-sm text-slate-400 mt-1">Configure your online fruit store details and server environment.</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="mb-6 p-4 rounded-2xl bg-red-950/60 border border-red-800 text-red-200 text-sm">
        <?= e($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="/install?step=3" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div>
            <label class="block text-xs font-bold text-slate-300 mb-1.5">Website Name</label>
            <input type="text" name="app_name" value="<?= e($appName) ?>" required
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 mb-1.5">Storefront Base URL</label>
            <input type="url" name="app_url" value="<?= e($appUrl) ?>" required
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <span class="text-[11px] text-slate-500 mt-1 block">Full URL including scheme (e.g. https://yourdomain.com)</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Timezone</label>
                <select name="app_timezone"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="Asia/Kolkata" <?= $appTimezone === 'Asia/Kolkata' ? 'selected' : '' ?>>Asia/Kolkata (IST - UTC+5:30) [Default]</option>
                    <option value="UTC" <?= $appTimezone === 'UTC' ? 'selected' : '' ?>>UTC</option>
                    <option value="Asia/Dubai" <?= $appTimezone === 'Asia/Dubai' ? 'selected' : '' ?>>Asia/Dubai (GST - UTC+4:00)</option>
                    <option value="Asia/Singapore" <?= $appTimezone === 'Asia/Singapore' ? 'selected' : '' ?>>Asia/Singapore (SGT - UTC+8:00)</option>
                    <option value="Europe/London" <?= $appTimezone === 'Europe/London' ? 'selected' : '' ?>>Europe/London (BST/GMT)</option>
                    <option value="America/New_York" <?= $appTimezone === 'America/New_York' ? 'selected' : '' ?>>America/New York (EST)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Environment</label>
                <select name="app_env"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="production" <?= $appEnv === 'production' ? 'selected' : '' ?>>Production (Recommended for live hosting)</option>
                    <option value="development" <?= $appEnv === 'development' ? 'selected' : '' ?>>Development (Debug mode enabled)</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between pt-6 border-t border-slate-800 mt-6">
            <a href="/install?step=2" class="text-xs font-bold text-slate-400 hover:text-white transition-colors">
                &larr; Back to Database
            </a>
            <button type="submit" class="px-6 py-3 rounded-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-sm tracking-wide shadow-lg shadow-emerald-500/20 transition-all">
                Save &amp; Continue &rarr;
            </button>
        </div>
    </form>
</div>
