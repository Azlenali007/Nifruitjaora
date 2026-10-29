<?php

namespace App\Models;

use App\Core\Database;

class Category {
    public static function allActive(): array {
        $stmt = Database::getConnection()->query("
            SELECT * FROM `categories` 
            WHERE `is_active` = 1 
            ORDER BY `sort_order` ASC, `name` ASC
        ");
        return $stmt->fetchAll();
    }

    public static function all(): array {
        $stmt = Database::getConnection()->query("
            SELECT c.*, (SELECT COUNT(*) FROM `products` p WHERE p.category_id = c.id) as product_count
            FROM `categories` c 
            ORDER BY c.sort_order ASC, c.name ASC
        ");
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM `categories` WHERE `id` = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO `categories` (`name`, `slug`, `description`, `sort_order`, `is_active`)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['description'] ?? null,
            $data['sort_order'] ?? 0,
            isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $fields = [];
        $values = [];
        foreach ($data as $k => $v) {
            $fields[] = "`{$k}` = ?";
            $values[] = $v;
        }
        $values[] = $id;
        $sql = "UPDATE `categories` SET " . implode(', ', $fields) . " WHERE `id` = ?";
        $stmt = Database::getConnection()->prepare($sql);
        return $stmt->execute($values);
    }

    public static function delete(int $id): bool {
        $stmt = Database::getConnection()->prepare("DELETE FROM `categories` WHERE `id` = ?");
        return $stmt->execute([$id]);
    }
}
