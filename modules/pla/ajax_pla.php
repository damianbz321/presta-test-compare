<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER
 * support@mypresta.eu
 */
include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('pla.php');
$thismodule = new pla();

if (Tools::getValue('idp','false') != 'false') {
    $product = new Product(Tools::getValue('idp'), true, Context::getContext()->language->id);
    $thismodule->returnTable((array)$product);
}

?>