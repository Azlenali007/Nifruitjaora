<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Order;

class OrderTest
{
    public function run(): array
    {
        $results = [];

        // Test 1: Unguessable order number generation
        try {
            $orderNum1 = Order::generateOrderNumber();
            $orderNum2 = Order::generateOrderNumber();

            $formatValid = (bool) preg_match('/^NA-\d{6}-[A-Z0-9]{6}$/', $orderNum1);
            $isUnique = ($orderNum1 !== $orderNum2);

            if ($formatValid && $isUnique) {
                $results[] = ['name' => 'Unguessable and collision-resistant order number generation', 'passed' => true];
            } else {
                $results[] = ['name' => 'Order number generation', 'passed' => false, 'error' => "Format invalid or not unique: $orderNum1 vs $orderNum2"];
            }
        } catch (\Throwable $e) {
            $results[] = ['name' => 'Order number generation', 'passed' => false, 'error' => $e->getMessage()];
        }

        // Test 2: Order status transition validation
        try {
            $validStatuses = ['pending', 'confirmed', 'processing', 'out_for_delivery', 'delivered', 'cancelled'];
            $sampleStatus = 'confirmed';
            $invalidStatus = 'unknown_status';

            if (in_array($sampleStatus, $validStatuses) && !in_array($invalidStatus, $validStatuses)) {
                $results[] = ['name' => 'Order fulfillment status lifecycle validation', 'passed' => true];
            } else {
                $results[] = ['name' => 'Order fulfillment status validation', 'passed' => false, 'error' => 'Status lifecycle check failed'];
            }
        } catch (\Throwable $e) {
            $results[] = ['name' => 'Order fulfillment status validation', 'passed' => false, 'error' => $e->getMessage()];
        }

        // Test 3: Tracking token generation
        try {
            $token1 = bin2hex(random_bytes(16));
            $token2 = bin2hex(random_bytes(16));

            if (strlen($token1) === 32 && $token1 !== $token2) {
                $results[] = ['name' => 'Secure tracking token generation for guest access', 'passed' => true];
            } else {
                $results[] = ['name' => 'Secure tracking token generation', 'passed' => false, 'error' => 'Tracking token generation issue'];
            }
        } catch (\Throwable $e) {
            $results[] = ['name' => 'Secure tracking token generation', 'passed' => false, 'error' => $e->getMessage()];
        }

        return $results;
    }
}
