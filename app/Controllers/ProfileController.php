<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\User;
use App\Models\Order;
use App\Models\Address;

class ProfileController {
    public function index(): void {
        $customer = auth();
        if (!$customer) {
            redirect('/login');
            return;
        }

        $user = User::findById($customer['id']);
        $recentOrders = Order::getCustomerOrders($user['id']);
        $addresses = Address::getByUserId($user['id']);

        View::render('profile/index', [
            'title' => 'My Account | N.A Fresh Fruits & Coconuts',
            'user' => $user,
            'recentOrders' => array_slice($recentOrders, 0, 5),
            'addresses' => $addresses
        ]);
    }

    public function update(): void {
        $customer = auth();
        if (!$customer) {
            redirect('/login');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($name)) {
            flash('error', 'Name cannot be empty.');
            redirect('/profile');
            return;
        }

        User::update($customer['id'], [
            'name' => $name,
            'email' => !empty($email) ? $email : null
        ]);

        $_SESSION['customer']['name'] = $name;
        $_SESSION['customer']['email'] = $email;

        flash('success', 'Profile updated successfully.');
        redirect('/profile');
    }
}
