<?php

return [
    [
        'key' => 'affiliate',
        'name' => 'affiliate::app.admin.acl.referral-wallet',
        'route' => 'admin.affiliate.index',
        'sort' => 8,
    ],
    [
        'key' => 'affiliate.promoters',
        'name' => 'affiliate::app.admin.acl.promoters',
        'route' => 'admin.affiliate.index',
        'sort' => 1,
    ],
    [
        'key' => 'affiliate.rules',
        'name' => 'affiliate::app.admin.acl.commission-rules',
        'route' => 'admin.affiliate.rules.index',
        'sort' => 2,
    ],
    [
        'key' => 'affiliate.payouts',
        'name' => 'affiliate::app.admin.acl.payout-requests',
        'route' => 'admin.affiliate.payouts.index',
        'sort' => 3,
    ],
];
