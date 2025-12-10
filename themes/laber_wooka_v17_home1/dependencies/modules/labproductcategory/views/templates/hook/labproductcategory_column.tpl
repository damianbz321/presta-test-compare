{**
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{$number_line = 3}
{$id_lang = Context::getContext()->language->id}
<div class="row">
{foreach from=$group_cat_info item=cat_info name=g_cat_info}
<div class="labcolumnCateProducts labcolumn  col-md-4">
<div class="block-content clearfix">
	<div id="lab-prod-cat-{$cat_info.id_cat|intval}">
			<div class="content-title">
				<h3 style="color:{$cat_info.cat_color}">
					{if $cat_info.cat_icon!='' }
						<span class="icon_cat">
						   <img src="{$icon_path|escape:'html':'UTF-8'}{$cat_info.cat_icon|escape:'html':'UTF-8'}" alt=""/>
						</span>
					{/if}
					<a href="{$link->getCategoryLink($cat_info.id_cat, $cat_info.link_rewrite)|escape:'html':'UTF-8'}" title="{$cat_info.cat_name|escape:'html':'UTF-8'}">{$cat_info.cat_name|escape:'html':'UTF-8'}</a>
				</h3>
			</div>
			<div class="cat-banner">
				{if $cat_info.cat_banner!='' }
					<a href="{$link->getCategoryLink($cat_info.id_cat, $cat_info.link_rewrite)|escape:'html':'UTF-8'}" title="{$cat_info.cat_name|escape:'html':'UTF-8'}"><img src="{$banner_path|escape:'html':'UTF-8'}{$cat_info.cat_banner|escape:'html':'UTF-8'}" alt=""/></a>
				{/if}
				
				{if $cat_info.cat_desc!='' }
				<div class="cat-desc">
					{$cat_info.cat_desc|escape:'quotes':'UTF-8' nofilter}
				</div>
				{/if}
				
			</div>
			<div class="laberContentProduct-{$cat_info.id_cat}-{$group_cat.id_labgroupcategory}">
				{if isset($cat_info.product_list) && count($cat_info.product_list) > 0}
				<div class="laber-container" id="laber-{$smarty.foreach.g_cat_info.iteration|escape:'html':'UTF-8'}">	
					<div class="laberProducts-List row">
						<div id="cat-column-{$cat_info.id_cat}" class="product-list-cat">
						{foreach from=$cat_info.product_list item=product name=product_list}
							{if $smarty.foreach.product_list.iteration % $number_line == 1 || $number_line == 1}
								<div class="item-inner ajax_block_product">
							{/if}
								<div class="item">
									<div class="product_list product-list">
										{block name='product'}
											{include file="catalog/_partials/miniatures/productColumn.tpl" product=$product}
										{/block}			
									</div>
								</div>
							{if $smarty.foreach.product_list.iteration % $number_line == 0 || $smarty.foreach.product_list.last || $number_line == 1}
								</div>
							{/if}
						{/foreach}
						</div>
					</div>
				</div>
				<div class="owl-buttons">
					<p class="owl-prev prev-{$cat_info.id_cat}"><i class="fa fa-angle-left"></i></p>
					<p class="owl-next next-{$cat_info.id_cat}"><i class="fa fa-angle-right"></i></p>
				</div>
				
			 {else}
				<div class="item product-box no-product col-sm-12 col-md-12">
					<p class="alert alert-warning">{l s='No product at this category' d='Modules.LABProductCategory'}</p>
				</div>
			 {/if}
			</div><!-- end content product sub cat -->
		
	</div>
</div>

{if $cat_info.show_img == 1 && isset($cat_info.id_image) && $cat_info.id_image > 0}
<div class="cat-img">
	<a href="{$link->getCategoryLink($cat_info.id_cat, $cat_info.link_rewrite)|escape:'html':'UTF-8'}" title="{$cat_info.cat_name|escape:'html':'UTF-8'}">
		<img src="{$link->getCatImageLink($cat_info.link_rewrite, $cat_info.id_image, 'category_default')|escape:'html':'UTF-8'}"/>
	</a>
</div>
{/if}
</div>
{/foreach}

{foreach from=$group_cat_info item=cat_info name=g_cat_info}
{if $group_cat.use_slider == 1}	
	<script type="text/javascript">
	$(document).ready(function() {
		var owl = $("#cat-column-{$cat_info.id_cat}");
		owl.owlCarousel({
			items : 1,
			itemsDesktop : [1199,1],
			itemsDesktopSmall : [991,1],
			itemsTablet: [767,2],
			itemsMobile : [480,1],
			rewindNav : false,
			autoPlay :  false,
			stopOnHover: false,
			pagination : false,
		});
		$(".next-{$cat_info.id_cat}").click(function(){
			owl.trigger('owl.next');
		})
		$(".prev-{$cat_info.id_cat}").click(function(){
			owl.trigger('owl.prev');
		})
	});
	</script>
{/if}
{/foreach}
</div>