{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="laberFooter-top">
	{hook h='displayFooterBefore'}
</div>

<div class="laberFooter-center">
  <div class="container">
    <div class="row">
      {hook h='displayFooter'}
    </div>
  </div>
</div>
<div class="laberFooter-bottom">
	<div class="container">
		<div class="row">
		  {hook h='displayFooterAfter'}
		</div>
	</div>
</div>
<a href="javascript:void(0)" class="mypresta_scrollup hidden-phone open">
	<i class="icon-chevron-up"></i><span>{l s='Back to Top' d='Shop.Theme.Laberthemes'}</span>
</a>
{literal}
<script>


	$(document).ready(function (){

		$('body').on('click', '.product-variants-item .input-container', function (){

			$('.se-pre-con').css({'opacity':'0.7'});
			$('.se-pre-con').show();

			setTimeout(function() {
				$('.se-pre-con').hide();
				$('.se-pre-con').css({'opacity':'1'});
			}, 1000);

		});


		$("<br>").insertAfter(".displayMegamenu ul.menu-content li:nth-child(6)");
		$('.laberSetting').remove();


		if ($(window).width() > 767) {



			let tabs = $('#product .tabs.laberTabs').clone();
			$('#product .tabs.laberTabs').remove();
			$(tabs).insertAfter("#product #content.page-content");
			$('#product #tab-content').removeClass('col-sm-6');
			$('#product .tabs.laberTabs').css({"margin-top": "35px"});

		}
	});
</script>
{/literal}