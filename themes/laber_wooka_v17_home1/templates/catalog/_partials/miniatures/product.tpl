{**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
 <style>
 .laberCart.laberItem button {
   height:42px;
 }
 .laberCart.laberItem a.new_variant {
   height:42px;
 }
 .new_variant {
   text-transform: uppercase;
   font-size: 11px;
 }
 </style>
 <style>
 .new_variant:hover{
   color:#fff;
 }
 .new_variant{
   float:right;
   margin-top:20px;
 }
 </style>
 <div class="item">
<article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">
  <div class="laberProduct-container">
	  <div class="laberProduct-image">
		{block name='product_thumbnail'}
        {if $product.cover}
          <a href="{$product.url}" class="thumbnail product-thumbnail">
            <span class="cover_image">
				<img
				  src = "{$product.cover.bySize.home_default.url}"
				  alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
				  data-full-size-image-url = "{$product.cover.large.url}"
				  width="{$product.cover.medium.width}" height="{$product.cover.medium.height}"
				  style="height: auto;"
				/>
			</span>
			{if isset($product.images[1])}
			<span class="hover_image">
				<img
					src = "{$product.images[1].bySize.home_default.url}"
					alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
					data-full-size-image-url = "{$product.images[1].bySize.home_default.url}"
					width="{$product.cover.medium.width}" height="{$product.cover.medium.height}"
					style="height: auto;"
				/>
			</span>
			{/if}
          </a>
        {else}
          <a href="{$product.url}" class="thumbnail product-thumbnail">
            <img
              src = "{$urls.no_picture_image.bySize.home_default.url}"
            />
          </a>
        {/if}
      {/block}
		{block name='product_flags'}
			{if $product.flags}
				<ul class="laberProduct-flags">
				{foreach from=$product.flags item=flag}
					<li class="laber-flag laber-{$flag.type}"><span>{$flag.label}</span></li>
				{/foreach}
				</ul>
			{/if}
		{/block}
		{block name='product_variants'}
			{if $product.main_variants}
				{include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
			{/if}
		{/block}
		<div class="laberActions">
			<div class="laberActions-i">
				<div class="laberItem">
					{hook h='Buttoncompare' product=$product}
				</div>
				<div class="laberItem">
					{hook h='displayProductListFunctionalButtons' product=$product}
				</div>
				<div class="laberCart laberItem">
				<form action="{$urls.pages.cart}" method="post">
					<input type="hidden" name="token" value="{$static_token}">
					<input type="hidden" value="{$product.id_product}" name="id_product">
					<button title="Dodaj do koszyka" data-button-action="add-to-cart" class="laberBottom
					{if !$product.add_to_cart_url}
					  disabled
					{/if}
					"
					{if !$product.add_to_cart_url}
						disabled
					{/if}
					>
						<i class="icon-shopping-bag"></i>
						<span>Dodaj do koszyka</span>
					</button>
				</form>
				</div>
			</div>
		</div>


	</div>
  {assign var="attribute" value=$product.attributes|reset}
    <div class="laber-product-description">
		{block name='product_name'}
			<h2 class="productName" itemprop="name"><a href="{$product.url}">{$product.name} {if $attribute.name}({$attribute.name}){/if}</a></h2>
		{/block}
		<div class="laberLeft pull-left">
			{block name='product_price_and_shipping'}
				{if $product.show_price}
				  <div class="laber-product-price-and-shipping">
					<span itemprop="price" class="price">{$product.price}</span>

					{if $product.has_discount}
					  {hook h='displayProductPriceBlock' product=$product type="old_price"}
					  <span class="regular-price">{$product.regular_price}</span>
					{/if}

					{hook h='displayProductPriceBlock' product=$product type="before_price"}

					{hook h='displayProductPriceBlock' product=$product type='unit_price'}

					{hook h='displayProductPriceBlock' product=$product type='weight'}
				  </div>
				{/if}
			{/block}
		</div>
		<div class="laberRight pull-right">
			{hook h='displayProductListReviews' product=$product}
		</div>
    </div>
	  <div class="laberCart laberItem">
		  <form action="{$urls.pages.cart}" method="post">
			  <input type="hidden" name="token" value="{$static_token}">
			  <input type="hidden" value="{$product.id_product}" name="id_product">
			  <button style="margin-top: 20px;" data-button-action="add-to-cart" class="btn btn-primary laberBottom
					{if !$product.add_to_cart_url}
					  disabled
					{/if}
					"
					  {if !$product.add_to_cart_url}
						  disabled
					  {/if}
			  >
				  <i class="icon-shopping-bag"></i>
				  <span>{l s='Add to cart' d='Shop.Theme.Actions'}</span>
			  </button>
        {if $attribute.name}
          <a class="new_variant" style="background: #abcc35;padding: 12px 25px;display: block;" href="{$product.url}">Zamów inny wariant</a>
        {/if}
		  </form>
	  </div>
  </div>
</article>
</div>
