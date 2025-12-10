{if isset($product.id_product)}
<a 	href="javascript:void(0)" 
	class="button-action js-compare"  
    data-id-product="{$product.id_product|intval}"
    data-url-product="{$product.url}"
    data-name-product="{$product.name}"
    data-image-product="{$product.cover.small.url}"
   	data-url="{url entity='module' name='labercompare' controller='actions'}"  
    title="Dodaj do porównania">
	<i class="addCompare icon-layers"></i>
    <i class="removeCompare icon-check"></i>
    <span>Dodaj do porównania</span>
</a>
{/if} 