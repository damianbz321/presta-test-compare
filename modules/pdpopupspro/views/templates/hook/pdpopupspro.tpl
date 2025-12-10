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

{if $ps_version_16}
	<div id="pdpopupspro" class="clearfix">
		<span class="close icon icon-close" title="{l s='Close window' mod='pdpopupspro'}"></span>
		{if $show_title}
			<div class="pdpopupspro-header">
				<h2>{$name|escape:'html':'UTF-8'}</h2>
			</div>
		{/if}
		<div class="pdpopupspro-content">
			{* HTML, cannot escape *}
			{$content|stripslashes}
		</div>
		{if $show_newsletter}
			<div class="pdpopupspro-newsletter-form">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-7 border-right">
						<span class="promo-text">
							{* HTML, cannot escape *}
							{$content_newsletter|stripslashes}
						</span>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-5">
						<div class="form-inline">
							<input class="inputNew form-control grey newsletter-input" type="text" name="email" id="submited_email" size="18" placeholder="{l s='Enter your e-mail' mod='pdpopupspro'}" value="" />
							<input type="hidden" id="id_pdpopupspro" name="id_pdpopupspro"  value="{$id_pdpopupspro|escape:'html':'UTF-8'}" />
							<button type="submit" name="submitNewsletterSub" class="btn btn-default button button-small btn-newsletter icon icon-chevron-circle-right">
								<span>{l s='Subscribe' mod='pdpopupspro'}</span>
							</button>
							<p class="form_message"></p>

							{if isset($npc) && $npc}
								<p class="checkbox">
			                        <input type="checkbox" name="ncp" id="ncp" required="true" value="1" {if isset($checkedNCP) && $checkedNCP}checked="checked"{/if} />
			                        <label for="ncp">{l s='I have read the privacy policy' mod='pdpopupspro'} 
			                        {if isset($npc_cms_link) && $npc_cms_link != ''}  <a href="{$npc_cms_link|escape:'html':'UTF-8'}" class="iframe" rel="nofollow">{l s='(read)' mod='pdpopupspro'}</a>{/if}
			                        </label>
			                    </p>
			                {/if}
						</div>
					</div>
				</div>
			</div>
		{/if}
		<div class="pdpopupspro-notshow">
			<span>
				<input type="checkbox" class="checkbox" name="pdpopupspro-checkbox" id="pdpopupspro-checkbox" />
			</span>
			<label for="pdpopupspro-checkbox">{l s='Do not show again' mod='pdpopupspro'}</label>
		</div>
	</div>
	<div id="pdpopupspro-overlay"></div>

{else}
	<div id="pdpopupspro" class="clearfix">
		<span class="close icon icon-close" title="{l s='Close window' mod='pdpopupspro'}"></span>
		{if $show_title}
			<div class="pdpopupspro-header">
				<h2>{$name}</h2>
			</div>
		{/if}
		<div class="pdpopupspro-content">
			{* HTML, cannot escape *}
			{$content nofilter}
		</div>
		{if $show_newsletter}
			<div class="pdpopupspro-newsletter-form">
				<div class="form-group row">
					<div class="col-xs-12 col-sm-12 col-md-7 border-right">
						<span class="promo-text">
							{* HTML, cannot escape *}
							{$content_newsletter nofilter}
						</span>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-5">
						<div class="form-inline">
							<input class="inputNew form-control grey newsletter-input" type="text" name="email" id="submited_email" size="18" placeholder="{l s='Enter your e-mail' mod='pdpopupspro'}" value="" />
							<input type="hidden" id="id_pdpopupspro" name="id_pdpopupspro"  value="{$id_pdpopupspro}" />
							<button type="submit" name="submitNewsletterSub" class="btn btn-default button button-small btn-newsletter icon icon-chevron-circle-right">
								<span>{l s='Subscribe' mod='pdpopupspro'}</span>
							</button>
							<p class="form_message"></p>

							{if isset($npc) && $npc}
								<p class="checkbox">
			                        <input type="checkbox" name="ncp" id="ncp" required="true" value="1" {if $checkedNCP}checked="checked"{/if} />
			                        <label for="ncp">{l s='I have read the privacy policy' mod='pdpopupspro'} 
			                        {if isset($npc_cms_link) && $npc_cms_link != ''}  <a href="{$npc_cms_link}" class="iframe" rel="nofollow">{l s='(read)' mod='pdpopupspro'}</a>{/if}
			                        </label>
			                    </p>
			                {/if}
						</div>
					</div>
				</div>
			</div>
		{/if}
		<div class="pdpopupspro-notshow">
			<span>
				<input type="checkbox" class="checkbox" name="pdpopupspro-checkbox" id="pdpopupspro-checkbox" />
			</span>
			<label for="pdpopupspro-checkbox">{l s='Do not show again' mod='pdpopupspro'}</label>
		</div>
	</div>
	<div id="pdpopupspro-overlay"></div>
{/if}

