<?php

return function (PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `addresses` (
      `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `user_id` INT UNSIGNED NOT NULL,
      `recipient_name` VARCHAR(150) NOT NULL,
      `phone` VARCHAR(20) NOT NULL,
      `address_line1` VARCHAR(255) NOT NULL,
      `address_line2` VARCHAR(255) NULL,
      `landmark` VARCHAR(150) NULL,
      `city` VARCHAR(100) NOT NULL DEFAULT 'Mumbai',
      `state` VARCHAR(100) NOT NULL DEFAULT 'Maharashtra',
      `pincode` VARCHAR(10) NOT NULL,
      `is_default` TINYINT(1) DEFAULT 0,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX `idx_addresses_user` (`user_id`),
      FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
};
