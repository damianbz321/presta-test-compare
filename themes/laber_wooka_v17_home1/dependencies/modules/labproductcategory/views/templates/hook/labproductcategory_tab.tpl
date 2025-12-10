
{foreach from=$group_cat_result item=group_cat name=group_cat_result}

	{assign var='id' value=$group_cat.id_labgroupcategory}
	{assign var='show_sub' value=$group_cat.show_sub}
	{assign var='use_slider' value=$group_cat.use_slider}
	{assign var='type_display' value=$group_cat.type_display}

{/foreach}
{$number_line = 1}
{$id_lang = Context::getContext()->language->id}
{foreach from=$group_cat_result item=group_cat name=group_cat_result}
{assign var='nbItemsPerLine' value=4}
{assign var='nbItemsPerLineTablet' value=3}
{assign var='nbItemsPerLineMobile' value=2}
{if $group_cat.id_labgroupcategory == $id }
<div class="laberthemes">
<div class="LaberProductCategory laberProductGrid labProductList type-tab clearfix">
	<div class="lab_tab">
		<ul class="laberTab nav nav-tabs clearfix">
			{foreach from=$group_cat_info item=cat_info name=g_cat_info}
				<li class="nav-item">
					<a data-toggle="tab" href="#labProdCat-{$id}{$smarty.foreach.g_cat_info.index|intval}_tab" class="{if $smarty.foreach.g_cat_info.first} active{/if} nav-link">
						{if $cat_info.cat_icon !=''}
						<span class="icon_cat">
							<img src="{$icon_path nofilter}{$cat_info.cat_icon nofilter}" alt=""/>
						</span>
						{/if}
						 <span class="name">{$cat_info.cat_name  nofilter} </span>
					</a>
				</li>
			{/foreach}
		</ul>
	</div>
	<div class="tab-content clearfix ">
		{foreach from=$group_cat_info item=cat_info name=g_cat_info}
		<div id="labProdCat-{$id}{$smarty.foreach.g_cat_info.index|intval}_tab" class="tab-pane {if $smarty.foreach.g_cat_info.first} active{/if}">
			{if $cat_info.show_img == 1 && isset($cat_info.id_image) && $cat_info.id_image > 0}
			<div class="cat-img">
				<a href="{$link->getCategoryLink($cat_info.id_cat, $cat_info.link_rewrite)  nofilter}" title="{$cat_info.cat_name  nofilter}">
					<!-- <img src="{$link->getCatImageLink($cat_info.link_rewrite, $cat_info.id_image, 'medium_default')  nofilter}"/> -->
					<img src="../img/c/{$cat_info.id_image}.jpg"/>
				</a>
			</div>
			{/if}
			{if $cat_info.cat_desc}
				<div class="cat_desc">
					<p>{$cat_info.cat_desc nofilter}</p>
				</div>
			{/if}
			{if $show_sub}
				<ul class="tab_cates">
					{foreach from = $cat_info.sub_cat item=sub_cat name=sub_cat_info}
						<li><a href="{$link->getCategoryLink($sub_cat.id_category, $sub_cat.link_rewrite)  nofilter}" title="{$sub_cat.name  nofilter}">{$sub_cat.name  nofilter}</a></li>
					{/foreach}
				</ul>
			{/if}

			<div class="labContent">
				<div class="product_list row">
					<div class="owlProductCate-{$id}{$cat_info.id_cat|intval} ">
						{if isset($cat_info.product_list) && count($cat_info.product_list) > 0}
							{foreach from=$cat_info.product_list item=product name=product_list}
								{if $use_slider != 1}
									<div class="item-inner col-lg-3 col-md-4 col-sm-6 col-xs-12
									{if $smarty.foreach.product_list.iteration%$nbItemsPerLine == 0} last-in-line
									{elseif $smarty.foreach.product_list.iteration%$nbItemsPerLine == 1} first-in-line{/if}
									{if $smarty.foreach.product_list.iteration%$nbItemsPerLineTablet == 0} last-item-of-tablet-line
									{elseif $smarty.foreach.product_list.iteration%$nbItemsPerLineTablet == 1} first-item-of-tablet-line{/if}
									{if $smarty.foreach.product_list.iteration%$nbItemsPerLineMobile == 0} last-item-of-mobile-line
									{elseif $smarty.foreach.product_list.iteration%$nbItemsPerLineMobile == 1} first-item-of-mobile-line{/if}
									">
								{/if}
								{if $use_slider == 1}
									{if $smarty.foreach.product_list.iteration % $number_line == 1 || $number_line == 1}
										<div class="item-inner ajax_block_product">
									{/if}
								{/if}

								{block name='product'}
									{include file="catalog/_partials/miniatures/product.tpl" product=$product}
								{/block}
								{if $use_slider != 1}
									</div>
								{/if}
								{if $use_slider == 1}
									{if $smarty.foreach.product_list.iteration % $number_line == 0 ||$smarty.foreach.product_list.last || $number_line == 1}
									</div>
									{/if}
								{/if}
							{/foreach}
							{if $use_slider != 1}
								<div class="viewAll clearfix"><a href="{$link->getCategoryLink($cat_info.id_cat, $cat_info.link_rewrite)  nofilter}" title="{$cat_info.cat_name  nofilter}">{l s='View all products ' d='Shop.Theme.Laberthemes'} {$cat_info.cat_name  nofilter}</a></div>
							{/if}
							
						{else}
							<div class="item product-box ajax_block_product">
								<p class="alert alert-warning">{l s='No product at this time' d='Shop.Theme.Laberthemes'}</p>
							</div>
						{/if}
					</div>
					{if count($cat_info.manufacture)>0}
					<div class="manu-list">
						<ul>
							{foreach from=$cat_info.manufacture item=manu_item name=manufacture}
								<li><a href="#">{$manu_item->name  nofilter}</a></li>
							{/foreach}
						</ul>
					</div>
					{/if}

				</div>
				{if $use_slider == 1}
					<div class="owl-buttons">
						<div class="owl-prev prev-{$id}{$cat_info.id_cat|intval}-tab"><i class="fa fa-angle-left"></i></div>
						<div class="owl-next next-{$id}{$cat_info.id_cat|intval}-tab"><i class="fa fa-angle-right"></i></div>
					</div>
				{/if}
			</div>
			{if $use_slider == 1}
				<script type="text/javascript">
					$(document).ready(function() {
						var owl = $(".owlProductCate-{$id}{$cat_info.id_cat|intval}");
						owl.owlCarousel({
							items : 4,
							itemsDesktop : [1199,3],
							itemsDesktopSmall : [991,3],
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
						$(".next-{$id}{$cat_info.id_cat|intval}-tab").click(function(){
						owl.trigger('owl.next');
						})
						$(".prev-{$id}{$cat_info.id_cat|intval}-tab").click(function(){
						owl.trigger('owl.prev');
						})
					});
				</script>
			{/if}
		</div>
	{/foreach}
	</div>
</div>
</div>
{/if}
{/foreach}
