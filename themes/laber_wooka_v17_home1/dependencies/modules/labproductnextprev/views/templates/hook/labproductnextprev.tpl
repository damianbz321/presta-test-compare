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


{if isset($product_next)}
<div class="lab_next_product">
<a style="background: {$lab_bkg_color|escape:'html':'UTF-8'}; border: 1px solid {$lab_border_color|escape:'html':'UTF-8'};" href="{$link->getProductLink($product_next)|escape:'html':'UTF-8'}" class="button button_next"><span style="color: {$lab_button_color|escape:'html':'UTF-8'};"></span></a>
<div class="lab_next_prev_content">
<h5 class="product-name">
<a class="product-name" href="{$link->getProductLink($product_next)|escape:'html':'UTF-8'}" title="{$product_next->name|escape:'html':'UTF-8'}">{$product_next->name|truncate:50:'...'|strip_tags|escape:'html':'UTF-8'}</a></h5>
<a class="product-name" href="{$link->getProductLink($product_next)|escape:'html':'UTF-8'}" title="{$product_next->name|escape:'html':'UTF-8'}">
<img class="img-responsive"  src="{$link->getImageLink($product_next->link_rewrite, $id_images_next, 'home_default')|escape:'html':'UTF-8'}" itemprop="image" /></a>

</div>
</div>
{/if}

{if isset($product_prev)}
<div class="lab_prev_product">
<a style="background: {$lab_bkg_color|escape:'html':'UTF-8'}; border: 1px solid {$lab_border_color|escape:'html':'UTF-8'};" href="{$link->getProductLink($product_prev)|escape:'html':'UTF-8'}" class="button button_prev"><span style="color: {$lab_button_color|escape:'html':'UTF-8'};"></span></a>
<div class="lab_next_prev_content">
<h5 class="product-name"><a class="product-name" href="{$link->getProductLink($product_prev)|escape:'html':'UTF-8'}" title="{$product_prev->name|escape:'html':'UTF-8'}">{$product_prev->name|truncate:50:'...'|strip_tags|escape:'html':'UTF-8'}</a></h5>
<a class="product-name" href="{$link->getProductLink($product_prev)|escape:'html':'UTF-8'}" title="{$product_prev->name|escape:'html':'UTF-8'}">
<img class="img-responsive"  src="{$link->getImageLink($product_prev->link_rewrite, $id_images_prev, 'home_default')|escape:'html':'UTF-8'}" itemprop="image" /></a>

</div>
</div>
{/if}
