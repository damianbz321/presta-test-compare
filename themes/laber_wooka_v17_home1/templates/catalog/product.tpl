{**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file=$layout}

{block name='head_seo' prepend}
  <link rel="canonical" href="{$product.canonical_url}">
{/block}

{block name='head' append}
  <meta property="og:type" content="product">
  <meta property="og:url" content="{$urls.current_url}">
  <meta property="og:title" content="{$page.meta.title}">
  <meta property="og:site_name" content="{$shop.name}">
  <meta property="og:description" content="{$page.meta.description}">
  <meta property="og:image" content="{$product.cover.large.url}">
  <meta property="product:pretax_price:amount" content="{$product.price_tax_exc}">
  <meta property="product:pretax_price:currency" content="{$currency.iso_code}">
  <meta property="product:price:amount" content="{$product.price_amount}">
  <meta property="product:price:currency" content="{$currency.iso_code}">
  {if isset($product.weight) && ($product.weight != 0)}
  <meta property="product:weight:value" content="{$product.weight}">
  <meta property="product:weight:units" content="{$product.weight_unit}">
  {/if}
{/block}

{block name='content'}

  <section id="main">
    <meta itemprop="url" content="{$product.url}">

    <div class="laberProduct margin-bottom-60 padding-0-15">
    <div class="row">
      <div class="col-md-6">
        {block name='page_content_container'}
          <section class="page-content" id="content">
            {block name='page_content'}
              <!-- {block name='product_flags'}
                <ul class="product-flags">
                  {foreach from=$product.flags item=flag}
                    <li class="product-flag {$flag.type}">{$flag.label}</li>
                  {/foreach}
                </ul>
              {/block} -->

              {block name='product_cover_tumbnails'}
                {include file='catalog/_partials/product-cover-thumbnails.tpl'}
              {/block}
              <div class="scroll-box-arrows">
				<i class="icon-chevron-left left" aria-hidden="true"></i>
				<i class="icon-chevron-right right" aria-hidden="true"></i>
              </div>

            {/block}
          </section>
        {/block}
        </div>
        <div class="col-md-6">
			{hook h='displayProductNextPrev'}
          {block name='page_header_container'}
            {block name='page_header'}
              <h1 class="h1" itemprop="name">{block name='page_title'}{$product.name}{/block}</h1>
            {/block}
          {/block}
			{block name='product_description_short'}
              <div class="product-description-short" id="product-description-short-{$product.id}" itemprop="description">{$product.description_short nofilter}</div>
            {/block}
			{hook h='displayProductListReviews' product=$product}
          {block name='product_prices'}
            {include file='catalog/_partials/product-prices.tpl'}
          {/block}

			{block name='product_details'}
				{include file='catalog/_partials/product-details.tpl'}
			{/block}

          <div class="product-information">
            <pre style="display:none;">{$groups|print_r}</pre>
            {if $product.is_customizable && count($product.customizations.fields)}
              {block name='product_customization'}
                {include file="catalog/_partials/product-customization.tpl" customizations=$product.customizations}
              {/block}
            {/if}

            <div class="product-actions">
              {block name='product_buy'}
                <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                  <input type="hidden" name="token" value="{$static_token}">
                  <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                  <input type="hidden" name="id_customization" value="{$product.id_customization}" id="product_customization_id">

                  {block name='product_variants'}
                    {include file='catalog/_partials/product-variants.tpl'}
                  {/block}

                  {block name='product_pack'}
                    {if $packItems}
                      <section class="product-pack">
                        <h3 class="h4">{l s='This pack contains' d='Shop.Theme.Catalog'}</h3>
                        {foreach from=$packItems item="product_pack"}
                          {block name='product_miniature'}
                            {include file='catalog/_partials/miniatures/pack-product.tpl' product=$product_pack}
                          {/block}
                        {/foreach}
                    </section>
                    {/if}
                  {/block}

                  {block name='product_discounts'}
                    {include file='catalog/_partials/product-discounts.tpl'}
                  {/block}

                  {block name='product_add_to_cart'}
                    {include file='catalog/_partials/product-add-to-cart.tpl'}
                  {/block}

                  {hook h='displayProductButtons' product=$product}

                  {block name='product_refresh'}
                    <input class="product-refresh ps-hidden-by-js" name="refresh" type="submit" value="{l s='Refresh' d='Shop.Theme.Actions'}">
                  {/block}
                </form>
              {/block}

            </div>

            {hook h='displayReassurance'}

            {hook h='displayProductAdditionalInfo'}


        </div>
      </div>
    </div>
    </div>
	<div class="row tabs laberTabs margin-bottom-60">
		<div class="nav nav-tabs">
			<ul>
				{if $product.description}
				<li class="nav-item">
					<a class="nav-link{if $product.description} active{/if}" data-toggle="tab" href="#description">{l s='Description' d='Shop.Theme.Catalog'}</a>
				</li>
				{/if}
				{*<li class="nav-item">
					<a class="nav-link{if !$product.description} active{/if}" data-toggle="tab" href="#product-details">{l s='Product Details' d='Shop.Theme.Catalog'}</a>
				</li>*}
				{if $product.attachments}
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#attachments">{l s='Attachments' d='Shop.Theme.Catalog'}</a>
				</li>
				{/if}

				       {foreach from=$product.extraContent item=extra key=extraKey}
                    <li class="nav-item">
                      <a
                        class="nav-link"
                        data-toggle="tab"
                        href="#extra-{$extraKey}"
                        role="tab"
                        aria-controls="extra-{$extraKey}">{$extra.title}</a>
                    </li>
                  {/foreach}

                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#specyfikacja">Specyfikacja</a>
				</li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#komentarze">OPINIE</a>
				</li>
                                {hook h='tabReviews'}
			</ul>
		</div>
	  <div class="tab-content col-sm-6 col-xs-12" id="tab-content" style="padding: 0;">
	   <div class="tab-pane fade in{if $product.description} active{/if}" id="description">
		 {block name='product_description'}
		   <div class="product-description">{$product.description nofilter}</div>
		 {/block}
                 {hook h='displayModularDESC' pro=$product}
	   </div>


	   {block name='product_attachments'}
		 {if $product.attachments}
		  <div class="tab-pane fade in" id="attachments">
			 <section class="product-attachments">
			   <h3 class="h5 text-uppercase">{l s='Download' d='Shop.Theme.Actions'}</h3>
			   {foreach from=$product.attachments item=attachment}
				 <div class="attachment">
				   <h4><a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">{$attachment.name}</a></h4>
				   <p>{$attachment.description}</p
				   <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
					 {l s='Download' d='Shop.Theme.Actions'} ({$attachment.file_size_formatted})
				   </a>
				 </div>
			   {/foreach}
			 </section>
		   </div>
		 {/if}
	   {/block}
	   {foreach from=$product.extraContent item=extra key=extraKey}
	   <div class="tab-pane fade in {$extra.attr.class}" id="extra-{$extraKey}" {foreach $extra.attr as $key => $val} {$key}="{$val}"{/foreach}>
		   {$extra.content nofilter}
	   </div>
	   {/foreach}

           <div class="tab-pane fade in" id="specyfikacja">
			 <section class="product-specyfikacja">
			  {$product.custom_field_lang_wysiwyg|unescape:'html' nofilter}
			 </section>
		   </div>
            <div class="tab-pane fade in" id="komentarze">
                <section class="product-komentarze">
                    {hook h='productFooter' product=$product}
                </section>
            </div>
	</div>
  </div>
    {block name='product_accessories'}
      {if $accessories}
        <section class="padding-0-15 margin-bottom-60">
        <div class="laberthemes">
        <div class="product-accessories clearfix laberProductGrid laberGrid">
			<div class="title_block">
				<h3><span>{l s='You might also like Products' d='Shop.Theme.Catalog'}</span></h3>
			</div>
          <div class="laberAcce product_list">
          <div class="row">
			  <div class="laberAccessories">
				{foreach from=$accessories item="product_accessory"}
				  {block name='product_miniature'}
					<div class="item-inner  ajax_block_product">
						{include file='catalog/_partials/miniatures/product.tpl' product=$product_accessory}
					</div>
				  {/block}
				{/foreach}
			  </div>
          </div>
          </div>
		  <div class="owl-buttons">
			<p class="owl-prev prevAccessories"><i class="icon-chevron-left icon"></i></p>
			<p class="owl-next nextAccessories"><i class="icon-chevron-right icon"></i></p>
		</div>
		</div>
		</div>
        </section>
		<script type="text/javascript">
			$(document).ready(function() {
				var owl = $(".laberAccessories");
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
				$(".nextAccessories").click(function(){
				owl.trigger('owl.next');
				})
				$(".prevAccessories").click(function(){
				owl.trigger('owl.prev');
				})
			});
		</script>
      {/if}
    {/block}

    <div class="footer-product-down">
    {block name='product_footer'}
      {hook h='displayFooterProduct' product=$product category=$category}
    {/block}

    {block name='product_images_modal'}
      {include file='catalog/_partials/product-images-modal.tpl'}
    {/block}
    </div>

    {block name='page_footer_container'}
      <footer class="page-footer">
        {block name='page_footer'}
          <!-- Footer content -->
        {/block}
      </footer>
    {/block}
    <style>
        .product-comment-list-item h4 {
            display:none;
        }
        .product-komentarze .comments-note {
            display: none;
        }
        #myprestacommentsBlock h1 {
            display:none;
        }
        .fancybox-overlay.fancybox-overlay-fixed {
            opacity: 1;
            background-color: transparent;
        }
        .new_comment_form_content label {
            float: left;
        }
    </style>
    <script>
        $(document).ready(function() {

            var category_products_custom = $('.category_products_custom').html();
            $('.footer-product-down .category_products_custom').remove();
            $('.footer-product-down #myprestacommentsBlock').remove();
            $(category_products_custom).insertAfter("#main .laberTabs");


            $('#post-product-comment-form input[name="comment_title"]').parent('div').hide();
            $('#post-product-comment-form input[name="comment_title"]').val('Komentarz');

            $('.product-comment-list-item h4').remove();

            $('body').on('click', '.post-product-comment', function(){
                $('#post-product-comment-form .criterion-rating div.star-full div.star').removeClass('star-on').addClass('star-on');
                $('#post-product-comment-form .criterion-rating input[name="criterion[1]"]').val('5');
            });

             $('#comment_title').hide();
             $('#comment_title').prev('label').hide();
             $('#comment_title').val(' ');



        });

        $(window).load(function (){
            $('.laberProduct .laberTabs').clone().insertAfter('.laberProduct');
            $('.laberProduct .laberTabs').remove();
        });
    </script>
  </section>

{/block}
