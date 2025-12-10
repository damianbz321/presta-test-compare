<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_9_0($module)
{
    $result = true;
    $sql =  [
        'CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_birthday`(
            `id_freshmail_birthday` INT NOT NULL AUTO_INCREMENT ,
            `id_shop` INT NOT NULL ,
            `enable` TINYINT(1) NOT NULL DEFAULT 0,
            `tpl` VARCHAR(32),
            `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            `date_upd` TIMESTAMP NULL,
            PRIMARY KEY (`id_freshmail_birthday`)
        ) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci',
        'CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_birthday_lang`(
            `id_freshmail_birthday` INT NOT NULL AUTO_INCREMENT ,
            `id_lang` INT NOT NULL ,
            `content` TEXT,
            PRIMARY KEY (`id_freshmail_birthday`, `id_lang`)
            ) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;',
    ];
    foreach ($sql as $s){
        $result &= Db::getInstance()->execute(
            str_replace('PREFIX_', _DB_PREFIX_, $s)
        );
    }

    $fs = (new \FreshMail\Repository\FreshmailSettings())->findForShop(Context::getContext()->shop->id);

    if(!Validate::isLoadedObject($fs)){
        return $result;
    }

    return $result
        && \FreshMail\Installer\InstallerFactory::getInstaller($module)->installTab(
        'AdminFreshmailBirthday', 'Birthday', 'FreshmailConfig', false, ''
        )
    ;
}
