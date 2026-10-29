<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Customer Directory</h1>
            <p class="text-xs text-slate-500 mt-1">Manage registered accounts and order history totals.</p>
        </div>
        <div class="text-xs font-bold text-slate-500 bg-white px-4 py-2 rounded-2xl border border-slate-200">
            Total Customers: <strong class="text-brand-600"><?= $totalCustomers ?></strong>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider border-b border-slate-200">
                    <th class="py-3 px-6 font-bold">Customer Name</th>
                    <th class="py-3 px-4 font-bold">Mobile Phone</th>
                    <th class="py-3 px-4 font-bold">Email</th>
                    <th class="py-3 px-4 font-bold">Orders Placed</th>
                    <th class="py-3 px-4 font-bold">Total Spent</th>
                    <th class="py-3 px-4 font-bold">Registered Date</th>
                    <th class="py-3 px-6 font-bold text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                <?php if (empty($customers)): ?>
                <tr><td colspan="7" class="py-8 text-center text-slate-400">No registered customers yet.</td></tr>
                <?php else: ?>
                <?php foreach ($customers as $cust): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3.5 px-6 font-bold text-slate-900"><?= e($cust['name']) ?></td>
                    <td class="py-3.5 px-4 font-bold text-brand-700"><?= e($cust['phone']) ?></td>
                    <td class="py-3.5 px-4 text-slate-500"><?= e($cust['email'] ?? '&minus;') ?></td>
                    <td class="py-3.5 px-4 font-bold text-slate-800"><?= $cust['order_count'] ?> orders</td>
                    <td class="py-3.5 px-4 font-black text-slate-900"><?= format_price($cust['total_spent']) ?></td>
                    <td class="py-3.5 px-4 text-slate-400 text-[11px]"><?= date('d M Y', strtotime($cust['created_at'])) ?></td>
                    <td class="py-3.5 px-6 text-right">
                        <a href="/admin/customers/<?= $cust['id'] ?>" class="btn-secondary text-[11px] py-1.5 px-3">
                            View Profile
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
