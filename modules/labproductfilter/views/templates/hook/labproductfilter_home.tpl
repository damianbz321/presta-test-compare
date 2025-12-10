{**
* 2007-2015 PrestaShop
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
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!-- <script type="text/javascript" src="{$path_|escape:'html':'UTF-8'}views/js/jquery-1.7.1.min.js"></script> -->
{foreach from=$group_prod_fliter item=product_hook name=product_hook}
	{if $product_hook.type_display == 'accordion'}
		{include file="./labproductfilter_accordion.tpl" product_groups = $product_hook.product_group}
	{/if}
	{if $product_hook.type_display == 'column'}
		{include file="./labproductfilter_column.tpl" product_groups = $product_hook.product_group}
	{/if}
	{if $product_hook.type_display == 'tab'}
		{include file="./labproductfilter_tab.tpl" product_groups = $product_hook.product_group}
	{/if}
	
{/foreach}