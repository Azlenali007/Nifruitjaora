<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

echo "[Migrate] Running database schema setup...\n";

try {
    $pdo = Database::getConnection();
    
    // Create migrations tracker table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `migrations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `migration` VARCHAR(255) NOT NULL UNIQUE,
        `executed_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $files = glob(__DIR__ . '/../database/migrations/*.php');
    sort($files);

    foreach ($files as $file) {
        $filename = basename($file);
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `migrations` WHERE `migration` = ?");
        $stmt->execute([$filename]);
        if ($stmt->fetchColumn() > 0) {
            continue;
        }

        echo "  - Migrating: {$filename}... ";
        $migration = require $file;
        if (is_callable($migration)) {
            $migration($pdo);
        }
        
        $insert = $pdo->prepare("INSERT INTO `migrations` (`migration`) VALUES (?)");
        $insert->execute([$filename]);
        echo "Done.\n";
    }

    echo "[Migrate] All migrations executed successfully.\n";
} catch (Throwable $e) {
    echo "[Migrate Error] " . $e->getMessage() . "\n";
    exit(1);
}
