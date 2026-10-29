<div>
    <div class="mb-6">
        <h2 class="text-2xl font-black text-white tracking-tight">Review &amp; Execute Installation</h2>
        <p class="text-sm text-slate-400 mt-1">Please verify your settings. Clicking "Run Database Installation" will configure your store environment, run migrations, seed store catalogs, and seal the installer.</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="mb-6 p-4 rounded-2xl bg-red-950/60 border border-red-800 text-red-200 text-sm">
        <?= e($error) ?>
    </div>
    <?php endif; ?>

    <?php if ($existingTablesCount > 0): ?>
    <div class="mb-6 p-4 rounded-2xl bg-amber-950/60 border border-amber-800 text-amber-200 text-sm flex items-start gap-3">
        <span class="text-lg">🛡️</span>
        <div>
            <h4 class="font-bold">Existing Database Tables Detected (<?= $existingTablesCount ?> tables)</h4>
            <p class="text-xs text-amber-300 mt-0.5">
                Safe execution mode is active: Existing tables will be preserved and not dropped. Migrations will run non-destructively to ensure all necessary columns and seeders exist.
            </p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Summary Box -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60">
            <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2">Database Target</h4>
            <div class="text-sm text-white font-semibold space-y-1">
                <div>Host: <span class="font-normal text-slate-300"><?= e($installDb['host']) ?>:<?= e($installDb['port']) ?></span></div>
                <div>Database: <span class="font-normal text-slate-300"><?= e($installDb['name']) ?></span></div>
                <div>Username: <span class="font-normal text-slate-300"><?= e($installDb['user']) ?></span></div>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60">
            <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2">Store Profile</h4>
            <div class="text-sm text-white font-semibold space-y-1">
                <div>Store: <span class="font-normal text-slate-300"><?= e($installApp['name']) ?></span></div>
                <div>URL: <span class="font-normal text-slate-300"><?= e($installApp['url']) ?></span></div>
                <div>Admin: <span class="font-normal text-slate-300"><?= e($installAdmin['email']) ?></span></div>
            </div>
        </div>
    </div>

    <!-- Confirmation Form -->
    <form method="POST" action="/install?step=5">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div class="flex items-center justify-between pt-6 border-t border-slate-800">
            <a href="/install?step=4" class="text-xs font-bold text-slate-400 hover:text-white transition-colors">
                &larr; Back to Admin Setup
            </a>
            <button type="submit" class="px-8 py-3.5 rounded-full bg-gradient-to-r from-emerald-500 to-emerald-400 hover:from-emerald-400 hover:to-emerald-300 text-slate-950 font-black text-sm tracking-wide shadow-xl shadow-emerald-500/25 transition-all">
                ⚡ Run Database Installation Now
            </button>
        </div>
    </form>
</div>
