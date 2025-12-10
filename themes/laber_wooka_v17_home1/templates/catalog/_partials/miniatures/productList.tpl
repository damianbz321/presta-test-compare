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
<!-- <pre>
{$product.additional_shipping_cost|var_dump}
</pre> -->
<div class="item-inner clearfix">
<article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">
  <div class="laberProduct-container item">
	  <div class="row">
		  <div class="col-lg-4 col-md-6 col-sm-12">
			  <div class="laberProduct-image">
				{block name='product_thumbnail'}
				{if $product.cover}
				  <a href="{$product.url}" class="thumbnail product-thumbnail">
					<span class="cover_image">
						<img
						  src = "{$product.cover.bySize.large_default.url}"
						  alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
						  data-full-size-image-url = "{$product.cover.large_default.url}"
						/>
					</span>
					{if isset($product.images[1])}
					<span class="hover_image">
						<img
							src = "{$product.images[1].bySize.large_default.url}"
							alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
							data-full-size-image-url = "{$product.images[1].bySize.large_default.url}"
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
					<ul class="laberProduct-flags">
					{foreach from=$product.flags item=flag}
						<li class="laber-flag laber-{$flag.type}"><span>{$flag.label}</span></li>
					{/foreach}
					</ul>
				{/block}
				</div>
			</div>
		<div class="col-lg-8 col-md-6 col-sm-12">
      {assign var="attribute" value=$product.attributes|reset}
			<div class="laber-product-description">
			 {block name='product_name'}
				<h2 class="laber-product-title" itemprop="name"><a href="{$product.url}">{$product.name} {if $attribute.name}({$attribute.name}){/if}</a></h2>
			  {/block}
			  {hook h='displayProductListReviews' product=$product}
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
			  <div class="description_short">
				{$product.description_short|escape:'quotes':'UTF-8' nofilter}
			  </div>
			  {block name='product_variants'}
				{if $product.main_variants}
				  {include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
				{/if}
			  {/block}

			</div>
			<div class="laberProductRight">



					{block name='product_availability'}
						<div class="LaberProduct-availability">
							<span class="product-availability">
							  {if $product.show_availability && $product.availability_message}
								<span class="title">
								{l s='Availability:' d='Shop.Theme.Actions'}
								</span>
								{if $product.availability == 'available'}
								  <i class="material-icons product-available">&#xE5CA;</i>
								{elseif $product.availability == 'last_remaining_items'}
								  <i class="material-icons product-last-items">&#xE002;</i>
								{else}
								  <i class="material-icons product-unavailable">&#xE14B;</i>
								{/if}
								{$product.availability_message}
							  {/if}
							</span>
						</div>
					  {/block}
					<div class="actions clearfix">
						<div class="laberCart">
							<form action="{$urls.pages.cart}" method="post">
								<input type="hidden" name="token" value="{$static_token}">
								<input type="hidden" value="{$product.id_product}" name="id_product">
								<button data-button-action="add-to-cart" class="laberBottom
								{if !$product.add_to_cart_url}
								  disabled
								{/if}
								"
								{if !$product.add_to_cart_url}
									disabled
								{/if}
								>
									<span>{l s='Add to cart' d='Shop.Theme.Actions'}</span>
								</button>
                {if $attribute.name}
                  <a class="new_variant" style="background: #abcc35;margin-top: 5px;padding: 12px 5px;text-align: center;display: block;" href="{$product.url}">Zamów inny wariant</a>
                {/if}
							</form>
						</div>
						<div class="laberItem-center">
							<div class="laberItem pull-left">
								{hook h='displayProductListFunctionalButtons' product=$product}
							</div>

							<div class="laberItem pull-left">
								{hook h='Buttoncompare' product=$product}
							</div>
						</div>
					</div>
				</div>
		</div>
	  </div>
  </div>
</article>
</div>
