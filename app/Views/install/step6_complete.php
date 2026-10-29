<div class="text-center py-4">
    <div class="w-20 h-20 rounded-full bg-emerald-500/20 text-emerald-400 border-2 border-emerald-500/40 flex items-center justify-center text-4xl mx-auto mb-6 shadow-xl shadow-emerald-500/20 animate-bounce">
        🎉
    </div>

    <h2 class="text-3xl font-black text-white tracking-tight">Installation Successfully Completed!</h2>
    <p class="text-sm text-slate-300 mt-2 max-w-lg mx-auto">
        Your store for <strong class="text-white">N.A Fresh Fruits &amp; Coconuts</strong> has been fully initialized with database tables, catalog seeders, active coconut items, and superadmin credentials.
    </p>

    <!-- Security Notification -->
    <div class="mt-8 p-5 rounded-2xl bg-emerald-950/60 border border-emerald-700/60 text-left max-w-xl mx-auto">
        <h4 class="text-sm font-bold text-emerald-300 flex items-center gap-2">
            <span>🔒</span> Installer Automatically Locked
        </h4>
        <p class="text-xs text-slate-300 mt-1.5 leading-relaxed">
            The security lock file has been placed at <code class="text-emerald-400 bg-black/40 px-1.5 py-0.5 rounded font-mono">storage/installed.lock</code>. Further access to the installer is completely blocked to protect your system.
        </p>
        <p class="text-[11px] text-slate-400 mt-2">
            * In the rare event of recovery or server migration, simply delete <code class="text-slate-300">storage/installed.lock</code> via SSH or your server file manager to unlock.
        </p>
    </div>

    <!-- Quick Links -->
    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="/" class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-sm tracking-wide shadow-lg shadow-emerald-500/20 transition-all">
            🌴 Visit Customer Storefront
        </a>
        <a href="/admin/login" class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-slate-800 hover:bg-slate-700 text-white font-extrabold text-sm border border-slate-700 transition-all">
            🔐 Go to Admin Panel
        </a>
    </div>
</div>
