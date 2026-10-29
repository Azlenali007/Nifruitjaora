<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Overview Dashboard</h1>
            <p class="text-xs text-slate-500 mt-1">Real-time metrics, coconut inventory status, and sales revenue.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="/admin/products/create" class="btn-primary text-xs py-2.5 px-4">
                + Add New Coconut
            </a>
            <a href="/admin/reports" class="btn-secondary text-xs py-2.5 px-4">
                View Reports
            </a>
        </div>
    </div>

    <!-- 4 KPI Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- KPI 1: Today's Sales -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Today's Sales</span>
            <div class="text-2xl font-black text-brand-600 mt-2"><?= format_price($stats['today_sales']) ?></div>
            <span class="text-[11px] text-slate-500 mt-1 block"><?= $stats['today_orders'] ?> orders placed today</span>
        </div>

        <!-- KPI 2: Total Paid Revenue -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Paid Revenue</span>
            <div class="text-2xl font-black text-slate-900 mt-2"><?= format_price($stats['total_revenue']) ?></div>
            <span class="text-[11px] text-brand-600 font-bold mt-1 block">Verified via Razorpay</span>
        </div>

        <!-- KPI 3: Pending Orders -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Orders to Fulfill</span>
            <div class="text-2xl font-black text-amber-500 mt-2"><?= $stats['pending_orders'] ?></div>
            <span class="text-[11px] text-slate-500 mt-1 block">Awaiting packaging / delivery</span>
        </div>

        <!-- KPI 4: Total Refunds -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Refunds</span>
            <div class="text-2xl font-black text-slate-700 mt-2"><?= format_price($stats['total_refunds']) ?></div>
            <span class="text-[11px] text-slate-400 mt-1 block"><?= $stats['cancelled_orders'] ?> cancelled orders</span>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Trend Line Chart (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-black text-slate-900">7-Day Sales Trend (₹)</h3>
                <span class="text-xs text-slate-400">Database Confirmed</span>
            </div>
            <div class="h-64">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Orders Breakdown Donut (1 Col) -->
        <div class="lg:col-span-1 bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-black text-slate-900">Order Fulfillment</h3>
                <span class="text-xs text-slate-400">Status</span>
            </div>
            <div class="h-56 relative flex items-center justify-center">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables Row: Recent Orders & Top Coconuts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900">Recent Incoming Orders</h3>
                <a href="/admin/orders" class="text-xs font-bold text-brand-600 hover:underline">View All &rarr;</a>
            </div>

            <?php if (empty($recentOrders)): ?>
            <p class="text-xs text-slate-400 py-6 text-center">No orders recorded in database yet.</p>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="pb-2 font-bold">Order #</th>
                            <th class="pb-2 font-bold">Customer</th>
                            <th class="pb-2 font-bold">Payment</th>
                            <th class="pb-2 font-bold">Status</th>
                            <th class="pb-2 font-bold">Total</th>
                            <th class="pb-2 font-bold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php foreach (array_slice($recentOrders, 0, 6) as $ord): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 font-mono font-bold text-brand-700"><?= e($ord['order_number']) ?></td>
                            <td class="py-3"><?= e($ord['guest_name']) ?></td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    <?= $ord['payment_status'] === 'paid' ? 'bg-brand-100 text-brand-800' : 'bg-amber-100 text-amber-800' ?>">
                                    <?= e($ord['payment_status']) ?>
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                    <?= e(str_replace('_', ' ', $ord['fulfillment_status'])) ?>
                                </span>
                            </td>
                            <td class="py-3 font-bold"><?= format_price($ord['total_amount']) ?></td>
                            <td class="py-3 text-right">
                                <a href="/admin/orders/<?= $ord['id'] ?>" class="text-brand-600 hover:text-brand-800 font-bold">Manage</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Top Selling Products (1 Col) -->
        <div class="lg:col-span-1 bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-sm font-black text-slate-900 pb-3 border-b border-slate-100 mb-4">Top-Selling Coconuts</h3>

            <?php if (empty($topProducts)): ?>
            <p class="text-xs text-slate-400 py-6 text-center">No sales history yet.</p>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($topProducts as $idx => $tp): ?>
                <div class="flex items-center justify-between text-xs py-2 border-b border-slate-50 last:border-0">
                    <div class="flex items-center gap-3">
                        <span class="w-5 h-5 rounded-full bg-slate-100 font-bold flex items-center justify-center text-slate-600 text-[10px]"><?= $idx + 1 ?></span>
                        <span class="font-bold text-slate-800"><?= e($tp['product_name']) ?></span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-slate-900 block"><?= $tp['units_sold'] ?> sold</span>
                        <span class="text-[10px] text-brand-600 font-bold"><?= format_price($tp['gross_revenue']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Initialize Charts -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        initAdminCharts(<?= json_encode($salesTrend) ?>, <?= json_encode($statusDist) ?>);
    });
</script>
