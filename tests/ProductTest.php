<?php

use App\Models\Product;

class ProductTest {
    public function run(): array {
        $results = [];

        // 1. Fetch carousel products
        $carousel = Product::getCarouselProducts();
        $results['carousel_products_loaded'] = !empty($carousel);
        $results['carousel_first_is_nariyal_pani'] = isset($carousel[0]['name']) && str_contains($carousel[0]['name'], 'Nariyal Pani');

        // 2. Fetch all active products
        $active = Product::allActive();
        $results['active_products_exist'] = count($active) >= 4;

        // 3. Stock decrement test
        $first = $active[0];
        $initialStock = $first['stock_quantity'];
        $decremented = Product::decrementStock($first['id'], 1);
        $results['stock_decremented'] = $decremented;

        $refreshed = Product::findById($first['id']);
        $results['stock_quantity_correct'] = ($refreshed['stock_quantity'] === $initialStock - 1);

        // Restore stock
        \App\Core\Database::getConnection()->prepare("UPDATE products SET stock_quantity = ? WHERE id = ?")
            ->execute([$initialStock, $first['id']]);

        return $results;
    }
}
