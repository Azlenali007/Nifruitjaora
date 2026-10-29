<?php

namespace App\Admin\Controllers;

use App\Core\View;
use App\Models\User;
use App\Models\Order;
use App\Models\Address;

class CustomerController {
    public function index(): void {
        $customers = User::all(50, 0);
        $totalCustomers = User::count();

        View::render('admin/customers/index', [
            'title' => 'Customer Management | Admin',
            'customers' => $customers,
            'totalCustomers' => $totalCustomers
        ]);
    }

    public function show(string $id): void {
        $user = User::findById((int)$id);
        if (!$user) {
            flash('error', 'Customer not found.');
            redirect('/admin/customers');
            return;
        }

        $orders = Order::getCustomerOrders($user['id']);
        $addresses = Address::getByUserId($user['id']);

        View::render('admin/customers/show', [
            'title' => "Customer Profile: {$user['name']} | Admin",
            'user' => $user,
            'orders' => $orders,
            'addresses' => $addresses
        ]);
    }
}
