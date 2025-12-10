<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_5($object)
{
	if (!Db::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "product_lang` MODIFY `name` varchar(255) NOT NULL;"))
	   return false;
			
    return true;
}