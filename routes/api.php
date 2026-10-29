<?php

/** @var App\Core\Router $router */

use App\Controllers\PaymentController;
use App\Controllers\CartController;
use App\Middleware\RateLimitMiddleware;

// Razorpay Payment Verification
$router->post('/api/payments/razorpay/verify', [PaymentController::class, 'verify'], [RateLimitMiddleware::class]);
$router->post('/api/payments/razorpay/failed', [PaymentController::class, 'failed']);
$router->post('/api/payments/razorpay/webhook', [PaymentController::class, 'webhook']);

// Cart API
$router->get('/api/cart', [CartController::class, 'summary']);
$router->post('/api/cart/add', [CartController::class, 'add']);
$router->post('/api/cart/update', [CartController::class, 'update']);
$router->post('/api/cart/remove', [CartController::class, 'remove']);
$router->post('/api/cart/clear', [CartController::class, 'clear']);
