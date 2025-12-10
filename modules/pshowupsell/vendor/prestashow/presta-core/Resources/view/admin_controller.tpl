<style>
    .toolbar_btn.btn-help {
        display: none !important;
    }

    .toolbar_btn#page-header-desc-configuration-update {
        background: #ff4f4f;
        border-color: #ff4f4f;
        color: #fff;
    }
    /*.toolbar_btn#page-header-desc-configuration-ok {*/
    /*    background: #008d2b;*/
    /*    border-color: #008d2b;*/
    /*}*/
</style>

{function showTip type='success' id='' message='No message'}
    {if !PShow_Settings::getInstance($smarty.current_dir)->get('tip_'|cat:$id)}
        <div class="alert alert-{$type} fade in tip" id="{$id}">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <p>{$message}</p>
        </div>
    {/if}
{/function}

{assign var='modulePath' value=('../../../../../../'|cat:$module->name|cat:'/')}

<script>
    if (typeof SELECT_TAB !== 'undefined') {
        SELECT_TAB.init('{$select_menu_tab}');
    }
    let PSHOW_MODULE_CLASS_NAME_ = "{$PSHOW_MODULE_CLASS_NAME_}";
    let SETTINGS_URL = "{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Settings", true)}";
    let MOD_SETTINGS = JSON.parse('{$mod_settings|json_encode}');

    {if PShow_Settings::getInstance($smarty.current_dir)->get('fold_menu_on_enter')}
    $('body').addClass('page-sidebar-closed');
    {/if}

    // fix bug - prestashop 1.7.6.2 - address_token not defined
    $(document).ready(function () {
        if (typeof window.address_token === "undefined") {
            let match = RegExp('[?&]' + 'token' + '=([^&]*)').exec(window.location.search);
            window.address_token = match && decodeURIComponent(match[1].replace(/\+/g, ' '));
        }
    });
</script>

<div class="row">

    {if $action|lower == 'allnotifications'}
        <div class="col-lg-12 modulecontainer">
            {include file="./admin/main_allnotifications.tpl"}
        </div>
    {else}
        <div class="col-lg-2">

            <div class="tabs">
                <div class="list-group text-center">

                    <strong>
                        <a class="list-group-item inactive">
                            <big>
                                {$module->displayName}
                            </big>
                        </a>
                    </strong>

                    {include file=$modulePath|cat:'views/templates/side_menu.tpl'}

                    <a class="list-group-item {if $smarty.get.controller == "{$PSHOW_MODULE_CLASS_NAME_}Hook"}active{/if}"
                       href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Hook", true)}">
                        {l s='Positions' mod='pshowsystem'}
                    </a>

                    <a class="list-group-item {if $smarty.get.controller == "{$PSHOW_MODULE_CLASS_NAME_}Settings"}active{/if}"
                       href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Settings", true)}">
                        {l s='Module settings' mod='pshowsystem'}
                    </a>

                </div>
            </div>

            {if isset($hasMultistoreAddon)}
                <div class="panel">
                    <h3>
                        {l s='Module Extensions' mod='pshowsystem'}
                    </h3>
                    <div>
                        <div>
                            {l s='Multistore'}
                            <span class="pull-right label {if !$hasMultistoreAddon}label-danger{else}label-success{/if}">
                            {if $hasMultistoreAddon}
                                {l s='Active'}
                            {else}
                                {l s='Inactive'}
                            {/if}
                            </span>
                        </div>
                    </div>
                </div>
            {/if}

            {if isset($pshowHook_below_side_menu)}{$pshowHook_below_side_menu}{/if}

            {if isset($serverConfig)}
                <div class="panel">
                    <h3>
                        {l s='Server info' mod='pshowsystem'}
                    </h3>
                    <div>
                        {foreach from=$serverConfig item='item'}
                            <div>
                                {$item.label}:
                                <span class="pull-right label {if !$item.is_ok}label-danger{else}label-success{/if}">
                                    {$item.value}
                                </span>
                            </div>
                        {/foreach}
                        <br>
                        <a href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Update", true)}&page=phpinfo"
                           class="btn btn-sm btn-default">
                            click to see phpinfo
                        </a>
                    </div>
                </div>
            {else}
                <div class="panel">
                    <h3>{l s='Recommended' mod='pshowsystem'}</h3>
                    {if $recommended['image']}
                        <div style="margin: -15px -20px 0;">
                            <a href="{$recommended['url']}" target="_blank">
                                <img class="img-responsive" alt="" src="{$recommended['image']}">
                            </a>
                        </div>
                    {/if}
                    <a href="{$recommended['url']}" target="_blank"><h2>{$recommended['name']}</h2></a>
                    <p>{$recommended['description']}</p>
                    <div class="panel-footer">
                        <a href="{$recommended['url']}" target="_blank" class="btn btn-primary btn-block">
                            <h4>
                                {l s='Discover'}
                            </h4>
                        </a>
                    </div>
                </div>
            {/if}

        </div>
        <div class="col-lg-10 modulecontainer">

            <div class="alert alert-danger {$PSHOW_MODULE_CLASS_NAME_}-update-available"
                    {if !$isUpdateAvailable} style="display: none;"{/if}>
                {l s='Update your module! Updates are very important.' mod='pshowsystem'}
            </div>

            <div id="module_content">
                {include file='./admin/alerts.tpl'}
                {include file='./admin/tips.tpl'}

                {if isset($content) && $content}
                    {$content}
                {else}
                    <div class="{if isset($module_content_container)}{$module_content_container}{/if}">
                        {if in_array($controllername, array('settings', 'hook', 'backup', 'update', 'reportbug'))}
                            {include file="./admin/{$controllername|lower}_{$action|lower}.tpl"}
                        {else}
                            {include file=$modulePath|cat:"views/templates/admin/{$controllername|lower}_{$action|lower}.tpl"}
                        {/if}
                    </div>
                {/if}
            </div>
        </div>
    {/if}
    <div class="clearfix"></div>
</div>
