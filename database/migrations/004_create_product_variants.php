<?php

return function (PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `product_variants` (
      `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `product_id` INT UNSIGNED NOT NULL,
      `name` VARCHAR(100) NOT NULL,
      `sku` VARCHAR(100) NULL,
      `price` DECIMAL(10,2) NOT NULL,
      `stock_quantity` INT NOT NULL DEFAULT 50,
      `is_active` TINYINT(1) DEFAULT 1,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX `idx_variants_product` (`product_id`),
      FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
};
