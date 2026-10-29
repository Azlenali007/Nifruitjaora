<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\AdminUser;
use App\Models\Setting;
use App\Models\ActivityLog;

class AdminTest
{
    public function run(): array
    {
        $results = [];

        // Test 1: Superadmin credentials verification
        try {
            $admin = AdminUser::findByEmail('admin@nafreshfruits.com');
            if ($admin && password_verify('Admin@123456', $admin['password_hash'])) {
                $results[] = ['name' => 'Superadmin account exists and password hash verifies successfully', 'passed' => true];
            } else {
                $results[] = ['name' => 'Superadmin account verification', 'passed' => false, 'error' => 'Superadmin credentials could not be verified'];
            }
        } catch (\Throwable $e) {
            $results[] = ['name' => 'Superadmin account verification', 'passed' => false, 'error' => $e->getMessage()];
        }

        // Test 2: Invalid password rejected
        try {
            $admin = AdminUser::findByEmail('admin@nafreshfruits.com');
            if ($admin && !password_verify('WrongPassword999', $admin['password_hash'])) {
                $results[] = ['name' => 'Invalid administrator password rejected', 'passed' => true];
            } else {
                $results[] = ['name' => 'Invalid admin password rejected', 'passed' => false, 'error' => 'Wrong password was accepted'];
            }
        } catch (\Throwable $e) {
            $results[] = ['name' => 'Invalid admin password rejected', 'passed' => false, 'error' => $e->getMessage()];
        }

        // Test 3: Setting read/write functionality
        try {
            $testKey = 'test_sample_metric_' . time();
            Setting::set($testKey, 'sample_value_123', 'string', 'Test key');
            $val = Setting::get($testKey);

            if ($val === 'sample_value_123') {
                $results[] = ['name' => 'Store setting write and read from database', 'passed' => true];
            } else {
                $results[] = ['name' => 'Store setting write and read', 'passed' => false, 'error' => "Expected 'sample_value_123', got: " . var_export($val, true)];
            }
        } catch (\Throwable $e) {
            $results[] = ['name' => 'Store setting write and read', 'passed' => false, 'error' => $e->getMessage()];
        }

        // Test 4: Activity log creation
        try {
            $admin = AdminUser::findByEmail('admin@nafreshfruits.com');
            $adminId = $admin['id'] ?? 1;

            ActivityLog::log('test_verification', 'Verification test suite audit entry', 'admin_user', (int)$adminId);
            $logs = ActivityLog::all(5);
            $found = false;
            foreach ($logs as $l) {
                if ($l['action'] === 'test_verification') {
                    $found = true;
                    break;
                }
            }

            if ($found) {
                $results[] = ['name' => 'Admin activity audit logging records correctly', 'passed' => true];
            } else {
                $results[] = ['name' => 'Admin activity audit logging', 'passed' => false, 'error' => 'Created audit log entry not found in recent logs'];
            }
        } catch (\Throwable $e) {
            $results[] = ['name' => 'Admin activity audit logging', 'passed' => false, 'error' => $e->getMessage()];
        }

        return $results;
    }
}
