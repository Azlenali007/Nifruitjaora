<?php

namespace App\Admin\Controllers;

use App\Core\View;
use App\Core\Database;
use PDO;

class ReportController {
    public function index(): void {
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01'); // First of current month
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');

        $pdo = Database::getConnection();

        // Daily Sales Report
        $dailySalesStmt = $pdo->prepare("
            SELECT DATE(created_at) as report_date,
                   COUNT(id) as total_orders,
                   COALESCE(SUM(total_amount), 0) as paid_revenue,
                   COALESCE(SUM(delivery_charge), 0) as delivery_revenue,
                   COALESCE(SUM(discount_amount), 0) as total_discounts
            FROM `orders`
            WHERE `payment_status` = 'paid'
              AND DATE(created_at) BETWEEN ? AND ?
            GROUP BY DATE(created_at)
            ORDER BY report_date DESC
        ");
        $dailySalesStmt->execute([$dateFrom, $dateTo]);
        $dailySales = $dailySalesStmt->fetchAll();

        // Product Breakdown
        $productSalesStmt = $pdo->prepare("
            SELECT oi.product_name,
                   SUM(oi.quantity) as units_sold,
                   SUM(oi.total_price) as gross_revenue
            FROM `order_items` oi
            JOIN `orders` o ON oi.order_id = o.id
            WHERE o.payment_status = 'paid'
              AND DATE(o.created_at) BETWEEN ? AND ?
            GROUP BY oi.product_name
            ORDER BY units_sold DESC
        ");
        $productSalesStmt->execute([$dateFrom, $dateTo]);
        $productSales = $productSalesStmt->fetchAll();

        // Payment status breakdown
        $paymentBreakdown = $pdo->query("
            SELECT payment_status, COUNT(id) as count, COALESCE(SUM(total_amount), 0) as amount
            FROM `orders`
            GROUP BY payment_status
        ")->fetchAll();

        View::render('admin/reports/index', [
            'title' => 'Financial & Sales Reports | Admin',
            'dailySales' => $dailySales,
            'productSales' => $productSales,
            'paymentBreakdown' => $paymentBreakdown,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }

    public function exportCsv(): void {
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT o.order_number, o.created_at, o.guest_name, o.guest_phone,
                   o.payment_status, o.fulfillment_status, o.subtotal, o.discount_amount,
                   o.delivery_charge, o.total_amount
            FROM `orders` o
            WHERE DATE(o.created_at) BETWEEN ? AND ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$dateFrom, $dateTo]);
        $orders = $stmt->fetchAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=sales_report_' . date('Ymd_His') . '.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Order Number', 'Date', 'Customer Name', 'Phone', 'Payment Status', 'Fulfillment Status', 'Subtotal', 'Discount', 'Delivery', 'Total Amount']);

        foreach ($orders as $o) {
            fputcsv($output, [
                $o['order_number'],
                $o['created_at'],
                $o['guest_name'],
                $o['guest_phone'],
                $o['payment_status'],
                $o['fulfillment_status'],
                $o['subtotal'],
                $o['discount_amount'],
                $o['delivery_charge'],
                $o['total_amount']
            ]);
        }
        fclose($output);
        exit;
    }
}
