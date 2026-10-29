<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin Panel | N.A Fresh Fruits & Coconuts') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="/assets/css/app.css">
    
    <!-- SweetAlert2 & Admin JS -->
    <script src="/assets/js/sweetalert2.all.min.js"></script>
    <script src="/assets/js/notifications.js"></script>
    <script src="/assets/js/chart.umd.min.js"></script>
    <script src="/assets/js/admin-charts.js"></script>
    <script src="/assets/js/admin.js"></script>
    <script defer src="/assets/js/alpine.min.js"></script>
</head>
<body class="h-full font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false }">

    <div class="min-h-full flex">
        <!-- Sidebar Navigation -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-brand-950 text-white flex flex-col transition-transform duration-300 transform md:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
            
            <!-- Sidebar Header -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-brand-900">
                <a href="/admin/dashboard" class="flex items-center gap-3">
                    <img src="/assets/images/logo.svg" alt="N.A Fresh Fruits" class="h-8 w-auto">
                    <span class="text-xs font-black tracking-widest text-brand-400 uppercase">Admin</span>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white">
                    <svg class="w-6 h-6"><use href="/assets/icons/sprite.svg#icon-close"></use></svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto text-xs font-bold tracking-wide">
                <a href="/admin/dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-brand-900 transition-colors <?= str_contains($_SERVER['REQUEST_URI'], '/admin/dashboard') ? 'bg-brand-800 text-white' : 'text-slate-300' ?>">
                    <svg class="w-4 h-4 text-brand-400"><use href="/assets/icons/sprite.svg#icon-zap"></use></svg>
                    <span>Dashboard</span>
                </a>

                <a href="/admin/products" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-brand-900 transition-colors <?= str_contains($_SERVER['REQUEST_URI'], '/admin/products') ? 'bg-brand-800 text-white' : 'text-slate-300' ?>">
                    <svg class="w-4 h-4 text-brand-400"><use href="/assets/icons/sprite.svg#icon-leaf"></use></svg>
                    <span>Coconuts &amp; Products</span>
                </a>

                <a href="/admin/categories" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-brand-900 transition-colors <?= str_contains($_SERVER['REQUEST_URI'], '/admin/categories') ? 'bg-brand-800 text-white' : 'text-slate-300' ?>">
                    <svg class="w-4 h-4 text-brand-400"><use href="/assets/icons/sprite.svg#icon-circle-check"></use></svg>
                    <span>Categories</span>
                </a>

                <a href="/admin/orders" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-brand-900 transition-colors <?= str_contains($_SERVER['REQUEST_URI'], '/admin/orders') ? 'bg-brand-800 text-white' : 'text-slate-300' ?>">
                    <svg class="w-4 h-4 text-brand-400"><use href="/assets/icons/sprite.svg#icon-bag"></use></svg>
                    <span>Order Management</span>
                </a>

                <a href="/admin/customers" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-brand-900 transition-colors <?= str_contains($_SERVER['REQUEST_URI'], '/admin/customers') ? 'bg-brand-800 text-white' : 'text-slate-300' ?>">
                    <svg class="w-4 h-4 text-brand-400"><use href="/assets/icons/sprite.svg#icon-user"></use></svg>
                    <span>Customers</span>
                </a>

                <a href="/admin/payments" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-brand-900 transition-colors <?= str_contains($_SERVER['REQUEST_URI'], '/admin/payments') ? 'bg-brand-800 text-white' : 'text-slate-300' ?>">
                    <svg class="w-4 h-4 text-brand-400"><use href="/assets/icons/sprite.svg#icon-shield"></use></svg>
                    <span>Razorpay &amp; Payments</span>
                </a>

                <a href="/admin/coupons" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-brand-900 transition-colors <?= str_contains($_SERVER['REQUEST_URI'], '/admin/coupons') ? 'bg-brand-800 text-white' : 'text-slate-300' ?>">
                    <svg class="w-4 h-4 text-brand-400"><use href="/assets/icons/sprite.svg#icon-heart"></use></svg>
                    <span>Coupons &amp; Discounts</span>
                </a>

                <a href="/admin/reports" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-brand-900 transition-colors <?= str_contains($_SERVER['REQUEST_URI'], '/admin/reports') ? 'bg-brand-800 text-white' : 'text-slate-300' ?>">
                    <svg class="w-4 h-4 text-brand-400"><use href="/assets/icons/sprite.svg#icon-zap"></use></svg>
                    <span>Sales Reports &amp; CSV</span>
                </a>

                <div class="pt-4 mt-4 border-t border-brand-900/80">
                    <span class="text-[10px] uppercase font-black tracking-widest text-slate-500 px-3">Configuration</span>
                </div>

                <a href="/admin/homepage" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-brand-900 transition-colors <?= str_contains($_SERVER['REQUEST_URI'], '/admin/homepage') ? 'bg-brand-800 text-white' : 'text-slate-300' ?>">
                    <svg class="w-4 h-4 text-brand-400"><use href="/assets/icons/sprite.svg#icon-leaf"></use></svg>
                    <span>Homepage &amp; Carousel</span>
                </a>

                <a href="/admin/delivery" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-brand-900 transition-colors <?= str_contains($_SERVER['REQUEST_URI'], '/admin/delivery') ? 'bg-brand-800 text-white' : 'text-slate-300' ?>">
                    <svg class="w-4 h-4 text-brand-400"><use href="/assets/icons/sprite.svg#icon-circle-check"></use></svg>
                    <span>Delivery &amp; PIN Codes</span>
                </a>

                <a href="/admin/settings" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-brand-900 transition-colors <?= str_contains($_SERVER['REQUEST_URI'], '/admin/settings') ? 'bg-brand-800 text-white' : 'text-slate-300' ?>">
                    <svg class="w-4 h-4 text-brand-400"><use href="/assets/icons/sprite.svg#icon-zap"></use></svg>
                    <span>Store Profile Settings</span>
                </a>

                <a href="/admin/admin-users" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-brand-900 transition-colors <?= str_contains($_SERVER['REQUEST_URI'], '/admin/admin-users') ? 'bg-brand-800 text-white' : 'text-slate-300' ?>">
                    <svg class="w-4 h-4 text-brand-400"><use href="/assets/icons/sprite.svg#icon-user"></use></svg>
                    <span>Admin Team &amp; Roles</span>
                </a>

                <a href="/admin/activity-logs" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-brand-900 transition-colors <?= str_contains($_SERVER['REQUEST_URI'], '/admin/activity-logs') ? 'bg-brand-800 text-white' : 'text-slate-300' ?>">
                    <svg class="w-4 h-4 text-brand-400"><use href="/assets/icons/sprite.svg#icon-shield"></use></svg>
                    <span>Security Audit Logs</span>
                </a>
            </nav>

            <!-- Bottom User Card & Store Link -->
            <div class="p-4 border-t border-brand-900 bg-brand-950">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-white block"><?= e(admin_auth()['name'] ?? 'Admin') ?></span>
                        <span class="text-[10px] uppercase font-bold text-brand-400"><?= e(admin_auth()['role'] ?? 'staff') ?></span>
                    </div>
                    <form action="/admin/logout" method="POST">
                        <?= csrf_field() ?>
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4"><use href="/assets/icons/sprite.svg#icon-close"></use></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 md:pl-64 flex flex-col min-h-screen">
            <!-- Topbar -->
            <header class="h-20 bg-white border-b border-slate-200 px-6 sm:px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs">
                <button @click="sidebarOpen = true" class="md:hidden text-slate-600 hover:text-slate-900 focus:outline-none">
                    <svg class="w-6 h-6"><use href="/assets/icons/sprite.svg#icon-menu"></use></svg>
                </button>

                <div class="text-xs font-bold text-slate-500">
                    Asia/Kolkata: <strong class="text-slate-800"><?= date('d M Y, h:i A') ?></strong>
                </div>

                <div class="flex items-center gap-4">
                    <a href="/" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full bg-brand-50 text-brand-700 hover:bg-brand-100 transition-colors border border-brand-200">
                        <span>View Live Store &nearr;</span>
                    </a>
                </div>
            </header>

            <!-- Body View -->
            <main class="flex-1 p-6 sm:p-8 bg-slate-50">
                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- SweetAlert flash popups in Admin -->
    <?php if ($msg = flash('success')): ?>
    <script>document.addEventListener('DOMContentLoaded', () => Notify.toast(<?= json_encode($msg) ?>, 'success'));</script>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
    <script>document.addEventListener('DOMContentLoaded', () => Notify.toast(<?= json_encode($msg) ?>, 'error'));</script>
    <?php endif; ?>

</body>
</html>
