<?php

return function (PDO $pdo) {
    echo "  - Seeding Settings, Delivery Zones, Categories, and Products...\n";

    // 1. Settings
    $settings = [
        ['key' => 'store_name', 'value' => 'N.A Fresh Fruits & Coconuts', 'type' => 'string', 'description' => 'Business display name'],
        ['key' => 'store_tagline', 'value' => 'FRESH • HEALTHY • NATURAL', 'type' => 'string', 'description' => 'Store header tagline'],
        ['key' => 'contact_phone', 'value' => '+91 98765 43210', 'type' => 'string', 'description' => 'Customer support phone number'],
        ['key' => 'contact_email', 'value' => 'support@nafreshfruits.com', 'type' => 'string', 'description' => 'Store contact email'],
        ['key' => 'store_address', 'value' => 'Shop 4, Green Palm Arcade, Hill Road, Bandra West, Mumbai 400050', 'type' => 'string', 'description' => 'Store retail outlet address'],
        ['key' => 'store_hours', 'value' => '7:00 AM - 10:00 PM (All 7 Days)', 'type' => 'string', 'description' => 'Operating hours'],
        ['key' => 'currency', 'value' => 'INR', 'type' => 'string', 'description' => 'Currency code'],
        ['key' => 'currency_symbol', 'value' => '₹', 'type' => 'string', 'description' => 'Currency symbol'],
        ['key' => 'min_order_amount', 'value' => '80', 'type' => 'number', 'description' => 'Minimum checkout order amount'],
        ['key' => 'default_delivery_charge', 'value' => '30', 'type' => 'number', 'description' => 'Standard local delivery charge'],
        ['key' => 'free_delivery_threshold', 'value' => '250', 'type' => 'number', 'description' => 'Free delivery on orders above this amount'],
        ['key' => 'razorpay_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable Razorpay payment gateway'],
        ['key' => 'razorpay_mode', 'value' => 'test', 'type' => 'string', 'description' => 'Razorpay environment mode (test/live)'],
        ['key' => 'razorpay_key_id', 'value' => 'rzp_test_nafruits2026', 'type' => 'string', 'description' => 'Razorpay Key ID'],
        ['key' => 'razorpay_key_secret', 'value' => 'test_secret_nafruits_mock', 'type' => 'string', 'description' => 'Razorpay Key Secret'],
        ['key' => 'razorpay_webhook_secret', 'value' => 'test_webhook_secret_nafruits', 'type' => 'string', 'description' => 'Razorpay Webhook Secret'],
        ['key' => 'hero_heading', 'value' => 'Fresh tender coconuts harvested daily & delivered chilled to your doorstep', 'type' => 'string', 'description' => 'Homepage hero heading'],
        ['key' => 'hero_subheading', 'value' => '100% natural, no added sugar, rich in electrolytes and packed with hydration.', 'type' => 'string', 'description' => 'Homepage hero subheading'],
        ['key' => 'notice_banner', 'value' => '🎉 Fresh morning batch just arrived! Free express delivery on orders above ₹250.', 'type' => 'string', 'description' => 'Top announcement banner text'],
        ['key' => 'is_store_open', 'value' => '1', 'type' => 'boolean', 'description' => 'Store acceptance of incoming orders'],
    ];

    $setStmt = $pdo->prepare("INSERT INTO `settings` (`key`, `value`, `type`, `description`) 
        VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`), `description`=VALUES(`description`)");
    foreach ($settings as $s) {
        $setStmt->execute([$s['key'], $s['value'], $s['type'], $s['description']]);
    }

    // 2. Delivery Zones
    $zones = [
        ['pincode' => '400050', 'area_name' => 'Bandra West & Bandstand', 'delivery_charge' => 25.00, 'min_order_amount' => 80.00, 'estimated_time' => '25 - 35 mins'],
        ['pincode' => '400051', 'area_name' => 'Bandra East & BKC', 'delivery_charge' => 30.00, 'min_order_amount' => 80.00, 'estimated_time' => '30 - 45 mins'],
        ['pincode' => '400052', 'area_name' => 'Khar West & Danda', 'delivery_charge' => 25.00, 'min_order_amount' => 80.00, 'estimated_time' => '25 - 35 mins'],
        ['pincode' => '400053', 'area_name' => 'Andheri West & Lokhandwala', 'delivery_charge' => 40.00, 'min_order_amount' => 120.00, 'estimated_time' => '35 - 50 mins'],
        ['pincode' => '400054', 'area_name' => 'Santacruz West', 'delivery_charge' => 30.00, 'min_order_amount' => 80.00, 'estimated_time' => '30 - 40 mins'],
        ['pincode' => '400055', 'area_name' => 'Santacruz East & Vakola', 'delivery_charge' => 35.00, 'min_order_amount' => 100.00, 'estimated_time' => '30 - 45 mins'],
        ['pincode' => '400056', 'area_name' => 'Vile Parle West & Juhu', 'delivery_charge' => 35.00, 'min_order_amount' => 100.00, 'estimated_time' => '35 - 45 mins'],
    ];

    $zoneStmt = $pdo->prepare("INSERT INTO `delivery_zones` (`pincode`, `area_name`, `delivery_charge`, `min_order_amount`, `estimated_time`, `is_serviceable`)
        VALUES (?, ?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE `area_name`=VALUES(`area_name`), `delivery_charge`=VALUES(`delivery_charge`), `estimated_time`=VALUES(`estimated_time`)");
    foreach ($zones as $z) {
        $zoneStmt->execute([$z['pincode'], $z['area_name'], $z['delivery_charge'], $z['min_order_amount'], $z['estimated_time']]);
    }

    // 3. Categories (Active coconut categories only; Future categories inactive)
    $categories = [
        ['name' => 'Fresh Coconuts', 'slug' => 'fresh-coconuts', 'description' => 'Daily farm-fresh green tender coconuts rich in natural sweet water and soft malai.', 'sort_order' => 1, 'is_active' => 1],
        ['name' => 'Coconut Delights', 'slug' => 'coconut-delights', 'description' => 'Pure chilled coconut water, scraped malai bowls, and natural malai bars.', 'sort_order' => 2, 'is_active' => 1],
        ['name' => 'Fresh Fruit Juices', 'slug' => 'fresh-fruit-juices', 'description' => 'Future category - currently disabled in store catalogue.', 'sort_order' => 3, 'is_active' => 0], // Inactive per spec
        ['name' => 'Artisanal Kulfi & Bars', 'slug' => 'kulfi-bars', 'description' => 'Future category - currently disabled in store catalogue.', 'sort_order' => 4, 'is_active' => 0], // Inactive per spec
    ];

    $catStmt = $pdo->prepare("INSERT INTO `categories` (`name`, `slug`, `description`, `sort_order`, `is_active`)
        VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `description`=VALUES(`description`), `is_active`=VALUES(`is_active`)");
    foreach ($categories as $c) {
        $catStmt->execute([$c['name'], $c['slug'], $c['description'], $c['sort_order'], $c['is_active']]);
    }

    $coconutsCatId = $pdo->query("SELECT id FROM `categories` WHERE `slug` = 'fresh-coconuts'")->fetchColumn();
    $delightsCatId = $pdo->query("SELECT id FROM `categories` WHERE `slug` = 'coconut-delights'")->fetchColumn();

    // 4. Products matching reference design
    $products = [
        [
            'category_id' => $coconutsCatId,
            'name' => 'Fresh Nariyal Pani',
            'slug' => 'fresh-nariyal-pani',
            'price' => 40.00,
            'sale_price' => null,
            'stock_quantity' => 100,
            'is_in_stock' => 1,
            'is_featured' => 1,
            'is_carousel' => 1,
            'carousel_order' => 1,
            'image_url' => '/assets/images/nariyal-pani.webp',
            'short_description' => '100% Natural | No Added Sugar | Fresh & Healthy',
            'description' => 'Naturally sweet, nutrient-dense tender coconut water straight from freshly opened young green coconuts. Packed with potassium, magnesium, and essential hydration electrolytes with zero preservatives.',
            'sort_order' => 1,
            'variants' => [
                ['name' => 'Standard Tender Coconut', 'price' => 40.00, 'stock' => 60],
                ['name' => 'Large Sweet Water (400ml+)', 'price' => 50.00, 'stock' => 40],
            ]
        ],
        [
            'category_id' => $delightsCatId,
            'name' => 'Coconut Water With Ice',
            'slug' => 'coconut-water-with-ice',
            'price' => 50.00,
            'sale_price' => null,
            'stock_quantity' => 75,
            'is_in_stock' => 1,
            'is_featured' => 1,
            'is_carousel' => 1,
            'carousel_order' => 2,
            'image_url' => '/assets/images/coconut-water-ice.webp',
            'short_description' => 'Served ice cold in hygienic sealed cups',
            'description' => 'Crisp, rejuvenating fresh coconut water poured over purified ice cubes. Delivered in food-grade spill-proof cups with eco-friendly paper straws for instant refreshness.',
            'sort_order' => 2,
            'variants' => [
                ['name' => 'Regular Chilled Cup (300ml)', 'price' => 50.00, 'stock' => 45],
                ['name' => 'Jumbo Chilled Bottle (500ml)', 'price' => 85.00, 'stock' => 30],
            ]
        ],
        [
            'category_id' => $delightsCatId,
            'name' => 'Coconut Malai Bar',
            'slug' => 'coconut-malai-bar',
            'price' => 60.00,
            'sale_price' => null,
            'stock_quantity' => 50,
            'is_in_stock' => 1,
            'is_featured' => 1,
            'is_carousel' => 1,
            'carousel_order' => 3,
            'image_url' => '/assets/images/coconut-malai-bar.webp',
            'short_description' => 'Rich creamy tender coconut bar with real malai chunks',
            'description' => 'Handcrafted pure tender coconut ice bar made exclusively with fresh coconut water, thick creamy malai flesh, and subtle natural sweetness. No chemical additives or artificial thickening agents.',
            'sort_order' => 3,
            'variants' => [
                ['name' => 'Single Gourmet Bar', 'price' => 60.00, 'stock' => 30],
                ['name' => 'Twin Pack (2 Bars)', 'price' => 110.00, 'stock' => 20],
            ]
        ],
        [
            'category_id' => $delightsCatId,
            'name' => 'Fresh Coconut Malai Bowl',
            'slug' => 'fresh-coconut-malai-bowl',
            'price' => 50.00,
            'sale_price' => 45.00,
            'stock_quantity' => 40,
            'is_in_stock' => 1,
            'is_featured' => 1,
            'is_carousel' => 1,
            'carousel_order' => 4,
            'image_url' => '/assets/images/coconut-malai.webp',
            'short_description' => 'Soft, melt-in-mouth creamy malai scooped fresh',
            'description' => 'Delicate, buttery tender coconut meat lovingly scooped from premium green coconuts. High in medium-chain triglycerides (MCTs), healthy dietary fiber, and pure tropical flavor.',
            'sort_order' => 4,
            'variants' => [
                ['name' => 'Small Bowl (150g)', 'price' => 45.00, 'stock' => 25],
                ['name' => 'Family Bowl (300g)', 'price' => 85.00, 'stock' => 15],
            ]
        ],
        [
            'category_id' => $coconutsCatId,
            'name' => 'Pollachi Royal Tender Coconut',
            'slug' => 'pollachi-royal-tender-coconut',
            'price' => 65.00,
            'sale_price' => null,
            'stock_quantity' => 60,
            'is_in_stock' => 1,
            'is_featured' => 1,
            'is_carousel' => 0,
            'carousel_order' => 5,
            'image_url' => '/assets/images/hero-coconut.webp',
            'short_description' => 'Giant premium sweet tender coconut from Pollachi, Tamil Nadu',
            'description' => 'Celebrated for its extra sweetness and abundant water capacity (typically 450ml to 550ml). Hand-selected directly from organic coconut estates in Pollachi.',
            'sort_order' => 5,
            'variants' => [
                ['name' => 'Jumbo Whole Coconut', 'price' => 65.00, 'stock' => 35],
                ['name' => 'Pre-cut with Easy-Open Cap', 'price' => 70.00, 'stock' => 25],
            ]
        ]
    ];

    $prodStmt = $pdo->prepare("INSERT INTO `products` 
        (`category_id`, `name`, `slug`, `price`, `sale_price`, `stock_quantity`, `is_in_stock`, `is_featured`, `is_carousel`, `carousel_order`, `image_url`, `short_description`, `description`, `sort_order`, `is_active`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `price`=VALUES(`price`), `image_url`=VALUES(`image_url`), `is_carousel`=VALUES(`is_carousel`), `carousel_order`=VALUES(`carousel_order`), `short_description`=VALUES(`short_description`), `description`=VALUES(`description`)");

    $varStmt = $pdo->prepare("INSERT INTO `product_variants` (`product_id`, `name`, `sku`, `price`, `stock_quantity`, `is_active`)
        VALUES (?, ?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE `price`=VALUES(`price`), `stock_quantity`=VALUES(`stock_quantity`)");

    foreach ($products as $p) {
        $prodStmt->execute([
            $p['category_id'],
            $p['name'],
            $p['slug'],
            $p['price'],
            $p['sale_price'],
            $p['stock_quantity'],
            $p['is_in_stock'],
            $p['is_featured'],
            $p['is_carousel'],
            $p['carousel_order'],
            $p['image_url'],
            $p['short_description'],
            $p['description'],
            $p['sort_order']
        ]);
        
        $productId = $pdo->query("SELECT id FROM `products` WHERE `slug` = '{$p['slug']}'")->fetchColumn();
        if ($productId && !empty($p['variants'])) {
            foreach ($p['variants'] as $v) {
                $sku = strtoupper(substr($p['slug'], 0, 3)) . '-' . rand(100, 999);
                $varStmt->execute([$productId, $v['name'], $sku, $v['price'], $v['stock']]);
            }
        }
    }

    // 5. Welcome Coupons
    $coupons = [
        [
            'code' => 'FRESH10',
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'min_order_amount' => 100.00,
            'max_discount_amount' => 50.00,
            'usage_limit' => 500,
            'is_active' => 1,
            'starts_at' => date('Y-m-d 00:00:00'),
            'expires_at' => date('Y-m-d 23:59:59', strtotime('+1 year'))
        ],
        [
            'code' => 'COCONUT20',
            'discount_type' => 'fixed',
            'discount_value' => 20.00,
            'min_order_amount' => 120.00,
            'max_discount_amount' => null,
            'usage_limit' => 300,
            'is_active' => 1,
            'starts_at' => date('Y-m-d 00:00:00'),
            'expires_at' => date('Y-m-d 23:59:59', strtotime('+1 year'))
        ]
    ];

    $coupStmt = $pdo->prepare("INSERT INTO `coupons` (`code`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount_amount`, `usage_limit`, `is_active`, `starts_at`, `expires_at`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE `discount_value`=VALUES(`discount_value`), `is_active`=VALUES(`is_active`)");

    foreach ($coupons as $coup) {
        $coupStmt->execute([
            $coup['code'],
            $coup['discount_type'],
            $coup['discount_value'],
            $coup['min_order_amount'],
            $coup['max_discount_amount'],
            $coup['usage_limit'],
            $coup['is_active'],
            $coup['starts_at'],
            $coup['expires_at']
        ]);
    }
};
