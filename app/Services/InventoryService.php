<?php

namespace App\Services;

use App\Models\Product;

class InventoryService {
    public static function checkStock(int $productId, int $requestedQty): bool {
        $product = Product::findById($productId);
        if (!$product || !$product['is_active'] || !$product['is_in_stock']) {
            return false;
        }
        return $product['stock_quantity'] >= $requestedQty;
    }
}
