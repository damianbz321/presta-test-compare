{*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * Email Templates Manager
 * version 1.6.1
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
*}

<form name="editTemplateForm" id="editTemplateForm">
    <input type="hidden" name="etm_name" value="{$etm_template_name}"/>
<div class="panel">
    <div class="panel-heading">
        {l s='Edit template' mod='ordercoupons'} {$etm_template_name}
    </div>
    <div class="clearfix"></div>
    <div>
        <div class="productTabs">
            <ul class="tab nav nav-tabs">
                {foreach Language::getLanguages(false) AS $lang}
                    <li class="tab-row">
                        <a class="tab-page" id="module_page_link_{$lang.iso_code}" href="javascript:displayModulePageTabEtm('{$lang.iso_code}');">{$lang.iso_code}</a>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
    {foreach Language::getLanguages(false) AS $lang}
        <div id="module_page_{$lang.iso_code}" class="panel module_page_tabEtm" style="display: block;">
            <div class="etm_html_code col-lg-6 col-md-6 col-sm-12">
                <div class="panel">
                    <div class="panel-heading">{l s='html template'}<a href="#" class="etm_maximize_html pull-right"><i class="icon-fullscreen"></i></a></div>
                    <textarea class='rte' name="etm_html[{$lang.iso_code}]" rows="20">{$etm->returnEmailContents('html', $lang.iso_code, $etm_template, $etm_template_name)}</textarea>
                </div>
            </div>
            <div class="etm_txt_code col-lg-6 col-md-6 col-sm-12">
                <div class="panel">
                    <div class="panel-heading">{l s='txt template'}<a href="#" class="etm_maximize_txt pull-right"><i class="icon-fullscreen"></i></a></div>
                    <textarea name="etm_txt[{$lang.iso_code}]" rows="20">{$etm->returnEmailContents('txt', $lang.iso_code, $etm_template, $etm_template_name)}</textarea>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    {/foreach}
    <div class="clearfix"></div>
    <div class="panel-footer clearfix">
        <a href="&back" id="etm_button_backToList" class="pull-left btn btn-default"><i class="process-icon-back"></i> {l s='Back' mod='ordercoupons'}</a>
        <a href="&emailTemplateSave=1" id="etm_button_templateSave" class="pull-right btn btn-default"><i class="process-icon-save"></i> {l s='Save' mod='ordercoupons'}</a>
    </div>
    <script>
        $(document).ready(function(){
            displayModulePageTabEtm('{Configuration::get('PS_LOCALE_LANGUAGE')}');
        });
    </script>
</div>
</form>