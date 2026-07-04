<?php

return [
    'delivery_fee'    => (float) env('FLORAFETCH_DELIVERY_FEE', 150),
    'max_addresses'   => (int) env('FLORAFETCH_MAX_ADDRESSES', 10),
];
