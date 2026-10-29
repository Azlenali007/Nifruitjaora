<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= e($title ?? 'N.A Fresh Fruits & Coconuts') ?></title>
    <meta name="description" content="N.A Fresh Fruits & Coconuts - Daily farm fresh tender coconuts, chilled coconut water, and artisanal coconut malai delivered fresh.">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="/assets/css/app.css">
    
    <!-- SweetAlert2 CSS & Script -->
    <script src="/assets/js/sweetalert2.all.min.js"></script>
    <script src="/assets/js/notifications.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="/assets/js/alpine.min.js"></script>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/carousel.js"></script>
    <script src="/assets/js/cart.js"></script>
    
    <!-- Razorpay Checkout SDK -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen font-sans antialiased selection:bg-brand-500 selection:text-white"
      x-data="{ mobileMenuOpen: false, searchOpen: false }">

    <!-- Top Announcement Bar -->
    <?php $notice = \App\Models\Setting::get('notice_banner'); ?>
    <?php if ($notice): ?>
    <div class="bg-brand-900 text-brand-100 px-4 py-2 text-xs font-semibold text-center tracking-wide flex items-center justify-center gap-2">
        <span><?= e($notice) ?></span>
    </div>
    <?php endif; ?>

    <!-- Navigation Header (Modern, High Contrast, Matches Visual Reference) -->
    <header class="sticky top-0 z-40 bg-brand-950/95 backdrop-blur-md border-b border-brand-800/50 text-white transition-all shadow-lg">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18 sm:h-20">
                
                <!-- Brand Logo (High Visibility, Never Clipped or Overlapped) -->
                <a href="/" class="flex items-center gap-2 group focus:outline-none shrink-0 py-1" aria-label="N.A Fresh Fruits & Coconuts">
                    <img src="/assets/images/logo.svg" 
                         alt="N.A Fresh Fruits & Coconuts" 
                         class="h-9 sm:h-11 md:h-12 w-auto max-w-[190px] xs:max-w-[220px] sm:max-w-[270px] md:max-w-none object-contain transition-transform group-hover:scale-102 filter drop-shadow">
                </a>

                <!-- Desktop Navigation Links (Modern Spacious Treatment) -->
                <nav class="hidden md:flex items-center gap-1 lg:gap-2 text-sm font-semibold tracking-wide">
                    <a href="/" class="px-3.5 py-2 rounded-full text-white hover:text-brand-300 hover:bg-brand-900/60 transition-all">Home</a>
                    <a href="/products" class="px-3.5 py-2 rounded-full text-slate-200 hover:text-brand-300 hover:bg-brand-900/60 transition-all">Coconut Catalogue</a>
                    <a href="/track" class="px-3.5 py-2 rounded-full text-slate-200 hover:text-brand-300 hover:bg-brand-900/60 transition-all">Track Order</a>
                    <?php if (auth()): ?>
                        <a href="/my-orders" class="px-3.5 py-2 rounded-full text-slate-200 hover:text-brand-300 hover:bg-brand-900/60 transition-all">My Orders</a>
                    <?php endif; ?>
                </nav>

                <!-- Action Controls: Search, Cart, Account, Hamburger -->
                <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                    <!-- Search Button -->
                    <button @click="searchOpen = true" type="button" aria-label="Search coconuts"
                            class="p-2 sm:p-2.5 rounded-full text-slate-200 hover:text-white hover:bg-brand-800/70 transition-colors focus:outline-none">
                        <svg class="w-5 h-5"><use href="/assets/icons/sprite.svg#icon-search"></use></svg>
                    </button>

                    <!-- Cart Icon with Live Reactive Green Badge (Matches Mockup) -->
                    <?php 
                        $cartItemsCount = $cartSummary['total_items'] ?? 0;
                    ?>
                    <a href="/cart" aria-label="Shopping Cart"
                       class="relative p-2 sm:p-2.5 rounded-full text-white hover:bg-brand-800/70 transition-colors focus:outline-none">
                        <svg class="w-6 h-6"><use href="/assets/icons/sprite.svg#icon-cart"></use></svg>
                        <span id="cart-badge" 
                              class="<?= $cartItemsCount > 0 ? '' : 'hidden' ?> absolute -top-0.5 -right-0.5 bg-brand-500 text-white font-black text-[10px] sm:text-[11px] h-5 min-w-[20px] px-1 rounded-full flex items-center justify-center shadow-lg border-2 border-brand-950">
                            <?= $cartItemsCount ?>
                        </span>
                    </a>

                    <!-- User Account / Login Button (Desktop) -->
                    <?php if (auth()): ?>
                    <a href="/profile" class="hidden sm:inline-flex items-center gap-2 text-xs font-bold px-4 py-2 rounded-full bg-brand-800/80 hover:bg-brand-700 text-white transition-colors border border-brand-700/50">
                        <svg class="w-4 h-4"><use href="/assets/icons/sprite.svg#icon-user"></use></svg>
                        <span><?= e(explode(' ', auth()['name'])[0]) ?></span>
                    </a>
                    <?php else: ?>
                    <a href="/login" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-bold px-4 py-2 rounded-full bg-white text-brand-950 hover:bg-brand-100 transition-colors shadow-sm">
                        <span>Sign In</span>
                    </a>
                    <?php endif; ?>

                    <!-- Mobile Hamburger Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" aria-label="Toggle menu"
                            class="md:hidden p-2 rounded-xl text-slate-200 hover:text-white hover:bg-brand-800/70 focus:outline-none">
                        <svg class="w-6 h-6" x-show="!mobileMenuOpen"><use href="/assets/icons/sprite.svg#icon-menu"></use></svg>
                        <svg class="w-6 h-6" x-show="mobileMenuOpen" style="display:none;"><use href="/assets/icons/sprite.svg#icon-close"></use></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modern Mobile Drawer Navigation -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @click.away="mobileMenuOpen = false"
             class="md:hidden border-t border-brand-800/60 bg-brand-950/98 backdrop-blur-xl px-4 pt-4 pb-6 space-y-2 shadow-2xl" 
             style="display:none;">
            <a href="/" @click="mobileMenuOpen = false" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-base font-bold text-white hover:bg-brand-800/50">
                <span>🌴 Home</span>
            </a>
            <a href="/products" @click="mobileMenuOpen = false" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-base font-semibold text-slate-200 hover:bg-brand-800/50">
                <span>🥥 Coconut Catalogue</span>
            </a>
            <a href="/track" @click="mobileMenuOpen = false" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-base font-semibold text-slate-200 hover:bg-brand-800/50">
                <span>🚚 Track Order</span>
            </a>
            <a href="/cart" @click="mobileMenuOpen = false" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-base font-semibold text-slate-200 hover:bg-brand-800/50">
                <span>🛒 Shopping Cart</span>
                <span class="px-2 py-0.5 rounded-full bg-brand-500 text-white font-extrabold text-xs"><?= $cartItemsCount ?></span>
            </a>

            <!-- Store Hours & Support Info in Drawer -->
            <div class="px-3.5 py-3 rounded-xl bg-brand-900/60 border border-brand-800/60 text-xs text-brand-200 space-y-1">
                <div class="font-bold text-white flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Store Open: 7:00 AM &ndash; 10:00 PM</span>
                </div>
                <div class="text-[11px] text-brand-300">Fast local delivery in Bandra, Khar &amp; Andheri</div>
            </div>

            <div class="pt-2 border-t border-brand-800/50 flex flex-col gap-2">
                <?php if (auth()): ?>
                    <a href="/profile" class="block px-3.5 py-2.5 rounded-xl text-base font-semibold text-brand-300 hover:bg-brand-800/50">My Profile (<?= e(auth()['name']) ?>)</a>
                    <a href="/my-orders" class="block px-3.5 py-2.5 rounded-xl text-base font-semibold text-slate-200 hover:bg-brand-800/50">My Orders</a>
                    <form action="/logout" method="POST" class="pt-1">
                        <?= csrf_field() ?>
                        <button type="submit" class="w-full text-left px-3.5 py-2 text-sm text-red-400 font-semibold hover:bg-brand-800/50 rounded-xl">Sign Out</button>
                    </form>
                <?php else: ?>
                    <a href="/login" class="block w-full text-center px-4 py-3 rounded-full bg-gradient-to-r from-brand-600 to-brand-500 font-extrabold text-white shadow-lg shadow-brand-600/30">Sign In / Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Search Modal with Quick Suggestions -->
    <div x-show="searchOpen" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-start justify-center p-4 pt-16 sm:pt-20" style="display:none;">
        <div @click.away="searchOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl relative border border-slate-100">
            <button @click="searchOpen = false" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 p-1 rounded-full hover:bg-slate-100">
                <svg class="w-5 h-5"><use href="/assets/icons/sprite.svg#icon-close"></use></svg>
            </button>
            <h3 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-600"><use href="/assets/icons/sprite.svg#icon-search"></use></svg>
                <span>Search Coconut Store</span>
            </h3>
            <form action="/products" method="GET">
                <div class="relative">
                    <input type="text" name="search" placeholder="Search coconut water, malai bar, tender coconut..." 
                           class="w-full pl-4 pr-12 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
                    <button type="submit" class="absolute right-2 top-2 bottom-2 px-4 bg-brand-600 text-white rounded-lg font-bold text-sm hover:bg-brand-700">
                        Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Content Area -->
    <main class="flex-grow">
        <?= $content ?>
    </main>

    <!-- Customer Footer -->
    <footer class="bg-slate-900 text-slate-300 border-t border-slate-800 pt-16 pb-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- Col 1: Brand Info -->
                <div class="space-y-4 md:col-span-1">
                    <img src="/assets/images/logo.svg" alt="N.A Fresh Fruits & Coconuts" class="h-10 w-auto">
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Pure 100% natural tender coconuts, revitalizing fresh coconut water, and artisanal coconut delicacies harvested daily.
                    </p>
                    <div class="flex items-center gap-3 pt-2 text-xs font-semibold text-brand-400">
                        <span>Daily 7 AM - 10 PM</span>
                        <span>•</span>
                        <span>Mumbai Local Delivery</span>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div>
                    <h4 class="text-white text-sm font-bold tracking-wider uppercase mb-4">Quick Navigation</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="/" class="hover:text-brand-400 transition-colors">Home Store</a></li>
                        <li><a href="/products" class="hover:text-brand-400 transition-colors">Coconut Catalogue</a></li>
                        <li><a href="/cart" class="hover:text-brand-400 transition-colors">Shopping Cart</a></li>
                        <li><a href="/track" class="hover:text-brand-400 transition-colors">Track Your Order</a></li>
                        <li><a href="/login" class="hover:text-brand-400 transition-colors">Customer Login</a></li>
                    </ul>
                </div>

                <!-- Col 3: Delivery Areas -->
                <div>
                    <h4 class="text-white text-sm font-bold tracking-wider uppercase mb-4">Serviceable Areas</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li>Bandra West &amp; Bandstand (400050)</li>
                        <li>Khar West (400052)</li>
                        <li>Santacruz West (400054)</li>
                        <li>Andheri West (400053)</li>
                        <li>Juhu &amp; Vile Parle (400056)</li>
                    </ul>
                </div>

                <!-- Col 4: Contact & Store -->
                <div>
                    <h4 class="text-white text-sm font-bold tracking-wider uppercase mb-4">Store Location</h4>
                    <address class="not-italic text-xs text-slate-400 space-y-2 leading-relaxed">
                        <p><strong class="text-white">N.A Fresh Fruits &amp; Coconuts</strong></p>
                        <p>Shop 4, Green Palm Arcade, Hill Road, Bandra West, Mumbai, MH 400050</p>
                        <p class="pt-2 text-slate-200">Phone: <a href="tel:+919876543210" class="hover:text-brand-400 font-bold">+91 98765 43210</a></p>
                        <p class="text-slate-200">Email: support@nafreshfruits.com</p>
                    </address>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; <?= date('Y') ?> N.A Fresh Fruits & Coconuts. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <span>100% Farm Natural</span>
                    <span>Hygienic Packaging</span>
                    <a href="/admin/login" class="text-slate-600 hover:text-slate-400 transition-colors">Admin Portal</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- SweetAlert flash messages -->
    <?php if ($msg = flash('success')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Notify.toast(<?= json_encode($msg) ?>, 'success');
        });
    </script>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Notify.toast(<?= json_encode($msg) ?>, 'error');
        });
    </script>
    <?php endif; ?>
    <?php if ($msg = flash('warning')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Notify.toast(<?= json_encode($msg) ?>, 'warning');
        });
    </script>
    <?php endif; ?>

</body>
</html>
