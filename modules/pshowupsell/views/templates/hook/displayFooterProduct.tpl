{if count($products)}
    {foreach from=$products item="ruleProducts" key='ruleId'}
        <p>&nbsp;</p>
        <section class="cross-sell-product-sets clearfix">
            <h2 class="h2 products-section-title text-uppercase">
                {getCrossSellRuleName rule_id=$ruleId}
            </h2>
            <p>&nbsp;</p>
            <div class="products">
                {foreach from=$ruleProducts item="product"}
                    <div class="row cross-sell-product-set">
                        <div class="col-md-4">
                            {include file='./_partials/miniatures/product_footerProduct.tpl' product=$primaryProduct noCombination=1}
                        </div>
                        <div class="col-md-1">
                            <br>
                            <br>
                            <br>
                            <i class="material-icons">add</i>
                        </div>
                        <div class="col-md-4">
                            {include file='./_partials/miniatures/product_footerProduct.tpl' product=$product}
                        </div>
                        <div class="col-md-1">
                            <br>
                            <br>
                            <br>
                            <i class="material-icons">forward</i>
                        </div>
                        <div class="col-md-2">

                            <small><s>{$prices[$product['id_product']]['sum_regular']}</s></small>
                            {$prices[$product['id_product']]['sum_discounted']}
                            <br>
                            <br>

                            <form method="post" action="{$urls.pages.cart}">
                                <input type="hidden" name="token" value="{$static_token}">
                                <input type="hidden" name="product_id_1" value="{$primaryProduct['id_product']}">
                                <input type="hidden" name="product_id_2" value="{$product['id_product']}">
                                <input type="hidden" name="product_attribute_id_2" value="{$product['id_product_attribute']}">
                                <button class="btn btn-primary" type="submit" data-button-action="add-set-to-cart">
                                    <i class="material-icons">shopping_cart</i><br>
                                    Dodaj zestaw<br>
                                    do koszyka
                                </button>
                            </form>
                        </div>
                    </div>
                {/foreach}
            </div>
        </section>
    {/foreach}
{/if}

{if isset($debug) && $debug}
    <p>&nbsp;</p>
    <div>
        <h3>UpSell Debug</h3>
        {$debug nofilter}
    </div>
{/if}
