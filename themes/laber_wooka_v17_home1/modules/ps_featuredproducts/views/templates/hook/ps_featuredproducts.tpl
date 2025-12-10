<section class="laberProductGrid laberthemes laberGrid clearfix">
  <div class="title_block">
  <h3>
	<span class="strong">BESTSELLERY</span>
  </h3>
  </div>
	<div class="product_list">
		<div class="laberFeatured">
		{foreach from=$products item="product" name="product_list"}
			<div class="item-inner ajax_block_product">
				{include file="catalog/_partials/miniatures/product.tpl" product=$product}
			</div>
		{/foreach}
		</div>
	</div>
  <div class="owl-buttons">
		<div class="owl-prev prev-laberFeatured"><i class="icon-chevron-left icon"></i></div>
		<div class="owl-next next-laberFeatured"><i class="icon-chevron-right icon"></i></div>
	</div>
  {*<a class="all-product-link pull-xs-left pull-md-right h4" href="{$allProductsLink}">
    {l s='All products' d='Shop.Theme.Catalog'}<i class="material-icons">&#xE315;</i>
  </a>*}
</section>
<script type="text/javascript">
$(document).ready(function() {
	var owl = $(".laberFeatured");
	owl.owlCarousel({
		items : 3,
		itemsDesktop : [1199,3],
		itemsDesktopSmall : [991,3],
		itemsTablet: [767,2],
		itemsMobile : [480,1],
		rewindNav : false,
		autoPlay :  false,
		stopOnHover: false,
		pagination : false,
	});
	$(".next-laberFeatured").click(function(){
		owl.trigger('owl.next');
	})
	$(".prev-laberFeatured").click(function(){
		owl.trigger('owl.prev');
	})
});
</script>