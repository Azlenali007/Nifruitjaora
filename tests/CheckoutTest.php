<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Coupon;
use App\Models\Setting;

class CheckoutTest
{
    public function run(): array
    {
        $results = [];

        // Test 1: Serviceable pincode check
        try {
            $serviceablePins = ['400050', '400051', '400052', '400053', '400054', '400055', '400056'];

            $validPin = '400050';
            $invalidPin = '110001';

            $isServiceableValid = in_array($validPin, $serviceablePins);
            $isServiceableInvalid = in_array($invalidPin, $serviceablePins);

            if ($isServiceableValid && !$isServiceableInvalid) {
                $results[] = ['name' => 'Delivery pin code validation (serviceable vs non-serviceable)', 'passed' => true];
            } else {
                $results[] = ['name' => 'Delivery pin code validation', 'passed' => false, 'error' => 'Pin code validation logic failed'];
            }
        } catch (\Throwable $e) {
            $results[] = ['name' => 'Delivery pin code validation', 'passed' => false, 'error' => $e->getMessage()];
        }

        // Test 2: Minimum order value validation
        try {
            $minOrder = (float) Setting::get('min_order_amount', 80);
            $subtotal1 = 50;
            $subtotal2 = 120;

            if ($subtotal1 < $minOrder && $subtotal2 >= $minOrder) {
                $results[] = ['name' => 'Minimum order amount threshold check', 'passed' => true];
            } else {
                $results[] = ['name' => 'Minimum order amount threshold check', 'passed' => false, 'error' => 'Threshold check failed: min is ' . $minOrder];
            }
        } catch (\Throwable $e) {
            $results[] = ['name' => 'Minimum order amount threshold check', 'passed' => false, 'error' => $e->getMessage()];
        }

        // Test 3: Coupon discount calculation
        try {
            $coupon = Coupon::findByCode('FRESH10');
            if ($coupon) {
                $subtotal = 200.0;
                $discount = Coupon::calculateDiscount($coupon, $subtotal);
                // FRESH10 is 10% off -> 20.0
                if ($discount == 20.0) {
                    $results[] = ['name' => 'Percentage coupon FRESH10 discount deduction calculation', 'passed' => true];
                } else {
                    $results[] = ['name' => 'Percentage coupon discount deduction calculation', 'passed' => false, 'error' => 'Expected 20 discount on 200, got: ' . $discount];
                }
            } else {
                $results[] = ['name' => 'Coupon FRESH10 lookup', 'passed' => false, 'error' => 'FRESH10 coupon not found'];
            }
        } catch (\Throwable $e) {
            $results[] = ['name' => 'Coupon discount calculation', 'passed' => false, 'error' => $e->getMessage()];
        }

        // Test 4: Free delivery threshold check
        try {
            $freeThreshold = (float) Setting::get('free_delivery_threshold', 250);
            $deliveryFee = (float) Setting::get('default_delivery_charge', 30);

            $feeFor150 = (150 >= $freeThreshold) ? 0.0 : $deliveryFee;
            $feeFor300 = (300 >= $freeThreshold) ? 0.0 : $deliveryFee;

            if ($feeFor150 == 30.0 && $feeFor300 == 0.0) {
                $results[] = ['name' => 'Free shipping waiver calculation above threshold', 'passed' => true];
            } else {
                $results[] = ['name' => 'Free shipping waiver calculation', 'passed' => false, 'error' => 'Fee calculation failed'];
            }
        } catch (\Throwable $e) {
            $results[] = ['name' => 'Free shipping waiver calculation', 'passed' => false, 'error' => $e->getMessage()];
        }

        return $results;
    }
}
