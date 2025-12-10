{if count($products)}
  {foreach from=$products item="ruleProducts" key='ruleId'}
    <h3>
      {$rules[$ruleId]->getName()}
    </h3>
    <ul class="list-unstyled" itemscope itemtype="http://schema.org/ItemList">
      <li>
        <div class="radio">
          <label>
            <input type="radio" class="xsell_indi_selector" name="xsell_rule_{$ruleId}"
                   data-id-product="{$productAddedToCart_id_product}"
                   data-id-product-attribute="{$productAddedToCart_id_product_attribute}"
                   data-xid-product="0" data-xid-product-attribute="0" checked>
            {$rules[$ruleId]->getRuleTypeIndiProDefaultLabel()}
          </label>
        </div>
      </li>
      {foreach from=$ruleProducts item="product"}
        <li itemscope itemtype="http://schema.org/ListItem">
          <meta itemprop="name" content="{$product.name}" />
          <meta itemprop="price" content="{$product.price_amount}" />
          <div class="radio">
            <label>
              <input type="radio" class="xsell_indi_selector" name="xsell_rule_{$ruleId}"
                     data-id-product="{$productAddedToCart_id_product}"
                     data-id-product-attribute="{$productAddedToCart_id_product_attribute}"
                     data-xid-product="{$product.id_product}"
                     data-xid-product-attribute="{$product.id_product_attribute}">
              {$product.name} (+ {$product.price})
            </label>
          </div>
        </li>
      {/foreach}
    </ul>
  {/foreach}
{/if}
