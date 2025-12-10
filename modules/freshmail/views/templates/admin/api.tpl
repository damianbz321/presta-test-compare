{include file="{$module_templates}alerts.tpl"}
<form action="" method="POST">
    <div class="form-horizontal">
        <div class="panel">
            <div class="panel-heading"><i class="icon-cogs"></i>{l s='Webservice Accounts Configuration API' mod='freshmail'}</div>
            <div class="form-wrapper">
                <div class="form-group">
                    <div class="form-wrapper">
                        <label class="control-label col-lg-3 required" style="text-align: right;">
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                                {l s='Bearer token' mod='freshmail'}
                            </span>
                        </label>
                        <div class="col-lg-9 ">
                            <div class="row">
                                <div class="col-lg-5">
                                    <input type="text" name="api_key" id="code" value="{$freshmail_settings->api_token|escape:'htmlall':'UTF-8'}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-wrapper">
                        <label class="control-label col-lg-3 required" style="text-align: right;">
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                                {l s='Subscribers list' mod='freshmail'}
                            </span>
                        </label>
                        <div class="col-lg-9 ">
                            <div class="row">
                                <div class="col-lg-5">
                                    <select id="subscriber_list_hash" name="subscriber_list_hash" data-val="{$freshmail_settings->subscriber_list_hash}">
                                        <option value="0">{l s="Choose" mod="freshmail"}</option>
                                        {foreach from=$subscribers_list item='list'}
                                            <option {if $list.key==$freshmail_settings->subscriber_list_hash}selected{/if} value="{$list.key}">{$list.name}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">
                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Use FreshMail SMTP' mod='freshmail'}">
                            {l s='Synchronize' mod='freshmail'}
                        </span>
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="synchronize" id="synchronize_on" value="1" {if !empty($freshmail_settings->synchronize)}checked="checked"{/if}>
                            <label for="synchronize_on">Tak</label>
                            <input type="radio" name="synchronize" id="synchronize_off" value="0" {if empty($freshmail_settings->synchronize)}checked="checked"{/if}>
                            <label for="synchronize_off">Nie</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">
                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Use FreshMail SMTP' mod='freshmail'}">
                            {l s='Use FreshMail SMTP' mod='freshmail'}
                        </span>
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="smtp" id="smtp_on" value="1" {if !empty($freshmail_settings->smtp)}checked="checked"{/if}>
                            <label for="smtp_on">Tak</label>
                            <input type="radio" name="smtp" id="smtp_off" value="0"{if empty($freshmail_settings->smtp)}checked="checked"{/if}>
                            <label for="smtp_off">Nie</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">
                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Send FreshMail confirmation for new subscribers' mod='freshmail'}">
                            {l s='Send FreshMail confirmation for new subscribers' mod='freshmail'}
                        </span>
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="send_confirmation" id="send_confirmation_on" value="1" {if !empty($freshmail_settings->send_confirmation)}checked="checked"{/if}>
                            <label for="send_confirmation_on">Tak</label>
                            <input type="radio" name="send_confirmation" id="send_confirmation_off" value="0"{if empty($freshmail_settings->send_confirmation)}checked="checked"{/if}>
                            <label for="send_confirmation_off">Nie</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>

                {*<div class="form-group">
                    <div class="form-wrapper">
                        <label class="control-label col-lg-3 required" style="text-align: right;">
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                                {l s='Specific price rule' mod='freshmail'}
                            </span>
                        </label>
                        <div class="col-lg-9 ">
                            <div class="row">
                                <div class="col-lg-5">
                                    <select name="id_specific_price_rule">
                                        <option value="0">{l s="Choose" mod="freshmail"}</option>
                                        {foreach from=$specific_price_rules item='spr'}
                                            <option {if $spr.id_specific_price_rule==$freshmail_settings->id_specific_price_rule}selected{/if} value="{$spr.id_specific_price_rule}">{$spr.name}</option>
                                        {/foreach}

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
*}
                <div class="panel-footer">
                    <button type="submit" class="btn btn-default pull-right" name="submitEnterApiKeys"><i class="process-icon-save"></i> {l s='save' mod='freshmail'}</button>
                </div>
            </div>
        </div>
    </div>

    {if $showCheck}
        <div class="form-horizontal">
            <div class="panel">
                <div class="panel-heading"><i class="icon-cogs"></i>{l s='Check Validate the data and connection' mod='freshmail'}</div>
                <div class="form-wrapper">
                    <div class="form-group">
                        <label class="control-label col-lg-3" style="text-align: right;">
                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                            {l s='Click to check connection to freshmail.com' mod='freshmail'}
                        </span>
                        </label>
                        <div class="col-lg-9 ">
                            <div class="row">
                                {if !$response}
                                    <div class="col-lg-5">
                                        <input type="submit" class="btn btn-primary" name="submitConnectToApi" id="email" value="{l s='Check authorizations' mod='freshmail'}">
                                    </div>
                                {else}
                                    {if $response && isset($response['status']) && $response['status'] == 'OK'}
                                        <h4 class="col-sm-1 alert-success">
                                            {$response['status']|escape:'htmlall':'UTF-8'}
                                        </h4>
                                    {else}
                                        <h4 class="col-sm-12 alert alert-error">
                                            {$response['status']|escape:'htmlall':'UTF-8'}
                                        </h4>
                                    {/if}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    <div class="form-horizontal">
        <div class="panel">
            <div class="panel-heading"><i class="icon-cogs"></i>{l s='Reset FreshMail plugin settings' mod='freshmail'}</div>
            <div class="form-wrapper">
                <div class="form-group">
                    <label class="control-label col-lg-3" style="text-align: right;">
                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                            {l s='Click to reset FreshMail settings' mod='freshmail'}
                        </span>
                    </label>
                    <div class="col-lg-9 ">
                        <div class="row">
                            <div class="col-lg-5">
                                <input type="submit" class="btn btn-primary" name="resetSettings" id="resetSettings" value="{l s='Clear settings' mod='freshmail'}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</form>

<form action="https://app.freshmail.com/pl/actions/subscribe/" method="POST" target="_blank" name="add_to_newsletter">
    <div class="form-horizontal">
        <div class="panel">
            <div class="panel-heading"><i class="icon-cogs"></i>{l s='Stay up to date with changes' mod='freshmail'}</div>
            <div class="form-wrapper">
                <div class="form-group">
                    <label class="control-label col-lg-3" style="text-align: right;">
                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                            {l s='Give us your e-mail address so that we can send you information about future versions of the plug-in, changes and news' mod='freshmail'}
                        </span>
                    </label>
                    <div class="col-lg-9 ">
                        <div class="row">
                            <div class="col-lg-5">
                                <input type="text" class="btn btn-block" name="freshmail_email" id="freshmail_email" value="{$FRESHMAIL_NEWSLETTER_EMAIL|escape:'htmlall':'UTF-8'}">
                                <input type="hidden" class="btn btn-block" name="subscribers_list_hash" id="subscribers_list_hash" value="sqkph37wid">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="button" class="btn btn-default pull-right" name="submitEmail"><i class="process-icon-save"></i> {l s='save' mod='freshmail'}</button>
                </div>
            </div>
        </div>
    </div>
</form>


<div class="form-wrapper">
    <div class="alert alert-info"><button data-dismiss="alert" type="button" class="close">
            ×
        </button>
        {l s='Install and configure the plugin by selecting the API and SMTP mail sender. Then select the list to which the addresses in FreshMail should be saved and mark from where the confirmation messages should be sent.' mod='freshmail'}

    </div>
</div>

<script type="application/javascript">
    $( document ).ready(function () {

        $('#subscriber_list_hash').change(function () {
            let newVal = $(this).val();

            if(!confirm("{l s='Are you sure to change list?' mod='freshmail'}")){
                $(this).val($(this).data('val'));
                return false;
            }
            $(this).data('val', newVal);
            return true;
        });
    })

    $(document).on('click', '#resetSettings', function (){
        if(confirm("{l s='Are you sure to clear settings?' mod='freshmail'}")){
            return true;
        }
        return false;
    });

</script>