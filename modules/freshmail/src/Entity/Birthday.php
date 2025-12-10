<?php


namespace FreshMail\Entity;

use ObjectModel;

class Birthday extends ObjectModel
{
    public $id_shop;
    public $enable = false;
    public $tpl = '';
    public $content;
    public $email_subject;

    public static $definition = [
        'table' => 'freshmail_birthday',
        'primary' => 'id_freshmail_birthday',
        'multilang' => true,
        'fields' => [
            'enable' => [
                'type' => self::TYPE_BOOL
            ],
            'id_shop' => [
                'type' => self::TYPE_INT
            ],
            'tpl' => [
                'type' => self::TYPE_STRING
            ],
            'content' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
            ],
            'email_subject' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
            ],
        ]
    ];
}

