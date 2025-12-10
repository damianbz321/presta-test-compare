{*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 *}

<style>
    #resultInput i {
        cursor:pointer;
        opacity:0.9;
    }
    #resultInput i:hover {
        opacity:0.7;
    }
</style>

<div id="ajax_choose_product" class="clearfix">
    <div class="alert alert-info">{l s='Step 1. Search for attributes' mod='pla'}</div>
    <div class="input-group col-md-8">
        <span class="input-group-addon">{l s='search for attribute' mod='pla'}</span>
        <input type="text" id="attribute_autocomplete_input" name="attribute_autocomplete_input" data-type="attribute" autocomplete="on" class="ac_input">
        <span class="input-group-addon"><i class="icon-search"></i></span>
    </div>
    <br/>
    <div class="panel">
        <h3>{l s='Selected attributes:' mod='pla'}</h3>
        <div id="resultInput">
            <table class="table table-bordered">
                <tr><td>{l s='ID' mod='pla'}</td><td>{l s='Name' mod='pla'}</td><td>{l s='Remove' mod='pla'}</td></tr>
            </table>
        </div>
    </div>
</div>
<div class="alert alert-info">{l s='Step 2. Select categories where combinations with selected attribute will be hidden' mod='pla'}</div>
{$categoryTreeCa nofilter}

<script>
    function removeplatr(id){
        $('#resultInput table tr.'+'platr'+id).remove();
    }
    {literal}
    $(document).ready(function () {
        var lang = {/literal}{Context::getContext()->language->id}{literal};
        var tokenProducts = "{/literal}{Tools::getAdminTokenLite('AdminProducts')}{literal}";
        var link = "{/literal}{$pla_bo_link}&_token=" + tokenProducts + "{literal}";

        $("#attribute_autocomplete_input").each(function()
        {
            var searchInput = $(this);
            $(this).autocomplete(
                link, {
                    minChars: 2,
                    token: tokenProducts,
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
                            if (typeof data[i].id_attribute !== 'undefined') {
                                data[i].id = data[i].id_attribute;
                            }
                            mytab[mytab.length] = {
                                data: data[i],
                                value: '#' +data[i].id + ' - ' + data[i].ComputedName
                            };
                        }
                        return mytab;
                    },
                    extraParams: {
                        searchType: searchInput.data('type'),
                        limit: 50,
                        id_lang: lang
                    }
                }
            ).result(function (event, data, formatted) {
                if (data.id.length > 0){
                    $('#resultInput table tr:last').after('<tr class="platr'+data.id+'"><td>#'+data.id+'<input type="hidden" name="pla_hide_ca[]" value="'+data.id+'"></td><td>'+data.ComputedName+"</td><td><i onclick=\"removeplatr("+data.id+")\" class='material-icons'>delete</i></td></tr>");
                    //$('#'+searchInput.data('resultinput')).val($('#'+searchInput.data('resultinput')).val()+data.id+',');
                }
            });
        });
    });
    {/literal}
</script>

