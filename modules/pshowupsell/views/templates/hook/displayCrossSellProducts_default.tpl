{if count($products)}
    {foreach from=$products item="ruleProducts" key='ruleId'}
        {foreach from=$ruleProducts item="product"}
            {include file='catalog/_partials/miniatures/product.tpl' product=$product}
        {/foreach}
    {/foreach}
{/if}