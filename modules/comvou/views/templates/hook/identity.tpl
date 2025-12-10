{*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2020 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
*}
<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        {if cunsub::checkUnsubs(Context::getContext()->customer->email) == false}
        $('input[name=cunsub]').prop('checked', true);
        {/if}
    });
</script>