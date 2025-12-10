{*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="item product-box  ajax_block_product js-product-miniature" data-id-product="{$product.id_product|escape:'quotes':'UTF-8'}" data-id-product-attribute="{$product.id_product_attribute|escape:'quotes':'UTF-8'}">
		<div class="laberProduct-container ">	
		<div class="row no-margin">	
					<div class="laberProduct-image pull-left">
						{block name='product_thumbnail'}
						  <a href="{$product.url}" class="thumbnail product-thumbnail">
							<img
							  src = "{$product.cover.bySize.medium_default.url}"
							  alt = "{$product.cover.legend}"
							  data-full-size-image-url = "{$product.cover.large.url}"
							>
						  </a>
						{/block}
						
						<a href="javascript:void(0);" class="quick-view" data-toggle="tooltip" data-placement="top" data-link-action="quickview" title="{l s='Quick view' d='Modules.labproductcategory'}">
							<i class="fa fa-search" aria-hidden="true"></i>	
						</a>
						{if isset($product.new) && $product.new == 1}
							<span class="laberNew-label">{l s='New' d='Shop.Theme.Actions'}</span>
						{/if}
						{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
							<span class="laberSale-label">{l s='sale' d='Shop.Theme.Actions'}</span>
						{/if}
					</div>
					
					<div class="laber-product-description">
						{hook h='displayProductListReviews' product=$product}
					{block name='product_name'}
						<h2 class="h2 productName" itemprop="name"><a href="{$product.url}">{$product.name|truncate:25:'...'}</a></h2>
					  {/block}

					  {block name='product_price_and_shipping'}
						{if $product.show_price}
						  <div class="laber-product-price-and-shipping">
							<span itemprop="price" class="price">{$product.price}</span>
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
						
						<div class="laberActions">
							<div class="laberCart pull-left">
							{if !$product.add_to_cart_url}
								<a href="{$product.url}" class="laberDetail">{l s='Detail' d='Shop.Theme.Actions'}</a>
							{else}
								<form action="{$urls.pages.cart}" method="post">
								<input type="hidden" name="token" value="{$static_token}">
								<input type="hidden" value="{$product.id_product}" name="id_product">
								<button data-button-action="add-to-cart" class="laberBottom" >
									<span>{l s='Add to cart' d='Shop.Theme.Actions'}</span>
								</button>
								</form>
							{/if}
							</div>	
							<div class="laberItem pull-left">
								{hook h='displayProductListFunctionalButtons' product=$product}
							</div>	
							<div class="laberItem LaberButtoncompare pull-left">		
								{hook h='Buttoncompare' product=$product} 
							</div>							
						</div>						
					</div>
					
				
		</div>	
		</div>	
</div>
