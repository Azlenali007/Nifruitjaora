<?php

return [
    'session_lifetime' => 86400, // 24 hours
    'cookie_secure' => false, // true in HTTPS production
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'rate_limit_requests' => 60,
    'rate_limit_decay' => 60,
    'login_rate_limit' => 5, // 5 attempts per 5 minutes
    'login_lockout_time' => 300,
    'csrf_token_name' => '_csrf_token',
    'allowed_image_mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'],
    'max_upload_size' => 5 * 1024 * 1024, // 5MB
];
