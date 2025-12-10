{**
* 2007-2017 PrestaShop
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
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="laberNextPrev pull-right">
{if isset($product_prev)}
<div class="labPrev_product nextPrevProduct pull-left">
	<a href="{$link->getProductLink($product_prev)|escape:'html':'UTF-8'}" class="button button_prev">
		<i class="icon-chevron-left icon" aria-hidden="true"></i>
	</a>
	<div class="laberContent">
		<!-- <h2 class="h2 laber-product-title">
			<a class="product-name" href="{$link->getProductLink($product_prev)|escape:'html':'UTF-8'}" title="{$product_prev->name|escape:'html':'UTF-8'}">{$product_prev->name|truncate:50:'...'|strip_tags|escape:'html':'UTF-8'}</a>
		</h2> -->
		<a class="product-name" href="{$link->getProductLink($product_prev)|escape:'html':'UTF-8'}" title="{$product_prev->name|escape:'html':'UTF-8'}">
			<img class="img-responsive"  src="{$link->getImageLink($product_prev->link_rewrite, $id_images_prev, 'cart_default')|escape:'html':'UTF-8'}" itemprop="image" />
		</a>
	</div>
</div>
{/if}
{if isset($product_next)}
<div class="labNext_product nextPrevProduct pull-left">
	<a  href="{$link->getProductLink($product_next)|escape:'html':'UTF-8'}" class="button button_next">
		<i class="icon-chevron-right icon" aria-hidden="true"></i>
	</a>
	<div class="laberContent">
		<!-- <h2 class="h2 laber-product-title">
			<a class="product-name" href="{$link->getProductLink($product_next)|escape:'html':'UTF-8'}" title="{$product_next->name|escape:'html':'UTF-8'}">{$product_next->name|truncate:50:'...'|strip_tags|escape:'html':'UTF-8'}</a>
		</h2> -->
		<a class="product-img" href="{$link->getProductLink($product_next)|escape:'html':'UTF-8'}" title="{$product_next->name|escape:'html':'UTF-8'}">
			<img class="img-responsive"  src="{$link->getImageLink($product_next->link_rewrite, $id_images_next, 'cart_default')|escape:'html':'UTF-8'}" itemprop="image" />
		</a>
	</div>
</div>
{/if}
</div>


