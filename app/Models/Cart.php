<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Cart {
    public static function getOrCreateCartId(?int $userId = null, ?string $sessionId = null): int {
        $pdo = Database::getConnection();

        if ($userId) {
            $stmt = $pdo->prepare("SELECT id FROM `carts` WHERE `user_id` = ?");
            $stmt->execute([$userId]);
            $cartId = $stmt->fetchColumn();
            if ($cartId) {
                return (int)$cartId;
            }

            $stmt = $pdo->prepare("INSERT INTO `carts` (`user_id`) VALUES (?)");
            $stmt->execute([$userId]);
            return (int)$pdo->lastInsertId();
        }

        if (!$sessionId) {
            $sessionId = session_id();
        }

        $stmt = $pdo->prepare("SELECT id FROM `carts` WHERE `session_id` = ?");
        $stmt->execute([$sessionId]);
        $cartId = $stmt->fetchColumn();
        if ($cartId) {
            return (int)$cartId;
        }

        $stmt = $pdo->prepare("INSERT INTO `carts` (`session_id`) VALUES (?)");
        $stmt->execute([$sessionId]);
        return (int)$pdo->lastInsertId();
    }

    public static function getItems(int $cartId): array {
        $stmt = Database::getConnection()->prepare("
            SELECT ci.id as item_id, ci.quantity, ci.product_id, ci.variant_id,
                   p.name as product_name, p.slug as product_slug, p.image_url,
                   p.price as product_base_price, p.sale_price, p.stock_quantity as product_stock,
                   p.is_in_stock,
                   pv.name as variant_name, pv.price as variant_price, pv.stock_quantity as variant_stock
            FROM `cart_items` ci
            JOIN `products` p ON ci.product_id = p.id
            LEFT JOIN `product_variants` pv ON ci.variant_id = pv.id
            WHERE ci.cart_id = ? AND p.is_active = 1
        ");
        $stmt->execute([$cartId]);
        $rows = $stmt->fetchAll();

        $items = [];
        foreach ($rows as $r) {
            $unitPrice = $r['variant_price'] ?? ($r['sale_price'] ?: $r['product_base_price']);
            $availableStock = $r['variant_stock'] ?? $r['product_stock'];
            $items[] = [
                'id' => (int)$r['item_id'],
                'product_id' => (int)$r['product_id'],
                'variant_id' => $r['variant_id'] ? (int)$r['variant_id'] : null,
                'name' => $r['product_name'],
                'slug' => $r['product_slug'],
                'image_url' => $r['image_url'],
                'variant_name' => $r['variant_name'],
                'unit_price' => (float)$unitPrice,
                'quantity' => (int)$r['quantity'],
                'subtotal' => (float)($unitPrice * $r['quantity']),
                'available_stock' => (int)$availableStock,
                'is_available' => $r['is_in_stock'] && $availableStock >= $r['quantity']
            ];
        }
        return $items;
    }

    public static function addItem(int $cartId, int $productId, ?int $variantId = null, int $quantity = 1): bool {
        $pdo = Database::getConnection();

        // Check if item already exists
        if ($variantId) {
            $stmt = $pdo->prepare("SELECT id, quantity FROM `cart_items` WHERE `cart_id` = ? AND `product_id` = ? AND `variant_id` = ?");
            $stmt->execute([$cartId, $productId, $variantId]);
        } else {
            $stmt = $pdo->prepare("SELECT id, quantity FROM `cart_items` WHERE `cart_id` = ? AND `product_id` = ? AND `variant_id` IS NULL");
            $stmt->execute([$cartId, $productId]);
        }
        $existing = $stmt->fetch();

        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $update = $pdo->prepare("UPDATE `cart_items` SET `quantity` = ? WHERE `id` = ?");
            return $update->execute([$newQty, $existing['id']]);
        }

        $insert = $pdo->prepare("INSERT INTO `cart_items` (`cart_id`, `product_id`, `variant_id`, `quantity`) VALUES (?, ?, ?, ?)");
        return $insert->execute([$cartId, $productId, $variantId, $quantity]);
    }

    public static function updateQuantity(int $cartId, int $itemId, int $quantity): bool {
        if ($quantity <= 0) {
            return self::removeItem($cartId, $itemId);
        }
        $stmt = Database::getConnection()->prepare("UPDATE `cart_items` SET `quantity` = ? WHERE `id` = ? AND `cart_id` = ?");
        return $stmt->execute([$quantity, $itemId, $cartId]);
    }

    public static function removeItem(int $cartId, int $itemId): bool {
        $stmt = Database::getConnection()->prepare("DELETE FROM `cart_items` WHERE `id` = ? AND `cart_id` = ?");
        return $stmt->execute([$itemId, $cartId]);
    }

    public static function clear(int $cartId): bool {
        $stmt = Database::getConnection()->prepare("DELETE FROM `cart_items` WHERE `cart_id` = ?");
        return $stmt->execute([$cartId]);
    }

    public static function mergeGuestCartIntoUserCart(string $sessionId, int $userId): void {
        $pdo = Database::getConnection();
        
        $guestCartStmt = $pdo->prepare("SELECT id FROM `carts` WHERE `session_id` = ?");
        $guestCartStmt->execute([$sessionId]);
        $guestCartId = $guestCartStmt->fetchColumn();

        if (!$guestCartId) {
            return;
        }

        $userCartId = self::getOrCreateCartId($userId);
        $guestItems = self::getItems((int)$guestCartId);

        foreach ($guestItems as $item) {
            self::addItem($userCartId, $item['product_id'], $item['variant_id'], $item['quantity']);
        }

        // Delete guest cart
        $pdo->prepare("DELETE FROM `carts` WHERE `id` = ?")->execute([$guestCartId]);
    }
}
