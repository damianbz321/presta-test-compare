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
<div class="item product-box  ajax_block_product js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">
  <div class="laberProduct-container">
	  <div class="laberProduct-image">
		{block name='product_thumbnail'}
		  <a href="{$product.url}" class="thumbnail product-thumbnail">
			<img
			  src = "{$product.cover.bySize.medium_default.url}"
			  alt = "{$product.cover.legend}"
			  data-full-size-image-url = "{$product.cover.large.url}"
			>
		  </a>
		{/block}
		{if isset($product.new) && $product.new == 1}
				<span class="laberNew-label">{l s='New' d='Shop.Theme.Actions'}</span>
			{/if}
		{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
				<span class="laberSale-label">{l s='Sale' d='Shop.Theme.Actions'}</span>
			{/if}
		{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
			{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
				<span class="reduction_percent_display">
					
					{if $product.specific_prices && $product.specific_prices.reduction_type == 'percentage'}
					-{$product.specific_prices.reduction|escape:'quotes':'UTF-8' * 100}%
					{else}
					-{$product.price_without_reduction-$product.price|floatval}
					{/if}
					
				</span>
			{/if}
		{/if}
		<div class="laberActions">
			<div class="laberItem">		
				<a href="{$product.url}" class="Details">
					<i class="fa fa-link"></i><span>{l s='Details' d='Shop.Theme.Actions'}</span>
				</a>
			</div>
			<div class="laberQuick laberItem">
			  <a href="#" class="quick-view" data-link-action="quickview">
				<i class="fa fa-search"></i><span>{l s='Quickview' d='Shop.Theme.Actions'}</span>
			  </a>
			</div>
			<div class="laberCart laberItem">
				{if ($product.allow_oosp || $product.quantity > 0)}
					<form action="{$urls.pages.cart}" method="post">
					<input type="hidden" name="token" value="{$static_token}">
					<input type="hidden" value="{$product.id_product}" name="id_product">
					<button data-button-action="add-to-cart" class="laberBottom" >
						<i class="fa fa-shopping-cart" aria-hidden="true"></i><span>{l s='Add to cart' d='Shop.Theme.Actions'}</span>
					</button>
					</form>
				{else}
					<form action="{$urls.pages.cart}" method="post">
					<input type="hidden" name="token" value="{$static_token}">
					<input type="hidden" value="{$product.id_product}" name="id_product">
					<button data-button-action="add-to-cart" class="laberBottom disabled" disabled >
						<i class="fa fa-times" aria-hidden="true"></i><span>{l s='Out of stock' d='Shop.Theme.Actions'}</span>
					</button>
					</form>
				{/if}
			</div>
		</div>
		{block name='product_variants'}
			{if $product.main_variants}
				{include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
			{/if}
		{/block}
	</div>
    <div class="laber-product-description">
      {block name='product_name'}
        <h2 class="h2 productName" itemprop="name"><a href="{$product.url}">{$product.name|truncate:25:'...'}</a></h2>
      {/block}

      {block name='product_price_and_shipping'}
        {if $product.show_price}
          <div class="laber-product-price-and-shipping">
			<span itemprop="price" class="price">{$product.price}</span>
			{if $product.has_discount}
              {hook h='displayProductPriceBlock' product=$product type="old_price"}

              <span class="old-price regular-price">{$product.regular_price}</span>
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
