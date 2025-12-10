{**
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-2021 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER
* support@mypresta.eu
*}

<div class="panel col-lg-12">
    <h3><i class="icon-wrench"></i> {l s='Groups of customers' mod='ordercoupons'}</h3>
    <div class="alert alert-info">
        {l s='Select customer groups. Module will generate this reward when customer that placed an order will be associated with at least one from selected groups' mod='ordercoupons'}
    </div>

    <div class="table-responsive-row clearfix">
        <table class="table configuration">
            <thead>
            <tr class="nodrag nodrop">
                <th class="fixed-width-xs text-center">
                    <span class="title_box">{l s='ID' mod='ordercoupons'}</span>
                </th>
                <th class="">
                    <span class="title_box">{l s='Name' mod='ordercoupons'}</span>
                </th>
                <th class="">
                    <span class="title_box">{l s='Select' mod='ordercoupons'}</span>
                </th>
            </tr>
            </thead>
            <tbody>
            {foreach $oc_customerGroups AS $cgroup}
                <tr class="pointer {if $cgroup@iteration is odd by 1}odd{/if}">
                    <td class="fixed-width-xs text-center">
                        {$cgroup.id_group}
                    </td>
                    <td class="fixed-width-xs text-left">
                        {$cgroup.name}
                    </td>
                    <td>
                        <input type="checkbox" name="oc_cgroup[]" value="{$cgroup.id_group}" {if in_array($cgroup.id_group, $oc_cgroup)}checked="checked"{/if}>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>