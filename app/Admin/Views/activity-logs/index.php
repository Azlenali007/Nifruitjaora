<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Security &amp; Activity Audit Logs</h1>
        <p class="text-xs text-slate-500 mt-1">Immutable chronological audit trail of all administrative actions and logins.</p>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider border-b border-slate-200">
                    <th class="py-3 px-6 font-bold">Timestamp</th>
                    <th class="py-3 px-4 font-bold">Admin User</th>
                    <th class="py-3 px-4 font-bold">Action Event</th>
                    <th class="py-3 px-4 font-bold">Details Description</th>
                    <th class="py-3 px-6 font-bold text-right">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                <?php if (empty($logs)): ?>
                <tr><td colspan="5" class="py-8 text-center text-slate-400">No activity logs recorded yet.</td></tr>
                <?php else: ?>
                <?php foreach ($logs as $l): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-6 text-slate-400 font-mono text-[11px]"><?= date('d M Y, h:i:s A', strtotime($l['created_at'])) ?></td>
                    <td class="py-3 px-4 font-bold text-slate-900"><?= e($l['admin_name'] ?? 'System') ?></td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-slate-100 text-slate-800">
                            <?= e($l['action']) ?>
                        </span>
                    </td>
                    <td class="py-3 px-4 text-slate-600"><?= e($l['description']) ?></td>
                    <td class="py-3 px-6 text-right font-mono text-slate-400 text-[11px]"><?= e($l['ip_address'] ?? '127.0.0.1') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
