{*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * Email Templates Manager
 * version 1.6.1
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
*}

<script type="text/javascript" src="../modules/{$etm_addon}/lib/emailTemplatesManager/js/emailTemplatesManager.js"></script>
<script type="text/javascript" src="../js/tiny_mce/tinymce.min.js"></script>
<script type="text/javascript" src="../js/admin/tinymce.inc.js"></script>
<script>
    var iso = '{$etm_iso|addslashes}';
    var pathCSS = '{$smarty.const._THEME_CSS_DIR_|addslashes}';
    var ad = '{$etm_ad|addslashes}';
</script>
<a href="{$etm_module_url}" class="btn button btn-default emailTemplatesManager"><i class="process-icon-edit"></i>{l s='Email templates manager' mod='ordercoupons'}</a>
