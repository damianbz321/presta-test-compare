{*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2023 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
*}

<form name="editTemplateForm" id="editTemplateForm">
    <input type="hidden" name="etm_name" value="{$etm_template_name}"/>
    <div class="panel">
        <div class="panel-heading">{l s='Smarty template' mod='ppb'} {$etm_template_name}</div>
        <div class="alert alert-info">
            {l s='If you want to translate the contents to other languages you can use prestasho\'s translation tool (just remember about prestashops language syntax)' mod='ppb'}
            <br/>
            {l s='Remember to use correct smarty syntax, otherwise file will generate errors. You use this editor tool under your own responsibility.' mod='ppb'}</div>
        <div id="module_page" class="module_page_tab">
            <div class="etm_txt_code col-lg-12 col-md-12 col-sm-12">
                <textarea name="etm_txt" rows="20">{$etm->returnSmartyContents('tpl', $etm_template, $etm_template_name)}</textarea>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-footer clearfix">
            <a href="&back" id="etm_button_backToList" class="pull-left btn btn-default"><i class="process-icon-back"></i> {l s='Back' mod='ppb'}</a>
            <a href="&smartyTemplateSave=1" id="etm_button_templateSave" class="pull-right btn btn-default"><i class="process-icon-save"></i> {l s='Save' mod='ppb'}</a>
        </div>
    </div>
</form>