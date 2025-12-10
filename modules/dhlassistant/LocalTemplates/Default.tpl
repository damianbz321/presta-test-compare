<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function (event) {

        $('#delivery_option_{$dhlassistant_carrier_id}').click(function (e) {
            if ($('#delivery_option_{$dhlassistant_carrier_id}').is(':checked')
                && !['PL_CARRIER_POP', 'PL_CARRIER_POP_COD', 'FOREIGN_CARRIER_PARCELSHOP'].includes('{$dhlassistant_carrier_code}')
            ) {
                $('button[name="confirmDeliveryOption"]').attr('disabled', false);
            } else {
                $('button[name="confirmDeliveryOption"]').attr('disabled', true);
            }
        });
    })
</script>
