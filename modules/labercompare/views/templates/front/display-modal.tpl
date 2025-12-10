{if Configuration::get('PS_CATALOG_MODE')}
    {$is_catalog_mode		=	'ok'}
{/if}
{$urls['pages']['cart'] 	=	Context::getContext()->link->getPageLink('cart')}
{$static_token				=	Tools::getToken(false)}
<div id="moda_compare" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                {l s='Compare products'}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="mycompare">
                    {if $list_products}
                        <div id="js-compare-table">
                            <div class="compare-table-actions hidden">
                                <a href="javascript:void(0)" class="js-compare-remove-all"
                                   data-url="{url entity='module' name='labercompare' controller='actions'}">
                                    <i class="material-icons">&#xE872;</i> {l s='Remove all products' mod='labercompare'}
                                </a>
                            </div>
                            <div class="table-container">
                                <table>
                                        <tr>
                                            <td></td>
                                            {foreach from=$list_products item="product"} 
                                                <td class="comparison_infos js-compare-{$product.id_product}">
                                                    {include 'module:labercompare/views/templates/front/product.tpl' product=$product}
                                                </td>
                                            {/foreach}	
                                        </tr>
                                    <tr class="comparison_header">
                                        <td>
                                            {l s='Description Short:'}
                                        </td>
                                        {foreach from=$list_products item="product"} 
                                            <td class="js-compare-{$product.id_product} text-xs-center">
                                                {$product.description_short nofilter}
                                            </td>
                                        {/foreach}	
                                    </tr>
                                    <tr class="comparison_header">
                                        <td>
                                            {l s='Availability:'}
                                        </td>
                                        {foreach from=$list_products item="product"} 
                                            <td class="js-compare-{$product.id_product} text-xs-center">
                                                 {if $product.show_availability && $product.availability_message}
                                                    {if $product.availability == 'available'}
                                                        <span class="success">
                                                    {elseif $product.availability == 'last_remaining_items'}
                                                        <span class="warning">
                                                    {else}
                                                        <span class="error">
                                                    {/if}
                                                            {$product.availability_message}
                                                        </span>
                                                  {/if}
                                            </td>
                                        {/foreach}	
                                    </tr>
                                {if $ordered_features}
                                    <tr class="comparison_header">
                                        <td>
                                            {l s='Features:'}
                                        </td>
                                        {foreach from=$list_products item=product}
                                            <td class="js-compare-{$product.id_product} text-xs-center"></td>
                                        {/foreach}	
                                    </tr>
                                    {foreach from=$ordered_features item=feature}
                                        <tr>
                                            <td>
                                                {$feature.name|escape:'html':'UTF-8'}
                                            </td>
                                            {foreach from=$list_products item=product name=for_products}
                                                {assign var='product_id' value=$product.id_product}
                                                {assign var='feature_id' value=$feature.id_feature}
                                                {if isset($product_features[$product_id])}
                                                    {assign var='tab' value=$product_features[$product_id]}
                                                    <td class="comparison_infos js-compare-{$product.id_product}">
                                                        {if (isset($tab[$feature_id]))} {$tab[$feature_id]|escape:'html':'UTF-8'}{/if}
                                                    </td>
                                                {else}
                                                    <td class="comparison_infos js-compare-{$product.id_product}"></td>
                                                {/if}
                                            {/foreach}
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr>
                                        <td></td>
                                        <td colspan="{$list_products|@count}" class="text-center">{l s='No features to compare'}</td>
                                    </tr>
                                {/if}
                                {hook h='extraProductComparison' list_ids_product=$list_ids_product}
                                </table>
                            </div>
                        </div>
                        <p id="js-compare-warning" style="display:none;" class="alert alert-warning">{l s='There are no products selected for comparison.'}</p>
                    {else}
                        <p class="alert alert-warning">{l s='There are no products selected for comparison.'}</p>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>