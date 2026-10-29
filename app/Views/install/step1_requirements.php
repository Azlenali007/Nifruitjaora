<div>
    <div class="mb-6">
        <h2 class="text-2xl font-black text-white tracking-tight">System &amp; Server Requirements</h2>
        <p class="text-sm text-slate-400 mt-1">We are verifying that your hosting environment meets all requirements before setting up the store.</p>
    </div>

    <!-- Requirements List -->
    <div class="space-y-3 mb-8">
        <?php foreach ($checks as $key => $check): ?>
        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-800/60 border <?= $check['pass'] ? 'border-emerald-900/50' : 'border-red-900/50' ?>">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm <?= $check['pass'] ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' ?>">
                    <?= $check['pass'] ? '✓' : '✗' ?>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-white"><?= e($check['name']) ?></h4>
                    <span class="text-xs text-slate-400">Required: <?= e($check['required']) ?> &bull; Current: <span class="<?= $check['pass'] ? 'text-emerald-400 font-semibold' : 'text-red-400 font-semibold' ?>"><?= e($check['current']) ?></span></span>
                </div>
            </div>

            <?php if (!$check['pass']): ?>
            <span class="text-xs text-red-400 font-medium max-w-xs text-right"><?= e($check['help']) ?></span>
            <?php else: ?>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Passed</span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between pt-4 border-t border-slate-800">
        <a href="/install?step=1" class="text-xs font-bold text-slate-400 hover:text-white transition-colors">
            ↻ Re-check Requirements
        </a>

        <?php if ($allPass): ?>
        <a href="/install?step=2" class="px-6 py-3 rounded-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-sm tracking-wide shadow-lg shadow-emerald-500/20 transition-all">
            Continue to Database Setup &rarr;
        </a>
        <?php else: ?>
        <button disabled class="px-6 py-3 rounded-full bg-slate-800 text-slate-500 font-extrabold text-sm cursor-not-allowed">
            Fix Requirements to Continue
        </button>
        <?php endif; ?>
    </div>
</div>
