<?php
/**
* 2007-2016 PrestaShop
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
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2016 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class SampleDataHtml
{
	public function initData($base_url)
	{
		$content_block1 = '
			<div class="freeshipping">
				<p>Get free shipping and a worry - Free 30 day money back guarantee when you !</p>
			</div>
		';

		$content_block2 = '
			<div class="laberStatic">
			<div class="img"><a title="" href="#"> <img src="http://laberpresta.com/v17/laber_wooka_v17/img/cms/img_13_1.jpg" alt="images"> </a></div>
			<div class="img"><a title="" href="#"> <img src="http://laberpresta.com/v17/laber_wooka_v17/img/cms/img_13_2.jpg" alt="images"> </a></div>
			</div>
		';
		$content_block3 = '
			<div class="labertitle">
			<h2>FEATURED PRODUCTS</h2>
			<p>Commodo sociosqu venenatis cras dolor sagittis integer luctus sem primis eget <br> maecenas sed urna malesuada consectetuer</p>
			</div>
		';
		
		$content_block4 = '
			<div class="labertitle text-left">
			<h2>Top Deals OF the day</h2>
			<p>Commodo sociosqu venenatis cras dolor sagittis integer luctus sem primis eget <br> maecenas sed urna malesuada consectetuer</p>
			</div>
		';
		$content_block5 = '
			<div class="labertitle">
			<h2>LATEST ARRIVALS</h2>
			<p>Commodo sociosqu venenatis cras dolor sagittis integer luctus sem primis eget <br> maecenas sed urna malesuada consectetuer</p>
			</div>
		';

		$content_block6 = '
			<div class="laberStatic displayGrid">
			<div class="img"><a title="" href="#"> <img src="http://laberpresta.com/v17/laber_wooka_v17/img/cms/instagram13_1.jpg" alt="images"> </a></div>
			<div class="img"><a title="" href="#"> <img src="http://laberpresta.com/v17/laber_wooka_v17/img/cms/instagram13_2.jpg" alt="images"> </a></div>
			<div class="img"><a title="" href="#"> <img src="http://laberpresta.com/v17/laber_wooka_v17/img/cms/instagram13_3.jpg" alt="images"> </a></div>
			<div class="img"><a title="" href="#"> <img src="http://laberpresta.com/v17/laber_wooka_v17/img/cms/instagram13_4.jpg" alt="images"> </a></div>
			<div class="img"><a title="" href="#"> <img src="http://laberpresta.com/v17/laber_wooka_v17/img/cms/instagram13_5.jpg" alt="images"> </a></div>
			<div class="img"><a title="" href="#"> <img src="http://laberpresta.com/v17/laber_wooka_v17/img/cms/instagram13_6.jpg" alt="images"> </a></div>
			</div>
		';
		$content_block7 = '
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="copyright ">© 2019 <a href="https://themeforest.net/user/labertheme/portfolio">Laberthemes. </a>All Rights Reserved</div>
			</div>
		';

		/*install static Block*/
		$result = true;
		$result &= Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'laber_staticblock` (`id_labertaticblock`, `hook_position`, `posorder`, `active`, `showhook`) 
			VALUES
			(1, "displayNav1", 0, 0, 1),
			(2, "displayImageSliderRight", 0, 0, 1),
			(3, "displayPosition2", 0, 0, 1),
			(4, "displayPosition3", 0, 0, 1),
			(5, "displayPosition4", 0, 0, 1),
			(6, "displayFooterBefore", 0, 0, 1),
			(7, "displayFooterAfter", 0, 0, 1)
			
			;');

		$result &= Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'laber_staticblock_shop` (`id_labertaticblock`, `id_shop`) 
			VALUES 
			(1,1),
			(2,1),
			(3,1),
			(4,1),
			(5,1),
			(6,1),
			(7,1)
			
			;');
		
		foreach (Language::getLanguages(false) as $lang)
		{
			$result &= Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'laber_staticblock_lang` (`id_labertaticblock`, `id_lang`, `title`, `description`) 
			VALUES 
			( "1","'.$lang['id_lang'].'","Get free shipping and a worry", \''.$content_block1.'\'),
			( "2","'.$lang['id_lang'].'","banner display Right", \''.$content_block2.'\'),
			( "3","'.$lang['id_lang'].'","FEATURED PRODUCTS", \''.$content_block3.'\'),
			( "4","'.$lang['id_lang'].'","Top Deals OF the day", \''.$content_block4.'\'),
			( "5","'.$lang['id_lang'].'","LATEST ARRIVALS", \''.$content_block5.'\'),
			( "6","'.$lang['id_lang'].'","banner displayFooterBefore", \''.$content_block6.'\'),
			( "7","'.$lang['id_lang'].'","copyright", \''.$content_block7.'\')
			
			;');
		}
		return $result;
	}
}