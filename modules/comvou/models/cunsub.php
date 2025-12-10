<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2020 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 * @version   of the vouchers engine: 3.2
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class cunsub extends ObjectModel
{

    public $id_cunsub;
    public $email;
    public $ip;
    public $unsub_date;

    public static $definition = array(
        'table' => 'cunsub',
        'primary' => 'id_cunsub',
        'multilang' => false,
        'fields' => array(
            'id_cunsub' => array('type' => ObjectModel :: TYPE_INT),
            'email' => array('type' => ObjectModel :: TYPE_STRING),
            'ip' => array('type' => ObjectModel :: TYPE_STRING),
            'unsub_date' => array('type' => ObjectModel :: TYPE_STRING)
        ),
    );

    public static function checkUnsubs($email)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'cunsub` WHERE email="' . $email . '"');
    }

    public static function returnObjectByMail($email)
    {
        if ($return = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'cunsub` WHERE email="' . $email . '"')) {
            if (isset($return['email'])) {
                if (Validate::isEmail($return['email'])) {
                    return new cunsub($return['id_cunsub']);
                }
            }
        }
    }
}