/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2023 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */


function changeMain(clicked, iso) {
    clicked.parent().parent().parent().find('.dropdown-toggle').html(iso + '<i class="icon-caret-down"></i>');
}

function addinput(name, id) {
    $('.cat_feature_selected').append('<div><input type="hidden" name="cat_feature_v[]" value="' + id + '">' + name + ' <span class="remove" onclick="$(this).parent().remove();"></span></div>');
}

function addinputName(name, id) {
    $('.type_feature_selected').append('<div><input type="hidden" name="type_feature_v[]" value="' + id + '">' + name + ' <span class="remove" onclick="$(this).parent().remove();"></span></div>');
}

function blink_field(field) {
    var count = 0;
    var $input = $('.' + field);
    var interval = setInterval(function () {
        if ($input.hasClass('blink_blur')) {
            $input.removeClass('blink_blur').addClass('blink_focus');
            ++count;
        } else {
            $input.removeClass('blink_focus').addClass('blink_blur');
        }
        if (count === 3) {
            clearInterval(interval);
            $input.removeClass('blink_blur, blink_focus');
        }
    }, 300);
}

function ppbsubmit() {
    var theForm = $("#PPBglobalsettings").clone();
    $('<form id="PPBForm3" name="PPBForm3" style="display:none!important;" action="' + $("#PPBglobalsettings").attr('action') + '" method="POST"><div id="PPBform2"></div></form>').appendTo('body');
    $('#PPBform2').replaceWith(theForm);
    $("#PPBForm3 select[name='exc_p']").val($("#Ppb #PPBglobalsettings select[name='exc_p']").val());
    $("#PPBForm3 select[name='ppb_allconditions']").val($("#Ppb #PPBglobalsettings select[name='ppb_allconditions']").val());
    $("#PPBForm3 select[name='ppbp_block_position']").val($("#Ppb #PPBglobalsettings select[name='ppbp_block_position']").val());
    $("#PPBForm3 select[name='ppb_type']").val($("#Ppb #PPBglobalsettings select[name='ppb_type']").val());
    $("#PPBForm3 select[name='ppb_random']").val($("#Ppb #PPBglobalsettings select[name='ppb_random']").val());
    $("#PPBForm3 select[name='ppb_active']").val($("#Ppb #PPBglobalsettings select[name='ppb_active']").val());
    $("#PPBForm3 select[name='ppb_before']").val($("#Ppb #PPBglobalsettings select[name='ppb_before']").val());
    $("#PPBForm3 select[name='ppb_carousell']").val($("#Ppb #PPBglobalsettings select[name='ppb_carousell']").val());
    $("#PPBForm3 select[name='ppb_carousell_auto']").val($("#Ppb #PPBglobalsettings select[name='ppb_carousell_auto']").val());
    $("#PPBForm3 select[name='ppb_carousell_loop']").val($("#Ppb #PPBglobalsettings select[name='ppb_carousell_loop']").val());
    $("#PPBForm3 select[name='ppb_carousell_controls']").val($("#Ppb #PPBglobalsettings select[name='ppb_carousell_controls']").val());
    $("#PPBForm3 select[name='ppb_carousell_pager']").val($("#Ppb #PPBglobalsettings select[name='ppb_carousell_pager']").val())
    $("#PPBForm3 select[name='everywhere']").val($("#Ppb #PPBglobalsettings select[name='everywhere']").val());
    $("#PPBForm3 select[name='hidewc']").val($("#Ppb #PPBglobalsettings select[name='hidewc']").val());
    $("#PPBForm3 select[name='instock']").val($("#Ppb #PPBglobalsettings select[name='instock']").val());
    $("#PPBForm3 select[name='ppb_global_products']").val($("#Ppb #PPBglobalsettings select[name='ppb_global_products']").val());
    $("#PPBForm3 select[name='ppb_g_products_from_method']").val($("#Ppb #PPBglobalsettings select[name='ppb_g_products_from_method']").val());
    $("#PPBForm3 select[name='ppb_global_categories']").val($("#Ppb #PPBglobalsettings select[name='ppb_global_categories']").val());
    $("#PPBForm3 select[name='ppb_global_manufacturers']").val($("#Ppb #PPBglobalsettings select[name='ppb_global_manufacturers']").val());
    $("#PPBForm3 select[name='ppb_global_features']").val($("#Ppb #PPBglobalsettings select[name='ppb_global_features']").val());
    $("#PPBForm3 select[name='cat_feature']").val($("#Ppb #PPBglobalsettings select[name='cat_feature']").val());
    $("#PPBForm3 select[name='cat_stock_filter']").val($("#Ppb #PPBglobalsettings select[name='cat_stock_filter']").val());
    $("#PPBForm3 select[name='cat_price_from']").val($("#Ppb #PPBglobalsettings select[name='cat_price_from']").val());
    $("#PPBForm3 select[name='cat_price_to']").val($("#Ppb #PPBglobalsettings select[name='cat_price_to']").val());
    $("#PPBForm3 select[name='cat_manuf']").val($("#Ppb #PPBglobalsettings select[name='cat_manuf']").val());
    $("#PPBForm3 select[name='stockcheck']").val($("#Ppb #PPBglobalsettings select[name='stockcheck']").val());
    $("#PPBForm3 select[name='ppb_custom_path']").val($("#Ppb #PPBglobalsettings select[name='ppb_custom_path']").val());
    $("#PPBForm3 textarea[name='ppb_products']").val($("#Ppb #PPBglobalsettings textarea[name='ppb_products']").val());
    PPBForm3.submit();

    //theDiv.prepend('<form style="display:none!important;" action="'+$("#PPBglobalsettings").attr('action')+'" method="POST">');
}

function ppbp_remove(id_tab) {
    var postoptions = "id=" + id_tab + "&action=removeTab";
    $.post(ppb_url, postoptions, function (data) {
        $("#ppbp_" + id_tab).fadeOut('slow');
    });
}

function ppbp_duplicate(id_tab) {
    var postoptions = "id=" + id_tab + "&action=duplicateTab";
    $.post(ppb_url, postoptions, function (data) {
        eval(data);
    });
}

function ppbp_toggle(id_tab) {
    var postoptions = "id=" + id_tab + "&action=toggleTab";
    $.post(ppb_url, postoptions, function (data) {
        eval(data);
    });
}

function refreshListOfFields() {

    if ($('#exc_p').val() == 1) {
        $('#exc_p_list').show();
    } else {
        $('#exc_p_list').hide();
    }


    if ($('#ppb_type').val() == 1 || $('#ppb_type').val() == 9) {
        $('.ppb_type_category').show();
    } else {
        $('.ppb_type_category').hide();
    }

    if ($('#ppb_type').val() == 5) {
        $('#ppb_type_nb').show();
        $('.ppb_stock_filter').show();
        $('.ppb_random_field').show();
        $('#ppb_type_products').show();
    } else {
        $('#ppb_type_products').hide();
        $('#ppb_type_nb').show();
    }
    if ($('#ppb_type').val() == 11) {
        $('.ppb_type_feature').show();
    } else {
        $('.ppb_type_feature').hide();
    }

    if ($('#ppb_type').val() == 6) {
        $('.non-free').hide();
    } else {
        $('.non-free').show();
    }

    if ($('#ppb_type').val() == 8) {
        $('.ppb_type_searchphrase').show();
    } else {
        $('.ppb_type_searchphrase').hide();
    }

    if ($('#ppb_type').val() == 12) {
        $('.ppb_type_manufacturer').show();
    }

    if ($('#ppb_type').val() == 13) {
        $('.ppb_random_field').show();
    }

    if ($('#ppb_type').val() == 9) {
        $('.ppb_type_category').has('.ppb_value').hide();
    }
}

$(document).ready(function () {

    $('.ppb_custom_path_predefined').change(function(){
       $('.ppb_path').val($(this).find('option:selected').val());
    });

    $("#exc_p").change(function () {
        refreshListOfFields();
    });

    $("#ppb_type").change(function () {
        refreshListOfFields();
    });

    if (window.location.href.indexOf("ppbtab") > -1) {
        if (_PS_VERSION_[0] >= 8) {
            $('#product_extra_modules-tab-nav a').click();
            function show_ppb(){
                $('button[data-target="module-ppb"]').trigger('click');
            };
            window.setTimeout(show_ppb, 200 );

        } else {
            $('.module-selection').show();
        }
        $('.modules-list-select').val('module-ppb').trigger('change');
        if (typeof _PS_VERSION_ !== 'undefined') {
            if (_PS_VERSION_[0] != 8) {
                $('.module-render-container').hide();
            }
        } else {
            $('.module-render-container').hide();
        }
        $(`.module-ppb`).show();
    }


    if (typeof ppb_url !== 'undefined') {
        $('.cat_feature_selected div').click(function () {
            $(this).remove();
        });

        $(".ppb_search_feature").keypress(function () {
            $.post(ppb_url, {search_feature: $(".ppb_search_feature").val()}, function (data) {
                $("#ppb_search_feature_result").html(data);
            })
        });

        $(".ppb_search_feature").keypress(function () {
            $.post(ppb_url, {search_feature: $(".ppb_search_feature").val()}, function (data) {
                $("#ppb_search_feature_result").html(data);
            })
        });

        $(".cat_feature_s").keypress(function () {
            $.post(ppb_url, {search_feature_cat: $(".cat_feature_s").val()}, function (data) {
                $(".cat_feature_s").parent().parent().parent().find('.search_result').html(data);
            })
        });

        $(".type_feature_s").keypress(function () {
            $.post(ppb_url, {search_feature_type: $(".type_feature_s").val()}, function (data) {
                $(".type_feature_s").parent().parent().parent().find('.search_result').html(data);
            })
        });

        $(".ppb_search").keypress(function () {
            $.post(ppb_url, {search: $(".ppb_search").val()}, function (data) {
                $("#ppb_search_result").html(data);
            })
        });

        $(".ppb_search").keypress(function () {
            $.post(ppb_url, {search: $(".ppb_search").val()}, function (data) {
                $("#ppb_search_result").html(data);
            })
        });

        var $mySlides = $("#ppblist");
        $mySlides.sortable({
            opacity: 0.6,
            cursor: "move",
            update: function () {
                var order = $(this).sortable("serialize") + "&action=updateSlidesPosition";
                $.post(ppb_url, order);
            }
        });
        $mySlides.hover(function () {
                $(this).css("cursor", "move");
            },
            function () {
                $(this).css("cursor", "auto");
            });
    }

    if (typeof (ppb_link) !== 'undefined') {
        $(".searchToolInput").each(function () {
            var searchInput = $(this);
            $(this).autocompleteModified(
                ppb_link, {
                    minChars: 2,
                    max: 15,
                    width: 500,
                    selectFirst: false,
                    scroll: false,
                    dataType: "json",
                    formatItem: function (data, i, max, value, term) {
                        return value;
                    },
                    parse: function (data) {
                        var mytab = new Array();
                        for (var i = 0; i < data.length; i++) {
                            if (typeof data[i].id_manufacturer !== 'undefined') {
                                data[i].id = data[i].id_manufacturer;
                            }
                            if (typeof data[i].id_product !== 'undefined') {
                                data[i].id = data[i].id_product;
                            }
                            if (typeof data[i].id_category !== 'undefined') {
                                data[i].id = data[i].id_category;
                            }
                            if (typeof data[i].id_supplier !== 'undefined') {
                                data[i].id = data[i].id_supplier;
                            }
                            if (typeof data[i].id_cms_category !== 'undefined') {
                                data[i].id = data[i].id_cms_category;
                            }
                            if (typeof data[i].id_cms !== 'undefined') {
                                data[i].id = data[i].id_cms;
                                data[i].name = data[i].meta_title;
                            }
                            mytab[mytab.length] = {
                                data: data[i],
                                value: '#' + data[i].id + ' - ' + data[i].name
                            };
                        }
                        return mytab;
                    },
                    extraParams: {
                        searchType: searchInput.data('type'),
                        limit: 20,
                        id_lang: ppb_lang
                    }
                }
            ).result(function (event, data, formatted) {
                if (typeof data.id !== 'undefined') {
                    if (searchInput.data('replacementtype') == 'replace') {
                        $('input[name="' + searchInput.data('resultinput') + '"]').val(data.id);
                    } else {
                        $('input[name="' + searchInput.data('resultinput') + '"]').val($('input[name="' + searchInput.data('resultinput') + '"]').val() + data.id + ',');
                    }
                }
            });
        });
    }
    refreshListOfFields();
});