{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

    <!-- Module labslideshow -->
    {if isset($labslideshow_slides)}
	<div class="lab-nivoSlideshow">
		<div class="lab-loading"></div>
        <div id="lab-slideshow" class="slides">
                {foreach from=$labslideshow_slides item=slide}
                    {if $slide.active}
                                <img 
									data-thumb="{$link->getMediaLink("`$smarty.const._MODULE_DIR_`labslideshow/images/`$slide.image|escape:'htmlall':'UTF-8'`")}"  
									src="{$link->getMediaLink("`$smarty.const._MODULE_DIR_`labslideshow/images/`$slide.image|escape:'htmlall':'UTF-8'`")}"
                                     alt="{$slide.legend|escape:'htmlall':'UTF-8'}"
									 title="#htmlcaption{$slide.id_slide}" />
                    {/if}
                {/foreach}
        </div>
		
		{foreach from=$labslideshow_slides item=slide}

        {if $slide.active}
		<div id="htmlcaption{$slide.id_slide}" class=" nivo-html-caption nivo-caption">
			<div class="timeline" style=" 
								position:absolute;
								top:0;
								left:0;
								background-color: rgba(49, 56, 72, 0.298);
								height:5px;
								-webkit-animation: myfirst {$labslideshow.LAB_PAUSE}ms ease-in-out;
								-moz-animation: myfirst {$labslideshow.LAB_PAUSE}ms ease-in-out;
								-ms-animation: myfirst {$labslideshow.LAB_PAUSE}ms ease-in-out;
								animation: myfirst {$labslideshow.LAB_PAUSE}ms ease-in-out;
							
							">
			</div>
			<div class="lab_description {$slide.margin}">
			{if $labslideshow.LAB_TITLE =='true'}
			<div class="title animated a1">
				{$slide.title}
			</div>
			{/if}
			<div class="description animated a2">
				{$slide.description}
			</div>
			{if $slide.url}
			<div class="readmore animated a3">
				<a href="{$slide.url}">{l s='Readmore' mod='labslideshow_slides'}</a>
			</div>
			{/if}
			</div>
		</div>
		{/if}
        {/foreach}
	</div>



<script>
 var speed = {$labslideshow.LAB_SPEED};
 var pause = {$labslideshow.LAB_PAUSE}	;
 {if $labslideshow.LAB_E_N_P ==1 }
  var enp = true;
 {else}
 var enp == false ;
 {/if}
 {if $labslideshow.LAB_E_CONTROL ==1 }
  var econtrol = true;
 {else}
 var econtrol == false ;
 {/if}

 console.log(enp);
</script>

	<script src="{$labslideshow.js_file}"></script>



    {/if}
    <!-- /Module labslideshow -->
