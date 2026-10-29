<?php

namespace App\Models;

use App\Core\Database;

class ProductVariant {
    public static function getByProductId(int $productId): array {
        $stmt = Database::getConnection()->prepare("
            SELECT * FROM `product_variants` 
            WHERE `product_id` = ? AND `is_active` = 1
            ORDER BY `price` ASC
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM `product_variants` WHERE `id` = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO `product_variants` (`product_id`, `name`, `sku`, `price`, `stock_quantity`, `is_active`)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['product_id'],
            $data['name'],
            $data['sku'] ?? null,
            $data['price'],
            $data['stock_quantity'] ?? 50,
            $data['is_active'] ?? 1
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function deleteByProductId(int $productId): bool {
        $stmt = Database::getConnection()->prepare("DELETE FROM `product_variants` WHERE `product_id` = ?");
        return $stmt->execute([$productId]);
    }
}
