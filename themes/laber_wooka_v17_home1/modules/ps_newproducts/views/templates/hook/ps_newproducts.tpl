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
{if $page.page_name == 'index'}
{$number_line = 5}
<section class="laberProductColumn laberColumn clearfix">
  <h3>{l s='New products' d='Shop.Theme.Laberthemes'}</h3>
  <div class="product_list">
	  <div class="row">
		  <div class="laberNewProducts">
			{foreach from=$products item="product" name=product_list}
				{if $smarty.foreach.product_list.iteration % $number_line == 1 || $number_line == 1}
				<div class="item-inner">
				{/if}
					{include file="catalog/_partials/miniatures/productColumn.tpl" product=$product}
				{if $smarty.foreach.product_list.iteration % $number_line == 0 || $smarty.foreach.product_list.last || $number_line == 1}
				</div>
				{/if}
			{/foreach}
		</div>
	</div>
  </div>
	<div class="owl-buttons">
		<div class="owl-prev prev-laberNewProducts"><i class="fa fa-angle-left"></i></div>
		<div class="owl-next next-laberNewProducts"><i class="fa fa-angle-right"></i></div>
	</div>
  {*<a class="allProducts" href="{$allNewProductsLink}">{l s='All new products' d='Shop.Theme.Laberthemes'}</a>*}
</section>
<script type="text/javascript">
$(document).ready(function() {
	var owl = $(".laberNewProducts");
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
	$(".next-laberNewProducts").click(function(){
		owl.trigger('owl.next');
	})
	$(".prev-laberNewProducts").click(function(){
		owl.trigger('owl.prev');
	})
});
</script>
{/if}