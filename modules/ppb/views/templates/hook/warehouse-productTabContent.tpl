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
                <div class="ppb{$block->id} js-product-list" id="ppb{$block->id}">
                    <div class="products row products-grid">
                        {foreach from=$block->products item="product"}
                            {block name='product_miniature_item'}
                                <div class="js-product-miniature-wrapper {if $block->carousell!=1}{if isset($elementor) && $elementor}col-{$nbMobile} col-md-{$nbTablet} col-lg-{$nbDesktop} col-xl-{$nbDesktop}{else} col-{$iqitTheme.pl_grid_p} col-md-{$iqitTheme.pl_grid_t} col-lg-{$iqitTheme.pl_grid_d} col-xl-{$iqitTheme.pl_grid_ld}{/if}{/if}">
                                    <article class="product-miniature product-miniature-default product-miniature-grid product-miniature-layout-{$iqitTheme.pl_grid_layout} js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="https://schema.org/Product">
                                        {if $iqitTheme.pl_grid_layout == 1}
                                            {include file='catalog/_partials/miniatures/_partials/product-miniature-1.tpl'}
                                        {/if}

                                        {if $iqitTheme.pl_grid_layout == 2}
                                            {include file='catalog/_partials/miniatures/_partials/product-miniature-2.tpl'}
                                        {/if}

                                        {if $iqitTheme.pl_grid_layout == 3}
                                            {include file='catalog/_partials/miniatures/_partials/product-miniature-3.tpl'}
                                        {/if}
                                    </article>
                                </div>
                            {/block}
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