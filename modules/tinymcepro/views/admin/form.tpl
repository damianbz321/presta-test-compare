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
<script type="text/javascript">
    function checkAllSubcats(parentTree) {
        $('#' + parentTree + ' input[type=checkbox]:checked').each(function (e) {
            $(this).parent().parent().find('input[type=checkbox]').attr('checked', true);
        });
    }

    function uncheckAllsubcats(parentTree) {
        $('#' + parentTree + ' input[type=checkbox]:checked').each(function (e) {
            $(this).parent().parent().find('input[type=checkbox]').attr('checked', false);
        });
    }

    $(document).ready(function () {
        displayCartRuleTab('Installation');
        $('a.tinymcepro_fancybox').fancybox();
    });

    function displayCartRuleTab(tab) {
        $('.tinymcepro_tab').hide();
        $('.tinymcepro_tab_page').removeClass('selected');
        $('#tinymcepro_' + tab).show();
        $('#tinymcepro_link_' + tab).addClass('selected');
        $('#currentFormTab').val(tab);
    }
</script>
<div class="tinymcepro_container clearfix">
    <div class="col-lg-2" id="mass-pc">
        <div class="col-lg-12 panel">
            <h3>{l s='Menu' mod='tinymcepro'}</h3>
            <div class="productTabs">
                <ul class="tab">
                    <li class="tab-row">
                        <a class="tinymcepro_tab_page selected" id="tinymcepro_link_Installation"
                           href="javascript:displayCartRuleTab('Installation');">1. {l s='Installation' mod='tinymcepro'}</a>
                    </li>
                    <li class="tab-row">
                    <a class="tinymcepro_tab_page" id="tinymcepro_link_set"
                       href="javascript:displayCartRuleTab('set');">2. {l s='Editor settings' mod='tinymcepro'}</a>
                    </li>
                    <li class="tab-row">
                        <a class="tinymcepro_tab_page" id="tinymcepro_link_fos"
                           href="javascript:displayCartRuleTab('fos');">2. {l s='Front office settings' mod='tinymcepro'}</a>
                    </li>
                    <li class="tab-row">
                        <a class="tinymcepro_tab_page" id="tinymcepro_link_updates"
                           href="javascript:displayCartRuleTab('updates');">3. {l s='Updates' mod='tinymcepro'}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Tab Content -->
    <div class="col-lg-10">
        <input type="hidden" id="currentFormTab" name="currentFormTab" value="general"/>
        <div id="tinymcepro_Installation" class="tinymcepro_tab tab-pane">
            <div class="panel">
                <h3 class="tab">{l s='Installation' mod='tinymcepro'}</h3>
                <div class="alert alert-success">{l s='To install or reinstall the extended editor press "reinstall" button below' mod='tinymcepro'}</div>
                <div class="panel-footer">
                    <form action="{$tinymcepro_url|escape:'htmlall':'UTF-8'}&amp;saveConfiguration"
                          name="tinymcepro_formReinstall" id="tinymcepro_formReinstall" method="post"
                          enctype="multipart/form-data">
                        <button type="submit" name="submit_settings_reinstall"
                                class="{if $tinymcepro_restore != true}disabled{/if} button btn btn-default pull-right">
                            <i class="process-icon-save"></i>
                            {l s='Reinstall editor' mod='tinymcepro'}<br/>
                        </button>
                    </form>
                </div>
            </div>
            <div class="panel">
                <h3 class="tab">{l s='Backup' mod='tinymcepro'}</h3>
                {if $tinymcepro_restore == true}
                    <div class="alert alert-success">
                        {l s='Backup exists, you can restore original editor if you want' mod='tinymcepro'}
                    </div>
                {else}
                    <div class="alert alert-danger">
                        {l s='Backup does not exists, please install the extended editor first' mod='tinymcepro'}
                    </div>
                {/if}
                <div class="panel-footer">
                    <form action="{$tinymcepro_url|escape:'htmlall':'UTF-8'}&amp;saveConfiguration"
                          name="tinymcepro_formReinstall" id="tinymcepro_formReinstall" method="post"
                          enctype="multipart/form-data">
                        <button type="submit" name="{if $tinymcepro_restore == true}submit_settings_restore_backup{/if}"
                                class="{if $tinymcepro_restore != true}disabled{/if} button btn btn-default pull-right">
                            <i class="process-icon-refresh"></i>
                            {l s='Restore editor' mod='tinymcepro'}<br/>
                        </button>
                    </form>
                </div>
            </div>
            <div class="separation"></div>
        </div>
        <div id="tinymcepro_set" class="tinymcepro_tab tab-pane" style="display:none;">
            <div class="panel">
                <h3 class="tab">{l s='Editor settings' mod='tinymcepro'}</h3>
                {$tinymcepro_form_editor nofilter}
            </div>
            <div class="separation"></div>
        </div>
        <div id="tinymcepro_fos" class="tinymcepro_tab tab-pane" style="display:none;">
            <div class="panel">
                <h3 class="tab">{l s='Front office appearance settings' mod='tinymcepro'}</h3>
                {$tinymcepro_form_fos nofilter}
            </div>
            <div class="separation"></div>
        </div>
        <div id="tinymcepro_updates" class="tinymcepro_tab tab-pane" style="display:none;">
            <div class="separation"></div>
            {$tinymcepro_updates nofilter}
        </div>
        <div class="separation"></div>
    </div>
</div>
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

        #fieldset_myprestaupdates {
            margin-top: 0px !important;
        }

    </style>
{/literal}