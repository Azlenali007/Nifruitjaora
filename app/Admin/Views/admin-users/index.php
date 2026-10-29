<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Admin Team &amp; Access Roles</h1>
        <p class="text-xs text-slate-500 mt-1">Manage administrative credentials and role-based permissions.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Create Admin Form -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 text-xs">
            <h3 class="text-sm font-black text-slate-900 pb-3 border-b border-slate-100 mb-4">Add Staff / Manager</h3>

            <form action="/admin/admin-users/store" method="POST" class="space-y-3">
                <?= csrf_field() ?>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Full Name *</label>
                    <input type="text" name="name" required placeholder="Name"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Admin Email *</label>
                    <input type="email" name="email" required placeholder="staff@nafreshfruits.com"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Temporary Password *</label>
                    <input type="password" name="password" required minlength="8" placeholder="At least 8 characters"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Role *</label>
                    <select name="role" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                        <option value="staff">Staff (Fulfillment Only)</option>
                        <option value="manager">Manager (Orders, Products, Reports)</option>
                        <option value="superadmin">Super Admin (Full Access)</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary text-xs py-2.5 w-full mt-2">Create Account</button>
            </form>
        </div>

        <!-- Admin Users Table -->
        <div class="md:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900">Current Administrator Accounts</h3>
            </div>
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-6 font-bold">Administrator</th>
                        <th class="py-3 px-4 font-bold">Role</th>
                        <th class="py-3 px-4 font-bold">Last Active</th>
                        <th class="py-3 px-6 font-bold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <?php foreach ($admins as $adm): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3 px-6">
                            <strong class="font-bold text-slate-900 block"><?= e($adm['name']) ?></strong>
                            <span class="text-slate-400 text-[11px]"><?= e($adm['email']) ?></span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                <?= $adm['role'] === 'superadmin' ? 'bg-purple-100 text-purple-800' : 'bg-brand-100 text-brand-800' ?>">
                                <?= e($adm['role']) ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-400 text-[11px]">
                            <?= $adm['last_login_at'] ? date('d M Y, h:i A', strtotime($adm['last_login_at'])) : 'Never' ?>
                        </td>
                        <td class="py-3 px-6 text-right">
                            <?php if ($adm['role'] !== 'superadmin' || admin_auth()['id'] !== $adm['id']): ?>
                            <form action="/admin/admin-users/<?= $adm['id'] ?>/delete" method="POST" class="inline confirm-delete" data-message="Remove admin user '<?= e($adm['name']) ?>'?">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold">Delete</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
