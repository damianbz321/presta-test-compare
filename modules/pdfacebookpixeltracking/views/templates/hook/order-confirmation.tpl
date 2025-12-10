{*
* 2012-2016 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Facebook Pixel Tracking © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2016 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD Facebook Pixel Tracking PrestaShop 1.5.x and 1.6.x Module
* @version   1.1.0
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      24-05-2016
*}

<!-- PD Facebook Pixel Code - PURCHASE -->
<script type="text/javascript">
{if $products_multiple}
    fbq('track', 'Purchase', {
        content_ids: {$content_ids nofilter},
        content_type: '{$content_type nofilter}',
        value: {$value nofilter}, 
        currency: '{$currency nofilter}'
    });
{else}
    fbq('track', 'Purchase', {
        content_name: '{$content_name}',
        content_category: '{$content_category}',
        content_ids: {$content_ids nofilter},
        content_type: '{$content_type nofilter}',
        value: {$value nofilter}, 
        currency: '{$currency nofilter}'
    });
{/if}
</script>
<!-- End PD Facebook Pixel Code - PURCHASE -->


