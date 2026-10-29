<?php

namespace App\Models;

use App\Core\Database;

class OrderItem {
    public static function getByOrderId(int $orderId): array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM `order_items` WHERE `order_id` = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}
