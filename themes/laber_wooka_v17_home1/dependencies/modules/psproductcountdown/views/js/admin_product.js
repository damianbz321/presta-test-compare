/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2016 Presta.Site
 * @license   LICENSE.txt
 */
$(function(){
    $(document).on('change', '#pspc_specific_price', function () {
        if ($(this).val()) {
            var from = $(this).find('option:selected').data('from');
            var to = $(this).find('option:selected').data('to');
            $('#pspc_from').val(from);
            $('#pspc_to').val(to);
        }
    });

    $(document).on('click', '#pspc-reset-countdown',function(){
        var id_countdown = $(this).data('id-countdown');

        $('#psproductcountdown').find('input[type=text], select').val('');

        $.ajax({
            url: pspc_ajax_url,
            data: {ajax: true, action: 'removeCountdown', id_countdown: id_countdown},
            method: 'post',
            success: function () {
                location.reload();
            }
        });
    })
});
