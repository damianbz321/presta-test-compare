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

{$pla_scripts nofilter}
<div class="productTabs">
    <ul class="tab nav nav-tabs">
        <li class="tab-row active">
            <a class="tab-page" id="module_page_link_settings" href="javascript:displayModulePageTab('settings');"><i class="icon-wrench"></i> {l s='Module settings' mod='pla'}</a>
        </li>
        <li class="tab-row">
            <a class="tab-page" id="module_page_link_descriptions" href="javascript:displayModulePageTab('descriptions');"><i class="icon-wrench"></i> {l s='Attributes descriptions' mod='pla'}</a>
        </li>
        <li class="tab-row">
            <a class="tab-page" id="module_page_link_conditions" href="javascript:displayModulePageTab('conditions');"><i class="icon-wrench"></i> {l s='Visibility conditions' mod='pla'}</a>
        </li>
        <li class="tab-row">
            <a class="tab-page" id="module_page_link_hide" href="javascript:displayModulePageTab('hide');"><i class="icon-wrench"></i> {l s='Hide combinations' mod='pla'}</a>
        </li>
    </ul>
</div>
<div id="module_page_settings" class="panel module_page_tab" style="display: block;">
    <div class="alert alert-info">
        {l s='This form allows to configure the way of how module will appear on list of products in your shop' mod='pla'}
    </div>
    {$pla_settings_form nofilter}
</div>
<div id="module_page_descriptions" class="panel module_page_tab" style="display: none;">
    <div class="alert alert-info">
        {l s='Below you can define the description for each available attribute in your shop. If you enabled feature to display descriptions of the attributes - module will display these descriptions near attributes.' mod='pla'}
    </div>
    {$pla_descriptions_form nofilter}
</div>
<div id="module_page_conditions" class="panel module_page_tab" style="display: none;">
    <div class="alert alert-info">
        {l s='If you want to display module only for selected products - enable feature below and define the list of products where module will appear' mod='pla'}
    </div>
    {$pla_visibility_form nofilter}
</div>
<div id="module_page_hide" class="panel module_page_tab" style="display: none;">
    <div class="alert alert-info">
        {l s='Module allows to hide some combinations that contains selected attributes - depending on browsed category page.' mod='pla'}
    </div>
    {$pla_hide_ca_feature nofilter}
    <div class="alert alert-info">
        {l s='Form below allows to create new conditions. Firstly search for attributes. Then select categories where you want to hide combinations created with selected attributes' mod='pla'}
    </div>
    {$pla_hide_form nofilter}
    <div class="alert alert-info">
        {l s='Below you can find list of created conditions' mod='pla'}
    </div>
    {$pla_ca_list nofilter}
</div>