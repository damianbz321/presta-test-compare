{block name='pack_miniature_item'}
	<article data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope="" itemtype="http://schema.org/Product">
		<div class="pack-container">
			<div class="pack-image">
				<a href="{$product.link}" title="{$product.name|escape:'html':'UTF-8'}" class="thumbnail pack-thumbnail">
					<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}"{if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" ata-full-size-image-url="{$link->getImageLink($product.link_rewrite, $product.id_image, 'large_default')|escape:'html':'UTF-8'}" />
				</a>
			</div>
			<div class="pack-description">
				<h5 itemprop="name"><a href="{$product.link}" title="{$product.name|escape:'html':'UTF-8'}">{$product.name}</a></h5>
			</div>
		</div>
	</article>
{/block}