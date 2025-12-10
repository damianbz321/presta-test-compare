{*
*  2019 Fones Software
*
*  @author    Fones Software <ja@fones.pl>
*  @copyright 2019 Fones Software
*  @license   https://opensource.org/licenses/MIT
*}

{block name="trustisto"}
  {if isset($trustisto.sid)}

    <!-- Trustisto PrestaShop 1.7 Plugin 1.3.0 -->
    <script>
      {literal}
      (function(a,b,c,d,e,f,g,h,i){
        h=a.SPT={u:d},a.SP={init:function(a,b){h.ai=a;h.cb=b},
        go:function(){(h.eq=h.eq||[]).push(arguments)}}
        g=b.getElementsByTagName(c)[0],f=b.createElement(c),
        f.async=1,f.src="//js"+d+e,i=g.parentNode.insertBefore(f,g)
      })(window,document,"script",".trustisto.com","/socialproof.js");
      {/literal}
      
      SP.init("{$trustisto.sid}");
    </script>

    {if $page.page_name === "index"}
      <script>
        SP.go("startPage");
      </script>
    {/if}

    {if $page.page_name === "category"}
      <script>
        SP.go("categoryPage");
      </script>
    {/if}
      
    {if $page.page_name === "search"}
      <script>
        SP.go("searchPage");
      </script>
    {/if}

    {if $page.page_name === "product"}
      {assign var="trustisto_product_detail" value=Trustisto::getProduct($smarty.get.id_product)}
      <script>
        SP.go("productPage", {
          productId: "{$smarty.get.id_product}",
          product: "{$trustisto_product_detail.name}",
          link: "{$trustisto_product_detail.link}",
          image: "//{$trustisto_product_detail.image_url}"
        });
      </script>
    {/if}

    {if $page.page_name === "cart"}
      <script>
        SP.go("basketPage");
      </script>
    {/if}

    {if $page.page_name === "order-confirmation"}
      <script>
        SP.go("thankYouPage", {
          order: {
            id: "{$order.details.id}"
          },
          client: {
            firstname: "{$order.addresses.invoice.firstname}",
            city: "{$order.addresses.invoice.city}"
          },
          basket: [
            {foreach from=$order.products item="products"}
              {assign var="product" value=Trustisto::getProduct($products.product_id)}
              {
                productId: "{$products.product_id}",
                product: "{$products.product_name}",
                link: "{$product.link}",
                image: "{$products.cover.large.url}",
                quantity: "{$products.quantity}",
                price: "{$products.product_price_wt}",
                sum: "{$products.total_price_tax_incl}"
              },
            {/foreach}
          ]
        });
      </script>
    {/if}

  {/if}
{/block}