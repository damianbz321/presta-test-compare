<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2020 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class fpl extends ObjectModel
{
    public $id_fpl;
    //public $id_shop;
    public $id_category;
    public $active;
    public $features;
    public static $definition = array(
        'table' => 'fpl',
        'primary' => 'id_fpl',
        'multilang' => false,
        'fields' => array(
            'id_fpl' => array('type' => ObjectModel :: TYPE_INT),
            //'id_shop' => array('type' => ObjectModel :: TYPE_INT),
            'id_category' => array('type' => ObjectModel :: TYPE_INT),
            'active' => array('type' => ObjectModel :: TYPE_INT),
            'features' => array('type' => ObjectModel :: TYPE_HTML,),
        ),
    );

    public static function changeStatus($id, $type)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('UPDATE `' . _DB_PREFIX_ . 'fpl` SET `' . $type . '` = NOT `' . $type . '` WHERE `id_fpl`=' . $id . ' ');
    }

    public static function getAll($active = true)
    {
        $where = '';
        if ($active)
        {
            $where = ' AND active=1';
        }
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'fpl` WHERE 1=1 '.$where);
    }
}