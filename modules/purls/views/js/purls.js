/*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA Miłosz Myszczuk VATEU: PL9730945634
 * @copyright 2010-2022 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */


$(document).ready(function () {
    purls_hash = $(location).attr('hash');
    purls_hash = purls_hash.replace('#/', '');
    //chars = purls_hash.split('/');
    $.each(purls_attributes, function (key, i) {
        if (key == purls_hash) {
            chars = i.split('/');
            $.each(chars, function (kkey, ii) {
                character_attribute = chars[kkey].split('-');
                splitted_character_attribute = character_attribute[0].split('|');
                var name = 'select[name="group[' + splitted_character_attribute[0] + ']"]';
                $('select[name="group[' + splitted_character_attribute[0] + ']"]').val(splitted_character_attribute[1]);
                $('input[name="group[' + splitted_character_attribute[0] + ']"]').val(splitted_character_attribute[1]);
                $('#quantity_wanted').trigger('change');
            });
        }
    });
});

