<?php

namespace App\Admin\Controllers;

use App\Core\View;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\ActivityLog;
use App\Services\RazorpayService;

class PaymentController {
    public function index(): void {
        $payments = Payment::all(50, 0);
        $razorpay = new RazorpayService();

        View::render('admin/payments/index', [
            'title' => 'Payment & Razorpay Settings | Admin',
            'payments' => $payments,
            'razorpayEnabled' => $razorpay->isEnabled(),
            'razorpayMode' => $razorpay->getMode(),
            'keyId' => $razorpay->getKeyId(),
            'webhookSecret' => Setting::get('razorpay_webhook_secret', '')
        ]);
    }

    public function updateSettings(): void {
        $enabled = !empty($_POST['razorpay_enabled']) ? '1' : '0';
        $mode = $_POST['razorpay_mode'] === 'live' ? 'live' : 'test';
        $keyId = trim($_POST['razorpay_key_id'] ?? '');
        $keySecret = trim($_POST['razorpay_key_secret'] ?? '');
        $webhookSecret = trim($_POST['razorpay_webhook_secret'] ?? '');

        Setting::set('razorpay_enabled', $enabled, 'boolean', 'Enable Razorpay payment gateway');
        Setting::set('razorpay_mode', $mode, 'string', 'Razorpay mode (test/live)');

        if (!empty($keyId)) {
            if ($mode === 'live') {
                Setting::set('razorpay_live_key_id', $keyId, 'string', 'Razorpay Live Key ID');
            } else {
                Setting::set('razorpay_key_id', $keyId, 'string', 'Razorpay Test Key ID');
            }
        }

        if (!empty($keySecret) && !str_starts_with($keySecret, '••••')) {
            if ($mode === 'live') {
                Setting::set('razorpay_live_key_secret', $keySecret, 'string', 'Razorpay Live Secret');
            } else {
                Setting::set('razorpay_key_secret', $keySecret, 'string', 'Razorpay Test Secret');
            }
        }

        if (!empty($webhookSecret)) {
            Setting::set('razorpay_webhook_secret', $webhookSecret, 'string', 'Razorpay Webhook Secret');
        }

        ActivityLog::log('update_razorpay_settings', "Updated Razorpay settings (Mode: {$mode}, Enabled: {$enabled})");
        flash('success', 'Razorpay configuration saved successfully.');
        redirect('/admin/payments');
    }
}
