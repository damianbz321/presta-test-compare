{*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="category_products_custom">
<section class="padding-0-15 margin-bottom-60">
	<div class="laberthemes">
	<div class="Categoryproducts laberProductGrid">
		<div class="title_block">
			<h3>
				
				<span>
					{if $products|@count == 1}
					{l s='%s INNYCH PRODUKTÓW Z TEJ SAMEJ KATEGORII' sprintf=[$products|@count] d='Modules.Categoryproducts.Shop'}
					{else}
					{l s='%s INNYCH PRODUKTÓW Z TEJ SAMEJ KATEGORII' sprintf=[$products|@count] d='Modules.Categoryproducts.Shop'}
					{/if}
				</span>
			</h3>
		</div>
		<div class="laberCate product_list">
			<div class="row ">
				<div class="laberCategoryproducts">
					{foreach from=$products item="product"}
						<div class="item-inner  ajax_block_product">
							{include file="catalog/_partials/miniatures/product.tpl" product=$product}
						</div>
					{/foreach}
				</div>
			</div>
		</div>
		<div class="owl-buttons">
			<p class="owl-prev prevCategoryproducts"><i class="icon-chevron-left"></i></p>
			<p class="owl-next nextCategoryproducts"><i class="icon-chevron-right"></i></p>
		</div>
	</div>
	</div>
</section>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		var owl = $(".laberCategoryproducts");
		owl.owlCarousel({
			items : 4,
			itemsDesktop : [1199,3],
			itemsDesktopSmall : [991,2],
			itemsTablet: [767,2],
			itemsMobile : [480,1],
			rewindNav : false,
			autoPlay :  false,
			stopOnHover: false,
			pagination : false,
		});
		$(".nextCategoryproducts").click(function(){
		owl.trigger('owl.next');
		})
		$(".prevCategoryproducts").click(function(){
		owl.trigger('owl.prev');
		})
	});
</script>