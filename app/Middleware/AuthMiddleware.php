<?php

namespace App\Middleware;

class AuthMiddleware {
    public function handle(array $request, array $params): bool {
        if (!auth()) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            flash('error', 'Please log in to continue.');
            redirect('/login');
            return false;
        }
        return true;
    }
}
