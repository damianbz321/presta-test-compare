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

class LabSampleDataProdFilter
{
	public function initData()
	{
		$return = true;
		$id_shop = Configuration::get('PS_SHOP_DEFAULT');	
		$return &= Db::getInstance()->Execute('INSERT IGNORE INTO `'._DB_PREFIX_.'labproductfilter` (`id_labproductfilter`, `shownew`, `showfeature`, `showsale`, `showspecail`, `id_hook`, `type_display`, `num_show`,`num_row`,`num_column`, `use_slider`, `active`) VALUES 
		(1, 1, 0, 0, 0, "displayPosition4", "accordion", 16, 1, 4, 1, 1)
		
		;');
		
		$return &= Db::getInstance()->Execute('INSERT IGNORE INTO `'._DB_PREFIX_.'labproductfilter_shop` (`id_labproductfilter`, `shownew`, `showfeature`, `showsale`, `showspecail`, `id_shop`, `id_hook`, `type_display`, `num_show`,`num_row`,`num_column`, `use_slider`, `active`) VALUES 
		(1, 1, 0, 0, 0, "'.$id_shop.'", "displayPosition4", "accordion", 16, 1, 4, 1, 1)
		
		;');	
		return $return;
	}
}
?>