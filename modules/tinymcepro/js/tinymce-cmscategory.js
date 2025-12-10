/*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
$(document).ready(function () {
    if ($('.admincmscontent form[name="cms_page_category"]').length >= 1) {
        $('.admincmscontent form[name="cms_page_category"] textarea').addClass('rte');
        var tiny_path_array = baseAdminDir.split('/');
        tiny_path_array.splice((tiny_path_array.length - 2), 2);
        var tiny_final_path = tiny_path_array.join('/');
        window.tinyMCEPreInit = {};
        window.tinyMCEPreInit.base = tiny_final_path + '/js/tiny_mce';
        window.tinyMCEPreInit.suffix = '.min';
        $.getScript(tiny_final_path + '/js/admin/tinymce.inc.js');
        $.getScript(tiny_final_path + '/js/admin/tinymce_loader.js');
        $.getScript(tiny_final_path + '/js/tiny_mce/tinymce.min.js');
        tinySetup();
    }
});