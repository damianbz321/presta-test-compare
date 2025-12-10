/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

var ets_mc_func = {
    getE: function (name) {
        if (document.getElementById)
            var elem = document.getElementById(name);
        else if (document.all)
            var elem = document.all[name];
        else if (document.layers)
            var elem = document.layers[name];
        return elem;
    },
    gencode: function (name, size) {
        getE(name).value = '';
        /* There are no O/0 in the codes in order to avoid confusion */
        var chars = "123456789abcdefghijklmnpqrstuvwxyz";
        for (var i = 1; i <= size; ++i)
            getE(name).value += chars.charAt(Math.floor(Math.random() * chars.length));
    },
    copyToClipboard(ele) {
        ele.select();
        ele.parent().addClass('parents_copied').find('.data_copied').addClass('copied');
        document.execCommand("copy");
        setTimeout(function () {
            $('.data_copied').removeClass('copied').parent().removeClass('parents_copied');
        }, 1500);
    }
};

jQuery(document).ready(function () {
    const $ = jQuery;
    $('#ets_mc_gencode').click(function (e) {
        e.preventDefault();
        ets_mc_func.gencode('ETS_MC_ACCESS_TOKEN', 10);
    });
    $('.ets_mc_copied').click(function (e) {
        ets_mc_func.copyToClipboard($(this));
    });
});

