{**
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-9999 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu TEST
*}

{foreach from=$blocks item=block name='for'}
    {if $block->custom_path == 1 && file_exists($block->path)}
        {include file=$block->path block=$block}
    {else}
        <div id="ppbContainer{$block->id|escape:'int':'utf-8'}" class="featured-products ppb-products clearfix">
            <h1 class="h1 products-section-title text-uppercase ">{$block->name|escape:'html':'utf-8'}</h1>
            {if isset($block->products) && $block->products}
                <div class="ppb{$block->id} featured-products" id="ppb{$block->id}">
                    <div class="products">
                        {foreach from=$block->products item="product"}
                            {include file=$ppb_customtplpath product=$product}
                        {/foreach}
                    </div>
                </div>
            {else}
                <ul class="hppContainer{$block->id|escape:'int':'utf-8'}_noProducts tab-pane">
                    <li class="alert alert-info">{l s='No products at this time.' mod='ppb'}</li>
                </ul>
            {/if}
        </div>
    {/if}
{/foreach}