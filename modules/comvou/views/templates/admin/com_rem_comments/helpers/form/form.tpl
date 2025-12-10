{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-2020 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

{extends file="helpers/form/form.tpl"}
{block name="input"}
    {if $input.type == "customerFilter"}
        <div class="form-group col-lg-4">
            <input type="text" name="customerFilter" id="customerFilter" class="form-control" placeholder="{l s='Search for customer ...' mod='comvou'}">
            <input type="hidden" name="email" id="email">
            <input type="hidden" name="id_customer" id="id_customer">
        </div>
    {elseif $input.type == "orderFilter"}
        <div class="form-group col-lg-4">
            <select name="orderFilter" id="orderFilter" class="form-control">
                    <option value="0">{l s='Search for customer first...' mod='comvou'}</option>
            </select>
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="script"}
var currentToken = '{$currentToken|escape:'quotes'}';

    function returnGrade(grade){
        var grade_return='';
        for (i = 1; i <= 5; i++){
            if (grade >= i){
                grade_return=grade_return+'★';
            } else {
                grade_return=grade_return+'☆';
            }
        }
        return grade_return;
    }

$('#customerFilter')
    .autocomplete(
        'ajax-tab.php', {
            minChars: 2,
            max: 50,
            width: 500,
            selectFirst: false,
            scroll: false,
            dataType: 'json',
            formatItem: function(data, i, max, value, term) {
                return value;
            },
            parse: function(data) {
                var mytab = new Array();
                if (data.length!=0)
                {
                    for (var i = 0; i < data.length; i++)
                        mytab[mytab.length] = { data: data[i], value: data[i].cname + ' (' + data[i].email + ')' + (data[i].from_shop_name ? ' - ' + data[i].from_shop_name : '' ) };
                }
                return mytab;
            },
            extraParams: {
                controller: 'AdminComRemComments',
                token: currentToken,
                customerFilter: 1
            }
        }
    )
    .result(function(event, data, formatted) {
        $('#email').val(data.email);
        $('#id_customer').val(data.id_customer);
        getUserOrders(data.id_customer);
        $('#customerFilter').val(data.cname + ' (' + data.email + ')');
    });

    function getUserOrders(id_customer)
    {
        var html;
        $.post("ajax-tab.php",{ id_customer: id_customer, controller: 'AdminComRemComments', token: currentToken, orderFilter: 1}).
        done(function( data ) {
            data = $.parseJSON(data);
            if (data.length==0)
            {
                html='<option value="0">{l s='This customer has no orders in your shop' mod='comvou'}</option>';
            }
            else
            {
                for (var i = 0; i < data.length; i++)
                {
                    html=html+'<option value="'+data[i].id_order+'">(#'+data[i].id_order+') '+data[i].reference+' '+data[i].date_add+' '+data[i].payment+'</option>';
                }
            }
            $('#orderFilter').html(html);
        });
    }
{/block}