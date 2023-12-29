<?php

declare(strict_types=1);

return [
    [
        'url' => url('/'),
        'title' => __('Dashboard'),
        'icon_class' => 'las la-compass',
        'permissions' => ['read dashboard'],
    ],
    [
        'url' => url('/lead'),
        'title' => __('Leads'),
        'icon_class' => 'las la-filter',
        'permissions' => ['read lead'],
    ],
    [
        'url' => url('/customer'),
        'title' => __('Customers'),
        'icon_class' => 'las la-user-alt',
        'permissions' => ['read customer'],
    ],
    [
        'url' => url('/order'),
        'title' => __('Orders'),
        'icon_class' => 'las la-receipt',
        'permissions' => ['read order'],
    ],
    [
        'url' => url('/product'),
        'title' => __('Products'),
        'icon_class' => 'las la-box',
        'permissions' => ['read product'],
    ],
    [
        'url' => url('/calendar'),
        'title' => __('Calendar'),
        'icon_class' => 'las la-calendar',
        'permissions' => ['read calendar'],
    ],
    [
        'is_drop_down' => true,
        'title' => 'E-mail',
        'icon_class' => 'las la-envelope-open-text',
        'permissions' => [],
        'children' => [
            [
                'url' => url('/email'),
                'title' => 'E-mail',
                'icon_class' => 'las la-envelope-open-text',
                'permissions' => ['read email'],
            ],
            [
                'url' => url('/campaign'),
                'title' => __('Campaign'),
                'icon_class' => 'las la-mail-bulk',
                'permissions' => ['create campaign'],
            ],
        ],
    ],
    [
        'url' => url('/accounting'),
        'title' => __('Accounting'),
        'icon_class' => 'las la-coins',
        'permissions' => ['read accounting'],
    ],
    [
        'url' => url('/supplier'),
        'title' => __('Suppliers'),
        'icon_class' => 'las la-dolly',
        'permissions' => ['read supplier'],
    ],
    [
        'url' => url('/ticket'),
        'title' => __('Tickets'),
        'icon_class' => 'las la-tools',
        'permissions' => ['read ticket'],
    ],
    [
        'url' => url('/report'),
        'title' => __('Reports'),
        'icon_class' => 'las la-chart-pie',
        'permissions' => ['read report'],
    ],
];
