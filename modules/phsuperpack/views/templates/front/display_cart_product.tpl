{if !empty($items)}
	<section class="superpack_product clearfix mb-2 mt-3 setscart">
		<h2>{l s='Set contains' mod='phsuperpack'}</h2>
		<div id="idTabProductPack" class="products-flex">
			<div class="d-flex-packs">
				<div id="packs_0" class="d-flex card mt-2">
					<div class="prod_packs">
						<div class="packs_flex">
							{foreach from=$items item=p}
								<div class="prod_packs_item">
									{*if $p.price == '0'}
										{include file=$pack_template product=$p}
									{/if*}
									{include file=$pack_template product=$p}
								</div>
							{/foreach}
						</div>
					</div>
					<div class="prod_price">{strip}
						<div class="prod_price_flex">
							<h5>{l s='Set price:' mod='phsuperpack'}</h5>
							<p class="cennik">
								<span class="regular-price">{$regular_price}</span>
								<span class="price">{$price}</span>
								{if $has_discount}
									<span class="discount-price">{l s='You save' mod='phsuperpack'} {$discount} ({$discount_proc})</span>
								{/if}
							</p>
						</div>
					{/strip}</div>
				</div>
			</div>
		</div>
	</section>
{literal}
<script type="text/javascript">
setTimeout(function(){
  $(document).ready(function(){ 
	$(".product-line-info").find("a").removeAttr('href');
	$(".product-line-grid-left").find(".product-image img").each(function() {
		if($(this).attr("src") === "") {
			$(this).parent().hide();
		}
	});
  });
}, 100);
setTimeout(function(){
  $(document).ready(function(){ 
	$(".product-line-info").find("a").removeAttr('href');
	$(".product-line-grid-left").find(".product-image img").each(function() {
		if($(this).attr("src") === "") {
			$(this).parent().hide();
		}
	});
  });
}, 500);
setTimeout(function(){
  $(document).ready(function(){ 
	$(".product-line-info").find("a").removeAttr('href');
	$(".product-line-grid-left").find(".product-image img").each(function() {
		if($(this).attr("src") === "") {
			$(this).parent().hide();
		}
	});
  });
}, 1000);
</script>
{/literal}
{/if}