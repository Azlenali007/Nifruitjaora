<?php

namespace App\Models;

use App\Core\Database;

class AdminUser {
    public static function findById(int $id): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM `admin_users` WHERE `id` = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByEmail(string $email): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM `admin_users` WHERE `email` = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function hasPermission(int $adminId, string $permissionSlug): bool {
        $admin = self::findById($adminId);
        if (!$admin || $admin['status'] !== 'active') {
            return false;
        }

        if ($admin['role'] === 'superadmin') {
            return true;
        }

        $stmt = Database::getConnection()->prepare("
            SELECT COUNT(*) FROM `role_permissions` rp
            JOIN `permissions` p ON rp.permission_id = p.id
            WHERE rp.role = ? AND p.slug = ?
        ");
        $stmt->execute([$admin['role'], $permissionSlug]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function all(): array {
        $stmt = Database::getConnection()->query("SELECT id, name, email, role, status, last_login_at, created_at FROM `admin_users` ORDER BY `id` ASC");
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO `admin_users` (`name`, `email`, `password_hash`, `role`, `status`)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role'] ?? 'staff',
            $data['status'] ?? 'active'
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $fields = [];
        $values = [];
        foreach ($data as $k => $v) {
            if ($k === 'password') {
                $fields[] = "`password_hash` = ?";
                $values[] = password_hash($v, PASSWORD_BCRYPT);
            } else {
                $fields[] = "`{$k}` = ?";
                $values[] = $v;
            }
        }
        $values[] = $id;
        $sql = "UPDATE `admin_users` SET " . implode(', ', $fields) . " WHERE `id` = ?";
        $stmt = Database::getConnection()->prepare($sql);
        return $stmt->execute($values);
    }

    public static function updateLastLogin(int $id): void {
        $stmt = Database::getConnection()->prepare("UPDATE `admin_users` SET `last_login_at` = NOW() WHERE `id` = ?");
        $stmt->execute([$id]);
    }

    public static function delete(int $id): bool {
        $stmt = Database::getConnection()->prepare("DELETE FROM `admin_users` WHERE `id` = ?");
        return $stmt->execute([$id]);
    }
}
