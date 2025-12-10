<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_9($object)
{
	if (!Db::getInstance()->Execute('SELECT position from `'._DB_PREFIX_.'phsuperpack_product`'))
    { 
	   if (!Db::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "phsuperpack_product` ADD `position` int(11) NULL;"))
	   return false;
    }
		
	$return = Configuration::updateValue('PH_BANNER_IMG', 'default_banner.png');
    return $return;
}