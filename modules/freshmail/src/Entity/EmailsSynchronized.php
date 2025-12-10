<?php

namespace FreshMail\Entity;

use ObjectModel;

class EmailsSynchronized extends ObjectModel
{
    public $email;

    public $hash_list;

    public static $definition = [
        'table' => 'freshmail_emails_synchronized',
        'primary' => 'id_freshmail_emails_synchronized',
        'fields' => [
            'email' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'validate' => 'isEmail'
            ],
            'hash_list' => [
                'type' => self::TYPE_STRING,
                'required' => false
            ],
        ],
    ];

}