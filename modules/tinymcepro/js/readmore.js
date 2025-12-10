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
    $('.tinymcepro-more-button').click(function (e) {
        e.preventDefault();
        $(this).closest('.tinymcepro-readmore').find('.tinymcepro-more-contents-to-show').slideToggle(200);
    });
});