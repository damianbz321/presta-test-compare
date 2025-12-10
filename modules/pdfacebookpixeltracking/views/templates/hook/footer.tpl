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

{if $tagType != 'none'}
<!-- PD Facebook Pixel Code - STANDARD EVENTS AND 1 CUSTOM EVENT -->
<script type="text/javascript" >

    {if ($tagType === 'product')}
    
        console.log('Fired up event: ViewContent > Product');
        fbq('track', 'ViewContent', {
            content_name: '{$content_name}',
            content_category: '{$content_category}',
            content_ids: '{$content_ids nofilter}',
            content_type: '{$content_type nofilter}',
            value: {$value nofilter},
            currency: '{$currency nofilter}'
        });

    {else if ($tagType === 'cart')}
        
        console.log('Fired up event: InitiateCheckout');
        fbq('track', 'InitiateCheckout', {
            content_ids: {$content_ids nofilter},
            value: {$value nofilter},
            content_type: '{$content_type nofilter}',
            num_items: {$num_items nofilter},
            currency: '{$currency nofilter}'
        });


    {else if ($tagType === 'category')}

        console.log('Fired up event: ViewCategory');
        fbq('trackCustom', 'ViewCategory', {
            content_name: '{$content_name}',
            content_category: '{$content_category}',
            content_ids: {$content_ids nofilter},
            content_type: '{$content_type nofilter}',
        });

    {else if ($tagType === 'search')}

        console.log('Fired up event: Search');
        fbq('track', 'Search', {
        	content_type: '{$content_type nofilter}',
            content_ids: {$content_ids_search nofilter},
            search_string: '{$search_string nofilter}',
            currency: '{$currency nofilter}'
        });

    {else if ($tagType === 'cms')}

        console.log('Fired up event: ViewCMS');
        fbq('track', 'ViewCMS', {
            content_name: '{$content_name}',
            content_category: '{$content_category }'
        });

    {/if}

    {if isset($account_created)}

        console.log('Fired up event: CompleteRegistration');
        fbq('track', 'CompleteRegistration', {
            content_name: '{$registration_content_name}',
            status: true
        });

    {/if}

</script>
<!-- PD Facebook Pixel Code - STANDARD EVENTS -->
{/if}