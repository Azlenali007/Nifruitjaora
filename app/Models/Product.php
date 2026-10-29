<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Product {
    public static function findById(int $id): ?array {
        $stmt = Database::getConnection()->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM `products` p
            LEFT JOIN `categories` c ON p.category_id = c.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findBySlug(string $slug): ?array {
        $stmt = Database::getConnection()->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM `products` p
            LEFT JOIN `categories` c ON p.category_id = c.id
            WHERE p.slug = ? AND p.is_active = 1
        ");
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public static function getCarouselProducts(): array {
        $stmt = Database::getConnection()->query("
            SELECT p.*, c.name as category_name
            FROM `products` p
            JOIN `categories` c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.is_carousel = 1 AND c.is_active = 1
            ORDER BY p.carousel_order ASC, p.id ASC
        ");
        $products = $stmt->fetchAll();
        foreach ($products as &$p) {
            $p['variants'] = ProductVariant::getByProductId($p['id']);
        }
        return $products;
    }

    public static function getFeaturedProducts(int $limit = 6): array {
        $stmt = Database::getConnection()->prepare("
            SELECT p.*, c.name as category_name
            FROM `products` p
            JOIN `categories` c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.is_featured = 1 AND c.is_active = 1
            ORDER BY p.sort_order ASC, p.id ASC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll();
        foreach ($products as &$p) {
            $p['variants'] = ProductVariant::getByProductId($p['id']);
        }
        return $products;
    }

    public static function allActive(array $filters = []): array {
        $sql = "
            SELECT p.*, c.name as category_name
            FROM `products` p
            JOIN `categories` c ON p.category_id = c.id
            WHERE p.is_active = 1 AND c.is_active = 1
        ";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.short_description LIKE ? OR p.description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['category_slug'])) {
            $sql .= " AND c.slug = ?";
            $params[] = $filters['category_slug'];
        }

        $sort = $filters['sort'] ?? 'featured';
        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY p.price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY p.price DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY p.name ASC";
                break;
            default:
                $sql .= " ORDER BY p.sort_order ASC, p.id ASC";
                break;
        }

        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();
        foreach ($products as &$p) {
            $p['variants'] = ProductVariant::getByProductId($p['id']);
        }
        return $products;
    }

    public static function allAdmin(array $filters = []): array {
        $sql = "
            SELECT p.*, c.name as category_name,
                   (SELECT COUNT(*) FROM `order_items` oi WHERE oi.product_id = p.id) as total_sold
            FROM `products` p
            LEFT JOIN `categories` c ON p.category_id = c.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.slug LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $sql .= " AND p.is_active = ?";
            $params[] = $filters['is_active'];
        }

        $sql .= " ORDER BY p.sort_order ASC, p.id DESC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO `products` 
            (`category_id`, `name`, `slug`, `price`, `sale_price`, `stock_quantity`, `is_in_stock`, `is_featured`, `is_carousel`, `carousel_order`, `image_url`, `short_description`, `description`, `sort_order`, `is_active`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['category_id'] ?: null,
            $data['name'],
            $data['slug'],
            $data['price'],
            !empty($data['sale_price']) ? $data['sale_price'] : null,
            $data['stock_quantity'] ?? 50,
            !empty($data['is_in_stock']) ? 1 : 0,
            !empty($data['is_featured']) ? 1 : 0,
            !empty($data['is_carousel']) ? 1 : 0,
            $data['carousel_order'] ?? 0,
            $data['image_url'],
            $data['short_description'] ?? null,
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
        $sql = "UPDATE `products` SET " . implode(', ', $fields) . " WHERE `id` = ?";
        $stmt = Database::getConnection()->prepare($sql);
        return $stmt->execute($values);
    }

    public static function delete(int $id): bool {
        $stmt = Database::getConnection()->prepare("DELETE FROM `products` WHERE `id` = ?");
        return $stmt->execute([$id]);
    }

    public static function decrementStock(int $productId, int $quantity): bool {
        $stmt = Database::getConnection()->prepare("
            UPDATE `products` 
            SET `stock_quantity` = GREATEST(0, `stock_quantity` - ?),
                `is_in_stock` = CASE WHEN `stock_quantity` - ? <= 0 THEN 0 ELSE 1 END
            WHERE `id` = ? AND `stock_quantity` >= ?
        ");
        return $stmt->execute([$quantity, $quantity, $productId, $quantity]) && $stmt->rowCount() > 0;
    }
}
