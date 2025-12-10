{assign var="valDisc" value=($disc_type === 1 || $disc_type === 3)}
{assign var="perDisc" value=($disc_type > 1)}
<div id="lowest-price" {if $text_enh}class="ext-visibility" {/if}data-id-product="{$id_product}" data-lang="{$lang}" data-currency='{$currency}'>
    <span class="sph-line">
        {if $disc_type > 0}
            {if $disc_val_raw != 0}
                {if $valDisc}<span class="sph-discount val-disc{if $disc_val_raw < 0} red{/if}">{$disc_val}</span>{/if}
                {if $perDisc}<span class="sph-discount perc-disc{if $disc_per < 0} red{/if}">{$disc_per}%</span>{/if}
            {/if}
        {/if}
        {if $text_type == 0 || $on_sale === false}
            {if $custom_price_text}
                {$custom_price_text nofilter}
            {else}
                <span class="price-text">
                {if $disc_type > 0}
                    {if $disc_val_raw == 0}
                        {l s='Same as lowest price in last %s days' mod='seigipricehistory' sprintf=[$show_length]}
                    {else}
                        {l s='Comapared to lowest price in last %s days' mod='seigipricehistory' sprintf=[$show_length]}
                    {/if}
                {else}
                    {l s='Lowest price in last %s days' mod='seigipricehistory' sprintf=[$show_length]}
                {/if}
                    :</span> <span class="price lowest-price">{$lowest_price['price']}</span>
            {/if}
        {elseif $text_type == 1 & $on_sale}
            {if $custom_price_during}
                {$custom_price_during nofilter}
            {else}
                <span class="price-text">
                    {if $disc_type > 0}
                        {if $disc_val_raw == 0}
                            {l s='Same as lowest price up to %s days before reduction begining' mod='seigipricehistory' sprintf=[$show_length]}
                        {else}
                            {l s='Comapared to lowest price up to %s days before reduction begining' mod='seigipricehistory' sprintf=[$show_length]}
                        {/if}
                    {else}
                        {l s='Lowest price up to %s days before reduction begining' mod='seigipricehistory' sprintf=[$show_length]}
                    {/if}
                    :</span> <span class="price lowest-price">{$lowest_price['price']}
                </span>
        {/if}
        {elseif $text_type == 2 & $on_sale}
            {if $custom_price_before}
                {$custom_price_before nofilter}
            {else}
                <span class="price-text">
                    {if $disc_type > 0}
                        {if $disc_val_raw == 0}
                            {l s='Same as lowest price from %s days before reduction' mod='seigipricehistory' sprintf=[$show_length]}
                        {else}
                            {l s='Comapared to lowest price from %s days before reduction' mod='seigipricehistory' sprintf=[$show_length]}
                        {/if}
                    {else}
                        {l s='Lowest price from %s days before reduction' mod='seigipricehistory' sprintf=[$show_length]}
                    {/if}
                    :</span> <span class="price lowest-price">{$lowest_price['price']}
                </span>
            {/if}
        {/if}
    </span>
    {if $show_sale && $lowest_price['sale'] !== 0}
        <span id="lowest-discount" class="sph-line">
            {if $disc_type > 0}
                {if $sale_disc_val_raw != 0}
                    {if $valDisc}<span class="sph-discount val-disc{if $sale_disc_val_raw < 0} red{/if}">{$sale_disc_val}</span>{/if}
                    {if $perDisc}<span class="sph-discount perc-disc{if $sale_disc_per < 0} red{/if}">{$sale_disc_per}%</span>{/if}
                {/if}
            {/if}
            {if $text_type == 0 || $on_sale === false}
                {if $custom_sale_text}
                    {$custom_sale_text nofilter}
                {else}
                    <span class="price-text">
                        {if $disc_type > 0}
                            {if $disc_val_raw == 0}
                                {l s='Same as lowest discount in last %s days' mod='seigipricehistory' sprintf=[$show_length]}
                            {else}
                                {l s='Comapared to lowest discount in last %s days' mod='seigipricehistory' sprintf=[$show_length]}
                            {/if}
                        {else}
                            {l s='Lowest discount in last %s days' mod='seigipricehistory' sprintf=[$show_length]}
                        {/if}
                            :</span> <span class="price lowest-discount">{$lowest_price['sale']}
                    </span>
                {/if}
            {elseif $text_type == 1 & $on_sale}
                {if $custom_sale_during}
                    {$custom_sale_during nofilter}
                {else}
                    <span class="price-text">
                        {if $disc_type > 0}
                            {if $disc_val_raw == 0}
                                {l s='Same as lowest discount up to %s days before reduction begining' mod='seigipricehistory' sprintf=[$show_length]}
                            {else}
                                {l s='Comapared to lowest discount up to %s days before reduction begining' mod='seigipricehistory' sprintf=[$show_length]}
                            {/if}
                        {else}
                            {l s='Lowest discount up to %s days before reduction begining' mod='seigipricehistory' sprintf=[$show_length]}
                        {/if}
                        :</span> <span class="price lowest-discount">{$lowest_price['sale']}
                    </span>
                {/if}
            {elseif $text_type == 2 & $on_sale}
                {if $custom_sale_before}
                    {$custom_sale_before nofilter}
                {else}
                    <span class="price-text">
                        {if $disc_type > 0}
                            {if $disc_val_raw == 0}
                                {l s='Same as lowest discount %s days before reduction' mod='seigipricehistory' sprintf=[$show_length]}
                            {else}
                                {l s='Comapared to lowest discount %s days before reduction' mod='seigipricehistory' sprintf=[$show_length]}
                            {/if}
                        {else}
                            {l s='Lowest discount %s days before reduction' mod='seigipricehistory' sprintf=[$show_length]}
                        {/if}
                        :</span> <span class="price lowest-discount">{$lowest_price['sale']}
                    </span>
                {/if}
            {/if}
        </span>
    {/if}
    {if isset($sp_type)}
        <br>
        <span class="sph-line">
        {if $sp_type == 1}
            <span class="price-text">{l s='This price reduction will end on' mod='seigipricehistory'}:</span> <span class="price">{$sp_end}</span>
        {/if}
        {if $sp_type > 1}
            <span class="price-text">{l s='Time until reduction will end' mod='seigipricehistory'}:</span> <span class="price">
            {if $sp_type == 3 || $sp_diff['d'] > 0}
                {if $sp_diff['d'] > 0}
                    {if $sp_diff['d'] == 1}
                        {l s='%d day' mod='seigipricehistory' sprintf=[$sp_diff['d']]}
                    {else}
                        {l s='%d days' mod='seigipricehistory' sprintf=[$sp_diff['d']]}
                    {/if}
                {/if}
            {/if}
            {if $sp_type == 3 || $sp_diff['d'] == 0}
                {if $sp_diff['h'] > 1}
                    {if $sp_diff['h'] == 1}
                        {l s='%d hour' mod='seigipricehistory' sprintf=[$sp_diff['h']]}
                    {else}
                        {l s='%d hours' mod='seigipricehistory' sprintf=[$sp_diff['h']]}
                    {/if}
                {/if}
                {if $sp_diff['i'] == 1}
                    {l s='%d minute' mod='seigipricehistory' sprintf=[$sp_diff['i']]}
                {else}
                    {l s='%d minutes' mod='seigipricehistory' sprintf=[$sp_diff['i']]}
                {/if}
            {/if}
        </span>
        {/if}
        </span>
    {/if}
    {if isset($lowest_price['last_price']) && $lowest_price['last_price']}
        <br>
        <span class="sph-line">
            <span class="price-text">{l s='Last Price' mod='seigipricehistory'}:</span> <span class="price last-price">{$lowest_price['last_price']}</span>
        </span>
    {/if}
</div>
