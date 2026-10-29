<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Sign In | N.A Fresh Fruits & Coconuts') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="/assets/js/sweetalert2.all.min.js"></script>
    <script src="/assets/js/notifications.js"></script>
    <script defer src="/assets/js/alpine.min.js"></script>
</head>
<body class="bg-gradient-to-br from-brand-950 via-slate-900 to-brand-900 min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 text-slate-800 font-sans antialiased">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="/" class="inline-block transition-transform hover:scale-105">
            <img class="h-16 w-auto mx-auto" src="/assets/images/logo.svg" alt="N.A Fresh Fruits & Coconuts">
        </a>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-8 px-6 shadow-2xl rounded-3xl sm:px-10 border border-white/20">
            <?= $content ?>
        </div>
        <p class="mt-6 text-center text-xs text-slate-400">
            &copy; <?= date('Y') ?> N.A Fresh Fruits & Coconuts. Fresh • Healthy • Natural.
        </p>
    </div>

    <!-- Flash message popups -->
    <?php if ($msg = flash('success')): ?>
    <script>document.addEventListener('DOMContentLoaded', () => Notify.toast(<?= json_encode($msg) ?>, 'success'));</script>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
    <script>document.addEventListener('DOMContentLoaded', () => Notify.toast(<?= json_encode($msg) ?>, 'error'));</script>
    <?php endif; ?>
</body>
</html>
