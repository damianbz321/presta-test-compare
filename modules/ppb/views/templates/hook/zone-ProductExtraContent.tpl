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
{assign var='productGridColumns' value=4}
{assign var='configuration' value=false}
<div class="featured-products">
    {foreach from=$blocks item="block"}
        {if $block->custom_path == 1 && file_exists($block->path)}
            {include file=$block->path block=$block}
        {else}
            <div id="js-product-list" data-grid-columns="columns-{$productGridColumns}">
                <div class="ppb{$block->id} product-list" id="ppb{$block->id}">
                    <div class="products product-list-wrapper clearfix grid columns-{$productGridColumns} js-product-list-view">
                        {foreach from=$block->products item="product"}
                            {include file=$ppb_customtplpath product=$product}
                        {/foreach}
                    </div>
                </div>
            </div>
        {/if}
    {/foreach}
</div>