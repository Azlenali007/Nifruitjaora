<?php

namespace App\Models;

use App\Core\Database;

class ActivityLog {
    public static function log(string $action, string $description, ?string $entityType = null, ?int $entityId = null): void {
        $admin = admin_auth();
        $adminId = $admin['id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

        $stmt = Database::getConnection()->prepare("
            INSERT INTO `activity_logs` (`admin_id`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`, `user_agent`)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$adminId, $action, $entityType, $entityId, $description, $ip, $ua]);
    }

    public static function all(int $limit = 50, int $offset = 0): array {
        $stmt = Database::getConnection()->prepare("
            SELECT al.*, au.name as admin_name, au.email as admin_email
            FROM `activity_logs` al
            LEFT JOIN `admin_users` au ON al.admin_id = au.id
            ORDER BY al.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
