{*
* 2007-2018 PrestaShop
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
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2018 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
<li style="cursor:pointer;" class="keep-it-tight">
	<div class="col-sm-12 col-md-12 col-lg-12 keep-it-tight well" style="display: inline-block;" onclick="selectProduct({$id_product|escape:'htmlall':'UTF-8'},'{$val|escape:'htmlall':'UTF-8'}', '', '{$lang|escape:'htmlall':'UTF-8'}');$(this).parent().parent().parent().hide()">
		<div class="col-sm-12 col-md-12 col-lg-12 keep-it-tight">
			<h4>{$product_name|escape:'htmlall':'UTF-8'}</h4>
			<p><b>ID Product : </b>{$id_product|escape:'htmlall':'UTF-8'}</p>
		</div>
		<div class="col-sm-12 col-md-12 col-lg-12 keep-it-tight">
			<div class="col-sm-12 col-md-6 col-lg-6 keep-it-tight">
				<img style="height:auto; width:auto; max-width:150px; max-height:150px;"
						src="{$product_image|escape:'htmlall':'UTF-8'}"
				>
			</div>
			<div class="col-sm-12 col-md-6 col-lg-4 keep-it-tight" style="margin-left: 30px;">
				<span>{$product_price|escape:'htmlall':'UTF-8'}{$currency_symbol|escape:'htmlall':'UTF-8'}</span>
			</div>
		</div>
		{if $has_specific_price eq 1}
			<div class="col-sm-12 col-md-12 col-lg-12 keep-it-tight">
				<span class="badge badge-info" style="width:100%;">{l s='Contains specific price' mod='pushoncart'}</span>
			</div>
		{/if}
		<hr>
	</div>
</li>
