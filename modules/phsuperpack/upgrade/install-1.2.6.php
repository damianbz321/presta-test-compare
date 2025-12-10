<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_6($object)
{
	if (!Db::getInstance()->Execute('SELECT id_product_attribute from `'._DB_PREFIX_.'phsuperpack_product`'))
    { 
	   if (!Db::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "phsuperpack_product` ADD `id_product_attribute` int(11) NULL;"))
	   return false;
    }
			
    return true;
}