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
    {foreach from=$blocks item=block name='for'}
        {if $block->custom_path == 1 && file_exists($block->path)}
            {include file=$block->path block=$block}
        {else}
            <div class="products row products-grid">
                {foreach from=$block->products item="product"}
                    <div class="js-product-miniature-wrapper col-md-3 col-lg-3 col-6 col-sm-2">
                        <article class="product-miniature product-miniature-default product-miniature-grid product-miniature-layout-{$iqitTheme.pl_grid_layout} js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="https://schema.org/Product">
                            {include file=$ppb_customtplpath product=$product}
                        </article>
                    </div>
                {/foreach}
            </div>
        {/if}
    {/foreach}
</div>