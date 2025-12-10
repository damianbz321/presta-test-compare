
{foreach from=$group_cat_result item=group_cat name=group_cat_result}

	{assign var='id' value=$group_cat.id_labgroupcategory}
	{assign var='show_sub' value=$group_cat.show_sub}
	{assign var='use_slider' value=$group_cat.use_slider}
	{assign var='type_display' value=$group_cat.type_display}

{/foreach}
{$number_line = 1}
{$id_lang = Context::getContext()->language->id}
{foreach from=$group_cat_result item=group_cat name=group_cat_result}
{if $group_cat.show_sub}
	{assign var='nbItemsPerLine' value=4}
	{assign var='nbItemsPerLineTablet' value=3}
	{assign var='nbItemsPerLineMobile' value=2}
{/if}
{if $group_cat.id_labgroupcategory == $id }
<div class="laberthemes padding-0-15">
<div class="LaberProductCategory laberProductGrid labProductList type-tab clearfix">
	<div class="lab_tabCate">
		<ul class="tab_cates nav nav-tabs clearfix">
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
			{if $cat_info.cat_banner !=''}
			<div class="cat-banner">
				{if $cat_info.cat_banner!='' }
					<a href="{$link->getCategoryLink($cat_info.id_cat, $cat_info.link_rewrite)  nofilter}" title="{$cat_info.cat_name  nofilter}">
					<img src="{$banner_path  nofilter}{$cat_info.cat_banner  nofilter}" alt=""/>
					</a>
				{/if}
			</div>
			{/if}
			<div class="labContent">
				<div class="product_list">
				<div class="row">
					<div class="owlProductCate-{$id}{$cat_info.id_cat|intval} {if $use_slider != 1}row{/if}">
						{if isset($cat_info.product_list) && count($cat_info.product_list) > 0}
						{foreach from=$cat_info.product_list item=product name=product_list}
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
								{if $smarty.foreach.product_list.iteration % $number_line == 1 || $number_line == 1}
									<div class="item-inner ajax_block_product">
								{/if}
							{/if}

							{block name='product'}

								{include file="catalog/_partials/miniatures/laberProductcategory.tpl" product=$product}

							{/block}

								
							{if $use_slider != 1}
								</div>
							{else}
								{if $smarty.foreach.product_list.iteration % $number_line == 0 ||$smarty.foreach.product_list.last || $number_line == 1}
								</div>
								{/if}
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
								<li><a href="#">{$manu_item->name  nofilter}</a></li>
							{/foreach}
						</ul>
					</div>
					{/if}
				</div>
				</div>
			</div>
			{if $use_slider == 1}
				<div class="owl-buttons">
					<p class="owl-prev prev_{$id}{$cat_info.id_cat|intval}"><i class="icon-chevron-left icon"></i></p>
					<p class="owl-next next_{$id}{$cat_info.id_cat|intval}"><i class="icon-chevron-right icon"></i></p>
				</div>
				<script type="text/javascript">
					$(document).ready(function() {
						var owl = $(".owlProductCate-{$id}{$cat_info.id_cat|intval}");
						owl.owlCarousel({
							items : 3,
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
						$(".next_{$id}{$cat_info.id_cat|intval}").click(function(){
						owl.trigger('owl.next');
						})
						$(".prev_{$id}{$cat_info.id_cat|intval}").click(function(){
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
