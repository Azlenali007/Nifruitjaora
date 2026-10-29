<?php

return function (PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `order_items` (
      `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `order_id` INT UNSIGNED NOT NULL,
      `product_id` INT UNSIGNED NULL,
      `variant_id` INT UNSIGNED NULL,
      `product_name` VARCHAR(191) NOT NULL,
      `variant_name` VARCHAR(100) NULL,
      `unit_price` DECIMAL(10,2) NOT NULL,
      `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
      `total_price` DECIMAL(10,2) NOT NULL,
      `product_image` VARCHAR(255) NULL,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      INDEX `idx_order_items_order` (`order_id`),
      FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
};
