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
* @link		 http://prestadev.pl
* @package	 PD Facebook Pixel Tracking PrestaShop 1.5.x and 1.6.x Module
* @version	 1.1.0
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date		 24-05-2016
*}

<!-- PD Facebook Pixel Code - BASE Page View -->
<script type="text/javascript" >
{literal}
	!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
	n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
	t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
	document,'script','//connect.facebook.net/en_US/fbevents.js');
	fbq('init', '{/literal}{$id_tracking nofilter}{literal}');
	fbq('track', 'PageView');
{/literal}
</script>

<noscript>
	<img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={$id_tracking nofilter}&ev=PageView&noscript=1" />
</noscript>
<!-- End PD Facebook Pixel Code - BASE Page View -->


