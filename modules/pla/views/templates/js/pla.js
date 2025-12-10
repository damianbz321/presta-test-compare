/*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

$(document).ready(function () {
    prestashop.on('updateCart', function (event) {
        var $body = $('body');
        $('.modal-backdrop').remove();
        $body.removeClass('modal-open');
        $body.one('click', '#blockcart-modal', function (event) {
            $('#blockcart-modal').remove();
            $('.modal-backdrop').remove();
            $body.removeClass('modal-open');
        });
        $body.one('click', '.modal-backdrop', function (event) {
            $('.modal-backdrop').remove();
            $('#blockcart-modal').remove();
            $body.removeClass('modal-open');
        });
    });

    prepareBlockLayeredAfterPLA();
    prestashop.on("updateProductList", function () {
        prepareBlockLayeredAfterPLA();
    });
});


var ajaxCartPla = {
    add: function (idProduct, idCombination, addedFromProductPage, callerElement, quantity, whishlist) {
        var $body = $('body');
        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: cart_url + '?rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: "json",
            data: 'action=update&add=1&ajax=true&qty=' + ((quantity && quantity != null) ? quantity : '1') + '&id_product=' + idProduct + '&token=' + static_token + ((idCombination && idCombination != null) ? '&ipa=' + idCombination : '' + '&id_customization=' + ((typeof customizationId !== 'undefined') ? customizationId : 0)) + ((idCombination && idCombination != null) ? '&id_product_attribute=' + idCombination : ''),
            success: function (jsonData, textStatus, jqXHR) {
                $('.pla_matrix_dropdown_block.nobrdown').click();
                if (pla_addtocart_hide == 1) {
                    $.fancybox.close();
                }
                prestashop.emit('updateCart', {
                    reason: {
                        idProduct: idProduct,
                        idProductAttribute: idCombination,
                        linkAction: 'add-to-cart'
                    },
                    resp: jsonData,
                });
            }
        });
    }
}

function prepareBlockLayeredAfterPLA() {

    if (pla_addtocart_hide == 1) {
        $('.pla_addtocart .ct_submit').click(function () {
            {
                $.fancybox.close();
            }
        });
    }

    $(".pla_addtocart .qty").off();
    $(".pla_addtocart .qty").change(function () {
        if (+$(this).attr('max') < +$(this).val()) {
            $(this).val(+$(this).attr('max'));
        }
        if (+$(this).val() > 0) {
            $(this).parent().parent().attr('class', 'pla_matrix_row ctp_checked');
        } else {
            $(this).parent().parent().attr('class', 'pla_matrix_row');
        }
    });

    $('.pla_submit_bulk').off();
    $('.pla_submit_bulk').click(function () {
        $(this).parent().parent().parent().find('.qty').each(function () {
            if ($(this).val() > 0) {
                ajaxCartPla.add(
                    $(this).parent().find('input[name=id_product]').val(),
                    $(this).parent().find('input[name=id_product_attribute]').val(),
                    false,
                    '#ct_matrix_' + $(this).parent().find('input[name=id_product_attribute]').val(),
                    $(this).parent().find('.qty').val(),
                    null
                );
                if (pla_addtocart_hide == 1) {
                    $.fancybox.close();
                }
                $(this).val(0);
                $(this).parent().parent().removeClass('ctp_checked');
            }
        });
    });

    $(".pla_addtocart .qty").on("change paste", function() {

        if (+$(this).val() >= 0) {
            console.log($(this).val())
            if (+$(this).val() % ($(this).data('multiply') == 1 ? $(this).data('min'):1) != 0) {
                $(this).val(Math.round($(this).val()/($(this).data('multiply') == 1 ? $(this).data('min'):1))*($(this).data('multiply') == 1 ? $(this).data('min'):1));
            }
        } else {
            if ($(this).data('defqty') == 0) {
                $(this).val($(this).data('multiply') == 1 ? $(this).data('min') : 0);
            } else {
                $(this).val($(this).data('multiply') == 1 ? $(this).data('min') : $(this).data('defqty'));
            }
        }
    });

    $(".pla_addtocart .decrease_quantity").off();
    $(".pla_addtocart .decrease_quantity").click(function () {
        if ($(this).parent().find('.qty').val() > 0) {
            $(this).parent().find('.qty').val(+$(this).parent().find('.qty').val() - ($(this).parent().find('.qty').data('multiply') == 1 ? $(this).parent().find('.qty').data('min'):1));
        }
        $(this).parent().find('.qty').trigger('change');
    });

    $(".pla_addtocart .increase_quantity").off();
    $(".pla_addtocart .increase_quantity").click(function () {
        if (+$(this).parent().find('.qty').attr('max') >= (+$(this).parent().find('.qty').val() + ($(this).parent().find('.qty').data('multiply') == 1 ? $(this).parent().find('.qty').data('min'):1))) {
            $(this).parent().find('.qty').val(+$(this).parent().find('.qty').val() + ($(this).parent().find('.qty').data('multiply') == 1 ? $(this).parent().find('.qty').data('min'):1));
        }
        $(this).parent().find('.qty').trigger('change');
    });

    $(".pla_matrix_dropdown .pla_matrix_dropdown_block:not('.pla_popup')").off();
    $(".pla_matrix_dropdown .pla_matrix_dropdown_block:not('.pla_popup')").click(function () {
        $(".pla_matrix_dropdown .pla_matrix_dropdown_block.nobrdown:not('.pla_popup,.hasPla')").click();
        $(this).toggleClass("nobrdown");
        $(this).find('div').toggleClass("cdown");
        $(this).parent().find('.pla_matrix').toggleClass('pla_matrix_show');
    }, function () {
        $(this).toggleClass("nobrdown");
        $(this).find('div').toggleClass("cdown");
        $(this).parent().find('.pla_matrix').toggleClass('pla_matrix_show');
    });

    $('.pla_matrix_dropdown_block.pla_popup').off();
    $('.pla_matrix_dropdown_block.pla_popup').click(function () {
        element = $(this);
        $.fancybox({
            helpers: {
                overlay: {
                    locked: false,
                }
            },
            fixed: false,
            autoCenter: false,
            autoScale: false,
            wrapCSS: "pla_fancy",
            href: prestashop.urls.base_url + "modules/pla/ajax_pla.php?ajax=1&idp=" + element.parent().find('.pla_popup').data('productid'),
            type: 'ajax',
            afterShow: function () {
                prepareBlockLayeredAfterPLA()
            },
        });
    });
}