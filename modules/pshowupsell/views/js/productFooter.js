/*globals prestashop*/

function addProductsToCart(url, token, btn, productIds) {
    let productId = productIds[0][0];
    let productAttributeId = productIds[0][1];

    productIds = productIds.splice(1);

    let data = "qty=1&id_product=" + productId ;
    data += "&id_product_attribute=" + productAttributeId;
    data += "&id_customization=0&add=1&action=update&token=" + token;

    $.post(url, data, null, "json").then(function (e) {
        if (!productIds.length) {
            prestashop.emit("updateCart", {
                reason: {
                    idProduct: e.id_product,
                    idProductAttribute: e.id_product_attribute,
                    idCustomization: e.id_customization,
                    linkAction: "add-to-cart",
                    cart: e.cart
                }, resp: e
            });
            if (btn) {
                btn.closest('.cross-sell-product-set').remove();
            }
            if ($('.cross-sell-product-set').length === 0) {
                $('.cross-sell-product-sets').remove();
            }
        } else {
            addProductsToCart(url, token, btn, productIds);
        }
    }).fail(function (e) {
        prestashop.emit("handleError", {eventType: "addProductToCart", resp: e})
    });
}

$(function () {

    $(document).on('click', '[data-button-action="add-set-to-cart"]', function (e) {
        e.preventDefault();

        let productId1 = $('input[name="product_id_1"]');
        let productId2 = $('input[name="product_id_2"]');
        let productAttributeId2 = $('input[name="product_attribute_id_2"]');

        let formToken = productId1.closest('form').find('input[name="token"]').val();
        let formAction = productId1.closest('form').attr('action');

        addProductsToCart(formAction, formToken, $(this), [
            [productId1.val(), 0], // @todo Support primary product combination
            [productId2.val(), productAttributeId2.val()]
        ]);

        return false;
    });

});