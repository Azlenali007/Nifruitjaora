<?php

return function (PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `coupons` (
      `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `code` VARCHAR(50) NOT NULL UNIQUE,
      `discount_type` ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage',
      `discount_value` DECIMAL(10,2) NOT NULL,
      `min_order_amount` DECIMAL(10,2) DEFAULT 0.00,
      `max_discount_amount` DECIMAL(10,2) NULL,
      `usage_limit` INT NULL,
      `used_count` INT DEFAULT 0,
      `is_active` TINYINT(1) DEFAULT 1,
      `starts_at` DATETIME NULL,
      `expires_at` DATETIME NULL,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `coupon_usage` (
      `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `coupon_id` INT UNSIGNED NOT NULL,
      `user_id` INT UNSIGNED NULL,
      `order_id` INT UNSIGNED NOT NULL,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`coupon_id`) REFERENCES `coupons`(`id`) ON DELETE CASCADE,
      FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
};
