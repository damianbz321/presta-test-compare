{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author mypresta.eu
* @copyright  VEKIA|PL VATEU 9730945634
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

<div class="panel">
    <h3>{l s='Select features to display on list of products' mod='fplist'}</h3>
    <table class="odd table table-responsive">
        <tr>
            <th>-</th>
            <th>{l s='Name' mod='tplist'}</th>
        </tr>
        {foreach Feature::getFeatures(Context::getContext()->language->id) AS $f}
            <tr>
                <td style="width:20px; "><input value="{$f.id_feature}" name="selected_features[]" {if in_array($f.id_feature, $selected_features)}checked="checked"{/if} type="checkbox" style="top:2px; position:relative;"/></td>
                <td>{$f.name}</td>
            </tr>
        {/foreach}
    </table>
</div>