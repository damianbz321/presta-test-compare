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

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'labgroupcategory` (
    `id_labgroupcategory` int(11) NOT NULL AUTO_INCREMENT,
	`group_cat` varchar(255) NOT NULL,
	`id_hook` varchar(255) NOT NULL,
	`type_display` varchar(255) NOT NULL,
	`num_show` int(10) unsigned NOT NULL,
	`use_slider` int(10) unsigned NOT NULL,
	`show_sub` int(10) unsigned NOT NULL,
	`active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
    PRIMARY KEY  (`id_labgroupcategory`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'labgroupcategory_shop` (
    `id_labgroupcategory` int(11) NOT NULL AUTO_INCREMENT,
	`group_cat` varchar(255) NOT NULL,
	`id_shop` int(10) unsigned NOT NULL,
	`id_hook` varchar(255) NOT NULL,
	`type_display` varchar(255) NOT NULL,
	`num_show` int(10) unsigned NOT NULL,
	`use_slider` int(10) unsigned NOT NULL,
	`show_sub` int(10) unsigned NOT NULL,
	`active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
    PRIMARY KEY  (`id_labgroupcategory`, `id_shop`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'labcategory` (
	  `id_labcategory` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `id_labgroupcategory` int(10) unsigned NOT NULL,
	  `id_cat` int(10) unsigned NOT NULL,
	  `cat_icon` varchar(255) NOT NULL,
	  `cat_color` varchar(255) NOT NULL,
	  `manufacture` varchar(255) NOT NULL,
	  `position` int(10) unsigned NOT NULL DEFAULT \'0\',
	  `show_img` int(10) unsigned NOT NULL,
	  `special_prod` int(10) unsigned NOT NULL,
	  `active` int(10) unsigned NOT NULL,
	  PRIMARY KEY (`id_labcategory`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;';
	
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'labcategory_shop` (
	  `id_labcategory` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `id_labgroupcategory` int(10) unsigned NOT NULL,
	  `id_shop` int(10) unsigned NOT NULL,
	  `id_cat` int(10) unsigned NOT NULL,
	  `cat_icon` varchar(255) NOT NULL,
	  `cat_color` varchar(255) NOT NULL,
	  `manufacture` varchar(255) NOT NULL,
	  `position` int(10) unsigned NOT NULL DEFAULT \'0\',
	  `show_img` int(10) unsigned NOT NULL,
	  `special_prod` int(10) unsigned NOT NULL,
	  `active` int(10) unsigned NOT NULL,
	  PRIMARY KEY (`id_labcategory`, `id_shop`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;';
	
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'labcategory_lang` (
	  `id_labcategory` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `id_shop` int(10) unsigned NOT NULL,
	  `id_lang` int(10) unsigned NOT NULL,
	  `cat_desc` varchar(255) NOT NULL,
	  `cat_banner` varchar(255) NOT NULL,
	  PRIMARY KEY (`id_labcategory`, `id_shop`, `id_lang`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;';

foreach ($sql as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;
