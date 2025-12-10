<?php
return array(
    array(
        'type' => 'radio',
        'br' => true,
        'label' => 'The principle of adding discounts in the shopping cart',
        'name' => 'cart_products_discount_principle',
        'values' => [
            [
                'label' => 'Add discounts to the cheapest products (default)',
                'id' => 'CHEAPEST',
                'value' => 'CHEAPEST',
            ],
            [
                'label' => 'Add discounts to the most expensive products',
                'id' => 'MOST_EXPENSIVE',
                'value' => 'MOST_EXPENSIVE',
            ],
            [
                'label' => 'Add discounts to products in the order they are added to the shopping cart',
                'id' => 'ADD_ORDER',
                'value' => 'ADD_ORDER',
            ],
        ],
        'hint' => '',
        'default' => 'CHEAPEST',
    ),
    array(
        'type' => 'text',
        'name' => 'debug_ip',
        'label' => 'Debug IP',
        'desc' => 'Enter your IP address to see cross-sell debug. Your IP: ' . Tools::getRemoteAddr(),
        'default' => '',
    ),
);
