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

<div class="ppb{$block->id} js-product-list" id="ppb{$block->id}">
    {if $block->custom_path == 1 && file_exists($block->path)}
        {include file=$block->path block=$block}
    {else}
        {foreach from=$blocks item=block name='for'}
            {if $block->custom_path == 1 && file_exists($block->path)}
                {include file=$block->path block=$block}
            {else}
                <div id="ppbContainer{$block->id|escape:'int':'utf-8'}" class="featured-products block products_block clearfix {if $block->carousell==1}ppbContainerBlockMainDiv ppbCarouselBlock{/if}" {if $block->block_position == 2}style="display:none;"{/if}>
                    {if isset($block->products) && $block->products}
                        <div class="ppb{$block->id} featured-products" id="ppb{$block->id}">
                            <div id="js-product-list">
                                {include file='catalog/_partials/miniatures/list-item.tpl' products=$block->products}
                            </div>
                        </div>
                    {else}
                        <ul class="ppbContainer{$block->id|escape:'int':'utf-8'}_noProducts tab-pane">
                            <li class="alert alert-info">{l s='No products at this time.' mod='ppb'}</li>
                        </ul>
                    {/if}
                </div>
            {/if}
        {/foreach}
    {/if}
</div>