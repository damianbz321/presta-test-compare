<?php
/**
* 2014-2020 Presta-Mod.pl Rafał Zontek
*
* NOTICE OF LICENSE
*
* Poniższy kod jest kodem płatnym, rozpowszechanie bez pisemnej zgody autora zabronione
* Moduł można zakupić na stronie Presta-Mod.pl. Modyfikacja kodu jest zabroniona,
* wszelkie modyfikacje powodują utratę gwarancji
*
* http://presta-mod.pl
*
* DISCLAIMER
*
*
*  @author    Presta-Mod.pl Rafał Zontek <biuro@presta-mod.pl>
*  @copyright 2014-2020 Presta-Mod.pl
*  @license   Licecnja na jedną domenę
*  Presta-Mod.pl Rafał Zontek
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pminpostpaczkomatylist` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`id_cart` INT(11) NULL DEFAULT NULL,
	`machine` CHAR(255) NULL DEFAULT NULL COLLATE utf8_polish_ci,
	`nr_listu` CHAR(255) NULL DEFAULT NULL COLLATE utf8_polish_ci,
	`status` CHAR(255) NULL DEFAULT NULL COLLATE utf8_polish_ci,
	`post_info` LONGTEXT NULL COLLATE utf8_polish_ci,
	`id_pack` char(255) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
