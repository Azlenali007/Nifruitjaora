<?php

// Check if running on PHP built-in server and file exists on disk
if (php_sapi_name() === 'cli-server') {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $filePath = __DIR__ . $uri;
    if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
        return false; // Let PHP built-in server serve the static file directly
    }
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap environment
use App\Core\Session;
use App\Core\Router;

// Timezone configuration (Asia/Kolkata per specification)
date_default_timezone_set(config('app.timezone', 'Asia/Kolkata'));

// Debugging & Error Handling
if (config('app.debug', false)) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Start secure session
Session::start();

// Initialize Router & Routes
$router = new Router();
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/admin.php';
require_once __DIR__ . '/../routes/api.php';

// Dispatch current request
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

$router->dispatch($method, $uri);
