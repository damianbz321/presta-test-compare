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
class cvor extends ObjectModel
{

    public $id_cvor;
    public $id_order;
    public $deliverydate;
    public $email;
    public static $definition = array(
        'table' => 'cvor',
        'primary' => 'id_cvor',
        'multilang' => false,
        'fields' => array(
            'id_cvor' => array('type' => ObjectModel :: TYPE_INT),
            'id_order' => array('type' => ObjectModel :: TYPE_INT),
            'deliverydate' => array('type' => ObjectModel :: TYPE_DATE),
            'email' => array('type' => ObjectModel :: TYPE_STRING)
        ),
    );

    public static function getLastReminder($id_order){
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `'._DB_PREFIX_.'cvor` WHERE id_order ='.$id_order.' ORDER BY id_cvor DESC');
    }

    public static function getAll(){
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'cvor`');
    }

    public function __construct($id_cvov = null)
    {

        parent::__construct($id_cvov);
    }
}