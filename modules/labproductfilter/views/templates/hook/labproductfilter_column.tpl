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
{assign var='count' value= 12/$product_groups|count}
{foreach from = $product_groups item = product_group name = product_group}
	<div class="{$product_group.class|escape:'html':'UTF-8'} col-xs-12 col-sm-{$count}">
		<h3>{$product_group.title|escape:'html':'UTF-8'}</h3>
		{if isset($product_group.product_list) && count($product_group.product_list) > 0}
		<div class="LabProductFilter product_list">
			<div class="owlProductFilter-{$product_group.class|escape:'html':'UTF-8'}-column">
				{foreach from=$product_group.product_list item=product name=product_list}
					{if $smarty.foreach.product_list.iteration % $num_row == 1 || $num_row == 1}
					<div class="item-inner ajax_block_product">
					{/if}
						<div class="item">
							<div class="product-container media-body">

								{block name='leftcolumn'}
									{include file="./labproductfilter_leftcolumn.tpl" product_groups = $product_hook.product_group}

								{/block}

								{block name='rightcolumn'}
									{include file="./labproductfilter_rightcolumn.tpl" product_groups = $product_hook.product_group}

								{/block}

							</div>
						</div>
					{if $smarty.foreach.product_list.iteration % $num_row == 0 ||$smarty.foreach.product_list.last|| $num_row == 1}
					</div>
					{/if}
				{/foreach}
			</div>
		</div>
		{else}
			<div class="item product-box ajax_block_product">
				<p class="alert alert-warning">{l s='No product at this time' mod='labproductfilter'}</p>
			</div>	
		{/if}
		{if $use_slider == 1}
			<script type="text/javascript">
			$(document).ready(function() {
				var owl = $(".owlProductFilter-{$product_group.class|escape:'html':'UTF-8'}-column");
				owl.owlCarousel({
					items : 1,
					itemsDesktop : [1199,1],
					itemsDesktopSmall : [991,1],
					itemsTablet: [767,1],
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
