<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Sales &amp; Revenue Reports</h1>
            <p class="text-xs text-slate-500 mt-1">Audit real payments and generate verified CSV sales reports.</p>
        </div>
        <a href="/admin/reports/export-csv?date_from=<?= urlencode($dateFrom) ?>&date_to=<?= urlencode($dateTo) ?>" 
           class="btn-primary text-xs py-2.5 px-5 flex items-center gap-2">
            <span>Download CSV Export</span>
        </a>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200">
        <form action="/admin/reports" method="GET" class="flex flex-wrap items-center gap-4 text-xs">
            <div class="flex items-center gap-2">
                <label class="font-bold text-slate-700">From:</label>
                <input type="date" name="date_from" value="<?= e($dateFrom) ?>" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl">
            </div>
            <div class="flex items-center gap-2">
                <label class="font-bold text-slate-700">To:</label>
                <input type="date" name="date_to" value="<?= e($dateTo) ?>" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl">
            </div>
            <button type="submit" class="btn-secondary text-xs py-1.5 px-4">Generate Report</button>
        </form>
    </div>

    <!-- Daily Breakdown Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-black text-slate-900">Daily Revenue Summary</h3>
            <p class="text-xs text-slate-400">Strictly counts verified paid transactions only.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-6 font-bold">Date</th>
                        <th class="py-3 px-4 font-bold">Paid Orders</th>
                        <th class="py-3 px-4 font-bold">Delivery Collected</th>
                        <th class="py-3 px-4 font-bold">Discounts Given</th>
                        <th class="py-3 px-6 font-bold text-right">Net Paid Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <?php if (empty($dailySales)): ?>
                    <tr><td colspan="5" class="py-8 text-center text-slate-400">No paid orders in this date range.</td></tr>
                    <?php else: ?>
                    <?php foreach ($dailySales as $ds): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3.5 px-6 font-bold text-slate-900"><?= date('d M Y', strtotime($ds['report_date'])) ?></td>
                        <td class="py-3.5 px-4 font-bold text-brand-700"><?= $ds['total_orders'] ?> orders</td>
                        <td class="py-3.5 px-4 text-slate-600"><?= format_price($ds['delivery_revenue']) ?></td>
                        <td class="py-3.5 px-4 text-slate-600"><?= format_price($ds['total_discounts']) ?></td>
                        <td class="py-3.5 px-6 text-right font-black text-brand-600 text-sm"><?= format_price($ds['paid_revenue']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Product Volume Breakdown -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-black text-slate-900">Product Volume Breakdown</h3>
        </div>
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider border-b border-slate-200">
                    <th class="py-3 px-6 font-bold">Coconut Product</th>
                    <th class="py-3 px-4 font-bold">Units Sold</th>
                    <th class="py-3 px-6 font-bold text-right">Gross Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                <?php if (empty($productSales)): ?>
                <tr><td colspan="3" class="py-8 text-center text-slate-400">No product sales in this period.</td></tr>
                <?php else: ?>
                <?php foreach ($productSales as $ps): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3.5 px-6 font-bold text-slate-900"><?= e($ps['product_name']) ?></td>
                    <td class="py-3.5 px-4 font-bold text-slate-800"><?= $ps['units_sold'] ?> units</td>
                    <td class="py-3.5 px-6 text-right font-black text-slate-900"><?= format_price($ps['gross_revenue']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
