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

<div class="panel">
    <div class="panel-heading">{l s='Create new html template' mod='tinymcepro'}</div>
    <div class="form-group">
        <label class="control-label col-lg-3 required">
            {l s='Name of template' mod='tinymcepro'}
        </label>
        <div class="col-lg-9">
            <div class="input-group col-lg-10">
                {literal}
                <input onkeypress="return /[a-z0-9A-Z-]/i.test(event.key)" type="text" name="ptm_newname" id="ptm_newname" value="" class="col-lg-4" required="required">
                {/literal}
                <p class="help-block">
                    {l s='Letters and numbers only' mod='tinymcepro'}
                </p>
            </div>
        </div>
    </div>
        <div class="clearfix"></div>
        <div class="panel-footer clearfx">
            <a href="&createNewTemplatehtml=1" class="pull-right btn button btn-default ptm_button_createNew"><i class="process-icon-save"></i>{l s='Save' mod='tinymcepro'}</a>
        </div>

</div>