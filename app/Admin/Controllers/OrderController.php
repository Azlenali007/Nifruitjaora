<?php

namespace App\Admin\Controllers;

use App\Core\View;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ActivityLog;
use App\Services\RazorpayService;
use Exception;

class OrderController {
    public function index(): void {
        $search = trim($_GET['search'] ?? '');
        $paymentStatus = trim($_GET['payment_status'] ?? '');
        $fulfillmentStatus = trim($_GET['fulfillment_status'] ?? '');
        $dateFrom = trim($_GET['date_from'] ?? '');
        $dateTo = trim($_GET['date_to'] ?? '');

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search' => $search,
            'payment_status' => $paymentStatus,
            'fulfillment_status' => $fulfillmentStatus,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ];

        $orders = Order::all($filters, $limit, $offset);
        $totalOrders = Order::count($filters);
        $totalPages = ceil($totalOrders / $limit);

        View::render('admin/orders/index', [
            'title' => 'Order Management | Admin',
            'orders' => $orders,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalOrders' => $totalOrders
        ]);
    }

    public function show(string $id): void {
        $order = Order::findById((int)$id);
        if (!$order) {
            flash('error', 'Order not found.');
            redirect('/admin/orders');
            return;
        }

        View::render('admin/orders/show', [
            'title' => "Order #{$order['order_number']} Details | Admin",
            'order' => $order
        ]);
    }

    public function updateStatus(string $id): void {
        $orderId = (int)$id;
        $order = Order::findById($orderId);
        if (!$order) {
            flash('error', 'Order not found.');
            redirect('/admin/orders');
            return;
        }

        $fulfillmentStatus = $_POST['fulfillment_status'] ?? null;
        $adminNotes = $_POST['admin_notes'] ?? null;

        Order::updateStatus($orderId, null, $fulfillmentStatus, $adminNotes);
        ActivityLog::log('update_order_status', "Order #{$order['order_number']} status updated to {$fulfillmentStatus}", 'order', $orderId);

        flash('success', "Order #{$order['order_number']} updated successfully.");
        redirect("/admin/orders/{$orderId}");
    }

    public function refund(string $id): void {
        $orderId = (int)$id;
        $order = Order::findById($orderId);
        if (!$order) {
            flash('error', 'Order not found.');
            redirect('/admin/orders');
            return;
        }

        $payment = Payment::findByOrderId($orderId);
        if (!$payment || $payment['status'] !== 'captured' || empty($payment['razorpay_payment_id'])) {
            flash('error', 'No eligible captured payment record found for this order.');
            redirect("/admin/orders/{$orderId}");
            return;
        }

        $refundAmount = (float)($_POST['refund_amount'] ?? $order['total_amount']);
        $reason = trim($_POST['refund_reason'] ?? 'Customer request');

        try {
            $razorpay = new RazorpayService();
            $refund = $razorpay->refund($payment['razorpay_payment_id'], $refundAmount, $reason);

            Payment::markAsRefunded($payment['id'], $refund['id'] ?? 'rfnd_manual', $refundAmount);
            Order::updateStatus($orderId, 'refunded', 'cancelled', "Refund issued: ₹{$refundAmount}. Reason: {$reason}");

            ActivityLog::log('refund_order', "Refunded ₹{$refundAmount} for Order #{$order['order_number']}", 'order', $orderId);
            flash('success', "Refund of ₹{$refundAmount} initiated successfully!");
        } catch (Exception $e) {
            flash('error', 'Refund failed: ' . $e->getMessage());
        }

        redirect("/admin/orders/{$orderId}");
    }
}
