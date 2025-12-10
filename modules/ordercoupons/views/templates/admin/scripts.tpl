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
<script>
    function ordercouponsForm() {
        if ($('select[name="oc_cron"] option:selected').val() == 1) {
            $('input[name="oc_xdays"]').parent().parent().show();
        } else {
            $('input[name="oc_xdays"]').parent().parent().hide();
        }

        if ($('select[name="min"] option:selected').val() == 1) {
            $('input[name="min_order"]').parent().parent().show();
            $('select[name="min_currency"]').parent().parent().show();
        } else {
            $('input[name="min_order"]').parent().parent().hide();
            $('select[name="min_currency"]').parent().parent().hide();
        }

        if ($('select[name="max"] option:selected').val() == 1) {
            $('input[name="max_order"]').parent().parent().show();
            $('select[name="max_currency"]').parent().parent().show();
        } else {
            $('input[name="max_order"]').parent().parent().hide();
            $('select[name="max_currency"]').parent().parent().hide();
        }

        if ($('#ordercoupons_ordersdate_99').is(":checked")) {
            $('input[name="oc_date_from"]').parent().parent().parent().parent().show();
            $('input[name="oc_date_to"]').parent().parent().parent().parent().show();
        } else {
            $('input[name="oc_date_to"]').parent().parent().parent().parent().hide();
            $('input[name="oc_date_from"]').parent().parent().parent().parent().hide();
        }

        if ($('select[name="exvalcat"] option:selected').val() == 1) {
            $('input[name="excat"]').parent().parent().parent().show();
        } else {
            $('input[name="excat"]').parent().parent().parent().hide();
        }

        if ($('select[name="categories"] option:selected').val() == 1) {
            $('input[name="categories_list"]').parent().parent().parent().show();
        } else {
            $('input[name="categories_list"]').parent().parent().parent().hide();
        }

        if ($('select[name="manufacturers"] option:selected').val() == 1) {
            $('input[name="manufacturers_list"]').parent().parent().parent().show();
        } else {
            $('input[name="manufacturers_list"]').parent().parent().parent().hide();
        }

        if ($('select[name="prod"] option:selected').val() == 1) {
            $('input[name="prod_list"]').parent().parent().parent().show();
        } else {
            $('input[name="prod_list"]').parent().parent().parent().hide();
        }

        if ($('select[name="prod_eo"] option:selected').val() == 1) {
            $('input[name="prod_eo_list"]').parent().parent().parent().show();
        } else {
            $('input[name="prod_eo_list"]').parent().parent().parent().hide();
        }


        if ($('select[name="prod_diff_than"] option:selected').val() == 1) {
            $('input[name="prod_diff_than_list"]').parent().parent().parent().show();
        } else {
            $('input[name="prod_diff_than_list"]').parent().parent().parent().hide();
        }

        if ($('select[name="percentage_type"] option:selected').val() == 5 || $('select[name="percentage_type"] option:selected').val() == 6) {
            $('input[name="order_value_specific_product"]').parent().parent().parent().show();
            $('input[name="percentage_value"]').parent().parent().parent().show();
        } else {
            $('input[name="order_value_specific_product"]').parent().parent().parent().hide();
            $('input[name="percentage_value"]').parent().parent().parent().show();
        }

        if ($('select[name="percentage"] option:selected').val() == 1) {
            $('select[name="percentage_type"]').parent().parent().show();
            $('input[name="percentage_value"]').parent().parent().parent().show();
            $('input[name="order_value_specific_product"]').parent().parent().parent().show();

            if ($('select[name="percentage_type"] option:selected').val() == 5 || $('select[name="percentage_type"] option:selected').val() == 6) {
                $('input[name="order_value_specific_product"]').parent().parent().parent().show();
                $('input[name="percentage_value"]').parent().parent().parent().show();
            } else {
                $('input[name="order_value_specific_product"]').parent().parent().parent().hide();
                $('input[name="percentage_value"]').parent().parent().parent().show();
            }

        } else {
            $('select[name="percentage_type"]').parent().parent().hide();
            $('input[name="percentage_value"]').parent().parent().parent().hide();
            $('input[name="order_value_specific_product"]').parent().parent().parent().hide();
        }


    }

    $(document).ready(function () {
        $('select[name="categories"], select[name="percentage"], select[name="percentage_type"], select[name="prod"], select[name="prod_eo"], select[name="prod_diff_than"], select[name="exvalcat"], select[name="manufacturers"], select[name="max"], select[name="min"], select[name="oc_cron"], select[name="oc_counter"], input[name="oc_timeframe"]').change(function () {
            ordercouponsForm();
        });
        ordercouponsForm();
    });

</script>