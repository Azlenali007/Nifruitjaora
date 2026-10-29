<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class User {
    public static function findById(int $id): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM `users` WHERE `id` = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByPhone(string $phone): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM `users` WHERE `phone` = ?");
        $stmt->execute([$phone]);
        return $stmt->fetch() ?: null;
    }

    public static function findByEmail(string $email): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM `users` WHERE `email` = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO `users` (`name`, `email`, `phone`, `password_hash`, `status`) 
            VALUES (?, ?, ?, ?, 'active')");
        $stmt->execute([
            $data['name'],
            !empty($data['email']) ? $data['email'] : null,
            $data['phone'],
            password_hash($data['password'], PASSWORD_BCRYPT)
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
        $sql = "UPDATE `users` SET " . implode(', ', $fields) . " WHERE `id` = ?";
        $stmt = Database::getConnection()->prepare($sql);
        return $stmt->execute($values);
    }

    public static function all(int $limit = 50, int $offset = 0): array {
        $stmt = Database::getConnection()->prepare("
            SELECT u.*, COUNT(o.id) as order_count, COALESCE(SUM(o.total_amount), 0) as total_spent
            FROM `users` u
            LEFT JOIN `orders` o ON u.id = o.user_id AND o.payment_status = 'paid'
            GROUP BY u.id
            ORDER BY u.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function count(): int {
        return (int)Database::getConnection()->query("SELECT COUNT(*) FROM `users`")->fetchColumn();
    }
}
