{extends file='catalog/_partials/miniatures/product.tpl'}

{block name='product_price_and_shipping'}
    {$smarty.block.parent}

    <div>
        <form method="post" action="{$urls.pages.cart}" id="add-to-cart-or-refresh">
            <input type="hidden" name="token" value="{$static_token}">
            <input type="hidden" name="id_product" value="{$product['id_product']}">
            <input type="hidden" name="id_customization" value="0">
            <input type="hidden" name="qty" value="1">
            <button class="btn btn-primary add-to-cart" data-button-action="add-to-cart" type="submit">
                <i class="material-icons shopping-cart">shopping_cart</i>
                {l s='Add to cart' mod='pshowupsell'}
            </button>
        </form>
    </div>
{/block}
