{*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 *}

{if $ajax !=1 || $pla_amazzingfilter == "amazzingfilter"}
    {if ($pla_dropdown == 1 && count($pla_matrix)>0) || ($pla_dropdown == 1 && $pla_noattr ==1)}
<div class="{if count($pla_matrix)<=0}pla_is_empty{/if} pla_matrix_dropdown" data-productid="{$pla_product.id_product}">
    <div class="pla_matrix_dropdown_block {if $pla_popup}pla_popup{/if}" data-productid="{$pla_product.id_product}">{l s='Show available variants' mod='pla'}
        <div class="crotate"></div>
    </div>
    {/if}
    {/if}
    {if ($pla_popup !=1 || $ajax == 1) || $pla_dropdown != 1}
        {if count($pla_matrix)>0}
            {foreach from=$pla_matrix item=ct key=ctk name='ctr'}
                {if $pla_oos == 1}
                    {if $ct['combination']['quantity']>0}
                        {assign var='hidetr' value='0'}
                    {else}
                        {assign var='hidetr' value='1'}
                    {/if}
                {else}
                    {assign var='hidetr' value='0'}
                {/if}
            {/foreach}
            <table id="pla_matrix" class="table table-bordered pla_matrix">
                <tbody>
                {if $hidetr != 1}
                    {if $pla_show_th == 1}
                        <tr>
                            {if $pla_image==1}
                                <th class="tr_pla_image">{l s='Image' mod='pla'}</th>
                            {/if}
                            {if $pla_color_attr==1}
                                <th class="tr_pla_color">{l s='Color' mod='pla'}</th>
                            {/if}
                            {if $pla_comb==1}
                                <th class="tr_pla_cname">{l s='Variant' mod='pla'}</th>
                            {/if}
                            {if $pla_reference==1}
                                <th class="tr_pla_reference">{l s='Reference' mod='pla'}</th>
                            {/if}
                            {if $pla_ean==1}
                                <th class="tr_pla_ean">{l s='Ean13' mod='pla'}</th>
                            {/if}
                            {if $pla_upc==1}
                                <th class="tr_pla_upc">{l s='UPC' mod='pla'}</th>
                            {/if}
                            {if $pla_stock==1}
                                <th class="tr_pla_stock">{l s='Stock' mod='pla'}</th>
                            {/if}
                            {if $pla_price==1}
                                <th class="tr_pla_price">{l s='Price' mod='pla'}</th>
                            {/if}
                            {if $pla_addtocart==1 || $pla_quantity ==1}
                                <th class="pla_addtocart">{l s='Buy' mod='pla'}</th>
                            {/if}
                        </tr>
                    {/if}
                {/if}
                {foreach from=$pla_matrix item=ct key=ctk name='ctr'}
                    {if $pla_oos == 1}
                        {if $ct['combination']['quantity']>0}
                            {assign var='hidetr' value='0'}
                        {else}
                            {assign var='hidetr' value='1'}
                        {/if}
                    {else}
                        {assign var='hidetr' value='0'}
                    {/if}
                    {if $hidetr != 1}
                        <tr class="pla_matrix_row ctr{$smarty.foreach.ctr.index|escape:'htmlall':'utf-8'}">
                            <form action="{$link->getPageLink('cart')|escape:'html'}" method="post" id="ct_matrix_{$ct['combination']['id_product_attribute']|escape:'htmlall':'utf-8'}" name="ct_matrix_{$ct['combination']['id_product_attribute']|escape:'htmlall':'utf-8'}">
                                {if $pla_image==1}
                                    <td class="pla_image">
                                        {if $ct['image'] != 0}
                                            <img src="{$link->getImageLink($pla_product.link_rewrite,"{$pla_product.id_product}-{$ct['image']['id_image']}",$pla_imagetype)|escape:'htmlall':'utf-8'}"/>
                                        {else}
                                            {l s='-' mod='pla'}
                                        {/if}
                                    </td>
                                {/if}
                                {if $pla_color_attr == 1}
                                    <td class="pla_color">
                                        {assign var='has_color' value=0}
                                        {foreach from=$pla_matrix_attributes item=loop key=loopkey name='ctd'}
                                            {assign var='thelement' value=$ct.attributes.$loopkey}
                                            {if Configuration::get('pla_color_attr_name') != 1 && $thelement.type == "color"}
                                                {assign var='has_color' value=1}
                                                <div class="pla_color_parent">
                                                    {assign var='img_color_exists' value=file_exists($col_img_dir|cat:$ct.combination.id_attribute|cat:'.jpg')}
                                                    {if $img_color_exists}
                                                        <div style="background:url('{$theme_col_img_dir}{$ct.combination.id_attribute}.jpg')" class="pla_color pla_attr_group_{$loop.id} pla_attr_{$ct.combination.id_attribute} pla{$smarty.foreach.ctd.index|escape:'htmlall':'utf-8'}"></div>
                                                    {else}
                                                        <div style="background:{$thelement.color}" class="pla_color pla_attr_group_{$loop.id} pla_attr_{$ct.combination.id_attribute} pla{$smarty.foreach.ctd.index|escape:'htmlall':'utf-8'}"></div>
                                                    {/if}
                                                </div>
                                            {elseif $thelement.type == "color"}
                                                {assign var='has_color' value=1}
                                                <div class="pla_color pla_attr_group_{$loop.id} pla_attr_{$ct.combination.id_attribute} pla{$smarty.foreach.ctd.index|escape:'htmlall':'utf-8'}">
                                                    {if $thelement.name!=""}{$thelement.name|escape:'htmlall':'utf-8'}{else}{l s='-' mod='combinationstab'}{/if}
                                                </div>
                                            {/if}
                                        {/foreach}
                                        {if $has_color == 0}
                                            -
                                        {/if}
                                    </td>
                                {/if}
                                {if $pla_comb ==1}
                                    <td class="pla_cname">{$ct['combination_name'] nofilter}</td>
                                {/if}
                                {if $pla_reference==1}
                                    <td class="pla_reference">{$ct['reference']}</td>
                                {/if}
                                {if $pla_ean==1}
                                    <td class="pla_ean">{$ct['ean']}</td>
                                {/if}
                                {if $pla_upc==1}
                                    <td class="pla_upc">{$ct['upc']}</td>
                                {/if}
                                {if $pla_stock==1}
                                    <td class="tr_pla_stock">{$ct['combination']['quantity']}</td>
                                {/if}
                                {if $pla_price==1}
                                    <td class="pla_price">
                                        {if $pla_ver_price_customer == false && $pla_price_logonly == true}
                                            {l s='For logged customers only' mod='pla'}
                                        {else}
                                            {if $pla_prices_tax == 3}
                                                {if $priceDisplay == 1}
                                                    {assign var='reduction' value=Product::getPriceStatic($id_product, false,$ct['combination']['id_product_attribute'],6,null,true,false,1,false,null)}
                                                    {if $reduction>0 && Configuration::get('pla_price_before')==1}
                                                        <strike>{Tools::displayPrice((Product::getPriceStatic($id_product,false,$ct['combination']['id_product_attribute'],6,null,false,false,1,false,null)))}</strike>
                                                    {/if}
                                                    <strong class="strongprice">{Tools::displayPrice((Product::getPriceStatic($id_product,false,$ct['combination']['id_product_attribute'],6,null,false,true,1,false,null)))} {if Configuration::get('pla_lab') == 1}{l s='Tax excl.' mod='pla'}{/if}</strong>
                                                {else}
                                                    {assign var='reduction' value=Product::getPriceStatic($id_product,true,$ct['combination']['id_product_attribute'],6,null,true,false,1,false,null)}
                                                    {if $reduction>0 && Configuration::get('pla_price_before')==1}
                                                        <strike>{Tools::displayPrice((Product::getPriceStatic($id_product,true,$ct['combination']['id_product_attribute'],6,null,false,false,1,false,null)))}</strike>
                                                    {/if}
                                                    <strong class="strongprice">{Tools::displayPrice((Product::getPriceStatic($id_product,true,$ct['combination']['id_product_attribute'],6,null,false,true,1,false,null)))} {if Configuration::get('pla_lab') == 1}{l s='Tax incl.' mod='pla'}{/if}</strong>
                                                {/if}
                                            {elseif $pla_prices_tax!=1}
                                                {assign var='reduction' value=Product::getPriceStatic($id_product,false,$ct['combination']['id_product_attribute'],6,null,true,false,1,false,null)}
                                                {if $reduction>0 && Configuration::get('pla_price_before')==1}
                                                    <strike>{Tools::displayPrice((Product::getPriceStatic($id_product,false,$ct['combination']['id_product_attribute'],6,null,false,false,1,false,null)))}</strike>
                                                {/if}
                                                <strong class="strongprice">{Tools::displayPrice((Product::getPriceStatic($id_product,false,$ct['combination']['id_product_attribute'],6,null,false,true,1,false,null)))} {if Configuration::get('pla_lab') == 1}{l s='Tax excl.' mod='pla'}{/if}</strong>
                                            {else}
                                                {assign var='reduction' value=Product::getPriceStatic($id_product,true,$ct['combination']['id_product_attribute'],6,null,true,false,1,false,null)}
                                                {if $reduction>0 && Configuration::get('pla_price_before')==1}
                                                    <strike>{Tools::displayPrice((Product::getPriceStatic($id_product,true,$ct['combination']['id_product_attribute'],6,null,false,false,1,false,null)))}</strike>
                                                {/if}
                                                <strong class="strongprice">{Tools::displayPrice((Product::getPriceStatic($id_product,true,$ct['combination']['id_product_attribute'],6,null,false,true,1,false,null)))} {if Configuration::get('pla_lab') == 1}{l s='Tax incl.' mod='pla'}{/if}</strong>
                                            {/if}
                                        {/if}
                                    </td>
                                {/if}
                                {if $pla_addtocart==1 || $pla_quantity ==1}
                                    <td class="pla_addtocart">
                                        <input type="hidden" name="token" value="{Tools::getToken(false)}"/>
                                        <input type="hidden" name="id_product" value="{$id_product|escape:'htmlall':'utf-8'}"/>
                                        <input type="hidden" name="id_product_combination" id="ct_matrix_{$ct['combination']['id_product_attribute']|escape:'htmlall':'utf-8'}_idProduct" value="{$id_product|escape:'htmlall':'utf-8'}"/>
                                        <input type="hidden" name="add" value="1"/>
                                        <input type="hidden" name="id_product_attribute" id="ct_matrix_{$ct['combination']['id_product_attribute']|escape:'htmlall':'utf-8'}_idCombination" value="{$ct['combination']['id_product_attribute']|escape:'htmlall':'utf-8'}"/>
                                        {if $pla_quantity==1}
                                            <span class="decrease_quantity"><img src="{$content_dir}modules/pla/views/templates/img/minus.png"/></span>
                                            <input alt="ct_matrix_{$ct['combination']['id_product_attribute']}" name="qty" {if ($ct['combination']['quantity']<=0 && $allow_oosp==0) || $pla_product.available_for_order==0}disabled{/if} data-defqty="{if $pla_quantity_v > 0}{$pla_quantity_v}{else}0{/if}" class="qty" data-multiply="{if isset($ct['minqc_multiply'])}{$ct['minqc_multiply']}{else}0{/if}" data-min="{if isset($ct['minqc_value'])}{$ct['minqc_value']}{else}1{/if}" min="{if isset($ct['minqc_value'])}{$ct['minqc_value']}{else}1{/if}" id="ct_matrix_{$ct['combination']['id_product_attribute']}_idQty" value="{if $minqc != false}{if isset($ct['minqc_value'])}{$ct['minqc_value']}{else}{if $pla_quantity_v > 0}{$pla_quantity_v}{else}0{/if}{/if}{else}{if $pla_quantity_v > 0}{$pla_quantity_v}{else}0{/if}{/if}" type="text" style="" max="{if Configuration::get('pla_control_quantity')==1}99999999{else}{$ct['combination']['quantity']|trim}{/if}" onblur="if(value=='') value = '0'" onfocus="if(value=='0') value = ''"/>
                                            <span class="increase_quantity"><img src="{$content_dir}modules/pla/views/templates/img/plus.png"/></span>
                                        {else}
                                            <input alt="ct_matrix_{$ct['combination']['id_product_attribute']}" name="qty" {if ($ct['combination']['quantity']<=0 && $allow_oosp==0) || $pla_product.available_for_order==0}disabled{/if} data-defqty="{if $pla_quantity_v > 0}{$pla_quantity_v}{else}0{/if}" class="qty" data-multiply="{if isset($ct['minqc_multiply'])}{$ct['minqc_multiply']}{else}0{/if}" data-min="{if isset($ct['minqc_value'])}{$ct['minqc_value']}{else}1{/if}" min="{if isset($ct['minqc_value'])}{$ct['minqc_value']}{else}1{/if}" id="ct_matrix_{$ct['combination']['id_product_attribute']}_idQty" value="{if $minqc != false}{if isset($ct['minqc_value'])}{$ct['minqc_value']}{else}{if $pla_quantity_v > 0}{$pla_quantity_v}{else}0{/if}{/if}{else}{if $pla_quantity_v > 0}{$pla_quantity_v}{else}0{/if}{/if}" type="hidden" style="" max="{if Configuration::get('pla_control_quantity')==1}99999999{else}{$ct['combination']['quantity']|trim}{/if}" onblur="if(value=='') value = '1'" onfocus="if(value=='1') value = ''"/>
                                        {/if}
                                        <input type="hidden" name="Submit"/>
                                        {if $pla_bulk!=1}
                                            {if $pla_ajax==1}
                                                <div class="btn btn-primary btn-sm ct_submit {if ($ct['combination']['quantity']<=0 && $allow_oosp==0) || $pla_product.available_for_order==0} ct_submit_nostock{/if}" id="ct_matrix_{$ct['combination']['id_product_attribute']}_submit" {if ($pla_product.available_for_order==1) && (($allow_oosp==1 && $ct['combination']['quantity']>=0) || ($allow_oosp==1 && $ct['combination']['quantity']<=0) || ($allow_oosp==0 && $ct['combination']['quantity']>0))}onclick="ajaxCartPla.add( $('{if $pla_popup==1}.fancybox-inner{/if} #ct_matrix_{$ct['combination']['id_product_attribute']|escape:'htmlall':'utf-8'}_idProduct').val(), $('{if $pla_popup==1}.fancybox-inner{/if} #ct_matrix_{$ct['combination']['id_product_attribute']|escape:'htmlall':'utf-8'}_idCombination').val(), true, '#ct_matrix_{$ct['combination']['id_product_attribute']}_submit', $(this).parent().parent().find('{if $pla_popup==1}.fancybox-inner{/if} #ct_matrix_{$ct['combination']['id_product_attribute']|escape:'htmlall':'utf-8'}_idQty').val(), null);"{/if}>{l s='Add' mod='pla'}</div>
                                            {else}
                                                <div class="btn btn-primary btn-sm ct_submit {if ($ct['combination']['quantity']<=0 && $allow_oosp==0) || $pla_product.available_for_order==0} ct_submit_nostock{/if}" id="ct_matrix_{$ct['combination']['id_product_attribute']}_submit" {if ($pla_product.available_for_order==1) && (($allow_oosp==1 && $ct['combination']['quantity']>=0) || ($allow_oosp==1 && $ct['combination']['quantity']<=0) || ($allow_oosp==0 && $ct['combination']['quantity']>0))}onclick="ct_matrix_{$ct['combination']['id_product_attribute']|escape:'htmlall':'utf-8'}.submit();"{/if}>{l s='Add' mod='pla'}</div>
                                            {/if}
                                        {/if}
                                    </td>
                                {/if}
                            </form>
                        </tr>
                    {/if}
                {/foreach}
                <tr>
                    <td colspan="100%">
                        {if $pla_bulk==1}
                            {if $pla_ajax==1}
                                <div class="pla_submit_bulk ct_submit {if ($ct['combination']['quantity']<=0 && $allow_oosp==0) || $pla_product.available_for_order==0} ct_submit_nostock{/if}" id="ct_matrix_{$ct['combination']['id_product_attribute']}_submit" {if ($pla_product.available_for_order==1) && (($allow_oosp==1 && $ct['combination']['quantity']>=0) || ($allow_oosp==1 && $ct['combination']['quantity']<=0) || ($allow_oosp==0 && $ct['combination']['quantity']>0))}{/if}>
                                    <button class="btn-sm btn btn-primary" rel="nofollow" title="Add to cart">
                                        <span>{l s='Add to cart' mod='pla'}</span>
                                    </button>
                                </div>
                            {/if}
                        {/if}
                    </td>
                </tr>
                </tbody>
            </table>
        {else}
            {if Configuration::get('pla_noattr')==1}
                <table id="pla_matrix" class="table table-bordered pla_matrix">
                    <tbody>
                    <tr class="pla_matrix_row ctr{$smarty.foreach.ctr.index|escape:'htmlall':'utf-8'}">
                        <form action="{$link->getPageLink('cart')|escape:'html'}" method="post" id="ct_matrix_{$pla_product.id_product}" name="ct_matrix_{$pla_product.id_product}">
                            {if $pla_price==1}
                                {if $pla_ver_price_customer == false && $pla_price_logonly == true}
                                    {l s='For logged customers only' mod='pla'}
                                {else}
                                    <td class="pla_price">
                                        {if $pla_prices_tax == 3}
                                            {if $priceDisplay == 1}
                                                {assign var='reduction' value=Product::getPriceStatic($id_product, false,0,6,null,true,false,1,false,null)}
                                                {if $reduction>0 && Configuration::get('pla_price_before')==1}
                                                    <strike>{Tools::displayPrice((Product::getPriceStatic($id_product,false,0,6,null,false,false,1,false,null)))}</strike>
                                                {/if}
                                                <strong class="strongprice">{Tools::displayPrice((Product::getPriceStatic($id_product,false,0,6,null,false,true,1,false,null)))} {if Configuration::get('pla_lab') == 1}{l s='Tax excl.' mod='pla'}{/if}</strong>
                                            {else}
                                                {assign var='reduction' value=Product::getPriceStatic($id_product,true,0,6,null,true,false,1,false,null)}
                                                {if $reduction>0 && Configuration::get('pla_price_before')==1}
                                                    <strike>{Tools::displayPrice((Product::getPriceStatic($id_product,true,0,6,null,false,false,1,false,null)))}</strike>
                                                {/if}
                                                <strong class="strongprice">{Tools::displayPrice((Product::getPriceStatic($id_product,true,0,6,null,false,true,1,false,null)))} {if Configuration::get('pla_lab') == 1}{l s='Tax incl.' mod='pla'}{/if}</strong>
                                            {/if}
                                        {elseif $pla_prices_tax!=1}
                                            {assign var='reduction' value=Product::getPriceStatic($id_product,false,0,6,null,true,false,1,false,null)}
                                            {if $reduction>0 && Configuration::get('pla_price_before')==1}
                                                <strike>{Tools::displayPrice((Product::getPriceStatic($id_product,false,0,6,null,false,false,1,false,null)))}</strike>
                                            {/if}
                                            <strong class="strongprice">{Tools::displayPrice((Product::getPriceStatic($id_product,false,0,6,null,false,true,1,false,null)))} {if Configuration::get('pla_lab') == 1}{l s='Tax excl.' mod='pla'}{/if}</strong>
                                        {else}
                                            {assign var='reduction' value=Product::getPriceStatic($id_product,true,0,6,null,true,false,1,false,null)}
                                            {if $reduction>0 && Configuration::get('pla_price_before')==1}
                                                <strike>{Tools::displayPrice((Product::getPriceStatic($id_product,true,0,6,null,false,false,1,false,null)))}</strike>
                                            {/if}
                                            <strong class="strongprice">{Tools::displayPrice((Product::getPriceStatic($id_product, true,0,6,null,false,true,1,false,null)))} {if Configuration::get('pla_lab') == 1}{l s='Tax incl.' mod='pla'}{/if}</strong>
                                        {/if}
                                    </td>
                                {/if}
                            {/if}
                            {if $pla_addtocart==1 || $pla_quantity ==1}
                                <td class="pla_addtocart">
                                    <input type="hidden" name="token" value="{Tools::getToken(false)}"/>
                                    <input type="hidden" name="id_product" value="{$id_product|escape:'htmlall':'utf-8'}"/>
                                    <input type="hidden" name="id_product_combination" id="noattr_ct_matrix_{$id_product|escape:'htmlall':'utf-8'}_idProduct" value="{$id_product|escape:'htmlall':'utf-8'}"/>
                                    <input type="hidden" name="add" value="1"/>
                                    <input type="hidden" name="id_product_attribute" id="noattr_ct_matrix_{$pla_product.id_product}_idCombination" value="0"/>
                                    {if $pla_quantity == 1}
                                        <span class="decrease_quantity"><img src="{$content_dir}modules/pla/views/templates/img/minus.png"/></span>
                                        <input alt="ct_matrix_{$pla_product.id_product}" name="qty" {if ($pla_product.quantity<=0 && $allow_oosp==0) || $pla_product.available_for_order==0}disabled{/if} class="qty" id="noattr_ct_matrix_{$pla_product.id_product}_idQty" data-defqty="{if $pla_quantity_v > 0}{$pla_quantity_v}{else}0{/if}" class="qty" data-multiply="{if isset($ct['minqc_multiply'])}{$ct['minqc_multiply']}{else}0{/if}" data-min="{if isset($ct['minqc_value'])}{$ct['minqc_value']}{else}1{/if}" min="{if isset($ct['minqc_value'])}{$ct['minqc_value']}{else}1{/if}" id="ct_matrix_{$pla_product.id_product}_idQty" value="{if $minqc != false}{if isset($ct['minqc_value'])}{$ct['minqc_value']}{else}{if $pla_quantity_v > 0}{$pla_quantity_v}{else}0{/if}{/if}{else}{if $pla_quantity_v > 0}{$pla_quantity_v}{else}0{/if}{/if}" type="text" style="" max="{if Configuration::get('pla_control_quantity')==1}99999999{else}{$pla_product.quantity|trim}{/if}" onblur="if(value=='') value = '0'" onfocus="if(value=='0') value = ''"/>
                                        <span class="increase_quantity"><img src="{$content_dir}modules/pla/views/templates/img/plus.png"/></span>
                                    {else}
                                        <input alt="noattr_ct_matrix_{$pla_product.id_product}" name="qty" {if ($pla_product.quantity<=0 && $allow_oosp==0) || $pla_product.available_for_order==0}disabled{/if} class="qty" id="noattr_ct_matrix_{$pla_product.id_product}_idQty" data-defqty="{if $pla_quantity_v > 0}{$pla_quantity_v}{else}0{/if}" class="qty" data-multiply="{if isset($ct['minqc_multiply'])}{$ct['minqc_multiply']}{else}0{/if}" data-min="{if isset($ct['minqc_value'])}{$ct['minqc_value']}{else}1{/if}" min="{if isset($ct['minqc_value'])}{$ct['minqc_value']}{else}1{/if}" id="ct_matrix_{$pla_product.id_product}_idQty" value="{if $minqc != false}{if isset($ct['minqc_value'])}{$ct['minqc_value']}{else}{if $pla_quantity_v > 0}{$pla_quantity_v}{else}0{/if}{/if}{else}{if $pla_quantity_v > 0}{$pla_quantity_v}{else}0{/if}{/if}" type="hidden" style="" onblur="if(value=='') value = '1'" onfocus="if(value=='1') value = ''"/>
                                    {/if}
                                    <input type="hidden" name="Submit"/>

                                    {if $pla_ajax==1}
                                        <div class="btn btn-primary btn-sm ct_submit {if ($pla_product.quantity <=0 && $allow_oosp==0) || $pla_product.available_for_order==0} ct_submit_nostock{/if}" id="noattr_ct_matrix_{$pla_product.id_product}_submit" {if ($pla_product.available_for_order==1) && (($allow_oosp==1 && $pla_product.quantity>=0) || ($allow_oosp==1 && $pla_product.quantity<=0) || ($allow_oosp==0 && $pla_product.quantity>0))}onclick="ajaxCartPla.add( $('#noattr_ct_matrix_{$id_product}_idProduct').val(), $('#noattr_ct_matrix_{$id_product}_idCombination').val(), true, '#noattr_ct_matrix_{$id_product}_submit', $(this).parent().parent().find('#noattr_ct_matrix_{$id_product}_idQty').val(), null);"{/if}>{l s='Add' mod='pla'}</div>
                                    {else}
                                        <div class="btn btn-primary btn-sm ct_submit {if ($pla_product.quantity <=0 && $allow_oosp==0) || $pla_product.available_for_order==0} ct_submit_nostock{/if}" id="noattr_ct_matrix_{$pla_product.id_product}_submit" {if ($pla_product.available_for_order==1) && (($allow_oosp==1 && $pla_product.quantity>=0) || ($allow_oosp==1 && $pla_product.quantity<=0) || ($allow_oosp==0 && $pla_product.quantity>0))}onclick="noattr_ct_matrix_{$pla_product.id_product}.submit();"{/if}>{l s='Add' mod='pla'}</div>
                                    {/if}
                                </td>
                            {/if}
                        </form>
                    </tr>
                    </tbody>
                </table>
            {/if}
        {/if}
    {/if}
    {if $ajax !=1 || $pla_amazzingfilter == "amazzingfilter"}
    {if ($pla_dropdown == 1 && count($pla_matrix)>0) || ($pla_dropdown == 1 && $pla_noattr ==1)}
</div>
    {/if}
{/if}