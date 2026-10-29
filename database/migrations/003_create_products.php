<?php

return function (PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `products` (
      `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `category_id` INT UNSIGNED NULL,
      `name` VARCHAR(191) NOT NULL,
      `slug` VARCHAR(191) NOT NULL UNIQUE,
      `description` TEXT NULL,
      `short_description` VARCHAR(255) NULL,
      `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
      `sale_price` DECIMAL(10,2) NULL,
      `stock_quantity` INT NOT NULL DEFAULT 50,
      `is_in_stock` TINYINT(1) DEFAULT 1,
      `is_featured` TINYINT(1) DEFAULT 0,
      `is_carousel` TINYINT(1) DEFAULT 0,
      `carousel_order` INT DEFAULT 0,
      `image_url` VARCHAR(255) NOT NULL,
      `sort_order` INT DEFAULT 0,
      `is_active` TINYINT(1) DEFAULT 1,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX `idx_products_active` (`is_active`, `is_in_stock`),
      INDEX `idx_products_carousel` (`is_carousel`, `carousel_order`),
      FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
};
