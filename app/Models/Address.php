<?php

namespace App\Models;

use App\Core\Database;

class Address {
    public static function getByUserId(int $userId): array {
        $stmt = Database::getConnection()->prepare("
            SELECT * FROM `addresses` 
            WHERE `user_id` = ? 
            ORDER BY `is_default` DESC, `id` DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function findById(int $id, int $userId): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM `addresses` WHERE `id` = ? AND `user_id` = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();
        
        if (!empty($data['is_default'])) {
            $pdo->prepare("UPDATE `addresses` SET `is_default` = 0 WHERE `user_id` = ?")->execute([$data['user_id']]);
        }

        $stmt = $pdo->prepare("
            INSERT INTO `addresses` 
            (`user_id`, `recipient_name`, `phone`, `address_line1`, `address_line2`, `landmark`, `city`, `state`, `pincode`, `is_default`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['user_id'],
            $data['recipient_name'],
            $data['phone'],
            $data['address_line1'],
            $data['address_line2'] ?? null,
            $data['landmark'] ?? null,
            $data['city'] ?? 'Mumbai',
            $data['state'] ?? 'Maharashtra',
            $data['pincode'],
            !empty($data['is_default']) ? 1 : 0
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function delete(int $id, int $userId): bool {
        $stmt = Database::getConnection()->prepare("DELETE FROM `addresses` WHERE `id` = ? AND `user_id` = ?");
        return $stmt->execute([$id, $userId]);
    }
}
