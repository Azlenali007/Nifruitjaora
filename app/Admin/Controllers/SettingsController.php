<?php

namespace App\Admin\Controllers;

use App\Core\View;
use App\Models\Setting;
use App\Models\ActivityLog;

class SettingsController {
    public function index(): void {
        $settings = Setting::all();
        View::render('admin/settings/index', [
            'title' => 'General Store Settings | Admin',
            'settings' => $settings
        ]);
    }

    public function update(): void {
        $keys = [
            'store_name', 'store_tagline', 'contact_phone', 'contact_email', 
            'store_address', 'store_hours', 'currency', 'currency_symbol'
        ];

        foreach ($keys as $k) {
            if (isset($_POST[$k])) {
                Setting::set($k, trim($_POST[$k]));
            }
        }

        ActivityLog::log('update_settings', 'Updated general store profile information');
        flash('success', 'Store settings updated successfully.');
        redirect('/admin/settings');
    }
}
