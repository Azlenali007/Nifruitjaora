<?php

return function (PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
      `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `key` VARCHAR(100) NOT NULL UNIQUE,
      `value` TEXT NULL,
      `type` VARCHAR(50) DEFAULT 'string',
      `description` VARCHAR(255) NULL,
      `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `delivery_zones` (
      `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `pincode` VARCHAR(10) NOT NULL UNIQUE,
      `area_name` VARCHAR(100) NOT NULL,
      `delivery_charge` DECIMAL(10,2) NOT NULL DEFAULT 30.00,
      `min_order_amount` DECIMAL(10,2) NOT NULL DEFAULT 100.00,
      `is_serviceable` TINYINT(1) DEFAULT 1,
      `estimated_time` VARCHAR(100) DEFAULT '30 - 45 mins',
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
};
