<?php

use App\Models\Cart;
use App\Models\Product;
use App\Services\CartService;

class CartTest {
    public function run(): array {
        $results = [];

        $sessionId = 'test_session_' . bin2hex(random_bytes(6));
        $cartId = Cart::getOrCreateCartId(null, $sessionId);
        $results['cart_created'] = ($cartId > 0);

        $products = Product::allActive();
        $prod = $products[0];

        // Add 2 items
        Cart::addItem($cartId, $prod['id'], null, 2);
        $items = Cart::getItems($cartId);
        $results['items_added'] = (count($items) === 1 && $items[0]['quantity'] === 2);

        // Update quantity to 3
        Cart::updateQuantity($cartId, $items[0]['id'], 3);
        $summary = CartService::getCartSummary(null, $sessionId);
        $results['quantity_updated'] = ($summary['total_items'] === 3);
        $results['subtotal_calculated'] = ($summary['subtotal'] == round($prod['price'] * 3, 2));

        // Clean up
        Cart::clear($cartId);
        $clearedSummary = CartService::getCartSummary(null, $sessionId);
        $results['cart_cleared'] = ($clearedSummary['total_items'] === 0);

        return $results;
    }
}
