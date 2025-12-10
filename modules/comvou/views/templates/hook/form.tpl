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

<script type="text/javascript">
    $(document).ready(function () {
        displayCartRuleTab('tproducts');
        $("#reminder_days_repeat").html($("#cvo_r_d").val());

        $('#cvo_r_d').change(function() {
            $("#reminder_days_repeat").html($("#cvo_r_d").val());
        });
    });
    function displayCartRuleTab(tab) {
        $('.comvou_tab').hide();
        $('.comvou_tab_page').removeClass('selected');
        $('#comvou_' + tab).show();
        $('#comvou_link_' + tab).addClass('selected');
        $('#currentFormTab').val(tab);
    }
</script>
{if $errors != false}
    <div class="bootstrap">
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {foreach from=$errors item=value name=foo}
                {math equation="a+b" a=$smarty.foreach.foo.index b=1}. {$value}<br/>
            {/foreach}
        </div>
    </div>
{/if}
<div class="comvou_container">
    <div class="col-lg-2 " id="mass-pc">
        <div class="productTabs">
            <ul class="tab">
                <li class="tab-row">
                    <a class="comvou_tab_page selected" id="comvou_link_tproducts" href="javascript:displayCartRuleTab('tproducts');">1. {l s='Module settings' mod='comvou'}</a>
                </li>
                <li class="tab-row">
                    <a class="comvou_tab_page" id="comvou_link_wtd" href="javascript:displayCartRuleTab('wtd');">2. {l s='Voucher settings' mod='comvou'}</a>
                </li>
                <li class="tab-row">
                    <a class="comvou_tab_page" id="comvou_link_filters" href="javascript:displayCartRuleTab('filters');">3. {l s='Filters' mod='comvou'}</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Tab Content -->
    <div id="comvou_form" method="post" class="col-lg-10 " {if $version < 1.6}style="margin-left: 145px;"{/if}>
        <input type="hidden" id="currentFormTab" name="currentFormTab" value="general"/>
        <div id="comvou_tproducts" class="comvou_tab tab-pane">
            <div class="separation"></div>
            {$comvou->renderForm()}
        </div>
        <div id="comvou_wtd" class="comvou_tab tab-pane panel" style="display:none;">
            <h3 class="tab">{l s='Voucher settings' mod='comvou'}</h3>
            <div class="separation"></div>
            <form action="{$URL}" method="post" enctype="multipart/form-data" >
                <fieldset style="margin-bottom:10px;">
                    {$cvv->generateForm()}
                    <div class="panel-footer">
                        <button type="submit" value="1" id="module_form_submit_btn" name="save_voucher_settings" class="btn btn-default pull-right">
                            <i class="process-icon-save"></i> {l s='save' mod='comvou'}
                        </button>
                    </div>
                </fieldset>
            </form>
        </div>
        <div id="comvou_filters" class="comvou_tab tab-pane panel" style="display:none;">
            <h3 class="tab">{l s='Filters' mod='comvou'}</h3>
            <div class="separation"></div>
            <div class="alert alert-info">
                {l s='This form allows to set the filters that will be used during delivery of reminders and vouchers. Module will send them only if commented products will meet defined rules.' mod='comvou'}
            </div>
            {$comvou->renderFormFilters()}
        </div>
        <div class="separation"></div>
    </div>
    <div class="clearfix"></div>
</div>
<br></br>
<div class="clearfix"></div>
{literal}
    <style type="text/css">
        /*== PS 1.6 ==*/
        #mass-pc ul.tab {
            list-style: none;
            padding: 0;
            margin: 0
        }

        #mass-pc ul.tab li a {
            background-color: white;
            border: 1px solid #DDDDDD;
            display: block;
            margin-bottom: -1px;
            padding: 10px 15px;
        }

        #mass-pc ul.tab li a {
            display: block;
            color: #555555;
            text-decoration: none
        }

        #mass-pc ul.tab li a.selected {
            color: #fff;
            background: #00AFF0
        }

        #comvou_form .language_flags {
            display: none
        }

        form#comvou_form {
            background-color: #ebedf4;
            border: 1px solid #ccced7;
            /*min-height: 404px;*/
            padding: 5px 10px 10px;
        }
    </style>
{/literal}