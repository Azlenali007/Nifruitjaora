<?php

namespace App\Models;

use App\Core\Database;

class Setting {
    private static array $cache = [];

    public static function get(string $key, $default = null) {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $stmt = Database::getConnection()->prepare("SELECT `value`, `type` FROM `settings` WHERE `key` = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();

        if (!$row) {
            return $default;
        }

        $val = $row['value'];
        if ($row['type'] === 'boolean') {
            $val = filter_var($val, FILTER_VALIDATE_BOOLEAN);
        } elseif ($row['type'] === 'number') {
            $val = is_numeric($val) ? (float)$val : $val;
        } elseif ($row['type'] === 'json') {
            $val = json_decode($val, true);
        }

        self::$cache[$key] = $val;
        return $val;
    }

    public static function set(string $key, $value, string $type = 'string', ?string $desc = null): bool {
        self::$cache[$key] = $value;
        if (is_array($value)) {
            $value = json_encode($value);
            $type = 'json';
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
            $type = 'boolean';
        }

        $stmt = Database::getConnection()->prepare("
            INSERT INTO `settings` (`key`, `value`, `type`, `description`) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE `value`=VALUES(`value`), `type`=VALUES(`type`)
        ");
        return $stmt->execute([$key, $value, $type, $desc]);
    }

    public static function all(): array {
        $stmt = Database::getConnection()->query("SELECT * FROM `settings`");
        $rows = $stmt->fetchAll();
        $settings = [];
        foreach ($rows as $r) {
            $val = $r['value'];
            if ($r['type'] === 'boolean') {
                $val = filter_var($val, FILTER_VALIDATE_BOOLEAN);
            } elseif ($r['type'] === 'number') {
                $val = is_numeric($val) ? (float)$val : $val;
            } elseif ($r['type'] === 'json') {
                $val = json_decode($val, true);
            }
            $settings[$r['key']] = $val;
        }
        return $settings;
    }

    public static function getDeliveryZone(string $pincode): ?array {
        $stmt = Database::getConnection()->prepare("
            SELECT * FROM `delivery_zones` 
            WHERE `pincode` = ? AND `is_serviceable` = 1
        ");
        $stmt->execute([trim($pincode)]);
        return $stmt->fetch() ?: null;
    }

    public static function allDeliveryZones(): array {
        $stmt = Database::getConnection()->query("SELECT * FROM `delivery_zones` ORDER BY `pincode` ASC");
        return $stmt->fetchAll();
    }

    public static function saveDeliveryZone(array $data): bool {
        $stmt = Database::getConnection()->prepare("
            INSERT INTO `delivery_zones` (`pincode`, `area_name`, `delivery_charge`, `min_order_amount`, `estimated_time`, `is_serviceable`)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE `area_name`=VALUES(`area_name`), `delivery_charge`=VALUES(`delivery_charge`), `min_order_amount`=VALUES(`min_order_amount`), `estimated_time`=VALUES(`estimated_time`), `is_serviceable`=VALUES(`is_serviceable`)
        ");
        return $stmt->execute([
            trim($data['pincode']),
            $data['area_name'],
            $data['delivery_charge'] ?? 30.00,
            $data['min_order_amount'] ?? 100.00,
            $data['estimated_time'] ?? '30 - 45 mins',
            isset($data['is_serviceable']) ? (int)$data['is_serviceable'] : 1
        ]);
    }

    public static function deleteDeliveryZone(int $id): bool {
        $stmt = Database::getConnection()->prepare("DELETE FROM `delivery_zones` WHERE `id` = ?");
        return $stmt->execute([$id]);
    }
}
