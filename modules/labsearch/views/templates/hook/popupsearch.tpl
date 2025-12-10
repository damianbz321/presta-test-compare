<div class="wrap_item">
    {if isset($products) && $products}
        {foreach from=$products item=product name=product}
			{if $smarty.foreach.product.iteration <= $search_count}
				<div class="product clearfix">
					{if isset($enable_image) && $enable_image}
						<div class="search_img">
							<a href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" class="product_image">
								<img src="{$link->getImageLink($product.id_image, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" />
							</a>
						</div>
					{/if}
					<div class="search_info">
						{if isset($enable_name) && $enable_name}
							<a class="product_name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}">
								{$product.name|truncate:35:'...'|escape:'html':'UTF-8'}
							</a>
						{/if}
						{if isset($enable_price) && $enable_price}
							<span class="price">{$product.price|escape:'html':'UTF-8'}</span>
						{/if}
					</div>
				</div>
			{/if}
        {/foreach}
    {else}
        {$search_alert|escape:'html':'UTF-8'}
    {/if}
</div>