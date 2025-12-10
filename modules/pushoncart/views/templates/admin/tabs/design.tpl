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
<h3><i class="icon-paint-brush"></i> {l s='Design' mod='pushoncart'} <small>{$module_display|escape:'htmlall':'UTF-8'}</small></h3>
<link href="{$module_dir|escape:'htmlall':'UTF-8'}/views/css/reduction.css" rel="stylesheet">

<div id="pushoncart_design">
    <table id="cart_summary" class="poc-std poc-promo-table" style="border:1px solid {$design_colors.PUSHONCART_DESIGN_TOP_BANNER_BG|escape:'htmlall':'UTF-8'} !important;">
        <thead>
            <tr>
                <th class="cart_product first_item poc-promo-header" colspan=4 height="20" style="background:{$design_colors.PUSHONCART_DESIGN_TOP_BANNER_BG|escape:'htmlall':'UTF-8'};color:{$design_colors.POC_DESIGN_TOP_BANNER_TEXT|escape:'htmlall':'UTF-8'};">
                    <p>
                        <span style="margin-left:45%;">
                            <i style="cursor:pointer;" class="icon-edit" onclick="showColorPickerDesignTab('banner_text_color');" title="{l s='Edit Text Color' mod='pushoncart'}"></i>
                            &nbsp;&nbsp;<span id="top_banner_offer" {if $design_colors.PUSHONCART_DESIGN_PROMO_TEXT eq 1}style="display:none;"{/if}>{l s='Offer' mod='pushoncart'}</span>
                                        <span id="top_banner_promo_name" {if $design_colors.PUSHONCART_DESIGN_PROMO_TEXT eq 0}style="display:none;"{/if}>{l s='Promotion Name' mod='pushoncart'}</span>
                        </span>
                        <i style="cursor:pointer;float:right;" class="icon-edit" onclick="showColorPickerDesignTab('banner_bg_color');" title="{l s='Edit Background Color' mod='pushoncart'}"></i>
                    </p>
                </th>
            </tr>
        </thead>
        <tr>
            <td class="col-md-3 col-lg-3">
                <center>
                    <img id="pushoncart_product_img_design" style="height:auto; width:auto; max-width:200px; max-height:200px;" src='{$demo_imageLink|escape:'htmlall':'UTF-8'}'>
                </center>
            </td>
            <td  class="col-md-6 col-lg-6" style="vertical-align:middle;">
                <h4>Demo Product Name</h4>
                <br/>Demo product description<br/>
            </td>
            <td class="col-md-3 col-lg-3">
                <center>
                <div style="margin-left: auto; margin-right: auto;">
                    <h2 style="font-size:20px;padding-bottom:0px;">
                        <span id="pushoncart_sale_price_demo">$20</span>
                    </h2>
                    <h4>
                        <span class="price-percent-reduction small reduction">- 
                            <span id="pushoncart_discount_amount_demo">$10</span>
                        </span>
                        <strike style="opacity:0.5;"><span id="pushoncart_price_demo">$30</span></strike>
                    </h4>
                </div>
                <br/>
                <button name="pushoncart_add_to_cart" id="pushoncart_add_to_cart" class="btn btn-success reduction">{l s='Add to Cart' mod='pushoncart'}</button>&nbsp;&nbsp;<i style="cursor:pointer;" class="icon-edit" onclick="showColorPickerDesignTab('button_style');" title="{l s='Edit Background Color' mod='pushoncart'}"></i>
                </center>
            </td>
        </tr>
    </table>
    <div class="clear">&nbsp;</div>
    <div class="form-group col-sm-12 col-md-12 col-lg-12" id="banner_bg_color_div" style="display:none;">
        <label for="banner_bg_color" class="col-sm-12 col-md-5 col-lg-5">{l s='Choose a background color for the top of your promotion' mod='flashsalepro'}&nbsp;</label>
        <input type="color" data-hex="true" class="color mColorPickerInput mColorPicker" name="banner_bg_color" id="banner_bg_color" value="{$design_colors.PUSHONCART_DESIGN_TOP_BANNER_BG|escape:'htmlall':'UTF-8'}" style="color: black;" onchange="setPromoColor('banner_bg_color');" />&nbsp;&nbsp;<i style="cursor:pointer;" class="icon-remove" onclick="hideColorPickerDesignTab('banner_bg_color');"></i>
    </div>
    <div class="form-group col-sm-12 col-md-12 col-lg-12" id="banner_text_color_div" style="display:none;">
        <label for="banner_text_color" class="col-sm-12 col-md-5 col-lg-5">{l s='Choose a text color for the top of your promotion' mod='flashsalepro'}&nbsp;</label>
        <input type="color" data-hex="true" class="color mColorPickerInput mColorPicker" name="banner_text_color" id="banner_text_color" value="{$design_colors.POC_DESIGN_TOP_BANNER_TEXT|escape:'htmlall':'UTF-8'}" style="color: black;" onchange="setPromoColor('banner_text_color');" />&nbsp;&nbsp;<i style="cursor:pointer;" class="icon-remove" onclick="hideColorPickerDesignTab('banner_text_color');"></i>
        <span class="col-sm-12 col-md-12 col-lg-12" style="margin: 0px;padding: 0px;">
            <label for="top_promo_name_checkbox" class="col-sm-12 col-md-5 col-lg-5">
                {l s='Show the promotion name in the top banner or "Offer"' mod='flashsalepro'}
            </label>
            <input type="checkbox" id="top_promo_name_checkbox" name="top_promo_name_checkbox" value="1" onchange="setTopPromoName();" {if $design_colors.PUSHONCART_DESIGN_PROMO_TEXT eq 1}checked{/if}>
        </span>
    </div>
    <div class="form-group col-sm-12 col-md-12 col-lg-12" id="button_style_div" style="display:none;">
        <label for="button_style" class="col-sm-12 col-md-5 col-lg-5">{l s='Choose a button style for your promotion' mod='flashsalepro'}&nbsp;</label>
        <!--<button name="pushoncart_button_style_default" id="pushoncart_button_style_default" class="btn btn-default" onclick="setPromoColor('default');">{*l s='Choose' mod='pushoncart'*}</button>-->
        <button name="pushoncart_button_style_success" id="pushoncart_button_style_success" class="btn btn-success" onclick="setPromoColor('success');">{l s='Choose' mod='pushoncart'}</button>
        <button name="pushoncart_button_style_info" id="pushoncart_button_style_info" class="btn btn-info" onclick="setPromoColor('info');">{l s='Choose' mod='pushoncart'}</button>
        <button name="pushoncart_button_style_warning" id="pushoncart_button_style_warning" class="btn btn-warning" onclick="setPromoColor('warning');">{l s='Choose' mod='pushoncart'}</button>
        <button name="pushoncart_button_style_danger" id="pushoncart_button_style_danger" class="btn btn-danger" onclick="setPromoColor('danger');">{l s='Choose' mod='pushoncart'}</button>
        &nbsp;&nbsp;<i style="cursor:pointer;" class="icon-remove" onclick="hideColorPickerDesignTab('button_style');"></i>
    </div>
    <div class="clear">&nbsp;</div>
</div>