<?php

return [
    'enabled' => env('RAZORPAY_ENABLED', true),
    'mode' => env('RAZORPAY_MODE', 'test'), // 'test' or 'live'
    
    'test' => [
        'key_id' => env('RAZORPAY_TEST_KEY_ID', 'rzp_test_nafruits2026'),
        'key_secret' => env('RAZORPAY_TEST_KEY_SECRET', 'test_secret_nafruits_mock'),
        'webhook_secret' => env('RAZORPAY_TEST_WEBHOOK_SECRET', 'test_webhook_secret_nafruits'),
    ],
    
    'live' => [
        'key_id' => env('RAZORPAY_LIVE_KEY_ID', ''),
        'key_secret' => env('RAZORPAY_LIVE_KEY_SECRET', ''),
        'webhook_secret' => env('RAZORPAY_LIVE_WEBHOOK_SECRET', ''),
    ],
    
    'currency' => 'INR',
    'company_name' => 'N.A Fresh Fruits & Coconuts',
    'theme_color' => '#16a34a', // Emerald green
];
