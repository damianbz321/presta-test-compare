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

{literal}
    <style>
        div.accessory-heading {
            font-size: 15px;
            font-weight: 700;
            color: #000;
            padding: 5px;
        }

        div.ppbAccessoriesCheckbox {
            border: 1px solid #cecece;
            float: left;
            width: 100%;
            margin-bottom: 10px;
        }

        div.ajax_block_product {
            padding: 5px;
            background: #fff;
        }

        div.ajax_block_product table td {
            padding: 0px;
        }

        div.checker {
            margin:auto;
            display:block;
            top:4px;
        }

        div.ppbAccessoriesCheckbox .quick-view {
            color: #000;
        }

    </style>
{/literal}
<div class="ppb-accessories">
    {foreach from=$blocksProductFooter item=block}
        {if isset($block->products) && $block->products}
            <h4 class="title_block">{if $block->titleURL != "" && $block->titleURL != NULL}<a href="{$block->titleURL}">{$block->name|escape:'html':'utf-8'}</a>{else}{$block->name|escape:'html':'utf-8'}{/if}</h4>
            {foreach from=$block->products item=ppbAccessory}
                <div class="ppbAccessoriesCheckbox list-inline no-print">
                    <div class="ajax_block_product first_item">
                        <article class="product-miniature js-product-miniature" data-id-product="{$ppbAccessory.id_product|escape:'html':'UTF-8'}" data-id-product-attribute="" itemscope="" itemtype="http://schema.org/Product">
                            <table width="100%">
                                <tbody>
                                <tr class="disabled">
                                    <td width="20px" >
                                        <span><input class="accessories_checkbox" type="checkbox" name="accessories" value="{$ppbAccessory.id_product|escape:'html':'UTF-8'}"></span>
                                    </td>
                                    <td align="center" width="55px" class="clickable">
                                        <a href="{$ppbAccessory.link|escape:'html':'UTF-8'}" rel="{$ppbAccessory.link|escape:'html':'UTF-8'}" title="" class="quick-view" data-link-action="quickview">
                                            <img id="accessories_img_{$ppbAccessory.id_product|escape:'html':'UTF-8'}" src="{if isset($ppbAccessory.cover_image_id)}{$link->getImageLink($ppbAccessory.link_rewrite, $ppbAccessory.cover_image_id, 'home_default')|escape:'html':'UTF-8'}{else}{$link->getImageLink($ppbAccessory.link_rewrite, $ppbAccessory.id_image, 'home_default')|escape:'html':'UTF-8'}{/if}" alt="" width="50px"> 
                                        </a>
                                    </td>
                                    <td class="clickable">
                                        <a href="{$ppbAccessory.link|escape:'html':'UTF-8'}" rel="{$ppbAccessory.link|escape:'html':'UTF-8'}" title="" class="quick-view" data-link-action="quickview">
                                            <strong>{$ppbAccessory.name}</strong>
                                        </a>
                                    </td>
                                    <td align="right" class="clickable">
                                        <span class="price pull-right">
                                            +{$ppbAccessory.price}
                                        </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </article>
                    </div>
                </div>
            {/foreach}
        {/if}
    {/foreach}
</div>