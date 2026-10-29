<?php

use App\Core\Database;

return function (PDO $pdo) {
    echo "  - Seeding Admin Users and Permissions...\n";

    // 1. Permissions
    $permissions = [
        ['name' => 'Manage Products', 'slug' => 'manage_products', 'description' => 'Create, edit, delete products and variants'],
        ['name' => 'Manage Categories', 'slug' => 'manage_categories', 'description' => 'Manage category hierarchy and activation'],
        ['name' => 'Manage Orders', 'slug' => 'manage_orders', 'description' => 'View orders, update fulfillment and notes'],
        ['name' => 'Manage Payments', 'slug' => 'manage_payments', 'description' => 'View payments, configure Razorpay and refunds'],
        ['name' => 'Manage Coupons', 'slug' => 'manage_coupons', 'description' => 'Create and configure discount coupons'],
        ['name' => 'Manage Delivery', 'slug' => 'manage_delivery', 'description' => 'Configure delivery charges and PIN codes'],
        ['name' => 'Manage Settings', 'slug' => 'manage_settings', 'description' => 'Store general settings and homepage content'],
        ['name' => 'Manage Admins', 'slug' => 'manage_admins', 'description' => 'Manage admin accounts and roles'],
        ['name' => 'View Reports', 'slug' => 'view_reports', 'description' => 'Access revenue and sales reports'],
    ];

    $permStmt = $pdo->prepare("INSERT INTO `permissions` (`name`, `slug`, `description`) 
        VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `description`=VALUES(`description`)");
    foreach ($permissions as $p) {
        $permStmt->execute([$p['name'], $p['slug'], $p['description']]);
    }

    // Assign all to superadmin
    $allPerms = $pdo->query("SELECT id FROM `permissions`")->fetchAll(PDO::FETCH_COLUMN);
    $rolePermStmt = $pdo->prepare("INSERT IGNORE INTO `role_permissions` (`role`, `permission_id`) VALUES (?, ?)");
    foreach ($allPerms as $pid) {
        $rolePermStmt->execute(['superadmin', $pid]);
    }
    // Assign manager perms
    $managerSlugs = ['manage_products', 'manage_categories', 'manage_orders', 'manage_coupons', 'manage_delivery', 'view_reports'];
    $mgrStmt = $pdo->prepare("SELECT id FROM `permissions` WHERE `slug` IN ('" . implode("','", $managerSlugs) . "')");
    $mgrStmt->execute();
    $mgrPerms = $mgrStmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($mgrPerms as $pid) {
        $rolePermStmt->execute(['manager', $pid]);
    }

    // 2. Default Administrator
    $adminEmail = 'admin@nafreshfruits.com';
    $adminCheck = $pdo->prepare("SELECT COUNT(*) FROM `admin_users` WHERE `email` = ?");
    $adminCheck->execute([$adminEmail]);
    if ($adminCheck->fetchColumn() == 0) {
        $adminHash = password_hash('Admin@123456', PASSWORD_BCRYPT);
        $insertAdmin = $pdo->prepare("INSERT INTO `admin_users` (`name`, `email`, `password_hash`, `role`, `status`) 
            VALUES (?, ?, ?, 'superadmin', 'active')");
        $insertAdmin->execute(['Store Administrator', $adminEmail, $adminHash]);
    }
};
