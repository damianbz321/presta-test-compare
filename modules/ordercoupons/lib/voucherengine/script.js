/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-9999 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 * @version   of the vouchers engine: 5.5
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

$(document).ready(function () {
    $(".prod_search").keyup(function () {
        $.post("../modules/" + module_name + "/lib/voucherengine/ajax_engine.php", {
            selectbox_prefix: selectbox_prefix,
            search: $(".prod_search").val(),
            id_shop: voucherengine_id_shop
        }, function (data) {
            $("#prod_search_result").html(data);
        })
    });
    $(".free_gift_search").keyup(function () {
        $.post("../modules/" + module_name + "/lib/voucherengine/ajax_engine.php", {
            selectbox_prefix: selectbox_prefix,
            searchgift: $(".free_gift_search").val(),
            id_shop: voucherengine_id_shop
        }, function (data) {
            $("#free_gift_search_result").html(data);
        })
    });

    $('.' + selectbox_prefix + 'datefrom, ' + '.' + selectbox_prefix + 'dateto').datetimepicker({
        prevText: '',
        nextText: '',
        dateFormat: 'yy-mm-dd',
        // Define a custom regional settings in order to use PrestaShop translation tools
        currentText: 'Now',
        closeText: 'Done',
        ampm: false,
        amNames: ['AM', 'A'],
        pmNames: ['PM', 'P'],
        timeFormat: 'hh:mm:ss tt',
        timeSuffix: '',
        timeOnlyTitle: 'Choose Time',
        timeText: 'Time',
        hourText: 'Hour',
        minuteText: 'Minute',
    });
});


function loadattributes(id) {
    $.post("../modules/" + module_name + "/lib/voucherengine/ajax_engine.php", {
        selectbox_prefix: selectbox_prefix,
        id_product: id,
        id_shop: voucherengine_id_shop
    }, function (data) {
        $("#free_gift_search_result").html(data);
        if (data != '0') {
            $("#" + selectbox_prefix + "_fgc_id_div").show().css('display', 'inline-block');
        }
    })
}

function reduction_type(p, n) {
    var name = p + '' + n;
    var reduction_type_selected = jQuery("input:radio[name=" + name + "]:checked").val();
    if (reduction_type_selected == 1) {
        $("#" + name + "_2").hide(200);
        $("#" + name + "_" + reduction_type_selected).show(200);
        $("#" + p + "apply_discount_to").show(200);
        if ($("#" + p + "excludeSpecials_on").length) {
            $("#" + p + "excludeSpecials_on").parent().parent().show(200);
        }

    }
    if (reduction_type_selected == 2) {
        $("#" + name + "_1").hide(200);
        $("#" + name + "_" + reduction_type_selected).show(200);
        $("#" + p + "apply_discount_to").show(200);
        if ($("#" + p + "excludeSpecials_on").length) {
            $("#" + p + "excludeSpecials_on").parent().parent().hide(200);
        }
    }
    if (reduction_type_selected == 3) {
        $("#" + name + "_1").hide(200);
        $("#" + name + "_2").hide(200);
        $("#" + p + "apply_discount_to").hide(200);
    }
}

function apply_discount_to(name) {
    var apply_discount_to_selected = jQuery("input:radio[name=" + name + "]:checked").val();
    if (apply_discount_to_selected == "specific") {
        $("#" + name + "_specific").show(200);
    } else {
        $("#" + name + "_specific").hide(200);
    }
}

function category_restriction(name) {
    var category_restriction_selected = jQuery("input:radio[name=" + name + "]:checked").val();
    if (category_restriction_selected == "1") {
        $("#" + name + "_cr").show(200);
    } else {
        $("#" + name + "_cr").hide(200);
    }
}

function products_restriction(name) {
    var products_restriction_selected = jQuery("input:radio[name=" + name + "]:checked").val();
    if (products_restriction_selected == "1") {
        $("#" + name + "_pr").show(200);
    } else {
        $("#" + name + "_pr").hide(200);
    }
}

function manufacturers_restriction(name) {
    var manufacturers_restriction_selected = jQuery("input:radio[name=" + name + "]:checked").val();
    if (manufacturers_restriction_selected == "1") {
        $("#" + name + "_mr").show(200);
    } else {
        $("#" + name + "_mr").hide(200);
    }
}

function suppliers_restriction(name) {
    var suppliers_restriction_selected = jQuery("input:radio[name=" + name + "]:checked").val();
    if (suppliers_restriction_selected == "1") {
        $("#" + name + "_mr").show(200);
    } else {
        $("#" + name + "_mr").hide(200);
    }
}

function attributes_restriction(name) {
    var attributes_restriction_selected = jQuery("input:radio[name=" + name + "]:checked").val();
    if (attributes_restriction_selected == "1") {
        $("#" + name + "_mr").show(200);
    } else {
        $("#" + name + "_mr").hide(200);
    }
}

function carriers_restriction(name) {
    var carriers_restriction_selected = jQuery("input:radio[name=" + name + "]:checked").val();
    if (carriers_restriction_selected == "1") {
        $("#" + name + "_mr").show(200);
    } else {
        $("#" + name + "_mr").hide(200);
    }
}

function send_free_gift(name) {
    var send_free_gift_selected = jQuery("input:radio[name=" + name + "]:checked").val();
    if (send_free_gift_selected == "1") {
        $("#" + name + "_sfg").show(200);
    } else {
        $("#" + name + "_sfg").hide(200);
    }
}

function ps14freeshipping(name, action) {
    if (action == 1) {
        var products_free_shipping = jQuery("input:radio[name=" + name + "]:checked").val();
        if (products_free_shipping == "1") {
            $(".ps14freeshipping").hide(200);
        } else {
            $(".ps14freeshipping").show(200);
        }
    }
}

function groups_restriction(name) {
    var groups_restriction_selected = jQuery("input:radio[name=" + name + "]:checked").val();
    if (groups_restriction_selected == "1") {
        $("#" + name + "_mr").show(200);
    } else {
        $("#" + name + "_mr").hide(200);
    }
}

function countries_restriction(name) {
    var countries_restriction_selected = jQuery("input:radio[name=" + name + "]:checked").val();
    if (countries_restriction_selected == "1") {
        $("#" + name + "_mr").show(200);
    } else {
        $("#" + name + "_mr").hide(200);
    }
}

function changeMain(clicked, iso) {
    clicked.parent().parent().parent().find('.dropdown-toggle').html(iso + ' <i class="icon-caret-down"></i>');
}