<?php

return [
    /**
     * Affiliate System Configuration.
     */
    [
        'key' => 'affiliate',
        'name' => 'affiliate::app.admin.system.referral-program',
        'info' => 'affiliate::app.admin.system.referral-program-info',
        'sort' => 6,
    ],
    [
        'key' => 'affiliate.general',
        'name' => 'affiliate::app.admin.system.general-settings',
        'info' => 'affiliate::app.admin.system.general-settings-info',
        'icon' => 'settings/store.svg',
        'sort' => 1,
        'fields' => [
            [
                'name' => 'status',
                'title' => 'affiliate::app.admin.system.status',
                'type' => 'boolean',
                'channel_based' => false,
                'locale_based' => false,
            ],
            [
                'name' => 'default_commission_type',
                'title' => 'affiliate::app.admin.system.default-commission-type',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'Percentage (%)',
                        'value' => 'percentage',
                    ],
                    [
                        'title' => 'Fixed Amount (₹)',
                        'value' => 'fixed',
                    ],
                ],
                'channel_based' => false,
            ],
            [
                'name' => 'default_commission_value',
                'title' => 'affiliate::app.admin.system.default-commission-value',
                'type' => 'text',
                'validation' => 'numeric|min:0',
                'channel_based' => false,
            ],
            [
                'name' => 'cookie_duration',
                'title' => 'affiliate::app.admin.system.cookie-duration',
                'type' => 'text',
                'validation' => 'numeric|min:1',
                'channel_based' => false,
            ],
            [
                'name' => 'clearance_days',
                'title' => 'affiliate::app.admin.system.clearance-days',
                'type' => 'text',
                'validation' => 'numeric|min:0',
                'channel_based' => false,
            ],
            [
                'name' => 'auto_release_on_completion',
                'title' => 'affiliate::app.admin.system.auto-release-on-completion',
                'type' => 'boolean',
                'channel_based' => false,
                'locale_based' => false,
            ],
            [
                'name' => 'min_withdrawal_amount',
                'title' => 'affiliate::app.admin.system.min-withdrawal-amount',
                'type' => 'text',
                'validation' => 'numeric|min:0',
                'channel_based' => false,
            ],
            [
                'name' => 'max_commission_per_order',
                'title' => 'affiliate::app.admin.system.max-commission-cap',
                'type' => 'text',
                'validation' => 'numeric|min:0',
                'channel_based' => false,
            ],
        ],
    ],
];
