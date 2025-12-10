{*
* 2012-2019 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Facebook Pixel Tracking © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2019 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD Facebook Pixel Tracking PrestaShop 1.7.x Module
* @version   1.1.0
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      24-05-2019
*}

<script type="text/javascript">
    function execAddPaymentInfo() {
        console.log('Fired up event: AddPaymentInfo');
        fbq('track', 'AddPaymentInfo', {
            content_ids: {$content_ids nofilter},
            value: {$value nofilter},
            content_type: '{$content_type nofilter}',
            currency: '{$currency nofilter}'
        });             
    }	
</script>


