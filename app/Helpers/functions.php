<?php

if (!function_exists('env')) {
    function env(string $key, $default = null) {
        $val = getenv($key);
        if ($val === false) {
            return $default;
        }
        switch (strtolower($val)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
            case 'empty':
            case '(empty)':
                return '';
        }
        return $val;
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null) {
        static $configs = [];
        $parts = explode('.', $key);
        $file = $parts[0];
        
        if (!isset($configs[$file])) {
            $path = dirname(__DIR__, 2) . "/config/{$file}.php";
            if (file_exists($path)) {
                $configs[$file] = require $path;
            } else {
                $configs[$file] = [];
            }
        }
        
        $value = $configs[$file];
        for ($i = 1; $i < count($parts); $i++) {
            if (is_array($value) && array_key_exists($parts[$i], $value)) {
                $value = $value[$parts[$i]];
            } else {
                return $default;
            }
        }
        return $value;
    }
}

if (!function_exists('e')) {
    function e(?string $value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('format_price')) {
    function format_price(float|int $amount): string {
        return '₹' . number_format($amount, 0);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string {
        $base = rtrim(config('app.url', ''), '/');
        $path = '/' . ltrim($path, '/');
        return $base . $path;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    function redirect(string $to, int $status = 302): void {
        header("Location: {$to}", true, $status);
        exit;
    }
}

if (!function_exists('session')) {
    function session(?string $key = null, $default = null) {
        if (!headers_sent() && session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        if ($key === null) {
            return $_SESSION ?? [];
        }
        return $_SESSION[$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, ?string $message = null) {
        if (!headers_sent() && session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        if ($message !== null) {
            $_SESSION['_flash'][$key] = $message;
            return;
        }
        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = '') {
        $val = $_SESSION['_old'][$key] ?? $default;
        return e((string)$val);
    }
}

if (!function_exists('auth')) {
    function auth(): ?array {
        return session('customer');
    }
}

if (!function_exists('admin_auth')) {
    function admin_auth(): ?array {
        return session('admin');
    }
}
