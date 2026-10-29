<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $connection = config('database.default', 'mysql');
        $config = config("database.connections.{$connection}");

        if (!$config) {
            throw new RuntimeException("Database connection '{$connection}' is not configured.");
        }

        try {
            if ($config['driver'] === 'sqlite') {
                $dir = dirname($config['database']);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $dsn = "sqlite:{$config['database']}";
                $pdo = new PDO($dsn, null, null, $config['options'] ?? []);
            } else {
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
                $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options'] ?? [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            }

            self::$instance = $pdo;
            return self::$instance;
        } catch (PDOException $e) {
            // If MySQL is temporarily down or credentials invalid, try to reconnect or fail with clear message
            throw new RuntimeException("Database connection failed: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public static function transaction(callable $callback) {
        $pdo = self::getConnection();
        $pdo->beginTransaction();
        try {
            $result = $callback($pdo);
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
