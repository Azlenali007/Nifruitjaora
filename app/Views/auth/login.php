<div>
    <h2 class="text-2xl font-black text-slate-900 tracking-tight text-center">Customer Sign In</h2>
    <p class="text-xs text-slate-500 text-center mt-1 mb-8">Access your orders, saved addresses, and faster checkout.</p>

    <form action="/login" method="POST" class="space-y-4">
        <?= csrf_field() ?>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mobile Number</label>
            <input type="tel" name="phone" required placeholder="10-digit mobile number"
                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password</label>
                <a href="/forgot-password" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">Forgot?</a>
            </div>
            <input type="password" name="password" required placeholder="Your account password"
                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
        </div>

        <button type="submit" 
                class="w-full py-3.5 px-6 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-sm shadow-lg shadow-brand-600/30 transition-transform active:scale-[0.98] mt-2">
            Sign In
        </button>
    </form>

    <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
        Don't have an account yet? 
        <a href="/register" class="text-brand-600 font-bold hover:underline">Create Account</a>
    </div>
</div>
