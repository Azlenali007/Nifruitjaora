<?php

return function (PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `orders` (
      `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `order_number` VARCHAR(50) NOT NULL UNIQUE,
      `user_id` INT UNSIGNED NULL,
      `is_guest` TINYINT(1) DEFAULT 1,
      `guest_name` VARCHAR(150) NOT NULL,
      `guest_email` VARCHAR(191) NULL,
      `guest_phone` VARCHAR(20) NOT NULL,
      `subtotal` DECIMAL(10,2) NOT NULL,
      `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
      `delivery_charge` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
      `total_amount` DECIMAL(10,2) NOT NULL,
      `coupon_id` INT UNSIGNED NULL,
      `coupon_code` VARCHAR(50) NULL,
      `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
      `fulfillment_status` ENUM('pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
      `delivery_address` TEXT NOT NULL,
      `delivery_pincode` VARCHAR(10) NOT NULL,
      `delivery_instructions` VARCHAR(255) NULL,
      `admin_notes` TEXT NULL,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX `idx_orders_user` (`user_id`),
      INDEX `idx_orders_status` (`payment_status`, `fulfillment_status`),
      INDEX `idx_orders_created` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
};
