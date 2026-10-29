<?php

return function (PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `activity_logs` (
      `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `admin_id` INT UNSIGNED NULL,
      `action` VARCHAR(100) NOT NULL,
      `entity_type` VARCHAR(100) NULL,
      `entity_id` INT UNSIGNED NULL,
      `description` TEXT NOT NULL,
      `ip_address` VARCHAR(50) NULL,
      `user_agent` VARCHAR(255) NULL,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      INDEX `idx_activity_admin` (`admin_id`),
      INDEX `idx_activity_created` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `password_resets` (
      `email` VARCHAR(191) NOT NULL,
      `token` VARCHAR(100) NOT NULL,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      INDEX `idx_pwd_email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
};
