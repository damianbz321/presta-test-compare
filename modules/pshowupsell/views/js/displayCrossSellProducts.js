/* globals prestashop, pshowupsell_data */

$(function () {

  let bodyId = $('body').attr('id');

  if (bodyId === 'product') {
    let block = 0;
    prestashop.addListener('updateCart', function (data) {
      if (block > 0) {
        --block;
        return;
      }
      let currentPageProductId = parseInt($('input#product_page_product_id').val());
      if (data.resp.id_product === currentPageProductId) {
        let productIds = [];
        $('.cross_sell_products input:checked:not([value="0"])').each(function () {
          if ($(this).attr('type') === 'radio') {
            productIds.push(parseInt($(this).val().replace('cross_sell_product_', '')));
          } else {
            productIds.push(parseInt($(this).attr('name').replace('cross_sell_product_', '')));
          }
        });
        if (productIds.length) {
          let form = $('form#add-to-cart-or-refresh');
          let url = form.attr('action');
          let token = form.find('input[name="token"]').eq(0).val();
          block = productIds.length;
          addProductsToCart(url, token, null, productIds);
        }
      }
    });
  }

  $(document).on('change', 'input.xsell_indi_selector[type="radio"]', function () {
    let data = {
      "ajax": "1",
      "action": "IndividualCrossSellProductConnection",
      "id_product": $(this).data('id-product'),
      "id_product_attribute": $(this).data('id-product-attribute'),
      "xid_product": $(this).data('xid-product'),
      "xid_product_attribute": $(this).data('xid-product-attribute'),
    };
    $.ajax({
      "method": "get",
      "url": pshowupsell_data.frontControllerUrl,
      "data": data,
      "contentType": "application/json; charset=utf-8",
      "dataType": "json"
    });
  });

});
