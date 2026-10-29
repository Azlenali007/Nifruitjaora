<?php

/** @var App\Core\Router $router */

use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\OrderController;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\AddressController;
use App\Controllers\InstallController;
use App\Middleware\CsrfMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\RateLimitMiddleware;

// Web Installer
$router->get('/install', [InstallController::class, 'index']);
$router->post('/install', [InstallController::class, 'index']);

// Public Customer Routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/{slug}', [ProductController::class, 'show']);

// Cart Routes
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add'], [CsrfMiddleware::class]);
$router->post('/cart/update', [CartController::class, 'update'], [CsrfMiddleware::class]);
$router->post('/cart/remove', [CartController::class, 'remove'], [CsrfMiddleware::class]);
$router->post('/cart/clear', [CartController::class, 'clear'], [CsrfMiddleware::class]);
$router->post('/cart/coupon/apply', [CartController::class, 'applyCoupon'], [CsrfMiddleware::class]);
$router->post('/cart/coupon/remove', [CartController::class, 'removeCoupon'], [CsrfMiddleware::class]);

// Checkout
$router->get('/checkout', [CheckoutController::class, 'index']);
$router->post('/checkout/submit', [CheckoutController::class, 'submit'], [CsrfMiddleware::class]);
$router->get('/checkout/confirmation/{orderNumber}', [CheckoutController::class, 'confirmation']);

// Order Tracking
$router->get('/track', [OrderController::class, 'track']);
$router->get('/orders/{orderNumber}', [OrderController::class, 'show']);

// Customer Authentication
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login'], [CsrfMiddleware::class, RateLimitMiddleware::class]);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register'], [CsrfMiddleware::class, RateLimitMiddleware::class]);
$router->post('/logout', [AuthController::class, 'logout'], [CsrfMiddleware::class]);
$router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword'], [CsrfMiddleware::class]);

// Protected Customer Account
$router->get('/profile', [ProfileController::class, 'index'], [AuthMiddleware::class]);
$router->post('/profile/update', [ProfileController::class, 'update'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/my-orders', [OrderController::class, 'index'], [AuthMiddleware::class]);
$router->get('/addresses', [AddressController::class, 'index'], [AuthMiddleware::class]);
$router->post('/addresses/store', [AddressController::class, 'store'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/addresses/{id}/delete', [AddressController::class, 'delete'], [AuthMiddleware::class, CsrfMiddleware::class]);
