<?php

return [
    'currency' => env('NAVKWA_BUILD_CURRENCY', 'GHS'),
    'annual_billable_months' => (int) env('NAVKWA_BUILD_ANNUAL_BILLABLE_MONTHS', 10),

    'plans' => [
        'essential' => [
            'name' => 'Essential',
            'monthly_amount' => (float) env('NAVKWA_BUILD_ESSENTIAL_MONTHLY', 399),
            'checkout_enabled' => true,
        ],
        'professional' => [
            'name' => 'Professional',
            'monthly_amount' => (float) env('NAVKWA_BUILD_PROFESSIONAL_MONTHLY', 999),
            'checkout_enabled' => true,
        ],
        'business' => [
            'name' => 'Business',
            'monthly_amount' => (float) env('NAVKWA_BUILD_BUSINESS_MONTHLY', 2499),
            'checkout_enabled' => true,
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'monthly_amount' => null,
            'checkout_enabled' => false,
        ],
    ],
];
