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

$(document).on('click', '.panel-heading', function (e) {
    e.preventDefault();
    var $this = $(this);
    if ($($this.find('a').attr('href')).hasClass('in')) {
        $(($this.find('a').attr('href'))).slideUp().removeClass('in');
    } else {
        $(($this.find('a').attr('href'))).slideDown().addClass('in');
    }
});