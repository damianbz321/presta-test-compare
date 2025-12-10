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
<div class="item">
<article class="product-miniature js-product-miniature newtest123" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">
  <div class="laberProduct-container">
	  <div class="row">
		  <div class="laberProduct-image col-md-5 col-xs-5">
			{block name='product_thumbnail'}
			{if $product.cover}
			  <a href="{$product.url}" class="thumbnail product-thumbnail">
				<img
				  src = "{$product.cover.bySize.home_default.url}"
				  alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
				  data-full-size-image-url = "{$product.cover.large.url}"
				/>
			  </a>
			{else}
			  <a href="{$product.url}" class="thumbnail product-thumbnail">
				<img
				  src = "{$urls.no_picture_image.bySize.home_default.url}"
				/>
			  </a>
			{/if}
		  {/block}
		</div>
    {assign var="attribute" value=$product.attributes|reset}
		<div class="laber-product-description col-md-7 col-xs-7">
		  {block name='product_name'}
			<h2 class="productName" itemprop="name"><a href="{$product.url}" title="{$product.name}">{$product.name} {if $attribute.name}({$attribute.name}){/if}</a></h2>
		  {/block}
		  {hook h='displayProductListReviews' product=$product}
		  {block name='product_price_and_shipping'}
			{if $product.show_price}
			  <div class="laber-product-price-and-shipping">
				<span itemprop="price" class="price">{$product.price}</span>
				{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
					{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
						{if $product.discount_type === 'percentage'}
						  <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
						{elseif $product.discount_type === 'amount'}
						  <span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
						{/if}
					{/if}
				{/if}
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

	  </div>
  </div>
</article>
</div>
