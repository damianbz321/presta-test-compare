{*
* 2012-2020 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Pop-Ups Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2020 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD PopUps Pro - PrestaShop 1.6.x and 1.7.x Module
* @version   1.3.1
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      15-12-2020
*}

{extends file="helpers/form/form.tpl"}

{block name="script"}

	$(document).ready(function(){
		$('.iframe-upload').fancybox({	
				'width'		: 900,
				'height'	: 600,
				'type'		: 'iframe',
				'autoScale' : false,
				'autoDimensions': false,
				'fitToView' : false,
				'autoSize' : false,
				onUpdate : function(){ $('.fancybox-iframe').contents().find('a.link').data('field_id', $(this.element).data("input-name"));
					 $('.fancybox-iframe').contents().find('a.link').attr('data-field_id', $(this.element).data("input-name"));},
				afterShow: function(){
					 $('.fancybox-iframe').contents().find('a.link').data('field_id', $(this.element).data("input-name"));
					 $('.fancybox-iframe').contents().find('a.link').attr('data-field_id', $(this.element).data("input-name"));
				}
		});
	});

{/block}

{block name="input"}
	{if $input.type == 'background_image'}
		<p>
			<input id="{$input.name|escape:'html':'UTF-8'}" type="text" name="{$input.name|escape:'html':'UTF-8'}" value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}"> 
		</p>
		<a href="filemanager/dialog.php?type=1&field_id={$input.name|escape:'htmlall':'UTF-8'}" class="btn btn-default iframe-upload" data-input-name="{$input.name|escape:'html':'UTF-8'}" type="button">
			{l s='Background image selector' mod='pdpopupspro'}
			<i class="icon-angle-right"></i>
		</a>				 
	{else}
		{$smarty.block.parent}
	{/if}
{/block}



