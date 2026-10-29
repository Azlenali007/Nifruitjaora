<div>
    <div class="mb-6">
        <h2 class="text-2xl font-black text-white tracking-tight">Database Configuration</h2>
        <p class="text-sm text-slate-400 mt-1">Enter your MySQL / MariaDB database credentials. We will verify the connection before continuing.</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="mb-6 p-4 rounded-2xl bg-red-950/60 border border-red-800 text-red-200 text-sm flex items-start gap-3">
        <span class="text-lg">⚠️</span>
        <div>
            <h4 class="font-bold">Database Error</h4>
            <p class="text-xs text-red-300 mt-0.5"><?= e($error) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <form method="POST" action="/install?step=2" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Database Host</label>
                <input type="text" name="db_host" value="<?= e($dbHost) ?>" required
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <span class="text-[11px] text-slate-500 mt-1 block">Usually 127.0.0.1 or localhost</span>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Port</label>
                <input type="number" name="db_port" value="<?= e($dbPort) ?>" required
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <span class="text-[11px] text-slate-500 mt-1 block">Default: 3306</span>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 mb-1.5">Database Name</label>
            <input type="text" name="db_name" value="<?= e($dbName) ?>" required
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <span class="text-[11px] text-slate-500 mt-1 block">Will be automatically created if user has CREATE permissions.</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Database Username</label>
                <input type="text" name="db_user" value="<?= e($dbUser) ?>" required
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Database Password</label>
                <input type="password" name="db_pass" placeholder="••••••••"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <span class="text-[11px] text-slate-500 mt-1 block">Never shown in plaintext.</span>
            </div>
        </div>

        <div class="flex items-center justify-between pt-6 border-t border-slate-800 mt-6">
            <a href="/install?step=1" class="text-xs font-bold text-slate-400 hover:text-white transition-colors">
                &larr; Back to Requirements
            </a>
            <button type="submit" class="px-6 py-3 rounded-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-sm tracking-wide shadow-lg shadow-emerald-500/20 transition-all">
                Validate &amp; Continue &rarr;
            </button>
        </div>
    </form>
</div>
