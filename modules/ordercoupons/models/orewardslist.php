<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 * @version   of the vouchers engine: 4.3
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class orewardslist extends ObjectModel
{
    public $id_reward;
    public $id_cart_rule;
    public $id_customer;
    public $id_order;
    public $reward;
    public $date_add;
    public $date_upd;
    public $date_del;

    public static $definition = array(
        'table' => 'orewards_list',
        'primary' => 'id_reward',
        'multilang' => false,
        'fields' => array(
            'id_reward' => array('type' => ObjectModel :: TYPE_INT),
            'id_cart_rule' => array('type' => ObjectModel :: TYPE_INT),
            'id_customer' => array('type' => ObjectModel :: TYPE_INT),
            'id_order' => array('type' => ObjectModel :: TYPE_INT),
            'reward' => array('type' => ObjectModel :: TYPE_STRING),
            'date_add' => array('type' => ObjectModel :: TYPE_STRING),
            'date_upd' => array('type' => ObjectModel :: TYPE_STRING),
            'date_del' => array('type' => ObjectModel :: TYPE_STRING)
        ),
    );

    public function __construct($id_reward = null)
    {
        parent::__construct($id_reward);
    }

    public static function getByIdCustomer($id_customer)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT count(*) AS count FROM `' . _DB_PREFIX_ . 'orewards_list` WHERE id_customer="' . $id_customer . '"');
    }

    public static function getByIdCustomerAndIdReward($id_customer, $id_reward)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT count(*) AS count FROM `' . _DB_PREFIX_ . 'orewards_list` WHERE id_customer="' . $id_customer . '" AND id_cart_rule="' . $id_reward . '"');
    }

    public static function getByIdCustomerAndIdOrder($id_customer, $id_order)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT count(*) AS count FROM `' . _DB_PREFIX_ . 'orewards_list` WHERE id_order="' . $id_order . '" AND id_customer="' . $id_customer . '"');
    }
}