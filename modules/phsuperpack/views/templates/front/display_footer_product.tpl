{if !empty($list)}
	<section class="superpack_product clearfix mb-3 mt-3">
		<h2>{l s='Buy in set' mod='phsuperpack'}</h2>
		<div id="idTabProductPack" class="products-flex">
			<div class="d-flex-packs">
				{foreach from=$list item=pack}

					<div id="packs_{$pack.id_superpack_product}" class="d-flex card mt-2 attrib_{$pack.id_product_attribute} attrib_all">
						<div class="prod_packs">
							<div class="packs_flex">
								{foreach from=$pack.items item=p}

									<div class="prod_packs_item">
										{include file=$pack_template product=$p}
									</div>
								{/foreach}
							</div>
						</div>
						<div class="prod_price">{strip}
							<div class="prod_price_flex">
								<div>
									<table class="w-100">
										{foreach from=$pack.items item=p}

											<tr class="prod_packs_item--line">
												<td>{$p.pack_quantity}x</td>
												<td style="text-align: left;"><a href="{$link->getProductLink($p.id_product)}">{$p.name}</a> </td>
												<td style="text-align: right">{Tools::displayPrice($p.price)}</td>
											</tr>
										{/foreach}
									</table>
								</div>
								<hr>
								<table class="w-100">
									{if $pack.price != $pack.regular_price}
										<tr>

											<td style="text-align: left;">SUMA</td>
											<td style="text-align: right;">
												<span class="regular-price"><s>{$pack.regular_price}</s></span>
											</td>
										</tr>
									{else}
										<tr>
											<td style="text-align: left;">
												SUMA
											</td>
											<td style="text-align: right;">
												<span class="price">{$pack.price}</span>
											</td>
										</tr>
									{/if}
									{if $pack.has_discount}
										<tr>
											<td style="text-align: left;">
												<p class="cennik">
													<span class="discount-price">
														{$pack.discount} ({$pack.discount_proc})
													</span>
												</p>
											</td>
											<td style="text-align: right;">
												<span class="price">{$pack.price}</span>
											</td>
										</tr>

									{/if}
								</table>



																
								{if $psVersion}
									<a class="button ajax_add_to_cart_button btn btn-default" 
									href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" 
									data-id-product-attribute="0" data-id-product="{$pack.id_product_pack}" data-minimal_quantity="0">
										<span>{l s='Add to cart' mod='phsuperpack'}</span>
									</a>
								{else}
									<form action="{$urls.pages.cart}" method="post" class="add-to-cart-or-refresh">
										<input type="hidden" name="token" value="{$static_token}">
										<input type="hidden" name="id_product" value="{$pack.id_product_pack}" class="product_id">
										<input type="hidden" name="id_customization" value="0" class="id_customization">
										<input type="hidden" name="qty" value="1">
										<button class="btn btn-primary add-to-cart" data-button-action="add-to-cart" type="submit">
											<i class="material-icons shopping-cart"></i>
											{l s='Add to cart' mod='phsuperpack'}
										</button>
									</form>
								{/if}
							</div>
						{/strip}</div>
					</div>
				{/foreach}
			</div>
		</div>
	</section>
	
{literal}
<script type="text/javascript">

function handleProductList(){
		// return;
	var id = 0;
	// alert($('#idCombination').val() );
	if(typeof $('#idCombination').val() !== "undefined") {
		id = parseInt($('#idCombination').val());
	}
	else {
		id = parseInt($('#product-details').data('product').id_product_attribute);
	}
		
	if(id) {
		$(".attrib_all").hide();
		$(".attrib_" + id).show();
	}
	
}

setInterval(handleProductList, 1000);

</script>
{/literal}
	
{/if}