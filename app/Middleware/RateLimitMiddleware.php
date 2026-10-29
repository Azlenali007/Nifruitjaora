<?php

namespace App\Middleware;

use App\Helpers\Response;

class RateLimitMiddleware {
    public function handle(array $request, array $params): bool {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = 'rate_limit_' . md5($ip . ($_SERVER['REQUEST_URI'] ?? ''));
        $maxAttempts = config('security.rate_limit_requests', 60);
        $decaySeconds = config('security.rate_limit_decay', 60);

        $now = time();
        $record = $_SESSION[$key] ?? ['attempts' => 0, 'expires_at' => $now + $decaySeconds];

        if ($now > $record['expires_at']) {
            $record = ['attempts' => 0, 'expires_at' => $now + $decaySeconds];
        }

        $record['attempts']++;
        $_SESSION[$key] = $record;

        if ($record['attempts'] > $maxAttempts) {
            http_response_code(429);
            if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
                Response::error('Too many requests. Please slow down and try again later.', [], 429);
                return false;
            }
            echo '<div style="font-family:sans-serif;padding:50px;text-align:center;"><h2>429 Too Many Requests</h2><p>Please wait a moment before trying again.</p></div>';
            return false;
        }

        return true;
    }
}
