<div id="dhlassistant_{$dhlassistant_carrier_code}'" style="display:block; width: 100%" class="box">

    {if $dhlassistant_is_ps_available}
        <div id="dhlassistant_ps_selector"{if $dhlassistant_is_ps_only_service} style="display:block;"{/if}
             class="checkbox">
            <div class="field-parcelshop"
                    {if $dhlassistant_iso_code !== "DE"} style="display: none"{/if}
            >
                <label class="label">{l s='Send to Parcelshop' mod='dhlassistant'}</label>
                <input type="checkbox"
                       id="dhlassistant_ps_checkbox" checked="checked">
            </div>
        </div>
    {/if}
    {if $dhlassistant_is_pl_available}
        <div id="dhlassistant_pl_selector"
                {if $dhlassistant_iso_code != "DE"}   style="display: none" {/if}
             class="checkbox">
            <label class="label">{l s='Send to Parcelstation' mod='dhlassistant'}</label>
            <input type="checkbox" id="dhlassistant_pl_checkbox" checked="checked">
        </div>
    {/if}

    <div id="dhlassistant_parcel_identt" class="form-group" style="display: none">
        <label class="label">{l s='Nr. Parcelshop:' mod='dhlassistant'}</label> <input type="text"
                                                                                       id="dhlassistant_parcel_ident_value_{$dhlassistant_carrier_code}"
                                                                                       class="form-control"
                                                                                       value="{$dhlassistant_parcel_ident|escape}">
    </div>
    <div id="dhlassistant_postnummer" class="form-group" style="display: none">
        <label class="label" style="display: block; text-align: left;">Postnummer</label> <input type="text"
                                                                             id="dhlassistant_postnummer_value"
                                                                             class="form-control"
                                                                             style="display: block"
                                                                             value="{$dhlassistant_postnummer|escape}">
    </div>
    <div id="dhlassistant_parcel_postal_code" class="form-group" style="display: none">
        <label class="label" style="display: none">Kod pocztowy Parcelshop</label> <input type="text"
                                                                                          id="dhlassistant_parcel_postal_code_value"
                                                                                          style="display: none"
                                                                                          class="form-control"
                                                                                          value="{$dhlassistant_parcel_postal_code|escape}">
    </div>

    {if ($dhlassistant_is_ps_available || $dhlassistant_is_pl_available)  && $dhlassistant_is_map_for_parcel_available}
        <div id="dhlassistant_map_{$dhlassistant_carrier_code}">

        </div>
    {/if}
    <div id="dhlassistant_save" style="display:none;">
        <button class="button button-medium"
                onclick="SaveDhlCarrierOptions_{$dhlassistant_carrier_code}(); return false;">
            <span>{l s='Save' mod='dhlassistant'}</span></button>
    </div>
    <div id="dhlassistant_messages">
        <div id="SCROLL_TO_{$dhlassistant_carrier_code}"></div>
        <div class="alert alert-danger error" style="display:none;">
            <p>{l s='Invalid data or write error!' mod='dhlassistant'}</p>
        </div>
        <p class="notice alert alert-success success current-address" style="display: none;">

        </p>
    </div>
    <input id="{$dhlassistant_carrier_code}_dhlassistant_ajax_catcher_url" type="hidden" name="dhlassistant_ajax_catcher_url" value="{$dhlassistant_ajax_catcher_url}">
    <input id="{$dhlassistant_carrier_code}_dhlassistant_carrier_id" type="hidden" name="dhlassistant_carrier_id" value="{$dhlassistant_carrier_id}">
    <input id="{$dhlassistant_carrier_code}_dhlassistant_is_ps_available" type="hidden" name="dhlassistant_is_ps_available" value="{$dhlassistant_is_ps_available}">
    <input id="{$dhlassistant_carrier_code}_dhlassistant_is_ps_only_service" type="hidden" name="dhlassistant_is_ps_only_service" value="{$dhlassistant_is_ps_only_service}">
    <input id="{$dhlassistant_carrier_code}_dhlassistant_is_pl_available" type="hidden" name="dhlassistant_is_pl_available" value="{$dhlassistant_is_pl_available}">
    <input id="{$dhlassistant_carrier_code}_dhlassistant_is_map_for_parcel_available" type="hidden" name="dhlassistant_is_map_for_parcel_available"
           value="{$dhlassistant_is_map_for_parcel_available}">
    <input id="{$dhlassistant_carrier_code}_dhlassistant_map_for_ps_url" type="hidden" name="dhlassistant_map_for_ps_url" value="{$dhlassistant_map_for_ps_url}">
    <input id="{$dhlassistant_carrier_code}_dhlassistant_map_for_pl_url" type="hidden" name="dhlassistant_map_for_pl_url" value="{$dhlassistant_map_for_pl_url}">
    <input id="{$dhlassistant_carrier_code}_dhlassistant_map_for_pl_url_cod" type="hidden" name="dhlassistant_map_for_pl_url_cod" value="{$dhlassistant_map_for_pl_url_cod}">
    <input id="{$dhlassistant_carrier_code}_dhlassistant_require_postnummer_for_pl" type="hidden" name="dhlassistant_require_postnummer_for_pl"
           value="{$dhlassistant_require_postnummer_for_pl}">
    <input id="{$dhlassistant_carrier_code}_dhlassistant_require_postalcode_for_ps" type="hidden" name="dhlassistant_require_postalcode_for_ps"
           value="{$dhlassistant_require_postalcode_for_ps}">
    <input id="{$dhlassistant_carrier_code}_dhlassistant_require_postalcode_for_pl" type="hidden" name="dhlassistant_require_postalcode_for_pl"
           value="{$dhlassistant_require_postalcode_for_pl}">

    {if $dhlassistant_carrier_code|in_array:['PL_CARRIER_POP', 'PL_CARRIER_POP_COD', 'FOREIGN_CARRIER_PARCELSHOP']}
        <input type="hidden" id="dhlassistant_need_selection_{$dhlassistant_carrier_id}"
               name="dhlassistant_need_selection" value="1">
        <input type="hidden" id="dhlassistant_carrier_code_{$dhlassistant_carrier_id}" name="dhlassistant_carrier_code"
               value="{$dhlassistant_carrier_code}">
    {/if}

    <script type="text/javascript" src="{$dhlassistant_carrier_handling_js_url}"></script>
    <script type="text/javascript" src="{$dhlassistant_checkout_validation_js_url}"></script>

</div>
<style type="text/css">
    .box .form-group .form-control {
        max-width: 271px;
    }

    #dhlassistant_map iframe {
        max-height: 813px;
        max-width: 835px;
    }
</style>