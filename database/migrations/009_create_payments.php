<?php

return function (PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `payments` (
      `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `order_id` INT UNSIGNED NOT NULL,
      `payment_method` VARCHAR(50) NOT NULL DEFAULT 'razorpay',
      `razorpay_order_id` VARCHAR(100) NULL UNIQUE,
      `razorpay_payment_id` VARCHAR(100) NULL UNIQUE,
      `razorpay_signature` VARCHAR(255) NULL,
      `amount` DECIMAL(10,2) NOT NULL,
      `currency` VARCHAR(10) NOT NULL DEFAULT 'INR',
      `status` ENUM('created', 'authorized', 'captured', 'failed', 'refunded') NOT NULL DEFAULT 'created',
      `refund_id` VARCHAR(100) NULL,
      `refund_amount` DECIMAL(10,2) DEFAULT 0.00,
      `error_code` VARCHAR(100) NULL,
      `error_description` TEXT NULL,
      `raw_response` TEXT NULL,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX `idx_payments_order` (`order_id`),
      FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
};
