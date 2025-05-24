<?php

view()->composer(
    [
        'order::dashboard.orders.index',
        'order::dashboard.all_orders.index',
        'setting::dashboard.tabs.*',
    ],
    \Modules\Order\ViewComposers\Dashboard\OrderStatusComposer::class
);
