<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA Miłosz Myszczuk VATEU: PL9730945634
 * @copyright 2010-2022 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class SupplierController extends SupplierControllerCore
{
    /*
    * module: purls
    * date: 2022-12-01 11:37:19
    * version: 2.9.2
    */
    public function init()
    {
        if (Configuration::get('purls_suppliers') == 1)
        {
            $link_pattern = Tools::safeOutput(urldecode(Tools::getValue('supplier_rewrite')));
            $supplier_pattern = '/.*?([0-9]+)\_\_([_a-zA-Z0-9-\pL]*)/';
            preg_match($supplier_pattern, $_SERVER['REQUEST_URI'], $Array);
            if (isset($Array[2]) && $Array[2] != "")
            {
                $link_pattern = $Array[2];
            }
            if ($link_pattern)
            {
                $sql = 'SELECT *
        		FROM `' . _DB_PREFIX_ . 'supplier` sp
        		LEFT JOIN `' . _DB_PREFIX_ . 'supplier_shop` s ON (sp.`id_supplier` = s.`id_supplier`) WHERE 1=1';
                if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP)
                {
                    $sql .= ' AND s.`id_shop` = ' . (int)Shop::getContextShopID();
                }
                $allsuppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
                foreach ($allsuppliers as $key => $supplier)
                {
                    if ($link_pattern == Tools::str2url($supplier['name']))
                    {
                        $id_supplier = $supplier['id_supplier'];
                    }
                }
                if ($id_supplier != "")
                {
                    $_GET['id_supplier'] = $id_supplier;
                    $_GET['supplier_rewrite'] = '';
                }
            }
            parent::init();
        }
        else
        {
            parent::init();
        }
    }
}