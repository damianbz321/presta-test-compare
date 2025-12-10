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
<link rel="stylesheet" href="../modules/{$ptm_addon}/lib/htmlTemplatesManager/css/htmlTemplatesManager.css">

<div id="content" class="bootstrap" style="padding:0px!important;">
    <div class="panel clearfix">
        <div class="panel-heading">{l s='html templates manager for module' mod='tinymcepro'} </div>
        <div class="col-lg-2" id="htmlTemplatesManagerMenu">
            <div class="productTabs">
                <ul class="tab">
                    <li class="tab-row">
                        <a class="ptm_tab_page selected" id="ptm_link_manageTemplates"
                           href="javascript:displayptmTab('manageTemplates');">1. {l s='Edit templates' mod='tinymcepro'}</a>
                        <a class="ptm_tab_page" id="ptm_link_createTemplates"
                           href="javascript:displayptmTab('createTemplates');">2. {l s='Create template' mod='tinymcepro'}</a>
                    </li>
                </ul>
            </div>
            {if $ptm_additional_variables != false}
                <div class="panel">
                    <div class="alert alert-info">
                        <strong>{l s='additional html variables available exclusively for this module' mod='tinymcepro'}</strong>
                    </div>
                    {foreach $ptm_additional_variables as $variable => $key}
                        {$variable}
                        <br/>
                    {/foreach}
                </div>
            {/if}
        </div>
        <div class="col-lg-10">
            <div id="ptm_manageTemplates" class="ptm_tab tab-pane" style="display:none;">
                {$ptm_templates nofilter}
            </div>
            <div id="ptm_createTemplates" class="ptm_tab tab-pane" style="display:none;">
                {$ptm_create_template nofilter}
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<script>
    var ptm_module_url = '{$ptm_module_url|escape:javascript}';
    $(document).ready(function () {
        displayptmTab('manageTemplates');
        BindptmScripts();
    });
</script>
