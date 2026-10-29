<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Razorpay Gateway &amp; Payment Logs</h1>
        <p class="text-xs text-slate-500 mt-1">Configure payment credentials, environment modes, and inspect transaction logs.</p>
    </div>

    <!-- Razorpay Configuration Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <h3 class="text-base font-black text-slate-900 pb-3 border-b border-slate-100 mb-6 flex items-center justify-between">
            <span>Razorpay Payment Gateway Settings</span>
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $razorpayEnabled ? 'bg-brand-100 text-brand-800' : 'bg-red-100 text-red-800' ?>">
                <?= $razorpayEnabled ? 'Gateway Active (Accepting Payments)' : 'Gateway Disabled' ?>
            </span>
        </h3>

        <form action="/admin/payments/settings" method="POST" class="space-y-5 text-xs">
            <?= csrf_field() ?>

            <!-- ON/OFF Toggle & Environment Mode -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between">
                    <div>
                        <strong class="font-bold text-slate-800 block">Gateway Online Status</strong>
                        <span class="text-slate-500 text-[11px]">When disabled, online checkout is blocked on server.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="razorpay_enabled" value="1" <?= $razorpayEnabled ? 'checked' : '' ?> class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                    </label>
                </div>

                <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between">
                    <div>
                        <strong class="font-bold text-slate-800 block">Operating Environment Mode</strong>
                        <span class="text-slate-500 text-[11px]">Toggle between test sandbox and live production.</span>
                    </div>
                    <select name="razorpay_mode" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl font-bold">
                        <option value="test" <?= $razorpayMode === 'test' ? 'selected' : '' ?>>Test Mode (Sandbox)</option>
                        <option value="live" <?= $razorpayMode === 'live' ? 'selected' : '' ?>>Live Mode (Production)</option>
                    </select>
                </div>
            </div>

            <!-- Credentials -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Razorpay Key ID</label>
                    <input type="text" name="razorpay_key_id" value="<?= e($keyId) ?>" placeholder="rzp_test_... or rzp_live_..."
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Razorpay Key Secret</label>
                    <input type="password" name="razorpay_key_secret" placeholder="••••••••••••••••••••"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <span class="text-[10px] text-slate-400 mt-1 block">Leave blank to retain existing configured secret securely.</span>
                </div>
            </div>

            <!-- Webhook Secret -->
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Razorpay Webhook Secret</label>
                <input type="text" name="razorpay_webhook_secret" value="<?= e($webhookSecret) ?>" placeholder="Optional webhook verification secret"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <!-- Webhook Instructions Box -->
            <div class="p-4 bg-brand-50/70 border border-brand-200 rounded-2xl space-y-1">
                <strong class="font-bold text-brand-900 block text-xs">Webhook Setup in Razorpay Dashboard:</strong>
                <p class="text-brand-800 text-[11px] leading-relaxed">
                    Set your Webhook URL to: <code class="bg-white/80 px-2 py-0.5 rounded font-mono font-bold text-brand-950"><?= e(config('app.url', 'https://your-domain.com')) ?>/api/payments/razorpay/webhook</code>
                </p>
                <p class="text-brand-700 text-[11px]">Subscribed Events: <span class="font-mono">payment.captured</span>, <span class="font-mono">payment.failed</span>.</p>
            </div>

            <div class="text-right pt-2 border-t border-slate-100">
                <button type="submit" class="btn-primary text-xs py-3 px-8">Save Payment Settings</button>
            </div>
        </form>
    </div>

    <!-- Payment Transaction Logs Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-black text-slate-900">Recent Payment Transactions</h3>
            <p class="text-xs text-slate-400 mt-0.5">Chronological record of checkout attempts and verified payments.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-6 font-bold">Transaction Date</th>
                        <th class="py-3 px-4 font-bold">Order #</th>
                        <th class="py-3 px-4 font-bold">Customer</th>
                        <th class="py-3 px-4 font-bold">Razorpay Payment ID</th>
                        <th class="py-3 px-4 font-bold">Amount</th>
                        <th class="py-3 px-4 font-bold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <?php if (empty($payments)): ?>
                    <tr><td colspan="6" class="py-8 text-center text-slate-400">No payment transaction records yet.</td></tr>
                    <?php else: ?>
                    <?php foreach ($payments as $pay): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3.5 px-6 text-slate-500 text-[11px]"><?= date('d M Y, h:i A', strtotime($pay['created_at'])) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-brand-700">
                            <a href="/admin/orders/<?= $pay['order_id'] ?>" class="hover:underline"><?= e($pay['order_number']) ?></a>
                        </td>
                        <td class="py-3.5 px-4"><?= e($pay['guest_name']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-slate-600"><?= e($pay['razorpay_payment_id'] ?? $pay['razorpay_order_id'] ?? '&minus;') ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-900"><?= format_price($pay['amount']) ?></td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                <?= $pay['status'] === 'captured' ? 'bg-brand-100 text-brand-800' : ($pay['status'] === 'refunded' ? 'bg-purple-100 text-purple-800' : 'bg-red-100 text-red-800') ?>">
                                <?= e($pay['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
