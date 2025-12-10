/**
* 2012-2018 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Pop-Ups Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2018 Patryk Marek - PrestaDev.pl
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @link      http://prestadev.pl
* @package   PD Pop-Ups Pro - PrestaShop 1.5.x and 1.6.x Module
* @version   1.2.4
* @date      10-06-2015
*/

$(document).ready(function(){
    $option_selected = $('select#voucher_type').val();
    switchOptionsWithSelection($option_selected);
   	//alert($option_selected);

    $('select#voucher_type').change(function() {
        $option_selected = $(this).find("option:selected").attr('value');
        switchOptionsWithSelection($option_selected);
    });

    function switchOptionsWithSelection($option_selected) {
        
        // Disable some fields
        if ($option_selected == 1) 
        {
            $('#voucher_reduction_currency').prop('disabled', true);
            $('#voucher_reduction_tax').prop('disabled', true);
        } else {
            $('#voucher_reduction_currency').prop('disabled', false);
            $('#voucher_reduction_tax').prop('disabled', false);
        }
    }

});
