<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

echo "[Seed] Starting database seeders...\n";

try {
    $pdo = Database::getConnection();

    $seeders = [
        __DIR__ . '/../database/seeds/AdminSeeder.php',
        __DIR__ . '/../database/seeds/SettingsSeeder.php',
    ];

    foreach ($seeders as $seedFile) {
        if (file_exists($seedFile)) {
            $seeder = require $seedFile;
            if (is_callable($seeder)) {
                $seeder($pdo);
            }
        }
    }

    echo "[Seed] Database seeded successfully!\n";
} catch (Throwable $e) {
    echo "[Seed Error] " . $e->getMessage() . "\n";
    exit(1);
}
