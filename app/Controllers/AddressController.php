<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Address;
use App\Models\Setting;
use App\Helpers\Validator;

class AddressController {
    public function index(): void {
        $customer = auth();
        if (!$customer) {
            redirect('/login');
            return;
        }

        $addresses = Address::getByUserId($customer['id']);
        $zones = Setting::allDeliveryZones();

        View::render('addresses/index', [
            'title' => 'My Addresses | N.A Fresh Fruits & Coconuts',
            'addresses' => $addresses,
            'zones' => $zones
        ]);
    }

    public function store(): void {
        $customer = auth();
        if (!$customer) {
            redirect('/login');
            return;
        }

        $validator = Validator::make($_POST, [
            'recipient_name' => 'required|min:2',
            'phone' => 'required|phone',
            'address_line1' => 'required|min:5',
            'pincode' => 'required|pincode'
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/addresses');
            return;
        }

        Address::create([
            'user_id' => $customer['id'],
            'recipient_name' => trim($_POST['recipient_name']),
            'phone' => preg_replace('/[^0-9]/', '', $_POST['phone']),
            'address_line1' => trim($_POST['address_line1']),
            'address_line2' => trim($_POST['address_line2'] ?? ''),
            'landmark' => trim($_POST['landmark'] ?? ''),
            'city' => trim($_POST['city'] ?? 'Mumbai'),
            'state' => trim($_POST['state'] ?? 'Maharashtra'),
            'pincode' => trim($_POST['pincode']),
            'is_default' => !empty($_POST['is_default']) ? 1 : 0
        ]);

        flash('success', 'Address added successfully.');
        redirect('/addresses');
    }

    public function delete(string $id): void {
        $customer = auth();
        if (!$customer) {
            redirect('/login');
            return;
        }

        Address::delete((int)$id, $customer['id']);
        flash('success', 'Address removed.');
        redirect('/addresses');
    }
}
