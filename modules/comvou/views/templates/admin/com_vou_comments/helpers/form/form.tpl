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
    {elseif $input.type == "commentFilter"}
        <div class="form-group col-lg-4">
            <input type="text" name="commentFilter" id="commentFilter" class="form-control" placeholder="{l s='Search for product that customer commented ...' mod='comvou'}">
            <input type="hidden" name="id_product" id="id_product">
            <input type="hidden" name="id_product_comment" id="id_product_comment">
            <strong>{l s='Results legend' mod='comvou'}</strong><br/>
            <strong>☑</strong> {l s='Customer received voucher' mod='comvou'}<br/>
            <strong>☐</strong> {l s='No voucher generated for this comment' mod='comvou'}
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
    for (var i = 0; i < data.length; i++)
    mytab[mytab.length] = { data: data[i], value: data[i].cname + ' (' + data[i].email + ')' + (data[i].from_shop_name ? ' - ' + data[i].from_shop_name : '' ) };
    return mytab;
    },
    extraParams: {
    controller: 'AdminComVouComments',
    token: currentToken,
    customerFilter: 1
    }
    }
    )
    .result(function(event, data, formatted) {
    $('#email').val(data.email);
    $('#id_customer').val(data.id_customer);
    $('#customerFilter').val(data.cname + ' (' + data.email + ')');
    });


    $('#commentFilter')
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
                    for (var i = 0; i < data.length; i++)
                    mytab[mytab.length] = { data: data[i], value: (data[i].id_cvov != null ? '☑':'☐') + ' ' + data[i].name + ' ' + returnGrade(data[i].grade) + ' ' + (data[i].title).substring(0,45) + '...' + (data[i].content).substring(0,45)+'...'};
                    return mytab;
                },
                extraParams: {
                    controller: 'AdminComVouComments',
                    token: currentToken,
                    commentFilter: 1,
                    id_customer: function() {
                        return $('#id_customer').val();
                    }
                }
            }
        )
        .result(function(event, data, formatted) {
            $('#id_product').val(data.id_product);
            $('#id_product_comment').val(data.id_product_comment);
            $('#commentFilter').val((data.id_cvov != null ? '☑':'☐') + ' ' + data.name + ' ' + returnGrade(data.grade));
        });

{/block}