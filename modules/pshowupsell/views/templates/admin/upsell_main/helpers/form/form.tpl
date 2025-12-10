{extends file="helpers/form/form.tpl"}

{block name="label"}
    {if isset($input.legend)}
        <label class="col-lg-12 condition-legend-label">
            {$input.legend}
        </label>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="field"}
    {if !isset($input.legend)}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="defaultForm"}
    <div id="pshowupsell-js-loader">
        <div class="lds-dual-ring"></div>
    </div>
    <div class="filter-blur">{$smarty.block.parent}</div>
{/block}

{block name="input"}
    {if $input.type == 'product_list'}
        <div class="table">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th>{l s='ID' mod='pshowupsell'}</th>
                    <th>{l s='Reference' mod='pshowupsell'}</th>
                    <th>{l s='Name' mod='pshowupsell'}</th>
                    <th>{l s='Combination' mod='pshowupsell'}</th>
                    {if $input.name == 'action_product_list'}
                        <th class="product_item_list_price">{l s='New price' mod='pshowupsell'}</th>
                    {/if}
                    <th></th>
                </tr>
                </thead>
                <tbody id="{$input.name}">
                <tr class="{$input.name}_item_template" style="display: none">
                    <td>
                        <input type="hidden" name="|#list_prefix#|item_list[|#item_num#|][id_product]"
                               value="|#id_product#|">
                        |#id_product#|
                    </td>
                    <td>|#reference#|</td>
                    <td>|#name#|</td>
                    <td>
                        <select class="product-combination-selector"
                                name="|#list_prefix#|item_list[|#item_num#|][id_product_attribute]"
                                id="|#list_prefix#|item_list[|#item_num#|][id_product_attribute]">
                            |#combinations#|
                        </select>
                    </td>
                    {if $input.name == 'action_product_list'}
                        <td class="product_item_list_price">
                            <div class="input-group">
                                <input type="text" class="|#list_prefix#|item_list_price form-control"
                                   name="|#list_prefix#|item_list[|#item_num#|][price]"
                                   id="|#list_prefix#|item_list[|#item_num#|][price]"
                                   value="|#price#|">
                                <span class="input-group-addon">
								    {$currency_sign}
								</span>
                            </div>
                        </td>
                    {/if}
                    <td>
                        <button class="del-product btn btn-default pull-right" data-id="|#id_product#|">
                            <i class="icon-remove"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        {if $input.name == 'action_product_list'}
            <script>
                window.action_product_item_list = JSON.parse('{$fields_value[$input.name]|json_encode}');
            </script>
        {elseif $input.name == 'condition_product_list'}
            <script>
                window.condition_product_item_list = JSON.parse('{$fields_value[$input.name]|json_encode}');
            </script>
        {/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="after"}
    <script>
        let productSearchUrl = '{$link->getAdminLink('PShowUpsellMain')}';
        let productListDetails = {$product_list_details|json_encode};
    </script>
{/block}
