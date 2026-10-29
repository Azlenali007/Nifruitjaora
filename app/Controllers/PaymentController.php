<?php

namespace App\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Services\RazorpayService;
use App\Services\NotificationService;
use App\Helpers\Response;
use Exception;

class PaymentController {
    public function verify(): void {
        $razorpayOrderId = trim($_POST['razorpay_order_id'] ?? '');
        $razorpayPaymentId = trim($_POST['razorpay_payment_id'] ?? '');
        $signature = trim($_POST['razorpay_signature'] ?? '');
        $orderNumber = trim($_POST['order_number'] ?? '');

        if (empty($razorpayOrderId) || empty($razorpayPaymentId) || empty($signature)) {
            Response::error('Missing Razorpay verification parameters.', [], 400);
            return;
        }

        $payment = Payment::findByRazorpayOrderId($razorpayOrderId);
        if (!$payment) {
            Response::error('Payment transaction record not found.', [], 404);
            return;
        }

        $order = Order::findById($payment['order_id']);
        if (!$order) {
            Response::error('Order record not found.', [], 404);
            return;
        }

        // Prevent duplicate processing if already paid
        if ($order['payment_status'] === 'paid') {
            Response::success('Payment has already been verified and confirmed.', [
                'order_number' => $order['order_number'],
                'redirect_url' => "/checkout/confirmation/{$order['order_number']}"
            ]);
            return;
        }

        // Server-side HMAC-SHA256 signature verification
        $razorpay = new RazorpayService();
        $isValid = $razorpay->verifySignature($razorpayOrderId, $razorpayPaymentId, $signature);

        if (!$isValid) {
            Payment::markAsFailed($payment['id'], 'INVALID_SIGNATURE', 'HMAC signature verification failed on server.');
            Order::updateStatus($order['id'], 'failed');
            Response::error('Payment verification failed. Invalid signature received.', [], 400);
            return;
        }

        // Mark payment captured and order paid & confirmed
        $rawPayload = json_encode($_POST);
        Payment::markAsPaid($payment['id'], $razorpayPaymentId, $signature, $rawPayload);
        Order::updateStatus($order['id'], 'paid', 'confirmed');

        NotificationService::notifyPaymentSuccess($order, $payment);

        Response::success('Payment verified successfully!', [
            'order_number' => $order['order_number'],
            'redirect_url' => "/checkout/confirmation/{$order['order_number']}"
        ]);
    }

    public function failed(): void {
        $razorpayOrderId = trim($_POST['razorpay_order_id'] ?? '');
        $errorCode = trim($_POST['error_code'] ?? 'PAYMENT_FAILED');
        $errorDesc = trim($_POST['error_description'] ?? 'Customer payment failed or cancelled');

        if (!empty($razorpayOrderId)) {
            $payment = Payment::findByRazorpayOrderId($razorpayOrderId);
            if ($payment) {
                Payment::markAsFailed($payment['id'], $errorCode, $errorDesc, json_encode($_POST));
                Order::updateStatus($payment['order_id'], 'failed');
            }
        }

        Response::error('Payment failed: ' . $errorDesc, ['order_id' => $razorpayOrderId], 400);
    }

    public function webhook(): void {
        $rawPayload = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

        $razorpay = new RazorpayService();
        if (!$razorpay->verifyWebhookSignature($rawPayload, $signature)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid webhook signature']);
            return;
        }

        $event = json_decode($rawPayload, true);
        $eventType = $event['event'] ?? '';

        if ($eventType === 'payment.captured') {
            $paymentEntity = $event['payload']['payment']['entity'] ?? [];
            $razorpayOrderId = $paymentEntity['order_id'] ?? '';
            $razorpayPaymentId = $paymentEntity['id'] ?? '';

            if ($razorpayOrderId) {
                $payment = Payment::findByRazorpayOrderId($razorpayOrderId);
                if ($payment && $payment['status'] !== 'captured') {
                    Payment::markAsPaid($payment['id'], $razorpayPaymentId, 'webhook_verified', $rawPayload);
                    Order::updateStatus($payment['order_id'], 'paid', 'confirmed');
                }
            }
        } elseif ($eventType === 'payment.failed') {
            $paymentEntity = $event['payload']['payment']['entity'] ?? [];
            $razorpayOrderId = $paymentEntity['order_id'] ?? '';
            if ($razorpayOrderId) {
                $payment = Payment::findByRazorpayOrderId($razorpayOrderId);
                if ($payment) {
                    Payment::markAsFailed($payment['id'], $paymentEntity['error_code'] ?? 'FAILED', $paymentEntity['error_description'] ?? 'Payment failed via webhook', $rawPayload);
                }
            }
        }

        http_response_code(200);
        echo json_encode(['status' => 'ok']);
    }
}
