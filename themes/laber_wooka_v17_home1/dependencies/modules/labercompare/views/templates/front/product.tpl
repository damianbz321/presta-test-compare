<div class="item-inner-compare">
    <a href="javascript:void(0)" class="js-compare-remove remove_to_compare"
     data-id-product="{$product.id_product|intval}"
    data-url="{url entity='module' name='labercompare' controller='actions'}">
      <i class="fa fa-trash-o"></i>
    </a>
     <div class="js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
     <div class="left-product-compare">
          <a href="{$product.url}" title="{$product.name}">
            <span class="cover_image">
                <img
                  class="img-responsive"
                  src = "{$product.cover.bySize['home_default'].url}"
                  alt=""
                >
            </span>       
          </a>       
        </div>  
        <div class="right-product-compare">       
            <div class="product-description">
                <div class="product_name title_font" itemprop="name"><a href="{$product.url}">{$product.name}</a></div>          
                {if $product.show_price}
                  <div class="product-price-and-shipping">
                    
                    {hook h='displayProductPriceBlock' product=$product type="before_price"}
                    <span class="price">{$product.price}</span>
                    {if $product.has_discount}
                      {hook h='displayProductPriceBlock' product=$product type="old_price"}
                      <span class="regular-price">{$product.regular_price}</span>
                    {/if}
                    {hook h='displayProductPriceBlock' product=$product type='unit_price'}
        
                    {hook h='displayProductPriceBlock' product=$product type='weight'}
                  </div>
                {/if}
            </div>
            
        </div>
        <div class="button-action-product">
            <form action="{$urls.pages.cart}" method="post">
                   {if !isset($is_catalog_mode)}
                    <input type="hidden" name="token" value="{$static_token}">
                    <input type="hidden" name="id_product" value="{$product.id}">
                    {if !$product.quantity && !$product.allow_oosp}
                        {$product['add_to_cart_url'] = null}
                    {/if} 
                    {if $product.customizable == 2 || !empty($product.customization_required)}
                        {$product['add_to_cart_url'] = null}
                    {/if} 
                    <button class="btn" data-button-action="add-to-cart" type="submit" {if !$product.add_to_cart_url}disabled{/if}>
                        {l s='ADD TO CART'}
                    </button>
                  {/if}
            </form>
            </div>
    </div>
</div>