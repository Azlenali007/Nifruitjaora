<?php
/**
 * N.A Fresh Fruits & Coconuts - Front Controller Entry Point
 * 
 * Safely handles requests when document root points to workspace root.
 * Protects sensitive application directories, blocks directory indexing,
 * serves public static assets, and dispatches application routes.
 */

declare(strict_types=1);

// Disable PHP directory listing behavior
ini_set('auto_prepend_file', '');

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = urldecode((string)$uri);

// Block public access to sensitive files, hidden dotfiles, and system directories
if (preg_match('#(?:/\.|\b(composer\.(json|lock)|package\.json|\.env|phpunit\.xml|tailwind\.config\.js|postcss\.config\.js|database/|config/|app/|storage/|resources/|routes/|tests/|vendor/|scripts/))\b#i', $uri)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "403 Forbidden - Access to sensitive application resources is denied.";
    exit;
}

// If requesting an intended public asset (e.g. /assets/css/app.css, /assets/images/...)
$publicFilePath = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($publicFilePath) && !is_dir($publicFilePath)) {
    $realPath = realpath($publicFilePath);
    $publicRoot = realpath(__DIR__ . '/public');
    
    // Ensure file strictly resides inside public directory (path traversal protection)
    if ($realPath && $publicRoot && str_starts_with($realPath, $publicRoot)) {
        $ext = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
        $mimes = [
            'css'   => 'text/css; charset=utf-8',
            'js'    => 'application/javascript; charset=utf-8',
            'json'  => 'application/json',
            'svg'   => 'image/svg+xml',
            'webp'  => 'image/webp',
            'png'   => 'image/png',
            'jpg'   => 'image/jpeg',
            'jpeg'  => 'image/jpeg',
            'gif'   => 'image/gif',
            'ico'   => 'image/x-icon',
            'woff'  => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf'   => 'font/ttf',
            'txt'   => 'text/plain; charset=utf-8',
        ];

        if (isset($mimes[$ext])) {
            header("Content-Type: {$mimes[$ext]}");
        }
        
        // Cache public assets
        header('Cache-Control: public, max-age=86400');
        readfile($realPath);
        exit;
    }
}

// Forward to public front controller
require_once __DIR__ . '/public/index.php';
