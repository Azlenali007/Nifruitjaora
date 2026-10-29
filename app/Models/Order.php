<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;

class Order {
    public static function generateOrderNumber(): string {
        $prefix = 'NA-' . date('ymd');
        $random = strtoupper(bin2hex(random_bytes(3))); // 6 hex chars
        return "{$prefix}-{$random}";
    }

    public static function findById(int $id): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM `orders` WHERE `id` = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        if ($order) {
            $order['items'] = OrderItem::getByOrderId($order['id']);
            $order['payment'] = Payment::findByOrderId($order['id']);
        }
        return $order ?: null;
    }

    public static function findByOrderNumber(string $orderNumber): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM `orders` WHERE `order_number` = ?");
        $stmt->execute([$orderNumber]);
        $order = $stmt->fetch();
        if ($order) {
            $order['items'] = OrderItem::getByOrderId($order['id']);
            $order['payment'] = Payment::findByOrderId($order['id']);
        }
        return $order ?: null;
    }

    public static function getCustomerOrders(int $userId): array {
        $stmt = Database::getConnection()->prepare("
            SELECT * FROM `orders` 
            WHERE `user_id` = ? 
            ORDER BY `created_at` DESC
        ");
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll();
        foreach ($orders as &$o) {
            $o['items'] = OrderItem::getByOrderId($o['id']);
            $o['payment'] = Payment::findByOrderId($o['id']);
        }
        return $orders;
    }

    public static function all(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "SELECT o.* FROM `orders` o WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (o.order_number LIKE ? OR o.guest_name LIKE ? OR o.guest_phone LIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($filters['payment_status'])) {
            $sql .= " AND o.payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['fulfillment_status'])) {
            $sql .= " AND o.fulfillment_status = ?";
            $params[] = $filters['fulfillment_status'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND o.created_at >= ?";
            $params[] = $filters['date_from'] . ' 00:00:00';
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND o.created_at <= ?";
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        $sql .= " ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare($sql);
        $i = 1;
        foreach ($params as $param) {
            $stmt->bindValue($i++, $param);
        }
        $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, PDO::PARAM_INT);
        $stmt->execute();

        $orders = $stmt->fetchAll();
        foreach ($orders as &$o) {
            $o['items'] = OrderItem::getByOrderId($o['id']);
            $o['payment'] = Payment::findByOrderId($o['id']);
        }
        return $orders;
    }

    public static function count(array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM `orders` o WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (o.order_number LIKE ? OR o.guest_name LIKE ? OR o.guest_phone LIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($filters['payment_status'])) {
            $sql .= " AND o.payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['fulfillment_status'])) {
            $sql .= " AND o.fulfillment_status = ?";
            $params[] = $filters['fulfillment_status'];
        }

        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function createWithItems(array $orderData, array $items): int {
        return Database::transaction(function (PDO $pdo) use ($orderData, $items) {
            $stmt = $pdo->prepare("
                INSERT INTO `orders` 
                (`order_number`, `user_id`, `is_guest`, `guest_name`, `guest_email`, `guest_phone`, 
                 `subtotal`, `discount_amount`, `delivery_charge`, `total_amount`, 
                 `coupon_id`, `coupon_code`, `payment_status`, `fulfillment_status`, 
                 `delivery_address`, `delivery_pincode`, `delivery_instructions`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', ?, ?, ?)
            ");
            
            $stmt->execute([
                $orderData['order_number'],
                $orderData['user_id'] ?? null,
                !empty($orderData['is_guest']) ? 1 : 0,
                $orderData['guest_name'],
                $orderData['guest_email'] ?? null,
                $orderData['guest_phone'],
                $orderData['subtotal'],
                $orderData['discount_amount'] ?? 0.00,
                $orderData['delivery_charge'] ?? 0.00,
                $orderData['total_amount'],
                $orderData['coupon_id'] ?? null,
                $orderData['coupon_code'] ?? null,
                $orderData['delivery_address'],
                $orderData['delivery_pincode'],
                $orderData['delivery_instructions'] ?? null
            ]);

            $orderId = (int)$pdo->lastInsertId();

            $itemStmt = $pdo->prepare("
                INSERT INTO `order_items` 
                (`order_id`, `product_id`, `variant_id`, `product_name`, `variant_name`, `unit_price`, `quantity`, `total_price`, `product_image`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($items as $item) {
                // Decrement stock atomically
                $decremented = Product::decrementStock($item['product_id'], $item['quantity']);
                if (!$decremented) {
                    throw new Exception("Product '{$item['name']}' is out of stock or insufficient quantity.");
                }

                $itemStmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['variant_id'] ?? null,
                    $item['name'],
                    $item['variant_name'] ?? null,
                    $item['unit_price'],
                    $item['quantity'],
                    $item['subtotal'],
                    $item['image_url'] ?? null
                ]);
            }

            return $orderId;
        });
    }

    public static function updateStatus(int $id, ?string $paymentStatus = null, ?string $fulfillmentStatus = null, ?string $notes = null): bool {
        $fields = [];
        $values = [];

        if ($paymentStatus !== null) {
            $fields[] = "`payment_status` = ?";
            $values[] = $paymentStatus;
        }

        if ($fulfillmentStatus !== null) {
            $fields[] = "`fulfillment_status` = ?";
            $values[] = $fulfillmentStatus;
        }

        if ($notes !== null) {
            $fields[] = "`admin_notes` = ?";
            $values[] = $notes;
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $id;
        $sql = "UPDATE `orders` SET " . implode(', ', $fields) . " WHERE `id` = ?";
        $stmt = Database::getConnection()->prepare($sql);
        return $stmt->execute($values);
    }

    public static function getSalesStats(): array {
        $pdo = Database::getConnection();
        
        $todaySales = (float)$pdo->query("
            SELECT COALESCE(SUM(total_amount), 0) FROM `orders` 
            WHERE `payment_status` = 'paid' AND DATE(created_at) = CURDATE()
        ")->fetchColumn();

        $todayOrders = (int)$pdo->query("
            SELECT COUNT(*) FROM `orders` 
            WHERE DATE(created_at) = CURDATE()
        ")->fetchColumn();

        $totalRevenue = (float)$pdo->query("
            SELECT COALESCE(SUM(total_amount), 0) FROM `orders` 
            WHERE `payment_status` = 'paid'
        ")->fetchColumn();

        $pendingOrders = (int)$pdo->query("
            SELECT COUNT(*) FROM `orders` 
            WHERE `fulfillment_status` IN ('pending', 'confirmed', 'preparing', 'out_for_delivery')
        ")->fetchColumn();

        $awaitingPayment = (int)$pdo->query("
            SELECT COUNT(*) FROM `orders` 
            WHERE `payment_status` = 'pending'
        ")->fetchColumn();

        $cancelledOrders = (int)$pdo->query("
            SELECT COUNT(*) FROM `orders` 
            WHERE `fulfillment_status` = 'cancelled'
        ")->fetchColumn();

        $totalRefunds = (float)$pdo->query("
            SELECT COALESCE(SUM(refund_amount), 0) FROM `payments` 
            WHERE `status` = 'refunded'
        ")->fetchColumn();

        return [
            'today_sales' => $todaySales,
            'today_orders' => $todayOrders,
            'total_revenue' => $totalRevenue,
            'pending_orders' => $pendingOrders,
            'awaiting_payment' => $awaitingPayment,
            'cancelled_orders' => $cancelledOrders,
            'total_refunds' => $totalRefunds
        ];
    }
}
