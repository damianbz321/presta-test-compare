<div class="panel col-lg-6 comvou">
    <h3><i class="icon-wrench"></i> {l s='Order states' mod='comvou'}</h3>
    <div class="alert alert-info">
        {l s='Select order states. Module will send reminders for orders that have selected states only' mod='comvou'}
    </div>

    <div class="table-responsive-row clearfix">
        <table class="table configuration">
            <thead>
            <tr class="nodrag nodrop">
                <th class="fixed-width-xs text-center">
                    <span class="title_box">{l s='ID' mod='comvou'}</span>
                </th>
                <th class="">
                    <span class="title_box">{l s='Name' mod='comvou'}</span>
                </th>
                <th class="">
                    <span class="title_box">{l s='Select' mod='comvou'}</span>
                </th>
            </tr>
            </thead>
            <tbody>
            {foreach $orderStates AS $ostate}
                <tr class="pointer {if $ostate@iteration is odd by 1}odd{/if} {if in_array($ostate.id_order_state, $cvo_orderstates)}checked{/if}">
                    <td class="fixed-width-xs text-center">
                        {$ostate.id_order_state}
                    </td>
                    <td class="fixed-width-xs text-left">
                        <span class="label color_field" style="background-color:{$ostate.color}; color:{if Tools::getBrightness($ostate.color) < 128}white{else}#383838{/if}">
                            {$ostate.name}
                        </span>
                    </td>
                    <td>
                        <input type="checkbox" name="cvo_orderstates[]" value="{$ostate.id_order_state}" {if in_array($ostate.id_order_state, $cvo_orderstates)}checked="checked"{/if}>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>