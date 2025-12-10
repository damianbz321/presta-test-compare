{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2016 Presta.Site
* @license   LICENSE.txt
*}
<div id="pspc-pro-features">
    {if $psv == 1.5}
        <br/><fieldset><legend>{l s='PRO features' mod='psproductcountdown'} <a href="#" id="pspc_show_pro">({l s='show' mod='psproductcountdown'})</a></legend>
    {else}
        <div class="panel">
        <div class="panel-heading">
            <i class="icon-thumbs-o-up"></i> {l s='PRO features' mod='psproductcountdown'} <a href="#" id="pspc_show_pro">({l s='show' mod='psproductcountdown'})</a>
        </div>
    {/if}
        <div class="form-wrapper">
            <div class="form-group" style="display: none;" id="pspc_pro_features_content">
                {if $psv == 1.5}
                    {l s='Buy a PRO version of this module and get extra customization features' mod='psproductcountdown'} (<a target="_blank" href="http://presta.site/psmodule?module=pspc_pro"><b>Product Countdown PRO</b></a>):
                {else}
                    {l s='Buy a [1]PRO version[/1] of this module and get extra customization features:' tags=['<a target="_blank" href="http://presta.site/psmodule?module=pspc_pro">'] mod='psproductcountdown'}
                {/if}
                <ul style="padding-top: 5px;">
                    <li>{l s='new awesome themes' mod='psproductcountdown'}</li>
                    <li>{l s='options to configure font size, background, colors etc.' mod='psproductcountdown'}</li>
                    <li>{l s='options to choose module position in the product list and at the product page' mod='psproductcountdown'}</li>
                </ul>
                <a target="_blank" href="http://presta.site/psmodule?module=pspc_pro" title="{l s='new awesome themes' mod='psproductcountdown'}"><img src="{$img_path|escape:'html':'UTF-8'}pro-themes.png" /></a>
                <br/>
                <a target="_blank" href="http://presta.site/psmodule?module=pspc_pro" title="{l s='an option to show countdown over product image in the product list' mod='psproductcountdown'}"><img src="{$img_path|escape:'html':'UTF-8'}over.png" /></a>
            </div>
        </div>
    {if $psv == 1.5}
        </fieldset><br/>
    {else}
        </div>
    {/if}
</div>
