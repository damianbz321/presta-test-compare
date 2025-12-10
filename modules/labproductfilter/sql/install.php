<?php
/**
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'labproductfilter` (
    `id_labproductfilter` int(11) NOT NULL AUTO_INCREMENT,
	`shownew` int(10) unsigned NOT NULL,
	`showfeature` int(10) unsigned NOT NULL,
	`showsale` int(10) unsigned NOT NULL,
	`showspecail` int(10) unsigned NOT NULL,
	`id_hook` varchar(255) NOT NULL,
	`type_display` varchar(255) NOT NULL,
	`num_show` int(10) unsigned NOT NULL,
	`num_row` int(10) unsigned NOT NULL,
	`num_column` int(10) unsigned NOT NULL,
	`use_slider` int(10) unsigned NOT NULL,
	`active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
    PRIMARY KEY  (`id_labproductfilter`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'labproductfilter_shop` (
    `id_labproductfilter` int(11) NOT NULL AUTO_INCREMENT,
	`shownew` int(10) unsigned NOT NULL,
	`showfeature` int(10) unsigned NOT NULL,
	`showsale` int(10) unsigned NOT NULL,
	`showspecail` int(10) unsigned NOT NULL,
	`id_shop` int(10) unsigned NOT NULL,
	`id_hook` varchar(255) NOT NULL,
	`type_display` varchar(255) NOT NULL,
	`num_show` int(10) unsigned NOT NULL,
	`num_row` int(10) unsigned NOT NULL,
	`num_column` int(10) unsigned NOT NULL,
	`use_slider` int(10) unsigned NOT NULL,
	`active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
    PRIMARY KEY  (`id_labproductfilter`, `id_shop`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;
