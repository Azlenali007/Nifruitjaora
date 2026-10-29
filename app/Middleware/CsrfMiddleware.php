<?php

namespace App\Middleware;

use App\Helpers\Response;

class CsrfMiddleware {
    public function handle(array $request, array $params): bool {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
            return true;
        }

        // Exempt webhook URLs
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        if ($path === '/api/payments/razorpay/webhook') {
            return true;
        }

        $sessionToken = $_SESSION['_csrf_token'] ?? '';
        $submittedToken = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (empty($sessionToken) || !hash_equals($sessionToken, $submittedToken)) {
            if (str_starts_with($path, '/api/')) {
                Response::error('Invalid or expired CSRF security token.', [], 419);
                return false;
            }
            http_response_code(419);
            flash('error', 'Your session expired. Please try again.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
            return false;
        }

        return true;
    }
}
