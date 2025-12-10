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
<div class="row">
{assign var='count' value= 12/$product_groups|count}
		{foreach from = $product_groups item = product_group name = product_group}
			<div class="col-lg-{$count} col-md-6 col-sm-6 col-xs-12">
				<div class="laberProductColumn laberColumn clearfix">
					<div class="{$product_group.class|escape:'html':'UTF-8'} column">
						<div class="title_block">
							<h3>
								<span class="strong">
									{if $product_group.class|escape:'html':'UTF-8' =='Lab-new-prod'}
										{l s='New Products' d='Shop.Theme.Laberthemes'} 
									{else if $product_group.class|escape:'html':'UTF-8' =='Lab-featured-prod'}
										{l s='Featured Products' d='Shop.Theme.Laberthemes'} 
									{else if $product_group.class|escape:'html':'UTF-8' =='Lab-bestseller-prod'} 
										{l s='Bestseller' d='Shop.Theme.Laberthemes'} 
									{else if $product_group.class|escape:'html':'UTF-8' =='Lab-Specials-prod'}
										{l s='Specials' d='Shop.Theme.Laberthemes'} 
									{/if}
								</span>
							</h3>
						</div>
						{if isset($product_group.product_list) && count($product_group.product_list) > 0}
						<div class="labProductFilter row">
							<div class="owlProductFilter-{$product_group.class|escape:'html':'UTF-8'}-column laberColumn">
								{foreach from=$product_group.product_list item=product name=product_list}
									{if $smarty.foreach.product_list.iteration % $num_row == 1 || $num_row == 1}
										<div class="item-inner ajax_block_product ">
									{/if}
										
											{block name='product'}
												{include file="catalog/_partials/miniatures/productColumn.tpl" product=$product}
											{/block}
										
									{if $smarty.foreach.product_list.iteration % $num_row == 0 ||$smarty.foreach.product_list.last|| $num_row == 1}
										</div>
									{/if}
								{/foreach}
							</div>
						</div>
						{*{if $product_group.class|escape:'html':'UTF-8' =='Lab-new-prod'}
							<a class="all-product-link" href="{$allNewProductsLink}">
								{l s='See all new products' d='Shop.Theme.Laberthemes'} <i class="fa fa-caret-right"></i>
							</a>
						{else if $product_group.class|escape:'html':'UTF-8' =='Lab-featured-prod'}
							<a class="all-product-link" href="{$allProductsLink}">
								{l s='See all Featured' d='Shop.Theme.Laberthemes'} <i class="fa fa-caret-right"></i>
							</a>
						{else if $product_group.class|escape:'html':'UTF-8' =='Lab-bestseller-prod'} 
							<a class="all-product-link" href="{$allBestsalesLink}">
								{l s='See all bestseller' d='Shop.Theme.Laberthemes'} <i class="fa fa-caret-right"></i>
							</a>
						{else if $product_group.class|escape:'html':'UTF-8' =='Lab-Specials-prod'}
							<a class="all-product-link" href="{$allPricesdropLink}">
								{l s='See all Specials' d='Shop.Theme.Laberthemes'} <i class="fa fa-caret-right"></i>
							</a>	
						{/if}*}
						{else}
							<div class="item product-box ajax_block_product">
								<p class="alert alert-warning">{l s='No product at this time' d='Shop.Theme.Laberthemes'}</p>
							</div>	
						{/if}
						
						<div class="owl-buttons">
							<p class="owl-prev prev-{$product_group.class|escape:'html':'UTF-8'}-column"><i class="icon-chevron-left icon"></i></p>
							<p class="owl-next next-{$product_group.class|escape:'html':'UTF-8'}-column"><i class="icon-chevron-right icon"></i></p>
						</div>
						{if $use_slider == 1}
							<script type="text/javascript">
							$(document).ready(function() {
								var owl = $(".owlProductFilter-{$product_group.class|escape:'html':'UTF-8'}-column");
								owl.owlCarousel({
									items : {$num_column},
									itemsDesktop : [1199,1],
									itemsDesktopSmall : [991,1],
									itemsTablet: [767,1],
									itemsMobile : [480,1],
									slideSpeed : 2000,
									paginationSpeed : 2000,
									rewindSpeed : 2000,
									autoPlay :  false,	
									stopOnHover: false,
									pagination : false,
									addClassActive : true,
								});
								$(".next-{$product_group.class|escape:'html':'UTF-8'}-column").click(function(){
									owl.trigger('owl.next');
								})
								$(".prev-{$product_group.class|escape:'html':'UTF-8'}-column").click(function(){
									owl.trigger('owl.prev');
								})
							});
							</script>
						{/if}
					</div>
				</div>
			</div>
		{/foreach}		
</div>