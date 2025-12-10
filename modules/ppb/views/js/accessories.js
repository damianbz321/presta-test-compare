/*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-9999 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*/

$(document).ready(function(e) {
    function ppb_accessories_init(){
        $('.ajax_block_product td.clickable').click(function () {
            $(this).parent().find('.accessories_checkbox').click();
        });
        $(".product-add-to-cart button.add-to-cart").click(function () {
            $("input.accessories_checkbox").each(function (index, element) {
                if (this.checked) {
                    $.ajax({
                        type: 'POST',
                        headers: {"cache-control": "no-cache"},
                        url: prestashop.urls.base_url + '?rand=' + new Date().getTime(),
                        async: false,
                        cache: false,
                        dataType: "json",
                        data: 'controller=cart&add=1&ajax=true&qty=1&id_product=' + this.value + '&token=' + prestashop.static_token
                    });
                }
            });
        });
    }
    ppb_accessories_init();
    prestashop.on('updatedProduct', function () {
        ppb_accessories_init();
    });
});
