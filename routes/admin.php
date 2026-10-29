<?php

/** @var App\Core\Router $router */

use App\Admin\Controllers\DashboardController;
use App\Admin\Controllers\ProductController;
use App\Admin\Controllers\CategoryController;
use App\Admin\Controllers\OrderController;
use App\Admin\Controllers\CustomerController;
use App\Admin\Controllers\PaymentController;
use App\Admin\Controllers\CouponController;
use App\Admin\Controllers\ReportController;
use App\Admin\Controllers\HomepageController;
use App\Admin\Controllers\DeliveryController;
use App\Admin\Controllers\SettingsController;
use App\Admin\Controllers\AdminUserController;
use App\Admin\Controllers\ActivityLogController;
use App\Admin\Controllers\AuthController as AdminAuthController;
use App\Admin\Middleware\AdminAuth;
use App\Middleware\CsrfMiddleware;

// Admin Auth Routes (Public to unauthenticated visitors)
$router->get('/admin/login', [AdminAuthController::class, 'showLogin']);
$router->post('/admin/login', [AdminAuthController::class, 'login'], [CsrfMiddleware::class]);
$router->post('/admin/logout', [AdminAuthController::class, 'logout'], [CsrfMiddleware::class]);

// Admin Protected Group
$router->group(['prefix' => 'admin', 'middleware' => [AdminAuth::class]], function ($router) {
    // Dashboard
    $router->get('/dashboard', [DashboardController::class, 'index']);
    $router->get('/', [DashboardController::class, 'index']);

    // Products
    $router->get('/products', [ProductController::class, 'index']);
    $router->get('/products/create', [ProductController::class, 'create']);
    $router->post('/products/store', [ProductController::class, 'store'], [CsrfMiddleware::class]);
    $router->get('/products/{id}/edit', [ProductController::class, 'edit']);
    $router->post('/products/{id}/update', [ProductController::class, 'update'], [CsrfMiddleware::class]);
    $router->post('/products/{id}/delete', [ProductController::class, 'delete'], [CsrfMiddleware::class]);

    // Categories
    $router->get('/categories', [CategoryController::class, 'index']);
    $router->get('/categories/create', [CategoryController::class, 'create']);
    $router->post('/categories/store', [CategoryController::class, 'store'], [CsrfMiddleware::class]);
    $router->get('/categories/{id}/edit', [CategoryController::class, 'edit']);
    $router->post('/categories/{id}/update', [CategoryController::class, 'update'], [CsrfMiddleware::class]);
    $router->post('/categories/{id}/delete', [CategoryController::class, 'delete'], [CsrfMiddleware::class]);

    // Orders
    $router->get('/orders', [OrderController::class, 'index']);
    $router->get('/orders/{id}', [OrderController::class, 'show']);
    $router->post('/orders/{id}/status', [OrderController::class, 'updateStatus'], [CsrfMiddleware::class]);
    $router->post('/orders/{id}/refund', [OrderController::class, 'refund'], [CsrfMiddleware::class]);

    // Customers
    $router->get('/customers', [CustomerController::class, 'index']);
    $router->get('/customers/{id}', [CustomerController::class, 'show']);

    // Payments & Razorpay
    $router->get('/payments', [PaymentController::class, 'index']);
    $router->post('/payments/settings', [PaymentController::class, 'updateSettings'], [CsrfMiddleware::class]);

    // Coupons
    $router->get('/coupons', [CouponController::class, 'index']);
    $router->get('/coupons/create', [CouponController::class, 'create']);
    $router->post('/coupons/store', [CouponController::class, 'store'], [CsrfMiddleware::class]);
    $router->get('/coupons/{id}/edit', [CouponController::class, 'edit']);
    $router->post('/coupons/{id}/update', [CouponController::class, 'update'], [CsrfMiddleware::class]);
    $router->post('/coupons/{id}/delete', [CouponController::class, 'delete'], [CsrfMiddleware::class]);

    // Reports
    $router->get('/reports', [ReportController::class, 'index']);
    $router->get('/reports/export-csv', [ReportController::class, 'exportCsv']);

    // Homepage & Carousel
    $router->get('/homepage', [HomepageController::class, 'index']);
    $router->post('/homepage/update', [HomepageController::class, 'update'], [CsrfMiddleware::class]);

    // Delivery & PIN Codes
    $router->get('/delivery', [DeliveryController::class, 'index']);
    $router->post('/delivery/rules', [DeliveryController::class, 'updateRules'], [CsrfMiddleware::class]);
    $router->post('/delivery/zones/save', [DeliveryController::class, 'saveZone'], [CsrfMiddleware::class]);
    $router->post('/delivery/zones/{id}/delete', [DeliveryController::class, 'deleteZone'], [CsrfMiddleware::class]);

    // Settings
    $router->get('/settings', [SettingsController::class, 'index']);
    $router->post('/settings/update', [SettingsController::class, 'update'], [CsrfMiddleware::class]);

    // Admin Users
    $router->get('/admin-users', [AdminUserController::class, 'index']);
    $router->post('/admin-users/store', [AdminUserController::class, 'store'], [CsrfMiddleware::class]);
    $router->post('/admin-users/{id}/update', [AdminUserController::class, 'update'], [CsrfMiddleware::class]);
    $router->post('/admin-users/{id}/delete', [AdminUserController::class, 'delete'], [CsrfMiddleware::class]);

    // Activity Logs
    $router->get('/activity-logs', [ActivityLogController::class, 'index']);
});
