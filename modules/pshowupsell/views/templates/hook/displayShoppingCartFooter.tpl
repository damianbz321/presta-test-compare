{if count($products)}
    <p>&nbsp;</p>
    <h3>{l s='Get products with low prices!' mod='pshowupsell'}</h3>
    <p>&nbsp;</p>
    <div class="featured-products">
        <div class="products row">
            {foreach from=$products item="ruleProducts"}
              {foreach from=$ruleProducts item="product"}
                {include file='./_partials/miniatures/product_shoppingCartFooter.tpl' product=$product}
              {/foreach}
            {/foreach}
        </div>
    </div>
{/if}

{if isset($debug) && $debug}
    <p>&nbsp;</p>
    <div>
        <h3>UpSell Debug</h3>
        {$debug nofilter}
    </div>
{/if}
