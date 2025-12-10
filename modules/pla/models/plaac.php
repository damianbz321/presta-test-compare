<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

class plaac extends ObjectModel
{
    public $id_plaac;
    public $id_attribute;
    public $id_category;
    public $active;
    public static $definition = array(
        'table' => 'plaac',
        'primary' => 'id_plaac',
        'multilang' => false,
        'fields' => array(
            'id_plaac' => array('type' => ObjectModel :: TYPE_INT),
            'id_attribute' => array('type' => ObjectModel :: TYPE_INT),
            'id_category' => array('type' => ObjectModel :: TYPE_INT),
            'active' => array('type' => ObjectModel :: TYPE_INT),
        ),
    );

    public static function getAllConditions($id_category = 0){
        return Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'plaac` a WHERE a.`active` = 1 AND a.`id_category` = '.$id_category);
    }
}