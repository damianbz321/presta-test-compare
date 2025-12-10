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
    function pla_prepare_selection_form() {
        if ($('select[name="pla_image"] option:selected').val() == 0) {
            $('select[name="pla_defimg"]').parent().parent().hide();
            $('select[name="pla_imagetype"]').parent().parent().hide();
        } else {
            $('select[name="pla_defimg"]').parent().parent().show();
            $('select[name="pla_imagetype"]').parent().parent().show();
        }

        if ($('select[name="pla_dropdown"] option:selected').val() == 0) {
            $('select[name="pla_popup"]').parent().parent().hide();
            $('select[name="pla_addtocart_hide"]').parent().parent().hide();
        } else {
            $('select[name="pla_popup"]').parent().parent().show();
            $('select[name="pla_addtocart_hide"]').parent().parent().show();
        }

        if ($('select[name="pla_color_attr"] option:selected').val() == 0) {
            $('select[name="pla_color_attr_name"]').parent().parent().hide();
        } else {
            $('select[name="pla_color_attr_name"]').parent().parent().show();
        }

        if ($('select[name="pla_comb"] option:selected').val() == 0) {
            $('select[name="pla_comb_label"]').parent().parent().hide();
            $('select[name="pla_desc"]').parent().parent().hide();
        } else {
            $('select[name="pla_comb_label"]').parent().parent().show();
            $('select[name="pla_desc"]').parent().parent().show();
        }

        if ($('select[name="pla_price"] option:selected').val() == 0) {
            $('select[name="pla_price_before"]').parent().parent().hide();
            $('select[name="pla_price_logonly"]').parent().parent().hide();
            $('select[name="pla_prices_tax"]').parent().parent().hide();
            $('select[name="pla_lab"]').parent().parent().hide();
        } else {
            $('select[name="pla_price_before"]').parent().parent().show();
            $('select[name="pla_price_logonly"]').parent().parent().show();
            $('select[name="pla_prices_tax"]').parent().parent().show();
            $('select[name="pla_lab"]').parent().parent().show();
        }

    }

    $('document').ready(function () {
        $('select[name="pla_image"], select[name="pla_dropdown"], select[name="pla_color_attr"], select[name="pla_comb"], select[name="pla_price"]').change(function () {
            pla_prepare_selection_form();
        });
        pla_prepare_selection_form();


        {if Tools::getValue('plaacOrderway', 'false') != 'false' or
            Tools::getValue('plaacOrderby', 'false') != 'false' or
        (Tools::getValue('deleteconfiguration', 'false') != 'false' && Tools::getValue('id_plaac', 'false') != 'false') or
        (Tools::getValue('statusconfiguration', 'false') != 'false' && Tools::getValue('id_plaac', 'false') != 'false')}
        displayModulePageTab('hide');
        {/if}
    });

    function displayModulePageTab(tab) {
        $('.module_page_tab').hide();
        $('.tab-row.active').removeClass('active');
        $('#module_page_' + tab).show();
        $('#module_page_link_' + tab).parent().addClass('active');
    }
</script>