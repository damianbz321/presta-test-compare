<?php
/**
* 2012-2020 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Pop-Ups Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2020 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD PopUps Pro - PrestaShop 1.6.x and 1.7.x Module
* @version   1.3.1
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      15-12-2020
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * This function updates your module from previous versions to the version 1.1,
 * usefull when you modify your database, or register a new hook ...
 * Don't forget to create one file per version.
 */
function upgrade_module_1_2_7($module)
{
    /**
     * Do everything you want right there,
     * You could add a column in one of your module's tables
     */
    Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'pdpopupspro` ADD `npc` tinyint(1) unsigned NOT NULL DEFAULT \'1\'');
    Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'pdpopupspro` ADD `npc_cms` int(10) unsigned NOT NULL DEFAULT \'3\'');
    return true;
}
