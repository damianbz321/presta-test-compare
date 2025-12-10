<?php

namespace Freshmail\Entity;

use ObjectModel;

class FreshmailEmail extends ObjectModel
{
    public $id;
    public $email;
    public $hash;
    public $date_add;
    public $date_upd;


    public static $definition = [
        'table' => 'freshmail_email',
        'primary' => 'id_freshmail_email',
        'fields' => [
            'id_list' => [
                'type' => self::TYPE_INT,
                'required' => true
            ],
            'hash' => [
                'type' => self::TYPE_STRING,
                'required' => true
            ],
            'email' => [
                'type' => self::TYPE_STRING,
                'required' => false
            ],
        ],
    ];


}