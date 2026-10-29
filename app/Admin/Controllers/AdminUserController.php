<?php

namespace App\Admin\Controllers;

use App\Core\View;
use App\Models\AdminUser;
use App\Models\ActivityLog;
use App\Helpers\Validator;

class AdminUserController {
    public function index(): void {
        $admins = AdminUser::all();
        View::render('admin/admin-users/index', [
            'title' => 'Admin Team & Roles | Admin',
            'admins' => $admins
        ]);
    }

    public function store(): void {
        $validator = Validator::make($_POST, [
            'name' => 'required|min:2',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'role' => 'required'
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/admin/admin-users');
            return;
        }

        $existing = AdminUser::findByEmail(trim($_POST['email']));
        if ($existing) {
            flash('error', 'An admin user with this email already exists.');
            redirect('/admin/admin-users');
            return;
        }

        $adminId = AdminUser::create([
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'],
            'role' => $_POST['role'],
            'status' => 'active'
        ]);

        ActivityLog::log('create_admin', "Created admin account '{$_POST['email']}' (Role: {$_POST['role']})", 'admin', $adminId);
        flash('success', "Administrator account for {$_POST['name']} created successfully.");
        redirect('/admin/admin-users');
    }

    public function update(string $id): void {
        $adminId = (int)$id;
        $admin = AdminUser::findById($adminId);
        if (!$admin) {
            flash('error', 'Admin user not found.');
            redirect('/admin/admin-users');
            return;
        }

        $data = [
            'name' => trim($_POST['name'] ?? $admin['name']),
            'role' => $_POST['role'] ?? $admin['role'],
            'status' => $_POST['status'] ?? $admin['status']
        ];

        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }

        AdminUser::update($adminId, $data);
        ActivityLog::log('update_admin', "Updated admin account '{$admin['email']}'", 'admin', $adminId);

        flash('success', 'Admin user updated successfully.');
        redirect('/admin/admin-users');
    }

    public function delete(string $id): void {
        $adminId = (int)$id;
        $currentAdmin = admin_auth();
        if ($currentAdmin && (int)$currentAdmin['id'] === $adminId) {
            flash('error', 'You cannot delete your own admin account.');
            redirect('/admin/admin-users');
            return;
        }

        $admin = AdminUser::findById($adminId);
        if ($admin) {
            AdminUser::delete($adminId);
            ActivityLog::log('delete_admin', "Deleted admin '{$admin['email']}'", 'admin', $adminId);
            flash('success', 'Admin user removed.');
        }
        redirect('/admin/admin-users');
    }
}
