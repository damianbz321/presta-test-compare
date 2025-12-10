{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA

*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="laberLogo_manufacturer">
<div class="container">
<div class="laberTitle">
		<h3>{l s='Our Clients' mod='labmanufacture'}</h3>
</div>
<div class="content-manufacturer">
		<div class="list_manufacturer">
		{$i=0}
		{foreach from=$list_manu item=manufacturer name=list_manu}
			<div class="item-inner {if $smarty.foreach.list_manu.first}first_item{elseif $smarty.foreach.list_manu.last}last_item{/if}">
				<div class="item">
					<a class="image_hoverwashe" href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$manufacturer.name|escape:'htmlall':'UTF-8'}">
					<img src="{$path_ssl|escape:'htmlall':'UTF-8'}img/m/{$manufacturer.id_manufacturer|escape:'htmlall':'UTF-8'}.jpg" alt="{$manufacturer.name|escape:'htmlall':'UTF-8'}" />
					<span class="hover_bkg_light"></span>
					</a>
				</div>
			</div>
		{/foreach}
		</div>
	</div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	var owl = $(".list_manufacturer");
	owl.owlCarousel({
		items : 6,
		itemsDesktop : [1199,5],
		itemsDesktopSmall : [991,4],
		itemsTablet: [767,3],
		itemsMobile : [480,2],
		navigation : true,
		navigationText : ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
		rewindNav : false,
		autoPlay :  false,
		stopOnHover: false,
		pagination : false,
	});
});
</script>
