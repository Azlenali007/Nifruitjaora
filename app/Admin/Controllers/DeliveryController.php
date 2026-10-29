<?php

namespace App\Admin\Controllers;

use App\Core\View;
use App\Models\Setting;
use App\Models\ActivityLog;

class DeliveryController {
    public function index(): void {
        $zones = Setting::allDeliveryZones();
        $settings = Setting::all();

        View::render('admin/delivery/index', [
            'title' => 'Delivery Rules & Serviceable Areas | Admin',
            'zones' => $zones,
            'settings' => $settings
        ]);
    }

    public function updateRules(): void {
        $defaultCharge = (float)($_POST['default_delivery_charge'] ?? 30);
        $minOrder = (float)($_POST['min_order_amount'] ?? 80);
        $freeThreshold = (float)($_POST['free_delivery_threshold'] ?? 250);

        Setting::set('default_delivery_charge', $defaultCharge, 'number', 'Default delivery fee');
        Setting::set('min_order_amount', $minOrder, 'number', 'Minimum order threshold');
        Setting::set('free_delivery_threshold', $freeThreshold, 'number', 'Free delivery threshold');

        ActivityLog::log('update_delivery_rules', 'Updated general delivery pricing rules');
        flash('success', 'Delivery rules saved successfully.');
        redirect('/admin/delivery');
    }

    public function saveZone(): void {
        $pincode = trim($_POST['pincode'] ?? '');
        $area = trim($_POST['area_name'] ?? '');

        if (empty($pincode) || strlen($pincode) !== 6 || empty($area)) {
            flash('error', 'Please provide a valid 6-digit PIN code and area name.');
            redirect('/admin/delivery');
            return;
        }

        Setting::saveDeliveryZone([
            'pincode' => $pincode,
            'area_name' => $area,
            'delivery_charge' => (float)($_POST['delivery_charge'] ?? 30),
            'min_order_amount' => (float)($_POST['min_order_amount'] ?? 80),
            'estimated_time' => trim($_POST['estimated_time'] ?? '30 - 45 mins'),
            'is_serviceable' => isset($_POST['is_serviceable']) ? 1 : 0
        ]);

        ActivityLog::log('save_delivery_zone', "Saved delivery zone PIN: {$pincode} ({$area})");
        flash('success', "Serviceable PIN code {$pincode} updated successfully.");
        redirect('/admin/delivery');
    }

    public function deleteZone(string $id): void {
        Setting::deleteDeliveryZone((int)$id);
        ActivityLog::log('delete_delivery_zone', "Deleted delivery zone ID: {$id}");
        flash('success', 'Serviceable PIN code removed.');
        redirect('/admin/delivery');
    }
}
