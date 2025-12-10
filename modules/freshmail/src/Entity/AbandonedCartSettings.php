<?php

namespace Freshmail\Entity;

use ObjectModel;

class AbandonedCartSettings extends ObjectModel
{
    const DISCOUNT_NONE = 0;
    const DISCOUNT_PERCENT = 1;
    const DISCOUNT_CUSTOM = 2;

    public $id;
    public $api_token;
    public $id_shop;
    public $enabled = 0;
    public $send_after;
    public $discount_type;
    public $discount_code;
    public $discount_percent;
    public $discount_lifetime;
    public $template;
    public $template_id_hash;

    public $email_subject;
    public $email_preheader;


    public $date_add;
    public $date_upd;


    public static $definition = [
        'table' => 'freshmail_cart_setting',
        'primary' => 'id_freshmail_cart_setting',
        'multilang' => true,
        'fields' => [
            'id_shop' => [
                'type' => self::TYPE_INT,
                'required' => true
            ],
            'enabled' => [
                'type' => self::TYPE_BOOL,
            ],

            'discount_type' => [
                'type' => self::TYPE_STRING,
                'required' => false
            ],
            'discount_code' => [
                'type' => self::TYPE_STRING,
                'required' => false
            ],
            'discount_percent' => [
                'type' => self::TYPE_STRING,
                'required' => false
            ],
            'discount_lifetime' => [
                'type' => self::TYPE_INT,
                'required' => false
            ],
            'send_after' => [
                'type' => self::TYPE_INT,
                'required' => false
            ],
            'template' => [
                'type' => self::TYPE_STRING,
                'required' => false
            ],
            'template_id_hash' => [
                'type' => self::TYPE_STRING,
                'required' => false
            ],
            'email_subject' => [
                'lang' => true,
                'type' => self::TYPE_STRING,
            ],
            'email_preheader' => [
                'lang' => true,
                'type' => self::TYPE_STRING,
                'required' => false
            ],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null, $translator = null)
    {
        parent::__construct($id, $id_lang, $id_shop, $translator);

        $templateFile = _PS_MODULE_DIR_.'freshmail/install/default_template';

        if(empty($this->template) && file_exists($templateFile)){
            $this->template = file_get_contents($templateFile);
        }
    }


}