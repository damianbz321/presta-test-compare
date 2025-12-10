<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_10_0($module)
{
    $result = true;
    $sql =  [
        'DROP TABLE IF EXISTS `PREFIX_freshmail_list_field`',
        'DROP TABLE IF EXISTS `PREFIX_freshmail_form`',

        'CREATE TABLE `PREFIX_freshmail_form` (
            `id_freshmail_form` int(11) NOT NULL AUTO_INCREMENT,
            `id_shop` INT NOT NULL ,
            `form_hash` char(10) COLLATE utf8_general_ci NOT NULL,
            `hook` varchar(50) COLLATE utf8_general_ci NOT NULL,
            `position` int(11) NOT NULL,
            `last` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `active` tinyint(4) NOT NULL,
            PRIMARY KEY (`id_freshmail_form`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ',
    ];
    foreach ($sql as $s){
        $result &= Db::getInstance()->execute(
            str_replace('PREFIX_', _DB_PREFIX_, $s)
        );
    }

    foreach ($module->getHooksForm() as $hook){
        $result &= $module->registerHook($hook);
    }

    return $result;
}
