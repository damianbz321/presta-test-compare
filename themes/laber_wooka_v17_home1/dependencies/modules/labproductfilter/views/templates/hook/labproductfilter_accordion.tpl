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
{foreach from=$group_prod_fliter item=product_hook name=product_hook}
	{assign var='id' value=$product_hook.id_labproductfilter}
	{assign var='use_slider' value=$product_hook.use_slider}
	{assign var='num_column' value=$product_hook.num_column}
	{assign var='num_row' value=$product_hook.num_row}
{/foreach}

{$id_lang = Context::getContext()->language->id}
{if $use_slider != 1}
	{assign var='nbItemsPerLine' value=4}
	{assign var='nbItemsPerLineTablet' value=3}
	{assign var='nbItemsPerLineMobile' value=2}
{/if}

{foreach from = $product_groups item = product_group name = product_group}
	<div class="{$product_group.class|escape:'html':'UTF-8'} labProductList clearfix">
		<p class="title_block">
			<span>{$product_group.title|escape:'html':'UTF-8'}</span>
		</p>
		<div class="prod-filter">
				{if isset($product_group.product_list) && count($product_group.product_list) > 0}
				<div class="LabProductFilter">
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


								<div class="product">

									{block name='product'}
										{include file="catalog/_partials/miniatures/product.tpl" product=$product}
									{/block}

								</div>
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
				{else}
					<div class="item product-box ajax_block_product">
						<p class="alert alert-warning">{l s='No product at this time' mod='labproductfilter'}</p>
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
					autoHeight : true,
					navigation : true,
					navigationText : ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
					rewindNav : false,
					autoPlay :  false,
					stopOnHover: false,
					pagination : false,
				});
			});
			</script>
		{/if}
	</div>
	
{/foreach}

	