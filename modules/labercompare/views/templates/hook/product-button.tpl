
{if isset($product.id_product)}
<a 	href="javascript:void(0)" 
	class="button-action js-compare"  
    data-id-product="{$product.id_product|intval}"
    data-url-product="{$product.url}"
    data-name-product="{$product.name}"
    data-image-product="{$product.cover.small.url}"
   	data-url="{url entity='module' name='labercompare' controller='actions'}"  
    title="">
    <i class="addCompare pe-7s-repeat"></i>
    <i class="removeCompare pe-7s-refresh"></i><span>{l s='add to Compare' d='Shop.Theme.Laberthemes'}</span>
</a>
{/if} 