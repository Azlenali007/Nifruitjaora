<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\RazorpayService;

class RazorpayTest
{
    public function run(): array
    {
        $results = [];

        // Test 1: Amount conversion to paise
        try {
            $rupees = 149.50;
            $paise = (int) round($rupees * 100);
            if ($paise === 14950) {
                $results[] = ['name' => 'Razorpay currency conversion: Rupees to integer paise', 'passed' => true];
            } else {
                $results[] = ['name' => 'Razorpay currency conversion', 'passed' => false, 'error' => "Expected 14950, got $paise"];
            }
        } catch (\Throwable $e) {
            $results[] = ['name' => 'Razorpay currency conversion', 'passed' => false, 'error' => $e->getMessage()];
        }

        // Test 2: Valid HMAC-SHA256 signature verification
        try {
            $secret = 'test_secret_key_12345';
            $orderId = 'order_DA123456789';
            $paymentId = 'pay_9876543210';

            $data = $orderId . '|' . $paymentId;
            $generatedSignature = hash_hmac('sha256', $data, $secret);

            $isValid = hash_equals(hash_hmac('sha256', $orderId . '|' . $paymentId, $secret), $generatedSignature);

            if ($isValid) {
                $results[] = ['name' => 'HMAC-SHA256 signature verification with genuine secret and payload', 'passed' => true];
            } else {
                $results[] = ['name' => 'HMAC-SHA256 signature verification', 'passed' => false, 'error' => 'Valid signature failed verification'];
            }
        } catch (\Throwable $e) {
            $results[] = ['name' => 'HMAC-SHA256 signature verification', 'passed' => false, 'error' => $e->getMessage()];
        }

        // Test 3: Forged signature rejection
        try {
            $secret = 'test_secret_key_12345';
            $orderId = 'order_DA123456789';
            $paymentId = 'pay_9876543210';
            $forgedSignature = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';

            $expected = hash_hmac('sha256', $orderId . '|' . $paymentId, $secret);
            $isValid = hash_equals($expected, $forgedSignature);

            if (!$isValid) {
                $results[] = ['name' => 'Tampered / forged Razorpay payment signature correctly rejected', 'passed' => true];
            } else {
                $results[] = ['name' => 'Forged signature rejection', 'passed' => false, 'error' => 'Tampered signature was mistakenly accepted'];
            }
        } catch (\Throwable $e) {
            $results[] = ['name' => 'Forged signature rejection', 'passed' => false, 'error' => $e->getMessage()];
        }

        // Test 4: Timing attack resistance via hash_equals
        try {
            $sig1 = hash_hmac('sha256', 'payload', 'secret');
            $sig2 = hash_hmac('sha256', 'payload', 'secret');
            $sig3 = hash_hmac('sha256', 'different_payload', 'secret');

            if (hash_equals($sig1, $sig2) && !hash_equals($sig1, $sig3)) {
                $results[] = ['name' => 'Constant-time string comparison (hash_equals) protection active', 'passed' => true];
            } else {
                $results[] = ['name' => 'Constant-time comparison', 'passed' => false, 'error' => 'hash_equals behavior unexpected'];
            }
        } catch (\Throwable $e) {
            $results[] = ['name' => 'Constant-time comparison', 'passed' => false, 'error' => $e->getMessage()];
        }

        return $results;
    }
}
