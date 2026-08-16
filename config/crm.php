<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    |
    | Business rules applied when persisting orders.
    |
    */

    'orders' => [
        /*
        | Maximum discount percentage a user without the "override order price"
        | permission may apply to a single order line. Larger discounts require
        | the "override order price" permission.
        */
        'max_discount_percent' => (float) env('CRM_ORDERS_MAX_DISCOUNT_PERCENT', 20),
    ],

];
