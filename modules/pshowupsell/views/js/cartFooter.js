$(function () {

  function updateIndividualCrossSellProducts() {

    for (let pshowupsell_id in pshowupsell_data) {
      if (pshowupsell_id.indexOf('product.indi.') !== 0) {
        continue;
      }

      let item = pshowupsell_data[pshowupsell_id];
      let cartListIndex = parseInt(pshowupsell_id.replace('product.indi.', ''));
      let cartListObj = $('#content-wrapper .cart-items .cart-item').eq(cartListIndex);

      let cartListItemName = cartListObj.find('.product-line-info a').parent();

      console.log(cartListObj.find('.product-line-info a').html());

      let idProduct = item.id_product;
      let idProductAttribute = item.id_product_attribute;

      // remove cover
      cartListObj.find('.product-image').html('').addClass('xsell-product-image');

      // remove 'trash' button
      cartListObj.find('.cart-line-product-actions').remove();

      // remove quantity input
      let qtyContainer = cartListObj.find('.product-line-actions .qty');
      qtyContainer.find('.input-group').remove();

      // put selector in the product's name place
      let crossSellProductSelectorOptions = ['<option value="0">' + item.label_default + '</option>'];
      for (let i in item.crossSellProducts) {
        if (!item.crossSellProducts.hasOwnProperty(i)) {
          continue;
        }
        let product = item.crossSellProducts[i];
        let selected = product.selected ? 'selected' : '';
        crossSellProductSelectorOptions.push(
          `<option value="${product.id_product}-${product.id_product_attribute}" ${selected}>${product.name}</option>`
        );
      }
      let crossSellProductSelector = `
        <select class="form-control xsell-indi-selector" data-id-product="${idProduct}"
            data-id-product-attribute="${idProductAttribute}">
            ${crossSellProductSelectorOptions.join('')}
        </select>
        `;
      cartListItemName.html(crossSellProductSelector);
    }
  }

  prestashop.on('updatedCart', updateIndividualCrossSellProducts);

  updateIndividualCrossSellProducts();

  $(document).on('change', 'select.xsell-indi-selector', function () {
    let xSellId = $(this).val().split('-');
    let data = {
      "ajax": "1",
      "action": "IndividualCrossSellProductConnection",
      "id_product": $(this).data('id-product'),
      "id_product_attribute": $(this).data('id-product-attribute'),
      "xid_product": xSellId[0],
      "xid_product_attribute": xSellId[1],
    };
    $.ajax({
      "method": "get",
      "url": pshowupsell_data.frontControllerUrl,
      "data": data,
      "contentType": "application/json; charset=utf-8",
      "dataType": "json"
    }).always(() => {
      // prestashop.emit('updateCart', {reason: {}});
      document.location.reload();
    });
  });

});
