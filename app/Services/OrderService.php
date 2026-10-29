<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Coupon;
use RuntimeException;

class OrderService {
    public static function processCheckout(array $customerData, ?string $couponCode = null): array {
        $user = auth();
        $userId = $user['id'] ?? null;
        $sessionId = session_id();

        $cartSummary = CartService::getCartSummary($userId, $sessionId, $couponCode, $customerData['pincode']);

        if ($cartSummary['total_items'] === 0) {
            throw new RuntimeException("Your cart is empty.");
        }

        if ($cartSummary['has_out_of_stock']) {
            throw new RuntimeException("One or more items in your cart are currently out of stock. Please adjust quantities.");
        }

        if ($cartSummary['subtotal'] < $cartSummary['min_order_amount']) {
            throw new RuntimeException("Minimum order amount of ₹{$cartSummary['min_order_amount']} is required for delivery.");
        }

        if (!$cartSummary['is_serviceable']) {
            throw new RuntimeException("We currently do not deliver to PIN code {$customerData['pincode']}. Please choose a serviceable area.");
        }

        // Check if Razorpay is enabled
        $razorpay = new RazorpayService();
        if (!$razorpay->isEnabled()) {
            throw new RuntimeException("Online payments are temporarily unavailable. Please try again shortly.");
        }

        // Generate unguessable order number
        $orderNumber = Order::generateOrderNumber();

        // Order data
        $orderData = [
            'order_number' => $orderNumber,
            'user_id' => $userId,
            'is_guest' => $userId ? 0 : 1,
            'guest_name' => $customerData['name'],
            'guest_email' => $customerData['email'] ?? null,
            'guest_phone' => $customerData['phone'],
            'subtotal' => $cartSummary['subtotal'],
            'discount_amount' => $cartSummary['discount_amount'],
            'delivery_charge' => $cartSummary['delivery_charge'],
            'total_amount' => $cartSummary['total_amount'],
            'coupon_id' => $cartSummary['applied_coupon']['id'] ?? null,
            'coupon_code' => $cartSummary['applied_coupon']['code'] ?? null,
            'delivery_address' => $customerData['address'],
            'delivery_pincode' => $customerData['pincode'],
            'delivery_instructions' => $customerData['instructions'] ?? null,
        ];

        // Create Order and line items
        $orderId = Order::createWithItems($orderData, $cartSummary['items']);

        // Record coupon usage if applied
        if (!empty($cartSummary['applied_coupon'])) {
            Coupon::incrementUsage($cartSummary['applied_coupon']['id'], $userId, $orderId);
        }

        // Create Razorpay order
        $rzpOrder = $razorpay->createOrder($cartSummary['total_amount'], $orderNumber, [
            'order_id' => (string)$orderId,
            'customer_name' => $customerData['name'],
            'customer_phone' => $customerData['phone']
        ]);

        // Create local payment record
        Payment::create([
            'order_id' => $orderId,
            'razorpay_order_id' => $rzpOrder['id'],
            'amount' => $cartSummary['total_amount'],
            'currency' => 'INR'
        ]);

        // Clear cart now that order is generated
        Cart::clear($cartSummary['cart_id']);

        return [
            'order_id' => $orderId,
            'order_number' => $orderNumber,
            'total_amount' => $cartSummary['total_amount'],
            'razorpay_order_id' => $rzpOrder['id'],
            'razorpay_key_id' => $razorpay->getKeyId(),
            'currency' => 'INR',
            'customer' => [
                'name' => $customerData['name'],
                'email' => $customerData['email'] ?? '',
                'phone' => $customerData['phone']
            ]
        ];
    }
}
