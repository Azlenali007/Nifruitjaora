<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Installation Wizard | N.A Fresh Fruits & Coconuts') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="/assets/css/app.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-emerald-950 via-slate-900 to-emerald-900 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-emerald-500 selection:text-white">

    <!-- Top Header -->
    <header class="py-6 border-b border-emerald-800/40 bg-black/20 backdrop-blur-md">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center shadow-lg shadow-emerald-500/20 text-white font-black text-xl">
                    🥥
                </div>
                <div>
                    <h1 class="text-base sm:text-lg font-black tracking-tight text-white">N.A Fresh Fruits &amp; Coconuts</h1>
                    <p class="text-[11px] font-semibold tracking-wider text-emerald-400 uppercase">Production Web Installer</p>
                </div>
            </div>

            <div class="text-xs font-semibold px-3 py-1.5 rounded-full bg-emerald-900/60 border border-emerald-700/50 text-emerald-300">
                v1.0.0
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-grow flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-3xl bg-slate-900/90 backdrop-blur-xl border border-emerald-800/40 rounded-3xl shadow-2xl overflow-hidden p-6 sm:p-10">
            
            <?php if (isset($step) && $step >= 1 && $step <= 5): ?>
            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex items-center justify-between text-xs font-bold text-slate-400 mb-3">
                    <span class="<?= $step >= 1 ? 'text-emerald-400' : '' ?>">1. Requirements</span>
                    <span class="<?= $step >= 2 ? 'text-emerald-400' : '' ?>">2. Database</span>
                    <span class="<?= $step >= 3 ? 'text-emerald-400' : '' ?>">3. Store Setup</span>
                    <span class="<?= $step >= 4 ? 'text-emerald-400' : '' ?>">4. Admin</span>
                    <span class="<?= $step >= 5 ? 'text-emerald-400' : '' ?>">5. Install</span>
                </div>
                <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-400 h-full rounded-full transition-all duration-500"
                         style="width: <?= ($step / 5) * 100 ?>%"></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Content Area -->
            <?= $content ?>

        </div>
    </main>

    <!-- Footer -->
    <footer class="py-4 text-center text-xs text-slate-500 border-t border-emerald-900/30">
        <p>&copy; <?= date('Y') ?> N.A Fresh Fruits &amp; Coconuts. All rights reserved.</p>
    </footer>

</body>
</html>
