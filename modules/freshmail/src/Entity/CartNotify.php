<?php


namespace FreshMail\Entity;

use ObjectModel;

class CartNotify extends ObjectModel
{
    public $id;
    public $id_freshmail_cart;
    public $date_add;

    public static $definition = [
        'table' => 'freshmail_cart_notify',
        'primary' => 'id',
        'fields' => [
            'id_freshmail_cart' => [
                'type' => self::TYPE_INT,
            ],
            'date_add' => [
                'type' => self::TYPE_DATE
            ],
        ],
    ];

}