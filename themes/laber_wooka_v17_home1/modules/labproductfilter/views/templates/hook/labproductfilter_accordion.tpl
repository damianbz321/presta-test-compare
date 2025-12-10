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

{$id_lang = Context::getContext()->language->id}
{if $use_slider != 1}
	{assign var='nbItemsPerLine' value=4}
	{assign var='nbItemsPerLineTablet' value=3}
	{assign var='nbItemsPerLineMobile' value=2}
{/if}
{foreach from = $product_groups item = product_group name = product_group}
	<div class="laberthemes clearfix padding-0-15">
	<div class="{$product_group.class|escape:'html':'UTF-8'} laberProductFilter laberGrid  laberProductGrid clearfix">
		{*<div class="title_block">
			<h3><span class="strong">
				{if $product_group.class|escape:'html':'UTF-8' =='Lab-new-prod'}
							{l s='New Products' d='Shop.Theme.Laberthemes'}
					{else if $product_group.class|escape:'html':'UTF-8' =='Lab-featured-prod'}
							{l s='Featured Products' d='Shop.Theme.Laberthemes'}
					{else if $product_group.class|escape:'html':'UTF-8' =='Lab-bestseller-prod'} 
							{l s='bestseller' d='Shop.Theme.Laberthemes'}
					{else if $product_group.class|escape:'html':'UTF-8' =='Lab-Specials-prod'}
							{l s='Specials' d='Shop.Theme.Laberthemes'}
					{/if}
			</span></h3>
		</div>*}
		<div class="prod-filter labContent">
			
					{if isset($product_group.product_list) && count($product_group.product_list) > 0}
					<div class="product_list">
						<div class="row">
							<div class="owlProductFilter-{$product_group.class|escape:'html':'UTF-8'} {if $use_slider != 1}no-Slider row{/if}">
								{foreach from=$product_group.product_list item=product name=product_list}
									{if isset($product.id_product)}
										{if $use_slider != 1}
											<div class="item-inner col-lg-3 col-md-3 col-sm-4 col-xs-12  
											{if $smarty.foreach.product_list.iteration%$nbItemsPerLine == 0} last-in-line
											{elseif $smarty.foreach.product_list.iteration%$nbItemsPerLine == 1} first-in-line{/if}
											{if $smarty.foreach.product_list.iteration%$nbItemsPerLineTablet == 0} last-item-of-tablet-line
											{elseif $smarty.foreach.product_list.iteration%$nbItemsPerLineTablet == 1} first-item-of-tablet-line{/if}
											{if $smarty.foreach.product_list.iteration%$nbItemsPerLineMobile == 0} last-item-of-mobile-line
											{elseif $smarty.foreach.product_list.iteration%$nbItemsPerLineMobile == 1} first-item-of-mobile-line{/if}
											">
											
										{else}
											{if $smarty.foreach.product_list.iteration % $num_row == 1 || $num_row == 1}
											<div class="item-inner  ajax_block_product">
											{/if}
										{/if}
											{block name='product'}
												{include file="catalog/_partials/miniatures/product.tpl" product=$product}
											{/block}
										{if $use_slider != 1}
											</div>
										{else}
											{if $smarty.foreach.product_list.iteration % $num_row == 0 || $smarty.foreach.product_list.last || $num_row == 1}
												</div>
											{/if}
										{/if}
									{/if}
								{/foreach}
							</div>
						</div>
					</div>
					{if $use_slider == 1}
					<div class="owl-buttons">
						<div class="owl-prev prev-{$product_group.class|escape:'html':'UTF-8'}"><i class="icon-chevron-left icon"></i></div>
						<div class="owl-next next-{$product_group.class|escape:'html':'UTF-8'}"><i class="icon-chevron-right icon"></i></div>
					</div>
					{/if}
					{else}
						<div class="item product-box ajax_block_product">
							<p class="alert alert-warning">{l s='No product at this time' d='Shop.Theme.Laberthemes'}</p>
						</div>	
					{/if}
			  
			
		</div>
		{if $use_slider == 1}
			<script type="text/javascript">
			$(document).ready(function() {
				var owl = $(".owlProductFilter-{$product_group.class|escape:'html':'UTF-8'}");
				owl.owlCarousel({
					items : {$num_column},
					itemsDesktop : [1199,3],
					itemsDesktopSmall : [991,2],
					itemsTablet: [767,2],
					itemsMobile : [480,1],
					slideSpeed : 2000,
					paginationSpeed : 2000,
					rewindSpeed : 2000,
					autoPlay :  false,	
					stopOnHover: false,
					pagination : false,
					addClassActive : true,
				});
				$(".next-{$product_group.class|escape:'html':'UTF-8'}").click(function(){
				owl.trigger('owl.next');
				})
				$(".prev-{$product_group.class|escape:'html':'UTF-8'}").click(function(){
				owl.trigger('owl.prev');
				})
			});
			</script>
		{/if}
	</div>
	</div>	
{/foreach}

	