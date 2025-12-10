{*
* 2007-2015 PrestaShop
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
* @copyright 2007-2015 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
<div id="pushoncart_product_attributes">
    {foreach from=$promo->product_info.all_the_atts.combinations key=k item=v}
        <label for="pushoncart_product_attribute[{$k|escape:'htmlall':'UTF-8'}]">{$promo->product_info.all_the_atts.group_names.$k}</label>
        <select id="pushoncart_product_attribute[{$k|escape:'htmlall':'UTF-8'}]" name="pushoncart_product_attribute[{$k|escape:'htmlall':'UTF-8'}]" 
        onchange="reloadImageAndPrice({$promo->parameters.id_product|escape:'htmlall':'UTF-8'}, '{$k|escape:'htmlall':'UTF-8'}', {$promo->product_info.all_the_atts.color_group_ids_js|escape:'htmlall':'UTF-8'}, {$promo->id_pushoncart|escape:'htmlall':'UTF-8'});" class="pushoncart-selected-product-attribute_{$promo->parameters.id_product|escape:'htmlall':'UTF-8'}">
            {foreach from=$v key=key item=foo}
                <option value="{$foo|escape:'htmlall':'UTF-8'}" {if $foo|in_array:$promo->product_info.all_the_atts.default_atts}selected{/if}>{$promo->product_info.all_the_atts.names.$k.$key|escape:'htmlall':'UTF-8'}</option>
            {/foreach}
        </select>&nbsp;&nbsp;&nbsp;
    {/foreach}
    <div class="clear">&nbsp;</div>
    <span class="price-percent-reduction small reduction" id="out_of_stock_alert_pushoncart_{$promo->id_pushoncart}" style="width:50%;display:{if $promo->product_info.all_the_atts.default_check_stock eq 0}block{else}none{/if};">{l s='This product is no longer in stock with those attributes but is available with others.' mod='pushoncart'}</span>
</div>         