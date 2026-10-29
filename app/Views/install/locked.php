<div class="text-center py-6">
    <div class="w-16 h-16 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/40 flex items-center justify-center text-3xl mx-auto mb-5 shadow-lg">
        🔒
    </div>

    <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Installer Is Locked</h2>
    <p class="text-sm text-slate-300 mt-2 max-w-lg mx-auto">
        This store has already been installed and secured. To prevent accidental overwrites and security vulnerabilities, the installation wizard is permanently locked.
    </p>

    <div class="mt-8 p-5 rounded-2xl bg-slate-800/70 border border-slate-700/80 text-left max-w-lg mx-auto text-xs space-y-2">
        <div class="flex justify-between text-slate-400">
            <span>Installed At:</span>
            <span class="text-white font-medium"><?= e($lockDetails['installed_at'] ?? 'Previously Configured') ?></span>
        </div>
        <div class="flex justify-between text-slate-400">
            <span>Admin Email:</span>
            <span class="text-white font-medium"><?= e($lockDetails['admin_email'] ?? 'Store Administrator') ?></span>
        </div>
        <div class="flex justify-between text-slate-400">
            <span>Security Lock:</span>
            <span class="text-emerald-400 font-mono">storage/installed.lock</span>
        </div>
    </div>

    <div class="mt-6 p-4 rounded-xl bg-amber-950/40 border border-amber-800/40 text-left max-w-lg mx-auto text-[11px] text-amber-200/90 leading-relaxed">
        <strong>Need to re-install or re-configure?</strong><br>
        For authorized administrators needing a legitimate recovery, delete the file <code class="bg-black/30 px-1 py-0.5 rounded text-white">storage/installed.lock</code> from your server filesystem or hosting cPanel, then refresh this page.
    </div>

    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="/" class="w-full sm:w-auto px-7 py-3 rounded-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-sm tracking-wide shadow-lg shadow-emerald-500/20 transition-all">
            Visit Storefront
        </a>
        <a href="/admin/login" class="w-full sm:w-auto px-7 py-3 rounded-full bg-slate-800 hover:bg-slate-700 text-white font-extrabold text-sm border border-slate-700 transition-all">
            Admin Login
        </a>
    </div>
</div>
