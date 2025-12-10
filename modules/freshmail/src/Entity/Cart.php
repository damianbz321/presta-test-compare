<?php


namespace FreshMail\Entity;

use ObjectModel;

class Cart extends ObjectModel
{
    public $id;
    public $id_cart;
    public $id_cart_rule;
    public $cart_token;
    public $discount_code;

    public $date_add;
    public $date_upd;


    public static $definition = [
        'table' => 'freshmail_cart',
        'primary' => 'id_freshmail_cart',
        'fields' => [
            'id_cart' => [
                'type' => self::TYPE_INT,
            ],
            'id_cart_rule' => [
                'type' => self::TYPE_INT,
            ],
            'cart_token' => [
                'type' => self::TYPE_STRING,
                'required' => true
            ],
            'discount_code' => [
                'type' => self::TYPE_STRING,
            ],
            'date_add' => [
                'type' => self::TYPE_DATE
            ],
            'date_upd' => [
                'type' => self::TYPE_DATE
            ],
        ],
    ];

}