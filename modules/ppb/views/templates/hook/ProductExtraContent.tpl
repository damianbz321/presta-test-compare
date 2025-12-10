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

<div class="featured-products">
    {foreach from=$blocks item=block name='for'}
        {if $block->custom_path == 1 && file_exists($block->path)}
            {include file=$block->path block=$block}
        {else}
            <div class="products">
                {foreach from=$block->products item="product"}
                    {include file=$ppb_customtplpath product=$product}
                {/foreach}
            </div>
        {/if}
    {/foreach}
</div>