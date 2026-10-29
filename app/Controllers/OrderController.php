<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Order;

class OrderController {
    public function index(): void {
        $user = auth();
        if (!$user) {
            redirect('/login');
            return;
        }

        $orders = Order::getCustomerOrders($user['id']);

        View::render('orders/index', [
            'title' => 'My Orders | N.A Fresh Fruits & Coconuts',
            'orders' => $orders
        ]);
    }

    public function show(string $orderNumber): void {
        $order = Order::findByOrderNumber($orderNumber);
        if (!$order) {
            flash('error', 'Order not found.');
            redirect('/');
            return;
        }

        // If registered user, ensure they can only view their own order
        $user = auth();
        if ($order['user_id'] !== null && (!$user || (int)$user['id'] !== (int)$order['user_id'])) {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Access Denied']);
            return;
        }

        View::render('orders/show', [
            'title' => "Track Order #{$order['order_number']} | N.A Fresh Fruits",
            'order' => $order
        ]);
    }

    public function track(): void {
        $orderNumber = trim($_GET['order_number'] ?? '');
        $phone = preg_replace('/[^0-9]/', '', $_GET['phone'] ?? '');

        $order = null;
        $error = null;

        if (!empty($orderNumber)) {
            $found = Order::findByOrderNumber($orderNumber);
            if ($found && (!empty($phone) ? $found['guest_phone'] === $phone : true)) {
                $order = $found;
            } else {
                $error = "No order found matching order number and phone.";
            }
        }

        View::render('orders/track', [
            'title' => 'Track Your Coconut Order | N.A Fresh Fruits & Coconuts',
            'order' => $order,
            'searchOrderNumber' => $orderNumber,
            'searchPhone' => $phone,
            'error' => $error
        ]);
    }
}
