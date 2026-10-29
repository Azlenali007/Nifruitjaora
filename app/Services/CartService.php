<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Setting;

class CartService {
    public static function getCartSummary(?int $userId = null, ?string $sessionId = null, ?string $couponCode = null, ?string $pincode = null): array {
        $cartId = Cart::getOrCreateCartId($userId, $sessionId);
        $items = Cart::getItems($cartId);

        $subtotal = 0.0;
        $totalItems = 0;
        $hasOutOfStock = false;

        foreach ($items as $item) {
            $subtotal += $item['subtotal'];
            $totalItems += $item['quantity'];
            if (!$item['is_available']) {
                $hasOutOfStock = true;
            }
        }

        // Delivery Charge Calculation
        $defaultDeliveryCharge = (float)Setting::get('default_delivery_charge', 30.00);
        $freeDeliveryThreshold = (float)Setting::get('free_delivery_threshold', 250.00);
        $minOrderAmount = (float)Setting::get('min_order_amount', 80.00);

        $deliveryCharge = $defaultDeliveryCharge;
        $isServiceable = true;
        $deliveryZone = null;

        if ($pincode) {
            $deliveryZone = Setting::getDeliveryZone($pincode);
            if ($deliveryZone) {
                $deliveryCharge = (float)$deliveryZone['delivery_charge'];
                $minOrderAmount = (float)$deliveryZone['min_order_amount'];
            } else {
                $isServiceable = false;
            }
        }

        if ($subtotal >= $freeDeliveryThreshold && $subtotal > 0) {
            $deliveryCharge = 0.0;
        }

        // Coupon Calculation
        $discountAmount = 0.0;
        $appliedCoupon = null;
        $couponError = null;

        if ($couponCode) {
            $coupon = Coupon::findByCode($couponCode);
            if ($coupon) {
                $discount = Coupon::calculateDiscount($coupon, $subtotal);
                if ($discount > 0) {
                    $discountAmount = $discount;
                    $appliedCoupon = $coupon;
                } else {
                    $couponError = "Minimum order of ₹{$coupon['min_order_amount']} required for coupon {$coupon['code']}.";
                }
            } else {
                $couponError = "Invalid or expired coupon code.";
            }
        }

        $totalAmount = max(0, $subtotal - $discountAmount + ($subtotal > 0 ? $deliveryCharge : 0));

        return [
            'cart_id' => $cartId,
            'items' => $items,
            'total_items' => $totalItems,
            'subtotal' => round($subtotal, 2),
            'delivery_charge' => round($deliveryCharge, 2),
            'free_delivery_threshold' => $freeDeliveryThreshold,
            'min_order_amount' => $minOrderAmount,
            'discount_amount' => round($discountAmount, 2),
            'total_amount' => round($totalAmount, 2),
            'applied_coupon' => $appliedCoupon,
            'coupon_error' => $couponError,
            'is_serviceable' => $isServiceable,
            'delivery_zone' => $deliveryZone,
            'has_out_of_stock' => $hasOutOfStock,
            'can_checkout' => $totalItems > 0 && !$hasOutOfStock && $subtotal >= $minOrderAmount && $isServiceable
        ];
    }
}
