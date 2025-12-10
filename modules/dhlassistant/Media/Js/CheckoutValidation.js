document.addEventListener("DOMContentLoaded", function (event) {
    function shippingMethodChange() {
        var activeRadio = $(".delivery-options input[type='radio']:checked");
        var elValue = parseInt(activeRadio.val());
        var carrierCode = $("#dhlassistant_carrier_code_" + elValue).val();
        var requireSelection = $('#dhlassistant_need_selection_' + elValue).val();
        var parcelIdent = $('#dhlassistant_parcel_ident_value_' + carrierCode).val();
        if (requireSelection && (typeof parcelIdent === 'undefined' || "" === parcelIdent)) {
            $('button[name="confirmDeliveryOption"]').attr('disabled', true);
            console.log(carrierCode);
        }
        else if (carrierCode == ['PL_CARRIER_POP'] || carrierCode == ['PL_CARRIER_POP_COD'] || carrierCode == ['FOREIGN_CARRIER_PARCELSHOP']) {
            $('button[name="confirmDeliveryOption"]').attr('disabled', true);
            $( ".current-address" ).hide();
        }
        else {
            $('button[name="confirmDeliveryOption"]').attr('disabled', false);
        }
    }

    function parcelMapChange() {
            $('button[name="confirmDeliveryOption"]').attr('disabled', true);
            $( ".current-address" ).hide();
    }

    $(".delivery-options input[type='radio']").change(function (e) {
        shippingMethodChange();
    });

    $('#dhlassistant_ps_checkbox').bind('change', parcelMapChange);
    $('#dhlassistant_pl_checkbox').bind('change', parcelMapChange);

    shippingMethodChange();
});