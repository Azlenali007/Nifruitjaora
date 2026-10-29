<?php

namespace App\Admin\Controllers;

use App\Core\View;
use App\Models\Coupon;
use App\Models\ActivityLog;
use App\Helpers\Validator;

class CouponController {
    public function index(): void {
        $coupons = Coupon::all();
        View::render('admin/coupons/index', [
            'title' => 'Coupons & Discounts | Admin',
            'coupons' => $coupons
        ]);
    }

    public function create(): void {
        View::render('admin/coupons/form', [
            'title' => 'Create Coupon | Admin',
            'coupon' => null
        ]);
    }

    public function store(): void {
        $validator = Validator::make($_POST, [
            'code' => 'required|min:3',
            'discount_value' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/admin/coupons/create');
            return;
        }

        $couponId = Coupon::create([
            'code' => trim($_POST['code']),
            'discount_type' => $_POST['discount_type'] ?? 'percentage',
            'discount_value' => (float)$_POST['discount_value'],
            'min_order_amount' => (float)($_POST['min_order_amount'] ?? 0),
            'max_discount_amount' => !empty($_POST['max_discount_amount']) ? (float)$_POST['max_discount_amount'] : null,
            'usage_limit' => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'starts_at' => !empty($_POST['starts_at']) ? $_POST['starts_at'] : null,
            'expires_at' => !empty($_POST['expires_at']) ? $_POST['expires_at'] : null,
        ]);

        ActivityLog::log('create_coupon', "Created coupon '{$_POST['code']}'", 'coupon', $couponId);
        flash('success', "Coupon '{$_POST['code']}' created successfully!");
        redirect('/admin/coupons');
    }

    public function edit(string $id): void {
        $coupon = Coupon::findById((int)$id);
        if (!$coupon) {
            flash('error', 'Coupon not found.');
            redirect('/admin/coupons');
            return;
        }

        View::render('admin/coupons/form', [
            'title' => "Edit Coupon: {$coupon['code']} | Admin",
            'coupon' => $coupon
        ]);
    }

    public function update(string $id): void {
        $couponId = (int)$id;
        $coupon = Coupon::findById($couponId);
        if (!$coupon) {
            flash('error', 'Coupon not found.');
            redirect('/admin/coupons');
            return;
        }

        Coupon::update($couponId, [
            'code' => strtoupper(trim($_POST['code'])),
            'discount_type' => $_POST['discount_type'] ?? 'percentage',
            'discount_value' => (float)$_POST['discount_value'],
            'min_order_amount' => (float)($_POST['min_order_amount'] ?? 0),
            'max_discount_amount' => !empty($_POST['max_discount_amount']) ? (float)$_POST['max_discount_amount'] : null,
            'usage_limit' => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'starts_at' => !empty($_POST['starts_at']) ? $_POST['starts_at'] : null,
            'expires_at' => !empty($_POST['expires_at']) ? $_POST['expires_at'] : null,
        ]);

        ActivityLog::log('update_coupon', "Updated coupon '{$coupon['code']}'", 'coupon', $couponId);
        flash('success', 'Coupon updated successfully.');
        redirect('/admin/coupons');
    }

    public function delete(string $id): void {
        $couponId = (int)$id;
        $coupon = Coupon::findById($couponId);
        if ($coupon) {
            Coupon::delete($couponId);
            ActivityLog::log('delete_coupon', "Deleted coupon '{$coupon['code']}'", 'coupon', $couponId);
            flash('success', 'Coupon deleted.');
        }
        redirect('/admin/coupons');
    }
}
