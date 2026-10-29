<?php

namespace App\Models;

use App\Core\Database;

class Coupon {
    public static function findByCode(string $code): ?array {
        $stmt = Database::getConnection()->prepare("
            SELECT * FROM `coupons` 
            WHERE `code` = ? AND `is_active` = 1
        ");
        $stmt->execute([strtoupper(trim($code))]);
        return $stmt->fetch() ?: null;
    }

    public static function all(): array {
        $stmt = Database::getConnection()->query("SELECT * FROM `coupons` ORDER BY `id` DESC");
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM `coupons` WHERE `id` = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function calculateDiscount(array $coupon, float $subtotal): float {
        if ($subtotal < (float)$coupon['min_order_amount']) {
            return 0.0;
        }

        $now = date('Y-m-d H:i:s');
        if (!empty($coupon['starts_at']) && $coupon['starts_at'] > $now) {
            return 0.0;
        }
        if (!empty($coupon['expires_at']) && $coupon['expires_at'] < $now) {
            return 0.0;
        }

        if (!empty($coupon['usage_limit']) && $coupon['used_count'] >= $coupon['usage_limit']) {
            return 0.0;
        }

        $discount = 0.0;
        if ($coupon['discount_type'] === 'percentage') {
            $discount = ($subtotal * (float)$coupon['discount_value']) / 100.0;
            if (!empty($coupon['max_discount_amount']) && $discount > (float)$coupon['max_discount_amount']) {
                $discount = (float)$coupon['max_discount_amount'];
            }
        } else {
            $discount = min($subtotal, (float)$coupon['discount_value']);
        }

        return round($discount, 2);
    }

    public static function incrementUsage(int $couponId, ?int $userId, int $orderId): void {
        $pdo = Database::getConnection();
        $pdo->prepare("UPDATE `coupons` SET `used_count` = `used_count` + 1 WHERE `id` = ?")->execute([$couponId]);
        $pdo->prepare("INSERT INTO `coupon_usage` (`coupon_id`, `user_id`, `order_id`) VALUES (?, ?, ?)")->execute([$couponId, $userId, $orderId]);
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO `coupons` 
            (`code`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount_amount`, `usage_limit`, `is_active`, `starts_at`, `expires_at`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            strtoupper(trim($data['code'])),
            $data['discount_type'],
            $data['discount_value'],
            $data['min_order_amount'] ?? 0.00,
            !empty($data['max_discount_amount']) ? $data['max_discount_amount'] : null,
            !empty($data['usage_limit']) ? (int)$data['usage_limit'] : null,
            isset($data['is_active']) ? (int)$data['is_active'] : 1,
            !empty($data['starts_at']) ? $data['starts_at'] : null,
            !empty($data['expires_at']) ? $data['expires_at'] : null,
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
        $sql = "UPDATE `coupons` SET " . implode(', ', $fields) . " WHERE `id` = ?";
        $stmt = Database::getConnection()->prepare($sql);
        return $stmt->execute($values);
    }

    public static function delete(int $id): bool {
        $stmt = Database::getConnection()->prepare("DELETE FROM `coupons` WHERE `id` = ?");
        return $stmt->execute([$id]);
    }
}
