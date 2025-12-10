{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-2021 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* Search Tool
* version 1.1.0
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

<script>
    {literal}
    function SearchToolRemoveItem(css, what, where, id) {
        var current = $('input[name="' + where + '"]').val();
        var current_exploded = current.split(",");
        current_exploded.forEach(function (item, index, object) {
            if (item === id) {
                object.splice(index, 1);
            }
        });
        $('input[name="' + where + '"]').val(current_exploded);
        $('.' + css).remove();
    }

    function SearchToolFormatItem(what, where, data) {
        return '<div class="clearfix ' + what + where + data.id + '"><span class="btn btn-default" onclick="SearchToolRemoveItem(\'' + what + '' + where + '' + data.id + '\',\'' + what + '\',\'' + where + '\',\'' + data.id + '\');"><i class="icon-remove"></i></span> #' + data.id + ' - ' + data.name + '</div>';
    }

    $(document).ready(function () {
        var link = "{/literal}{$SearchToolLink}{literal}";
        var lang = {/literal}{Context::getContext()->language->id}{literal};
        $(".searchToolInput").each(function () {
            var searchInput = $(this);
            $(this).autocomplete(
                link, {
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
                            if (typeof data[i].id_customer !== 'undefined') {
                                data[i].id = data[i].id_customer;
                                data[i].name = data[i].firstname+' '+data[i].lastname+' '+data[i].email;
                            }
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
                        id_lang: lang
                    }
                }
            ).result(function (event, data, formatted) {
                if (data.id.length > 0) {
                    if (searchInput.data('replacementtype') == 'replace') {
                        $('input[name="' + searchInput.data('resultinput') + '"]').val(data.id);
                        if ($('.' + searchInput.data('resultinput') + '_' + searchInput.data('type') + 'sBox').length) {
                            $('.' + searchInput.data('resultinput') + '_' + searchInput.data('type') + 'sBox').html(SearchToolFormatItem(searchInput.data('type'), searchInput.data('resultinput'), data));
                        }
                    } else {
                        var current = $('input[name="' + searchInput.data('resultinput') + '"]').val();
                        var current_exploded = current.split(",");
                        current_exploded.push(data.id);
                        var filtered_current_exploded = current_exploded.filter(function (e) {
                            return e
                        });
                        $('input[name="' + searchInput.data('resultinput') + '"]').val(filtered_current_exploded.join(","));
                        if ($('.' + searchInput.data('resultinput') + '_' + searchInput.data('type') + 'sBox').length) {
                            $('.' + searchInput.data('resultinput') + '_' + searchInput.data('type') + 'sBox').append(SearchToolFormatItem(searchInput.data('type'), searchInput.data('resultinput'), data));
                        }
                    }
                }
            });
        });
    });
    {/literal}
</script>