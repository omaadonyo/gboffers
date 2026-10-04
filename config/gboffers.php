<?php
return [
    'tagline' => 'Group up. Pay less.',
    'secondary' => 'Buy together, unlock better prices.',
    'currency' => 'UGX',
    'country' => 'UG',
    'phone_prefix' => '+256',
    'timezone' => 'Africa/Kampala',
    'reservation_ttl_minutes' => 15,
    'reservation_limits' => ['new' => 2, 'verified' => 3, 'trusted' => 5, 'limited' => 0],
    'commission' => [
        'food' => ['type' => 'fixed', 'amount' => 3000],
        'electronics' => ['type' => 'fixed', 'amount' => 5000],
        'default' => ['type' => 'percent', 'rate' => 2.0],
    ],
    'trust' => ['verified_min_completed' => 1, 'trusted_min_completed' => 5, 'limit_expired' => 5],
    'payments' => [
        'methods' => ['merchant_direct', 'momo_direct', 'flutterwave', 'iotec'],
        'flutterwave' => [
            'checkout_url' => env('FLUTTERWAVE_CHECKOUT_URL', ''),
            'webhook_secret' => env('FLUTTERWAVE_WEBHOOK_SECRET', ''),
        ],
        'iotec' => [
            'checkout_url' => env('IOTEC_CHECKOUT_URL', ''),
            'webhook_secret' => env('IOTEC_WEBHOOK_SECRET', ''),
        ],
    ],
    'features' => ['wanted' => env('FEATURE_WANTED', true), 'protected_checkout' => env('FEATURE_PROTECTED_CHECKOUT', false)],
];
