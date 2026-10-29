<?php

namespace App\Admin\Controllers;

use App\Core\View;
use App\Core\Database;
use App\Models\Order;
use App\Models\Product;

class DashboardController {
    public function index(): void {
        $stats = Order::getSalesStats();
        $recentOrders = Order::all([], 10, 0);

        // Sales trend for last 7 days from real database records
        $pdo = Database::getConnection();
        $salesTrendStmt = $pdo->query("
            SELECT DATE(created_at) as sale_date, 
                   COALESCE(SUM(total_amount), 0) as total_amount,
                   COUNT(id) as total_orders
            FROM `orders`
            WHERE `payment_status` = 'paid' AND `created_at` >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY sale_date ASC
        ");
        $salesTrend = $salesTrendStmt->fetchAll();

        // Fulfillment status distribution
        $statusDistStmt = $pdo->query("
            SELECT fulfillment_status, COUNT(id) as count
            FROM `orders`
            GROUP BY fulfillment_status
        ");
        $statusDist = $statusDistStmt->fetchAll();

        // Top selling products
        $topProductsStmt = $pdo->query("
            SELECT oi.product_name, SUM(oi.quantity) as units_sold, SUM(oi.total_price) as gross_revenue, oi.product_image
            FROM `order_items` oi
            JOIN `orders` o ON oi.order_id = o.id
            WHERE o.payment_status = 'paid'
            GROUP BY oi.product_name, oi.product_image
            ORDER BY units_sold DESC
            LIMIT 5
        ");
        $topProducts = $topProductsStmt->fetchAll();

        View::render('admin/dashboard/index', [
            'title' => 'Admin Dashboard | N.A Fresh Fruits & Coconuts',
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'salesTrend' => $salesTrend,
            'statusDist' => $statusDist,
            'topProducts' => $topProducts
        ]);
    }
}
