{if $chart}
    {if $is15}
        <a id="open-chart" class="button" type="button" data-id-product="{$id_product}" data-lang="{$lang}" data-currency='{$currency}'>
            {l s='Show Price Chart' mod='seigipricehistory'}
        </a>
    {else}
        <button id="open-chart" class="btn btn-block btn-info" type="button" data-id-product="{$id_product}" data-lang="{$lang}" data-currency='{$currency}'>
            {l s='Show Price Chart' mod='seigipricehistory'}
        </button>
    {/if}
{/if}
{if isset($lowest_price) && $lowest_price != false}
    {include file="./price_text.tpl" nocache}
{/if}
