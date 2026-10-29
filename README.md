# N.A Fresh Fruits & Coconuts — Production E-Commerce Platform

A production-ready e-commerce web platform for **N.A Fresh Fruits & Coconuts**, replicating the visual reference mobile design (`/public/assets/aistudio/8654E9E7-312D-476F-BAFC-07111F522CFC_0688375b.png`) with tender green coconuts, natural palm aesthetics, frosted glass cards, active carousel ordering, instant cart, Razorpay payments, customer accounts, and a comprehensive administrative back-office.

---

## 🥥 Architecture & Technology Stack

- **Backend Runtime:** Core PHP 8.2+ (CLI & Built-in Server with PDO MySQL)
- **Database:** MariaDB / MySQL 8+ with prepared statements, strict transactions, foreign keys, and indexes
- **Frontend Core:** HTML5, Modern CSS3 with Tailwind CSS (compiled via standalone standalone CLI)
- **Client Scripting:** Lightweight Alpine.js, SweetAlert2 modals, Chart.js analytics, Lucide vector icons
- **Payment Processing:** Server-verified Razorpay Gateway (HMAC-SHA256 signature verification, paise integer conversion, timing attack resistance via `hash_equals()`)
- **Zero TypeScript Policy:** **Strictly 0 `.ts` and 0 `.tsx` files** across the entire project repository.

---

## 🚀 Quick Start & Running the Platform

### Dev Server
The development environment automatically manages MariaDB, runs migrations, seeds initial data, boots the PHP server, and proxies to the AI Studio preview port 3000:

```bash
npm run dev
# or
node scripts/dev-server.js
```

### Build Assets
Compiles Tailwind CSS from source:

```bash
npm run build
```

### Run Verification & Tests
Runs the complete test suite checking the Zero TypeScript policy, database schemas, and all 31 assertions:

```bash
npm test
# or
php scripts/verify-project.php
```

---

## 🔑 Administrative Access & Credentials

- **Admin Login Portal:** `/admin/login`
- **Superadmin Email:** `admin@nafreshfruits.com`
- **Default Password:** `Admin@123456`
- **Privileges:** Full superadmin access to Dashboard Metrics, Orders, Catalog, Coupons, Delivery Zones, Store Settings, and Activity Audit Logs.

---

## 🎟️ Active Coupons & Promotion Codes

| Coupon Code | Discount | Terms |
|-------------|----------|-------|
| `FRESH10`   | 10% OFF  | Valid on orders above ₹150 (Max ₹50) |
| `COCONUT20` | ₹20 OFF  | Instant flat discount on orders above ₹100 |

---

## 🚚 Serviceable Delivery Areas (Mumbai Suburbs)

- `400050` — Bandra West & Bandstand (₹25 delivery, 25-35 mins)
- `400051` — Bandra East & BKC (₹30 delivery, 30-45 mins)
- `400052` — Khar West & Danda (₹25 delivery, 25-35 mins)
- `400053` — Andheri West & Lokhandwala (₹40 delivery, 35-50 mins)
- `400054` — Santacruz West (₹30 delivery, 30-40 mins)
- `400055` — Santacruz East & Vakola (₹35 delivery, 30-45 mins)
- `400056` — Vile Parle West & Juhu (₹35 delivery, 35-45 mins)

*Free Delivery Threshold:* All orders of ₹250 or more qualify for automatic free express delivery.

---

## 🛒 Customer Storefront Features

1. **Exact Visual Alignment with Attached Mockup:**
   - Dark forest green coconut palm background (`#0A1D13`) with lush canopy depth.
   - Brand header with `N.A Fresh Fruits & Coconuts`, tagline `FRESH • HEALTHY • NATURAL`, and live cart pill.
   - Interactive hero carousel centered on fresh tender coconuts with left/right arrows, product cards, price tags, quantity incrementors, and direct "Order Now" action.
   - 4-pill trust badges: `100% Natural`, `No Preservatives`, `Fresh Daily Coconuts`, `Healthy & Refreshing`.
   - Bottom category navigation showcasing fresh coconut products.
2. **Catalog & Product Variants:**
   - Active products: Tender Coconuts, Chilled Coconut Water Bottles, Scraped Malai Bowls, and Natural Coconut Malai Bars.
   - Future categories (Juices, Kulfi) safely archived as inactive until launch.
3. **Shopping Cart & Checkout Flow:**
   - Slide-over / dedicated cart with live quantity modification and subtotal updates.
   - Guest and registered customer checkout support.
   - PIN code delivery validation with dynamic fee computation.
   - Coupon discount application with instant calculation.
4. **Razorpay Payment Integration:**
   - Seamless checkout popup modal.
   - Cryptographic server-side verification using HMAC-SHA256 signature verification.
   - Instant order confirmation and secure token-based order tracking (`/orders/track/{token}`).

---

## 📊 Administration Back-Office

- **Live Analytics Dashboard:**
  - Total revenue, completed orders, active customers, average order value.
  - Interactive Chart.js sales trend line chart and category distribution donut chart.
  - Recent orders feed with quick status updating.
- **Product & Inventory Management:**
  - Add, edit, toggle visibility, and update stock counts.
  - Multiple image upload support with thumbnail generation.
- **Order Management:**
  - Full order lifecycle: `pending` → `confirmed` → `processing` → `out_for_delivery` → `delivered` (or `cancelled`).
  - Line items breakdown, customer address, payment reference IDs, and printable invoice layout.
- **Delivery Zones & Settings:**
  - Dynamic PIN code enable/disable, base rates, minimum order constraints.
  - Store status toggle (Open/Closed), operational hours, and Razorpay API key configuration.
- **Security & Audit Logs:**
  - Activity audit logging tracking admin actions with IP and timestamp records.
  - Role-based permissions and session timeout protection.

---

## 📁 Project Directory Structure

```
├── app/
│   ├── Core/               # Database, Router, View, Session, Controller
│   ├── Controllers/        # Customer controllers
│   │   └── Admin/          # Admin back-office controllers
│   ├── Helpers/            # functions.php, validation.php, response.php
│   ├── Middleware/         # CSRF, Auth, AdminAuth, RateLimiter
│   ├── Models/             # Product, Order, User, Cart, Setting, Payment, etc.
│   └── Services/           # RazorpayService, CartService, OrderService, etc.
├── config/                 # app.php, database.php, razorpay.php, security.php
├── database/
│   ├── migrations/         # 14 schema migration files
│   ├── seeds/              # AdminSeeder, SettingsSeeder
│   └── schema.sql          # Clean full schema reference
├── public/
│   ├── assets/
│   │   ├── css/            # Compiled tailwind styles (style.css)
│   │   ├── js/             # app.js, carousel.js, cart.js, checkout.js, etc.
│   │   ├── vendor/         # Alpine.js, SweetAlert2, Chart.js
│   │   └── images/         # Hero coconut, product images, brand icons
│   └── index.php           # Front controller entry point
├── routes/                 # web.php, admin.php, api.php
├── scripts/                # dev-server.js, build-assets.js, lint.js, verify-project.php
├── tests/                  # 7 Test suites (Auth, Product, Cart, Checkout, Razorpay, Admin, Order)
├── views/                  # Customer and Admin PHP templates
└── package.json            # Node scripts and dev runner
```

---

## 🔒 Verification & Compliance
- **PHP Syntax:** All 74 PHP files verified via `php -l`.
- **Zero TypeScript:** Verified 0 `.ts` and 0 `.tsx` files in entire workspace.
- **Build Status:** Verified passing via `node scripts/lint.js` and `scripts/verify-project.php`.
