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
class cvov extends ObjectModel
{

    public $id_cvov;
    public $id_product_comment;
    public $id_product;
    public $deliverydate;
    public $email;
    public $voucher;
    public $id_voucher;
    public static $definition = array(
        'table' => 'cvov',
        'primary' => 'id_cvov',
        'multilang' => false,
        'fields' => array(
            'id_cvov' => array('type' => ObjectModel :: TYPE_INT),
            'id_product_comment' => array('type' => ObjectModel :: TYPE_INT),
            'id_product' => array('type' => ObjectModel :: TYPE_INT),
            'deliverydate' => array('type' => ObjectModel :: TYPE_DATE),
            'email' => array('type' => ObjectModel :: TYPE_STRING),
            'voucher' => array('type' => ObjectModel :: TYPE_STRING),
            'id_voucher' => array('type' => ObjectModel :: TYPE_INT)
        ),
    );

    public function __construct($id_cvov = null)
    {

        parent::__construct($id_cvov);
    }
}