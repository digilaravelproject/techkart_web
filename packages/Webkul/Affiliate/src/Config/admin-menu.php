<?php

return [
    [
        'key' => 'affiliate',
        'name' => 'affiliate::app.admin.menu.referral-wallet',
        'route' => 'admin.affiliate.index',
        'sort' => 8,
        'icon' => 'icon-sales',
    ],
    [
        'key' => 'affiliate.promoters',
        'name' => 'affiliate::app.admin.menu.promoters',
        'route' => 'admin.affiliate.index',
        'sort' => 1,
        'icon' => '',
    ],
    [
        'key' => 'affiliate.rules',
        'name' => 'affiliate::app.admin.menu.commission-rules',
        'route' => 'admin.affiliate.rules.index',
        'sort' => 2,
        'icon' => '',
    ],
    [
        'key' => 'affiliate.payouts',
        'name' => 'affiliate::app.admin.menu.payout-requests',
        'route' => 'admin.affiliate.payouts.index',
        'sort' => 3,
        'icon' => '',
    ],
];
