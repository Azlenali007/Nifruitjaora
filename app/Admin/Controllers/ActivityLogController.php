<?php

namespace App\Admin\Controllers;

use App\Core\View;
use App\Models\ActivityLog;

class ActivityLogController {
    public function index(): void {
        $logs = ActivityLog::all(100, 0);

        View::render('admin/activity-logs/index', [
            'title' => 'Security & Activity Audit Logs | Admin',
            'logs' => $logs
        ]);
    }
}
