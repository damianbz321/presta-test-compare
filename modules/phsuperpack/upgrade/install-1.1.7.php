<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_7($object)
{
    $object;
	  
	$sql = "ALTER TABLE `" . _DB_PREFIX_ . "phsuperpack_product` ADD `position` int(11) NULL;";
	$return = Db::getInstance()->execute($sql);

    return $return;
}
