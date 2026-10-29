<div>
    <h2 class="text-2xl font-black text-slate-900 tracking-tight text-center">Create Customer Account</h2>
    <p class="text-xs text-slate-500 text-center mt-1 mb-8">Join N.A Fresh Fruits &amp; Coconuts for quick deliveries.</p>

    <form action="/register" method="POST" class="space-y-4">
        <?= csrf_field() ?>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Name</label>
            <input type="text" name="name" required placeholder="e.g. Priya Patel"
                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mobile Number</label>
            <input type="tel" name="phone" required placeholder="10-digit mobile number"
                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address (Optional)</label>
            <input type="email" name="email" placeholder="name@example.com"
                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Create Password</label>
            <input type="password" name="password" required minlength="6" placeholder="At least 6 characters"
                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
        </div>

        <button type="submit" 
                class="w-full py-3.5 px-6 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-sm shadow-lg shadow-brand-600/30 transition-transform active:scale-[0.98] mt-2">
            Create Account
        </button>
    </form>

    <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
        Already have an account? 
        <a href="/login" class="text-brand-600 font-bold hover:underline">Sign In</a>
    </div>
</div>
