{extends file='catalog/_partials/miniatures/product.tpl'}

{*{block name='product_name'}*}
{*    {if $page.page_name == 'index'}*}
{*        <h3 class="h3 product-title" itemprop="name"><a href="{$product.url}">{$product.name|truncate:30:'...'}</a></h3>*}
{*    {else}*}
{*        <h2 class="h3 product-title" itemprop="name"><a href="{$product.url}">{$product.name|truncate:30:'...'}</a></h2>*}
{*    {/if}*}
{*    <h4>aaa</h4>*}
{*{/block}*}

{*{block name='quick_view'}{/block}*}

{*{block name='product_variants'}{/block}*}

{*{block name='product_thumbnail'}*}
{*    <div class="col-md-6">*}
{*        {$smarty.block.parent}*}
{*    </div>*}
{*{/block}*}

{block name='product_miniature_item'}
    <article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}"
             data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
        <div class="thumbnail-container row">
            {block name='product_thumbnail'}
                <div class="col-xs-6">
                    {if $product.cover}
                        <a href="{$product.url}" class="thumbnail product-thumbnail">
                            <img
                                    src="{$product.cover.bySize.home_default.url}"
                                    alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
                                    data-full-size-image-url="{$product.cover.large.url}"
                            />
                        </a>
                    {else}
                        <a href="{$product.url}" class="thumbnail product-thumbnail">
                            <img src="{$urls.no_picture_image.bySize.home_default.url}"/>
                        </a>
                    {/if}
                </div>
            {/block}

            {block name='product_description'}
                <div class="col-xs-6">
                    {block name='product_name'}
                        <h2 class="h3 product-title" itemprop="name">
                            <a href="{$product.url}">
                                {$product.name|truncate:50:'...'}
                                {block name='product_attributes'}
                                    {if !isset($noCombination) || !$noCombination}
                                        {foreach from=$product.attributes item='attribute'}
                                            {$attribute.name}
                                        {/foreach}
                                    {/if}
                                {/block}
                            </a>
                        </h2>
                    {/block}

                    {block name='product_price_and_shipping'}
                        {if $product.show_price}
                            <div class="product-price-and-shipping">
                                {if $product.has_discount}
                                    {hook h='displayProductPriceBlock' product=$product type="old_price"}
                                    <span class="sr-only">{l s='Regular price' d='Shop.Theme.Catalog'}</span>
                                    <span class="regular-price">{$product.regular_price}</span>
                                    {if $product.discount_type === 'percentage'}
                                        <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
                                    {elseif $product.discount_type === 'amount'}
                                        <span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
                                    {/if}
                                {/if}

                                {hook h='displayProductPriceBlock' product=$product type="before_price"}

                                <span class="sr-only">{l s='Price' d='Shop.Theme.Catalog'}</span>
                                <span itemprop="price" class="price">
                                  {$product.price}
                                </span>

                                {hook h='displayProductPriceBlock' product=$product type='unit_price'}

                                {hook h='displayProductPriceBlock' product=$product type='weight'}
                            </div>
                        {/if}
                    {/block}

                    {block name='product_reviews'}
                        {hook h='displayProductListReviews' product=$product}
                    {/block}
                </div>
            {/block}

            {block name='product_flags'}
                <ul class="product-flags">
                    {foreach from=$product.flags item=flag}
                        <li class="product-flag {$flag.type}">{$flag.label}</li>
                    {/foreach}
                </ul>
            {/block}

        </div>
    </article>
{/block}
