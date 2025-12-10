<?php

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'phdescription` (
          `id_description` int(11) NOT NULL AUTO_INCREMENT,
          `id_product` int(11) NOT NULL,
          `type` int(11) NOT NULL,
          `position` int(11) NOT NULL,
          `active` int(11) NOT NULL,
          `id_shop` int(11) NOT NULL,
          `date_add` datetime NOT NULL,
          `date_upd` datetime NOT NULL,
          PRIMARY KEY (`id_description`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'phdescription_lang` (
          `id_description_lang` int(11) NOT NULL AUTO_INCREMENT,
          `id_product` int(11) NOT NULL,
          `id_description` int(11) NOT NULL,
          `text` TEXT NULL,
          `text2` TEXT NULL,
          `image` varchar(255) NULL,
          `image2` varchar(255) NULL,
          `id_lang` int(11) NOT NULL,
          `date_add` datetime NOT NULL,
          `date_upd` datetime NOT NULL,
          PRIMARY KEY (`id_description_lang`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) === false) {
        return false;
    }
}
return true;
