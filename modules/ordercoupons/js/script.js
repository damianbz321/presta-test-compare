/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 * @version   of the vouchers engine: 4.3
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

$(document).ready(function() {
    $("#ordercoupons_p_search").keyup(function () {
        $.post("../modules/ordercoupons/lib/voucherengine/ajax_engine.php", {selectbox_prefix: '', search: $("#ordercoupons_p_search").val()}, function (data) {
            $("#ordercoupons_p_search_results").html(data);
        })
    });
});
