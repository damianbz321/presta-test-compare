<?php

namespace FreshMail\Entity;

use ObjectModel;

class Form extends ObjectModel
{
    public $id;
    public $id_shop;
    public $form_hash;
    public $hook;
    public $position;
    public $active;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'freshmail_form',
        'primary' => 'id_freshmail_form',
        'fields' => [
            'form_hash' => ['type' => self::TYPE_STRING, 'reqired' => true],
            'hook' => ['type' => self::TYPE_STRING, 'required' => true],
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'position' => ['type' => self::TYPE_INT, 'required' => false, 'validate' => 'isUnsignedInt'],
            'active' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
        ]
    ];

    /**
     * Get repository class name
     *
     * @return string
     */
    public static function getRepositoryClassName()
    {
        return 'FormRepository';
    }

}