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
class oreward extends ObjectModel
{
    public $active;
    public $id_reward;
    public $id_shop;
    public $value;
    public $value2;
    public $min;
    public $min_order;
    public $min_currency;
    public $max;
    public $max_order;
    public $max_currency;
    public $internal_name;
    public $manufacturers;
    public $manufacturers_list;
    public $nocoupon;
    public $percentage;
    public $percentage_value;
    public $percentage_type;
    public $specific_product;
    public $no_guests;
    public $associate_customer;
    public $prod;
    public $prod_list;
    public $prod_eo;
    public $prod_eo_list;
    public $prod_diff_than_list;
    public $prod_diff_than;
    public $exvalcat;
    public $excat;
    public $groups;
    public $mail_template;
    public $minbasket;
    public $categories;
    public $categories_list;

    public static $definition = array(
        'table' => 'orewards',
        'primary' => 'id_reward',
        'multilang' => false,
        'fields' => array(
            'active' => array('type' => ObjectModel :: TYPE_INT),
            'id_reward' => array('type' => ObjectModel :: TYPE_INT),
            'id_shop' => array('type' => ObjectModel :: TYPE_INT),
            'value' => array('type' => ObjectModel :: TYPE_INT),
            'value2' => array('type' => ObjectModel :: TYPE_INT),
            'min' => array('type' => ObjectModel :: TYPE_INT),
            'min_order' => array('type' => ObjectModel :: TYPE_FLOAT),
            'min_currency' => array('type' => ObjectModel :: TYPE_INT),
            'max' => array('type' => ObjectModel :: TYPE_INT),
            'max_order' => array('type' => ObjectModel :: TYPE_FLOAT),
            'max_currency' => array('type' => ObjectModel :: TYPE_INT),
            'internal_name' => array('type' => ObjectModel :: TYPE_STRING),
            'manufacturers' => array('type' => ObjectModel :: TYPE_FLOAT),
            'manufacturers_list' => array('type' => ObjectModel :: TYPE_STRING),
            'nocoupon' => array('type' => ObjectModel::TYPE_INT),
            'percentage' => array('type' => ObjectModel :: TYPE_FLOAT),
            'percentage_value' => array('type' => ObjectModel :: TYPE_STRING),
            'percentage_type' => array('type' => ObjectModel :: TYPE_STRING),
            'no_guests' => array('type' => ObjectModel::TYPE_INT),
            'associate_customer' => array('type' => ObjectModel::TYPE_INT),
            'specific_product' => array('type' => ObjectModel::TYPE_INT),
            'prod' => array('type' => ObjectModel::TYPE_INT),
            'prod_list' => array('type' => ObjectModel :: TYPE_STRING),
            'prod_eo' => array('type' => ObjectModel::TYPE_INT),
            'prod_eo_list' => array('type' => ObjectModel :: TYPE_STRING),
            'prod_diff_than' => array('type' => ObjectModel::TYPE_INT),
            'prod_diff_than_list' => array('type' => ObjectModel :: TYPE_STRING),
            'exvalcat' => array('type' => ObjectModel::TYPE_STRING),
            'excat' => array('type' => ObjectModel::TYPE_STRING),
            'groups' => array('type' => ObjectModel::TYPE_STRING),
            'mail_template' => array('type' => ObjectModel::TYPE_STRING),
            'minbasket' => array('type' => ObjectModel::TYPE_STRING),
            'categories' => array('type' => ObjectModel :: TYPE_FLOAT),
            'categories_list' => array('type' => ObjectModel :: TYPE_STRING),
        ),
    );

    public static function getThemAll()
    {

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'orewards` WHERE `id_shop`=' . Context::getContext()->shop->id . ' ORDER BY value ASC');
    }

    public static function getByValue($value)
    {

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT count(*) AS count FROM `' . _DB_PREFIX_ . 'orewards` WHERE `active` = 1 AND  `id_shop`=' . Context::getContext()->shop->id . ' AND "' . $value . '" BETWEEN value AND value2');
    }

    public static function getByValueFull($value)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'orewards` WHERE `active` = 1 AND  `id_shop`=' . Context::getContext()->shop->id . ' AND "' . $value . '" BETWEEN value AND value2 ');
    }

    public static function getByValueFullArray($value)
    {

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'orewards` WHERE `active` = 1 AND  `id_shop`=' . Context::getContext()->shop->id . ' AND "' . $value . '" BETWEEN value AND value2 ');
    }
}