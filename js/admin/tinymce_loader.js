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
    tinySetup({
        editor_selector: "autoload_rte",
        setup: function (ed) {
            ed.on('keydown', function (ed, e) {
                handleCounterTiny(tinymce.activeEditor.id);
            });

            ed.on('loadContent', function(ed, e) {
                handleCounterTiny(tinymce.activeEditor.id);
            });

            ed.on('change', function(ed, e) {
                tinyMCE.triggerSave();
                handleCounterTiny(tinymce.activeEditor.id);
            });

            ed.on('blur', function(ed) {
                tinyMCE.triggerSave();
            });
        }
    });

    function handleCounterTiny(id) {
        $(document).ready(function (){
            var textarea = $('#'+id);
            var counter = textarea.attr('counter');
            var counter_type = textarea.attr('counter_type');
            var max = (tinyMCE.activeEditor.getBody() === null) ? 24000:tinyMCE.activeEditor.getBody().textContent.length ;

            textarea.parent().find('span.currentLength').text(max);
            if ('recommended' !== counter_type && max > counter) {
                textarea.parent().find('span.maxLength').addClass('alert alert-danger');
            } else {
                textarea.parent().find('span.maxLength').removeClass('alert alert-danger');
            }
        });
    }
});