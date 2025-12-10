{*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * html Templates Manager
 * version 1.7.2
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
*}

<script type="text/javascript" src="../modules/{$ptm_addon}/lib/htmlTemplatesManager/js/htmlTemplatesManager.js"></script>
<script type="text/javascript" src="../js/tiny_mce/tinymce.min.js"></script>
<script type="text/javascript" src="../js/admin/tinymce.inc.js"></script>
<script>
    var iso = '{$ptm_iso|addslashes}';
    var pathCSS = '{$smarty.const._THEME_CSS_DIR_|addslashes}';
    var ad = '{$ptm_ad|addslashes}';
</script>
<a href="{$ptm_module_url}" class="btn button btn-default htmlTemplatesManager"><i class="process-icon-edit"></i>{l s='html templates manager' mod='tinymcepro'}</a>
