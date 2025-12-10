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
class ManufacturerController extends ManufacturerControllerCore
{
    /*
    * module: purls
    * date: 2022-12-01 11:37:20
    * version: 2.9.2
    */
    public function init()
    {
        if (Configuration::get('purls_manufacturers') == 1)
        {
            $link_pattern = Tools::safeOutput(urldecode(Tools::getValue('manufacturer_rewrite')));
            $manufacturer_pattern = '/.*?([0-9]+)\([_a-zA-Z0-9-\pL]*)/';
            preg_match($manufacturer_pattern, $_SERVER['REQUEST_URI'], $Array);
            if (isset($Array[2]) && $Array[2] != "")
            {
                $link_pattern = $Array[2];
            }
            if ($link_pattern)
            {
                $sqlall = 'SELECT * FROM `' . _DB_PREFIX_ . 'manufacturer` m
       			LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer_shop` s ON (m.`id_manufacturer` = s.`id_manufacturer`) WHERE 1=1 ';
                if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP)
                {
                    $sqlall .= ' AND s.`id_shop` = ' . (int)Shop::getContextShopID();
                }
                $allmanufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sqlall);
                foreach ($allmanufacturers as $key => $manufacturer)
                {
                    if ($link_pattern == Tools::str2url($manufacturer['name']))
                    {
                        $id_manufacturer = $manufacturer['id_manufacturer'];
                    }
                }
                if ($id_manufacturer != "")
                {
                    $_GET['id_manufacturer'] = $id_manufacturer;
                    $_GET['manufacturer_rewrite'] = '';
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