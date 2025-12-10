{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2018 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

{if $ps17 neq true}
<script>
    var poc_ajax_url = "{$poc_ajax_url}";
</script>
{/if}

<link href="{$module_dir}/views/css/reduction.css" rel="stylesheet">


<div id="pushoncart_{$promo->id_pushoncart}">
    <form action="{$requestUri}" method="post">
    <div id="cart_summary" class="std" {if $design_colours.PUSHONCART_CUSTOM eq 1}style="border:1px solid {$design_colours.PUSHONCART_DESIGN_TOP_BANNER_BG} !important;"{/if}>
        <div class="row no-margin">
            <div class="cart_product first_item promotion-top" colspan=4 height="20"
                {if $design_colours.PUSHONCART_CUSTOM eq 1}
                    style="background:{$design_colours.PUSHONCART_DESIGN_TOP_BANNER_BG} !important;color:{$design_colours.POC_DESIGN_TOP_BANNER_TEXT} !important;"
                {/if}>
                {if $design_colours.PUSHONCART_DESIGN_PROMO_TEXT eq 1}
                    <center><p style="padding-bottom: 0;{if $design_colours.PUSHONCART_CUSTOM eq 1}
                   color:{$design_colours.POC_DESIGN_TOP_BANNER_TEXT} !important;{/if}">{$promo->parameters.discount_name}</p></center>
                {else}
                    {if $product_prices.reduction_percent neq 0}
                        <center><p style="padding-bottom: 0;{if $design_colours.PUSHONCART_CUSTOM eq 1}
                   color:{$design_colours.POC_DESIGN_TOP_BANNER_TEXT} !important;{/if}">{l s='Offer' mod='pushoncart'}</p></center>
                    {else}
                        <center><p style="padding-bottom: 0;{if $design_colours.PUSHONCART_CUSTOM eq 1}
                   color:{$design_colours.POC_DESIGN_TOP_BANNER_TEXT} !important;{/if}">{l s='Product You May Like' mod='pushoncart'}</p></center>
                    {/if}
                {/if}
            </div>
        </div>
        <div class="row promotion-main no-margin">
            <div class="col-md-3 col-lg-3">
                <a href="{$link->getProductLink($promo->parameters.id_product)}"><img id="pushoncart_product_img_{$promo->parameters.id_product}" class="img-{if $ps17 eq true}fluid{else}responsive{/if}" src='{$promo->product_info.image_link}'></a>
            </div>
            <div class=" col-md-6 col-lg-6" style="vertical-align:middle;">
                <h4><a href="{$link->getProductLink($promo->parameters.id_product)}">{$name}</a></h4>
                <br/>{$description nofilter}<br/>
                {if $promo->product_info.all_the_atts.att_count > 0}
                    <div class="clear">&nbsp;</div>
                    {include file="./productAttributes.tpl"}
                {/if}
            </div>
            <div class="col-md-3 col-lg-3">
                <center>
                <div style="margin-left: auto; margin-right: auto;">
                    <h2 style="font-size:20px;padding-bottom:0px;">
                        {if $currency_format eq 1}{$product_prices.symbol}
                            <span id="pushoncart_sale_price_{$promo->id_pushoncart}">{$product_prices.sale_price}</span>
                        {else}
                            <span id="pushoncart_sale_price_{$promo->id_pushoncart}">{$product_prices.sale_price|escape}</span>{$product_prices.symbol}
                        {/if}
                    </h2>
                    {if $product_prices.reduction_percent neq 0}
                        <h4>
                            {if $product_prices.reduction_percent neq 0}<span class="price-percent-reduction small reduction">-
                                {if $product_prices.discount_type eq 0}{$product_prices.reduction_percent}%
                                {else}
                                    {if $currency_format eq 1}{$product_prices.symbol}
                                        <span id="pushoncart_discount_amount_{$promo->id_pushoncart}">{$product_prices.discount_in_cash}</span>
                                    {else}
                                        <span id="pushoncart_discount_price_{$promo->id_pushoncart}">{$product_prices.discount_in_cash|escape}</span>{$product_prices.symbol}
                                    {/if}
                                {/if}</span>
                            {/if}
                            <strike style="opacity:0.5;">{if $currency_format eq 1}{$product_prices.symbol}<span id="pushoncart_price_{$promo->id_pushoncart}">{$product_prices.price}</span>{else}<span id="pushoncart_price_{$promo->id_pushoncart}">{$product_prices.price}</span>{$product_prices.symbol}{/if}</strike>
                        </h4>
                </div>{/if}
                <br/>
                    <input type="hidden" name="product" value="{$promo->parameters.id_product}" />
                    <input type="hidden" name="id_cart_rule" value="{$product_prices.id_cart_rule}" />
                    <input type="hidden" name="token" value="{$token}" />
                    <button type="submit" name="pushoncart_add_to_cart" id="pushoncart_add_to_cart_{$promo->id_pushoncart}"
                            class="btn btn-{if $design_colours.PUSHONCART_CUSTOM eq 1}{$design_colours.PUSHONCART_DESIGN_BUTTON}{else}success{/if} reduction{if $promo->product_info.all_the_atts.default_check_stock eq 0} disabled{/if}">
                            {l s='Add to Cart' mod='pushoncart'}
                    </button>
                </form>
                </center>
            </div>
        </div>
    </div>
</div>
