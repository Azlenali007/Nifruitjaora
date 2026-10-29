<?php

namespace App\Services;

class NotificationService {
    public static function notifyOrderPlaced(array $order): void {
        // Log order notification
        error_log("[Order Notification] New Order #{$order['order_number']} placed by {$order['guest_name']} for ₹{$order['total_amount']}.");
    }

    public static function notifyPaymentSuccess(array $order, array $payment): void {
        error_log("[Payment Notification] Order #{$order['order_number']} payment verified via Razorpay ID: {$payment['razorpay_payment_id']}.");
    }
}
