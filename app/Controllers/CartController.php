<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Helpers\Response;

class CartController {
    public function index(): void {
        $user = auth();
        $couponCode = session('applied_coupon_code');
        $pincode = session('delivery_pincode');

        $cartSummary = CartService::getCartSummary($user['id'] ?? null, session_id(), $couponCode, $pincode);

        View::render('cart/index', [
            'title' => 'Your Shopping Cart | N.A Fresh Fruits & Coconuts',
            'cartSummary' => $cartSummary
        ]);
    }

    public function add(): void {
        $productId = (int)($_POST['product_id'] ?? 0);
        $variantId = !empty($_POST['variant_id']) ? (int)$_POST['variant_id'] : null;
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        $product = Product::findById($productId);
        if (!$product || !$product['is_active']) {
            if ($this->isAjax()) {
                Response::error('Product is no longer available.', [], 404);
            }
            flash('error', 'Product is no longer available.');
            redirect('/products');
            return;
        }

        $user = auth();
        $cartId = Cart::getOrCreateCartId($user['id'] ?? null, session_id());
        Cart::addItem($cartId, $productId, $variantId, $quantity);

        $cartSummary = CartService::getCartSummary($user['id'] ?? null, session_id(), session('applied_coupon_code'));

        if ($this->isAjax()) {
            Response::success("Added {$product['name']} to cart!", [
                'total_items' => $cartSummary['total_items'],
                'subtotal' => $cartSummary['subtotal']
            ]);
            return;
        }

        flash('success', "Added {$product['name']} to your cart!");
        if (isset($_POST['order_now'])) {
            redirect('/checkout');
        } else {
            redirect($_SERVER['HTTP_REFERER'] ?? '/cart');
        }
    }

    public function update(): void {
        $itemId = (int)($_POST['item_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        $user = auth();
        $cartId = Cart::getOrCreateCartId($user['id'] ?? null, session_id());
        Cart::updateQuantity($cartId, $itemId, $quantity);

        $cartSummary = CartService::getCartSummary($user['id'] ?? null, session_id(), session('applied_coupon_code'), session('delivery_pincode'));

        if ($this->isAjax()) {
            Response::success('Cart updated successfully.', $cartSummary);
            return;
        }

        flash('success', 'Cart updated successfully.');
        redirect('/cart');
    }

    public function remove(): void {
        $itemId = (int)($_POST['item_id'] ?? 0);
        $user = auth();
        $cartId = Cart::getOrCreateCartId($user['id'] ?? null, session_id());
        Cart::removeItem($cartId, $itemId);

        $cartSummary = CartService::getCartSummary($user['id'] ?? null, session_id(), session('applied_coupon_code'), session('delivery_pincode'));

        if ($this->isAjax()) {
            Response::success('Item removed from cart.', $cartSummary);
            return;
        }

        flash('success', 'Item removed from your cart.');
        redirect('/cart');
    }

    public function clear(): void {
        $user = auth();
        $cartId = Cart::getOrCreateCartId($user['id'] ?? null, session_id());
        Cart::clear($cartId);
        unset($_SESSION['applied_coupon_code']);

        if ($this->isAjax()) {
            Response::success('Cart cleared.');
            return;
        }

        flash('success', 'Your cart has been cleared.');
        redirect('/cart');
    }

    public function applyCoupon(): void {
        $code = strtoupper(trim($_POST['coupon_code'] ?? ''));
        if (empty($code)) {
            if ($this->isAjax()) Response::error('Please enter a coupon code.');
            flash('error', 'Please enter a coupon code.');
            redirect('/cart');
            return;
        }

        $user = auth();
        $cartSummary = CartService::getCartSummary($user['id'] ?? null, session_id(), $code, session('delivery_pincode'));

        if ($cartSummary['applied_coupon']) {
            $_SESSION['applied_coupon_code'] = $code;
            if ($this->isAjax()) {
                Response::success("Coupon '{$code}' applied successfully!", $cartSummary);
                return;
            }
            flash('success', "Coupon '{$code}' applied!");
        } else {
            unset($_SESSION['applied_coupon_code']);
            $msg = $cartSummary['coupon_error'] ?? 'Coupon could not be applied.';
            if ($this->isAjax()) {
                Response::error($msg);
                return;
            }
            flash('error', $msg);
        }

        redirect('/cart');
    }

    public function removeCoupon(): void {
        unset($_SESSION['applied_coupon_code']);
        if ($this->isAjax()) {
            Response::success('Coupon removed.');
            return;
        }
        flash('success', 'Coupon removed.');
        redirect('/cart');
    }

    public function summary(): void {
        $user = auth();
        $couponCode = session('applied_coupon_code');
        $pincode = session('delivery_pincode') ?? ($_GET['pincode'] ?? null);

        $cartSummary = CartService::getCartSummary($user['id'] ?? null, session_id(), $couponCode, $pincode);
        Response::success('Cart retrieved', $cartSummary);
    }

    private function isAjax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json'));
    }
}
