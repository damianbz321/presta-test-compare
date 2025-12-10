{**
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-9999 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

<h2 class="h1 products-section-title text-uppercase ">{if $block->titleURL != "" && $block->titleURL != NULL}<a href="{$block->titleURL}">{$block->name|escape:'html':'utf-8'}</a>{else}{$block->name|escape:'html':'utf-8'}{/if}{if $block->carousell_controls==1 && $block->carousell==1}<i class="ppbback ppbb{$block->id} material-icons"></i><i class="material-icons ppbf{$block->id} ppbforward"></i>{/if}</h2>

<table class="ppb-table">
    <tr>
        <th>{l s='Image' mod='ppb'}</th>
        <th>{l s='Reference' mod='ppb'}</th>
        <th>{l s='Name' mod='ppb'}</th>
        <th>{l s='Available' mod='ppb'}</th>
        <th>{l s='Price' mod='ppb'}</th>
        <th>{l s='Add to cart' mod='ppb'}</th>
    </tr>
    {if isset($blocksProductFooter)}
        {foreach from=$blocksProductFooter item=block}
            {if isset($block->products) && $block->products}
                {foreach from=$block->products item=product}
                    <tr>
                        <td class="ppb-image">
                            <img id="accessories_img_{$product.id_product|escape:'html':'UTF-8'}" src="{if isset($product.images.0.bySize.cart_default.url)}{$product.images.0.bySize.cart_default.url}{else}{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}{/if}" alt="" width="50px">
                        </td>
                        <td>
                            {$product.reference}
                        </td>
                        <td>
                            <a href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}" title="" class="quick-view" data-link-action="quickview">
                                <strong>{$product.name}</strong>
                            </a>
                        </td>
                        <td>
                            {if $product.quantity > 0}
                                <span class="ppb-instock"></span>
                                {$product.quantity}
                            {else}
                                <span class="ppb-outofstock"></span>
                                {$product.quantity}
                            {/if}
                        </td>
                        <td>
                            {$product.price}
                        </td>
                        <td>
                            <div class="product-add-cart">
                                <form action="{$product.add_to_cart_url}&qty={$product.minimal_quantity}" method="post">
                                    <input type="hidden" name="id_product" value="{$product.id}">
                                    <div class="input-group input-group-add-cart">
                                        <button class="btn btn-product-list add-to-cart btn btn-primary" data-button-action="add-to-cart" type="submit" {if !$product.add_to_cart_url} disabled {/if} ><i class="fa fa-shopping-bag fa-fw bag-icon" aria-hidden="true"></i> <i class="fa fa-circle-o-notch fa-spin fa-fw spinner-icon" aria-hidden="true"></i> {l s='Add to cart' d='Shop.Theme.Actions'}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                {/foreach}
            {/if}
        {/foreach}
    {else}
        {if isset($block->products) && $block->products}
            {foreach from=$block->products item=product_ppb}
                <tr>
                    <td class="ppb-image">
                        <img id="accessories_img_{$product_ppb.id_product|escape:'html':'UTF-8'}" src="{if isset($product_ppb.cover_image_id)}{$link->getImageLink($product_ppb.link_rewrite, $product_ppb.cover_image_id, 'home_default')|escape:'html':'UTF-8'}{else}{$link->getImageLink($product_ppb.link_rewrite, $product_ppb.id_image, 'home_default')|escape:'html':'UTF-8'}{/if}" alt="" width="50px"> 
                    </td>
                    <td>
                        {$product_ppb.reference}
                    </td>
                    <td>
                        <a href="{$product_ppb.link|escape:'html':'UTF-8'}" rel="{$product_ppb.link|escape:'html':'UTF-8'}" title="" class="quick-view" data-link-action="quickview">
                            <strong>{$product_ppb.name}</strong>
                        </a>
                    </td>
                    <td>
                        {if $product_ppb.quantity > 0}
                            <span class="ppb-instock"></span>
                            {$product_ppb.quantity}
                        {else}
                            <span class="ppb-outofstock"></span>
                            {$product_ppb.quantity}
                        {/if}
                    </td>
                    <td>
                        {$product_ppb.price}
                    </td>
                    <td>
                        <div class="product-add-cart">
                            <form action="{$product_ppb.add_to_cart_url}&qty={$product_ppb.minimal_quantity}" method="post">
                                <input type="hidden" name="id_product" value="{$product_ppb.id}">
                                <div class="input-group input-group-add-cart">
                                    <button class="btn btn-product-list add-to-cart btn btn-primary" data-button-action="add-to-cart" type="submit" {if !$product_ppb.add_to_cart_url} disabled {/if} ><i class="fa fa-shopping-bag fa-fw bag-icon" aria-hidden="true"></i> <i class="fa fa-circle-o-notch fa-spin fa-fw spinner-icon" aria-hidden="true"></i> {l s='Add to cart' d='Shop.Theme.Actions'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
            {/foreach}
        {/if}
    {/if}
</table>