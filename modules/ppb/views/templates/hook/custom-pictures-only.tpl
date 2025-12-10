<div id="ppbContainer{$block->id|escape:'int':'utf-8'}" class="custom-pictures-only featured-products block products_block clearfix {if $block->carousell==1}ppbContainerBlockMainDiv ppbCarouselBlock{/if}" {if $block->block_position == 2}style="display:none;"{/if}>
    <h1 class="h1 products-section-title text-uppercase ">{if $block->titleURL != "" && $block->titleURL != NULL}<a href="{$block->titleURL}">{$block->name|escape:'html':'utf-8'}</a>{else}{$block->name|escape:'html':'utf-8'}{/if}{if $block->carousell_controls==1 && $block->carousell==1}<i class="ppbback ppbb{$block->id} material-icons">&#xE5CB;</i><i class="material-icons ppbf{$block->id} ppbforward">&#xE5CC;</i>{/if}</h1>
    {if isset($block->products) && $block->products}
        <div class="ppb{$block->id} featured-products" id="ppb{$block->id}">
            <div class="products">
                {foreach from=$block->products item="product"}
                    {if $product.cover}
                        <a href="{$product.url}" class="ppb_thumbnail thumbnail product-thumbnail {if Tools::getValue('id_product') == $product.id}ppb_selected{/if}">
                            <img
                                    src="{$product.cover.bySize.cart_default.url}"
                                    alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
                                    data-full-size-image-url="{$product.cover.large.url}"
                            />
                        </a>
                    {else}
                        <a href="{$product.url}" class="thumbnail product-thumbnail">
                            <img src="{$urls.no_picture_image.bySize.home_default.url}"/>
                        </a>
                    {/if}
                {/foreach}
            </div>
        </div>
    {/if}
</div>