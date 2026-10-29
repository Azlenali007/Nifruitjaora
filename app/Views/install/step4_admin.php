<div>
    <div class="mb-6">
        <h2 class="text-2xl font-black text-white tracking-tight">Create Super Administrator</h2>
        <p class="text-sm text-slate-400 mt-1">Configure your primary administrative account. This account will have full access to manage orders, inventory, and store settings.</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="mb-6 p-4 rounded-2xl bg-red-950/60 border border-red-800 text-red-200 text-sm">
        <?= e($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="/install?step=4" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div>
            <label class="block text-xs font-bold text-slate-300 mb-1.5">Administrator Full Name</label>
            <input type="text" name="admin_name" value="<?= e($adminName) ?>" required
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 mb-1.5">Admin Email Address</label>
            <input type="email" name="admin_email" value="<?= e($adminEmail) ?>" required placeholder="owner@yourdomain.com"
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <span class="text-[11px] text-slate-500 mt-1 block">You will use this email address to sign into the /admin portal.</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Admin Password</label>
                <input type="password" name="admin_password" required minlength="8" placeholder="At least 8 characters"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Confirm Password</label>
                <input type="password" name="confirm_password" required minlength="8" placeholder="Repeat password"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div class="p-3.5 rounded-2xl bg-slate-800/60 border border-emerald-900/40 text-xs text-slate-400">
            🔒 Password will be securely hashed with PHP <code class="text-emerald-400">PASSWORD_BCRYPT</code>.
        </div>

        <div class="flex items-center justify-between pt-6 border-t border-slate-800 mt-6">
            <a href="/install?step=3" class="text-xs font-bold text-slate-400 hover:text-white transition-colors">
                &larr; Back to Store Setup
            </a>
            <button type="submit" class="px-6 py-3 rounded-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-sm tracking-wide shadow-lg shadow-emerald-500/20 transition-all">
                Proceed to Installation &rarr;
            </button>
        </div>
    </form>
</div>
