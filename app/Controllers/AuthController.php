<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Models\User;
use App\Models\Cart;
use App\Helpers\Validator;

class AuthController {
    public function showLogin(): void {
        if (auth()) {
            redirect('/profile');
            return;
        }

        View::render('auth/login', [
            'title' => 'Sign In | N.A Fresh Fruits & Coconuts'
        ], 'layouts/auth');
    }

    public function login(): void {
        $validator = Validator::make($_POST, [
            'phone' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/login');
            return;
        }

        $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
        $user = User::findByPhone($phone);

        if (!$user || !password_verify($_POST['password'], $user['password_hash'])) {
            flash('error', 'Invalid mobile number or password.');
            redirect('/login');
            return;
        }

        if ($user['status'] !== 'active') {
            flash('error', 'Your account has been deactivated. Please contact support.');
            redirect('/login');
            return;
        }

        // Regenerate session to prevent session fixation
        Session::regenerate();

        // Merge guest cart into user cart
        $guestSessionId = session_id();
        Cart::mergeGuestCartIntoUserCart($guestSessionId, $user['id']);

        $_SESSION['customer'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone']
        ];

        flash('success', "Welcome back, {$user['name']}!");
        $intended = $_SESSION['intended_url'] ?? '/profile';
        unset($_SESSION['intended_url']);
        redirect($intended);
    }

    public function showRegister(): void {
        if (auth()) {
            redirect('/profile');
            return;
        }

        View::render('auth/register', [
            'title' => 'Create Customer Account | N.A Fresh Fruits & Coconuts'
        ], 'layouts/auth');
    }

    public function register(): void {
        $validator = Validator::make($_POST, [
            'name' => 'required|min:2|max:100',
            'phone' => 'required|phone',
            'password' => 'required|min:6',
            'email' => 'email'
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/register');
            return;
        }

        $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
        $existing = User::findByPhone($phone);
        if ($existing) {
            flash('error', 'An account with this mobile number already exists. Please log in.');
            redirect('/login');
            return;
        }

        $userId = User::create([
            'name' => trim($_POST['name']),
            'phone' => $phone,
            'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
            'password' => $_POST['password']
        ]);

        Session::regenerate();
        Cart::mergeGuestCartIntoUserCart(session_id(), $userId);

        $user = User::findById($userId);
        $_SESSION['customer'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone']
        ];

        flash('success', 'Account created successfully! Welcome to N.A Fresh Fruits & Coconuts.');
        redirect('/profile');
    }

    public function logout(): void {
        unset($_SESSION['customer']);
        flash('success', 'You have been signed out.');
        redirect('/');
    }

    public function showForgotPassword(): void {
        View::render('auth/forgot-password', [
            'title' => 'Forgot Password | N.A Fresh Fruits & Coconuts'
        ], 'layouts/auth');
    }

    public function forgotPassword(): void {
        $phone = preg_replace('/[^0-9]/', '', $_POST['phone'] ?? '');
        $user = User::findByPhone($phone);

        if ($user) {
            // Simulated secure reset SMS/token notification in dev mode
            flash('success', 'Password reset instructions have been sent to your registered mobile number.');
        } else {
            flash('error', 'No account found with that mobile number.');
        }

        redirect('/forgot-password');
    }
}
