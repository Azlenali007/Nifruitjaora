<?php

namespace App\Admin\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Models\AdminUser;
use App\Models\ActivityLog;
use App\Helpers\Validator;

class AuthController {
    public function showLogin(): void {
        if (admin_auth()) {
            redirect('/admin/dashboard');
            return;
        }

        View::render('admin/auth/login', [
            'title' => 'Admin Sign In | N.A Fresh Fruits & Coconuts'
        ], null);
    }

    public function login(): void {
        $validator = Validator::make($_POST, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/admin/login');
            return;
        }

        $email = trim($_POST['email']);
        $admin = AdminUser::findByEmail($email);

        if (!$admin || !password_verify($_POST['password'], $admin['password_hash'])) {
            flash('error', 'Invalid administrator credentials.');
            redirect('/admin/login');
            return;
        }

        if ($admin['status'] !== 'active') {
            flash('error', 'This admin account has been suspended.');
            redirect('/admin/login');
            return;
        }

        Session::regenerate();
        AdminUser::updateLastLogin($admin['id']);

        $_SESSION['admin'] = [
            'id' => $admin['id'],
            'name' => $admin['name'],
            'email' => $admin['email'],
            'role' => $admin['role']
        ];

        ActivityLog::log('admin_login', "Admin user '{$admin['email']}' logged in successfully");

        flash('success', "Welcome back, {$admin['name']}!");
        $intended = $_SESSION['admin_intended_url'] ?? '/admin/dashboard';
        unset($_SESSION['admin_intended_url']);
        redirect($intended);
    }

    public function logout(): void {
        $admin = admin_auth();
        if ($admin) {
            ActivityLog::log('admin_logout', "Admin user '{$admin['email']}' signed out");
        }
        unset($_SESSION['admin']);
        flash('success', 'Admin session terminated.');
        redirect('/admin/login');
    }
}
