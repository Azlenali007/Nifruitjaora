<?php

namespace App\Models;

use App\Core\Database;

class Payment {
    public static function findByOrderId(int $orderId): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM `payments` WHERE `order_id` = ? ORDER BY `id` DESC LIMIT 1");
        $stmt->execute([$orderId]);
        return $stmt->fetch() ?: null;
    }

    public static function findByRazorpayOrderId(string $razorpayOrderId): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM `payments` WHERE `razorpay_order_id` = ?");
        $stmt->execute([$razorpayOrderId]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO `payments` 
            (`order_id`, `payment_method`, `razorpay_order_id`, `amount`, `currency`, `status`)
            VALUES (?, 'razorpay', ?, ?, ?, 'created')
        ");
        $stmt->execute([
            $data['order_id'],
            $data['razorpay_order_id'] ?? null,
            $data['amount'],
            $data['currency'] ?? 'INR'
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function markAsPaid(int $id, string $paymentId, string $signature, ?string $raw = null): bool {
        $stmt = Database::getConnection()->prepare("
            UPDATE `payments` 
            SET `razorpay_payment_id` = ?,
                `razorpay_signature` = ?,
                `status` = 'captured',
                `raw_response` = ?
            WHERE `id` = ?
        ");
        return $stmt->execute([$paymentId, $signature, $raw, $id]);
    }

    public static function markAsFailed(int $id, string $code, string $description, ?string $raw = null): bool {
        $stmt = Database::getConnection()->prepare("
            UPDATE `payments` 
            SET `status` = 'failed',
                `error_code` = ?,
                `error_description` = ?,
                `raw_response` = ?
            WHERE `id` = ?
        ");
        return $stmt->execute([$code, $description, $raw, $id]);
    }

    public static function markAsRefunded(int $id, string $refundId, float $amount): bool {
        $stmt = Database::getConnection()->prepare("
            UPDATE `payments` 
            SET `status` = 'refunded',
                `refund_id` = ?,
                `refund_amount` = ?
            WHERE `id` = ?
        ");
        return $stmt->execute([$refundId, $amount, $id]);
    }

    public static function all(int $limit = 50, int $offset = 0): array {
        $stmt = Database::getConnection()->prepare("
            SELECT p.*, o.order_number, o.guest_name, o.guest_phone
            FROM `payments` p
            JOIN `orders` o ON p.order_id = o.id
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
