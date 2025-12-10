<?php

namespace FreshMail\Entity;

use ObjectModel;

class EmailToSynchronize extends ObjectModel
{
    public $email;

    public $name;

    public $hash_list;

    public static $definition = [
        'table' => 'freshmail_emails_to_synchronize',
        'primary' => 'id_freshmail_emails_to_synchronize',
        'fields' => [
            'email' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'validate' => 'isEmail'
            ],
            'name' => [
                'type' => self::TYPE_STRING,
                'required' => false
            ],
            'hash_list' => [
                'type' => self::TYPE_STRING,
                'required' => false
            ],
        ],
    ];

}