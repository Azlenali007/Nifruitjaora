<?php

namespace App\Admin\Middleware;

class AdminAuth {
    public function handle(array $request, array $params): bool {
        if (!admin_auth()) {
            $_SESSION['admin_intended_url'] = $_SERVER['REQUEST_URI'];
            flash('error', 'Please log in to access the administration panel.');
            redirect('/admin/login');
            return false;
        }
        return true;
    }
}
