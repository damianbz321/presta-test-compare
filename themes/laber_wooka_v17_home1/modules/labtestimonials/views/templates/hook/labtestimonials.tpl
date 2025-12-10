{*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}{if $page.page_name == 'index'}
<div class="laberTestimonial">		<div class="row">			<div class="laberTestimonialOwl">
				{foreach from=$testimonials item=testimonial name=myLoop}
					<div class="item-inner">						<div class="item">
							<!-- {if $show_date == 1}
								<div class="datetime">
									{$testimonial.posttime nofilter}
								</div>
							{/if} -->
							<div class="laberComment" >
								{$testimonial.comment nofilter}
							</div>																					{if $show_image}								<div class="image">									<span>										<img class="img-responsive" src="{$image_url nofilter}resized_{$testimonial.image nofilter}" alt="{$testimonial.author nofilter}" />									</span>								</div>							{/if}														<div class="laberAuthor">								<p class="author">{$testimonial.author nofilter}</p>								{if $show_office == 1}									<p class="laberOffice">										{$testimonial.office nofilter}									</p>								{/if}							</div>
						</div>					</div>
				{/foreach}
			</div>			<script type="text/javascript">				$(document).ready(function() {					var owl = $(".laberTestimonialOwl");					owl.owlCarousel({						items : 1,						itemsDesktop : [1199,1],						itemsDesktopSmall : [991,1],						itemsTablet: [767,1],						itemsMobile : [480,1],						rewindNav : false,						autoPlay :  true,						stopOnHover: false,						pagination : true,					});				});			</script>		</div></div>{/if}