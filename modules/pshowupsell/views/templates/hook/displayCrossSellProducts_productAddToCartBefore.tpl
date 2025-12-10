{if count($products)}
    <div class="cross_sell_products">
        {foreach from=$products item="ruleProducts" key='ruleId'}
            <div class="cross_sell_products_checkbox">
                <p class="title">
                    {getCrossSellRuleName rule_id=$ruleId}
                </p>
                {if count($ruleProducts) > 1}

                    {assign var='availableValues' value=[0 => 'No, thanks']}
                    {foreach from=$ruleProducts item="product"}
                        {if isset($product['regular_price'])}
                            {assign var='label' value='%s <s>%s</s> %s'|sprintf:$product['name']:$product['regular_price']:$product['price_wt']}
                        {else}
                            {assign var='label' value='%s %s'|sprintf:$product['name']:$product['price_wt']}
                        {/if}

                        {appendToArray array_name='availableValues' key='cross_sell_product_'|cat:$product['id_product'] value=$label}
                    {/foreach}

                    {include
                    file='./_partials/form-fields.tpl'
                    field=[
                    'type' => 'radio-buttons',
                    'availableValues' => $availableValues,
                    'label' => '',
                    'required' => false,
                    'value' => 0,
                    'name' => 'cross_sell_product_'|cat:$ruleId,
                    'value' => false,
                    'errors' => []
                    ]
                    }

                {else}

                    {foreach from=$ruleProducts item="product"}
                        {if isset($product['regular_price'])}
                            {assign var='label' value='%s <s>%s</s> %s'|sprintf:$product['name']:$product['regular_price']:$product['price_wt']}
                        {else}
                            {assign var='label' value='%s %s'|sprintf:$product['name']:$product['price_wt']}
                        {/if}
                        {assign var='productId' value=$product['id_product']}
                    {/foreach}

                    {include
                    file='./_partials/form-fields.tpl'
                    field=[
                    'type' => 'checkbox',
                    'label' => $label,
                    'required' => false,
                    'value' => '1',
                    'name' => 'cross_sell_product_'|cat:$productId,
                    'value' => false,
                    'errors' => []
                    ]
                    }

                {/if}
            </div>
        {/foreach}
    </div>
{/if}
