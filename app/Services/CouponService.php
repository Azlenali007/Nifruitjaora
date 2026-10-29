<?php

namespace App\Services;

use App\Models\Coupon;

class CouponService {
    public static function validate(string $code, float $cartSubtotal): array {
        $coupon = Coupon::findByCode($code);
        if (!$coupon) {
            return ['valid' => false, 'message' => 'Invalid or expired coupon code.'];
        }

        $discount = Coupon::calculateDiscount($coupon, $cartSubtotal);
        if ($discount <= 0) {
            return ['valid' => false, 'message' => "Order does not meet the minimum requirement of ₹{$coupon['min_order_amount']}."];
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'message' => 'Coupon applied successfully!'
        ];
    }
}
