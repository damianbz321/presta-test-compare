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

<div class="panel">
    <div class="panel-heading">{l s='Create new email template' mod='ppb'}</div>
    <div class="form-group">
        <label class="control-label col-lg-3 required">
            {l s='Name of template' mod='ppb'}
        </label>
        <div class="col-lg-9">
            <div class="input-group col-lg-10">
                {literal}
                <input onkeypress="return /[a-z0-9A-Z-]/i.test(event.key)" type="text" name="etm_newname" id="etm_newname" value="" class="col-lg-4" required="required">
                {/literal}
                <p class="help-block">
                    {l s='Letters and numbers only' mod='ppb'}
                </p>
            </div>
        </div>
    </div>
        <div class="clearfix"></div>
        <div class="panel-footer clearfx">
            <a href="&createNewTemplate=1" class="pull-right btn button btn-default etm_button_createNew"><i class="process-icon-save"></i>{l s='Save' mod='ppb'}</a>
        </div>

</div>