<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin Sign In') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="/assets/js/sweetalert2.all.min.js"></script>
    <script src="/assets/js/notifications.js"></script>
</head>
<body class="h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 font-sans antialiased bg-gradient-to-br from-brand-950 via-slate-900 to-slate-950 text-slate-800">
    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-3xl shadow-2xl border border-white/20">
        <div class="text-center">
            <img class="mx-auto h-12 w-auto" src="/assets/images/logo.svg" alt="N.A Fresh Fruits & Coconuts">
            <h2 class="mt-4 text-2xl font-black text-slate-900 tracking-tight">Admin Portal Sign In</h2>
            <p class="mt-1 text-xs text-slate-500">Restricted staff &amp; management access.</p>
        </div>

        <form class="mt-8 space-y-4" action="/admin/login" method="POST">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Administrator Email</label>
                <input type="email" name="email" required placeholder="admin@nafreshfruits.com"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                <input type="password" name="password" required placeholder="••••••••••••"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div class="pt-2">
                <button type="submit" 
                        class="w-full py-3.5 px-6 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-sm shadow-lg shadow-brand-600/30 transition-transform active:scale-[0.98]">
                    Sign In to Dashboard
                </button>
            </div>
        </form>

        <div class="pt-4 text-center">
            <a href="/" class="text-xs font-bold text-brand-600 hover:text-brand-700">&larr; Return to Customer Store</a>
        </div>
    </div>

    <?php if ($msg = flash('error')): ?>
    <script>document.addEventListener('DOMContentLoaded', () => Notify.toast(<?= json_encode($msg) ?>, 'error'));</script>
    <?php endif; ?>
</body>
</html>
