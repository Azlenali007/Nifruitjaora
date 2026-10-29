<?php

namespace App\Admin\Middleware;

use App\Models\AdminUser;
use App\Helpers\Response;

class PermissionMiddleware {
    private string $permissionSlug;

    public function __construct(string $permissionSlug = '') {
        $this->permissionSlug = $permissionSlug;
    }

    public function handle(array $request, array $params): bool {
        $admin = admin_auth();
        if (!$admin) {
            redirect('/admin/login');
            return false;
        }

        // Superadmin has all permissions
        if ($admin['role'] === 'superadmin') {
            return true;
        }

        if (!empty($this->permissionSlug)) {
            $hasPermission = AdminUser::hasPermission($admin['id'], $this->permissionSlug);
            if (!$hasPermission) {
                http_response_code(403);
                flash('error', 'You do not have permission to perform this administrative action.');
                redirect('/admin/dashboard');
                return false;
            }
        }

        return true;
    }
}
