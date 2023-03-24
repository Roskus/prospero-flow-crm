<?php

declare(strict_types=1);

return [
    [
        'url' => url('/'),
        'title' => __('Dashboard'),
        'icon_class' => 'las la-compass',
    ],
    [
        'url' => url('/lead'),
        'title' => __('Leads'),
        'icon_class' => 'las la-filter',
        'permission' => 'read lead'
    ],
    [
        'url' => url('/customer'),
        'title' => __('Customers'),
        'icon_class' => 'las la-user-alt',
        'permission' => 'read customer'
    ],
    [
        'url' => url('/order'),
        'title' => __('Orders'),
        'icon_class' => 'las la-receipt',
        'permission' => 'read order'
    ],
    [
        'url' => url('/product'),
        'title' => __('Products'),
        'icon_class' => 'las la-box',
        'permission' => 'read product'
    ],
    [
        'url' => url('/calendar'),
        'title' => __('Calendar'),
        'icon_class' => 'las la-calendar',
    ],
    [
        'is_drop_down' => true,
        'title' => 'E-mail',
        'icon_class' => 'las la-envelope-open-text',
        'children' => [
            [
                'url' => url('/email'),
                'title' => 'E-mail',
                'icon_class' => 'las la-envelope-open-text',
            ],
            [
                'url' => url('/campaign'),
                'title' => __('Campaign'),
                'icon_class' => 'las la-mail-bulk',
            ],
        ],
    ],
    [
        'url' => url('/accounting'),
        'title' => __('Accounting'),
        'icon_class' => 'las la-coins',
        'permission' => 'read accounting'
    ],
    [
        'url' => url('/supplier'),
        'title' => __('Suppliers'),
        'icon_class' => 'las la-dolly',
        'permission' => 'read supplier'
    ],
    [
        'url' => url('/ticket'),
        'title' => __('Tickets'),
        'icon_class' => 'las la-tools',
    ],
    [
        'url' => url('/report'),
        'title' => __('Reports'),
        'icon_class' => 'las la-chart-pie',
        'permission' => 'read report'
    ],
];
