<?php

namespace Freshmail\Entity;

use ObjectModel;

class FreshmailSetting extends ObjectModel
{
    public $id;
    public $api_token;
    public $id_shop;
    public $smtp = 0;
    public $synchronize = 0;
    public $subscriber_list_hash;
    public $wizard_completed = 0;
    public $id_specific_price_rule = 0;
    public $send_confirmation = 0;
    public $date_add;
    public $date_upd;


    public static $definition = [
        'table' => 'freshmail_setting',
        'primary' => 'id_freshmail_setting',
        'fields' => [
            'id_shop' => [
                'type' => self::TYPE_INT,
                'required' => true
            ],
            'api_token' => [
                'type' => self::TYPE_STRING,
                'required' => false
            ],
            'subscriber_list_hash' => [
                'type' => self::TYPE_STRING,
                'required' => false
            ],
            'smtp' => [
                'type' => self::TYPE_BOOL,
            ],
            'synchronize' => [
                'type' => self::TYPE_BOOL
            ],
            'wizard_completed' => [
                'type' => self::TYPE_BOOL
            ],
            'send_confirmation' => [
                'type' => self::TYPE_BOOL
            ],
            'id_specific_price_rule' => [
                'type' => self::TYPE_INT,
                'required' => true
            ],
        ],
    ];


}