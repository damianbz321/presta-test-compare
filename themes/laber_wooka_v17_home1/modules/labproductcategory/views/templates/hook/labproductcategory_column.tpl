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

{$number_line = 3}
{foreach from=$group_cat_result item=group_cat name=group_cat_result}
	{assign var='id' value=$group_cat.id_labgroupcategory}
	{assign var='show_sub' value=$group_cat.show_sub}
	{assign var='use_slider' value=$group_cat.use_slider}
	{assign var='type_display' value=$group_cat.type_display}
{/foreach}
{$id_lang = Context::getContext()->language->id}
	<div class="laberColumnProductCategoty clearfix">
		{foreach from=$group_cat_info item=cat_info name=g_cat_info}
		<div class="laberColumn  lab-prod-cat-{$cat_info.id_cat|intval}">
			<div class="cat-bar">
			  <div class="out-lab-prod">
				{if $cat_info.cat_icon!='' }
					<span class="icon_cat" style="background-color:{$cat_info.cat_color|escape:'html':'UTF-8'}">
					   <img src="{$icon_path|escape:'html':'UTF-8'}{$cat_info.cat_icon|escape:'html':'UTF-8'}" alt=""/>
					</span>
				{/if}
			    <div class="title_block">
					<h3><a href="{$link->getCategoryLink($cat_info.id_cat, $cat_info.link_rewrite)|escape:'html':'UTF-8'}" title="{$cat_info.cat_name|escape:'html':'UTF-8'}"><span>{$cat_info.cat_name|escape:'html':'UTF-8'}</span></a></h3>
				</div>
			  </div>
			</div>
			{if $cat_info.show_img == 1 && isset($cat_info.id_image) && $cat_info.id_image > 0}
			<div class="cat-img">
				<a href="{$link->getCategoryLink($cat_info.id_cat, $cat_info.link_rewrite)|escape:'html':'UTF-8'}" title="{$cat_info.cat_name|escape:'html':'UTF-8'}">
					<img src="{$link->getCatImageLink($cat_info.link_rewrite, $cat_info.id_image, 'medium_default')|escape:'html':'UTF-8'}"/>
				</a>
			</div>
			{/if}
			{if $show_sub}
				<ul class="tab_cates">
					{foreach from = $cat_info.sub_cat item=sub_cat name=sub_cat_info}
						<li><a href="{$link->getCategoryLink($sub_cat.id_category, $sub_cat.link_rewrite)|escape:'html':'UTF-8'}" title="{$sub_cat.name|escape:'html':'UTF-8'}">{$sub_cat.name|escape:'html':'UTF-8'}</a></li>
					{/foreach}
					
				</ul>
			{/if}
			{if $cat_info.cat_banner!='' }
			<div class="cat-banner">
				<a href="{$link->getCategoryLink($cat_info.id_cat, $cat_info.link_rewrite)|escape:'html':'UTF-8'}" title="{$cat_info.cat_name|escape:'html':'UTF-8'}">
					<img src="{$banner_path|escape:'html':'UTF-8'}{$cat_info.cat_banner|escape:'html':'UTF-8'}" alt=""/>
				</a>
			</div>
			{/if}
			<div class="product_list row">
			
				<div class="owlProductCate-{$type_display|escape:'html':'UTF-8'}-{$cat_info.id_cat|intval}{$id}">
					{if isset($cat_info.product_list) && count($cat_info.product_list) > 0}
					{foreach from=$cat_info.product_list item=product name=product_list}
						{if $smarty.foreach.product_list.iteration % $number_line == 1 || $number_line == 1}
						<div class="item-inner ajax_block_product">
						{/if}
							
								{block name='product'}
									{include file="catalog/_partials/miniatures/productColumn.tpl" product=$product}
								{/block}
					
						{if $smarty.foreach.product_list.iteration % $number_line == 0 || $smarty.foreach.product_list.last || $number_line == 1}
						</div>
						{/if}
					{/foreach}
				{else}
					<div class="item product-box ajax_block_product">
						<p class="alert alert-warning">{l s='No product at this time' d='Modules.labproductcategory.Admin'}</p>
					</div>
				{/if}
				</div>
				{if count($cat_info.manufacture)>0}
				<div class="manu-list">
					<ul>
						{foreach from=$cat_info.manufacture item=manu_item name=manufacture}
							<li><a href="#">{$manu_item->name|escape:'html':'UTF-8'}</a></li>
						{/foreach}
					</ul>
				</div>
				{/if}
	
			</div>
			{if $use_slider == 1}
			<div class="owl-buttons2">
				<div class="owl-prev prev-{$type_display|escape:'html':'UTF-8'}-{$cat_info.id_cat|intval}{$id}"><i class="icon-chevron-left icon"></i></div>
				<div class="owl-next next-{$type_display|escape:'html':'UTF-8'}-{$cat_info.id_cat|intval}{$id}"><i class="icon-chevron-right icon"></i></div>
			</div>
			{/if}
			{if $use_slider == 1}
				<script type="text/javascript">
				$(document).ready(function() {
					var owl = $(".owlProductCate-{$type_display|escape:'html':'UTF-8'}-{$cat_info.id_cat|intval}{$id}");
					owl.owlCarousel({
						items : 4,
						itemsDesktop : [1199,2],
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
					$(".next-{$type_display|escape:'html':'UTF-8'}-{$cat_info.id_cat|intval}{$id}").click(function(){
					owl.trigger('owl.next');
					})
					$(".prev-{$type_display|escape:'html':'UTF-8'}-{$cat_info.id_cat|intval}{$id}").click(function(){
					owl.trigger('owl.prev');
					})
				});
				</script>
			{/if}
		</div>
		{/foreach}
	</div>