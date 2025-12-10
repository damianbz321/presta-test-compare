{*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * html Templates Manager
 * version 1.7.2
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
*}

<form name="editTemplateForm" id="editTemplateForm">
    <input type="hidden" name="ptm_name" value="{$ptm_template_name}"/>
    <div class="panel">
        <div class="panel-heading">
            {l s='Edit template' mod='tinymcepro'} {$ptm_template_name}
        </div>
        <div class="clearfix"></div>

        <textarea name="ptm_txt" rows="20">{$ptm->returnhtmlContents('txt', $ptm_template, $ptm_template_name)}</textarea>


        <div class="clearfix"></div>
        <div class="panel-footer clearfix">
            <a href="&back" id="ptm_button_backToList" class="pull-left btn btn-default"><i class="process-icon-back"></i> {l s='Back' mod='tinymcepro'}</a>
            <a href="&htmlTemplateSave=1" id="ptm_button_templateSave" class="pull-right btn btn-default"><i class="process-icon-save"></i> {l s='Save' mod='tinymcepro'}</a>
        </div>
    </div>
</form>