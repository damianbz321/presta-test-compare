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
<link rel="stylesheet" href="../modules/{$etm_addon}/lib/smartyTemplatesManager/css/smartyTemplatesManager.css">

<div id="content" class="bootstrap" style="padding:0px!important;">
    <div class="panel clearfix">
        <div class="panel-heading">{l s='Smarty templates manager for module' mod='ppb'} </div>
        <div class="col-lg-12">
            <div id="etm_manageTemplates" class="etm_tab tab-pane" style="display:none;">
                {$etm_templates nofilter}
            </div>
            <div id="etm_createTemplates" class="etm_tab tab-pane" style="display:none;">
                {$etm_create_template nofilter}
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<script>
    var etm_module_url = '{$etm_module_url|escape:javascript}';
    $(document).ready(function () {
        displayEtmTab('manageTemplates');
        BindEtmScripts();
    });
</script>
