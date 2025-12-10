{*
* 2014-2020 Presta-Mod.pl Rafał Zontek
*
* NOTICE OF LICENSE
*
* Poniższy kod jest kodem płatnym, rozpowszechanie bez pisemnej zgody autora zabronione
* Moduł można zakupić na stronie Presta-Mod.pl. Modyfikacja kodu jest zabroniona,
* wszelkie modyfikacje powodują utratę gwarancji
*
* http://presta-mod.pl
*
* DISCLAIMER
*
*
*  @author    Presta-Mod.pl Rafał Zontek <biuro@presta-mod.pl>
*  @copyright 2014-2020 Presta-Mod.pl
*  @license   Licecnja na jedną domenę
*  Presta-Mod.pl Rafał Zontek
*}

{if isset($output)}
	{$output} {* can not be escaped*}
{/if}

{if isset($company_informations)}
<div class="panel form-horizontal pminpostpaczkomaty">
	<div class="row">
		<div class="col-md-12">
			<strong>{l s='Data from InPost server' mod='pminpostpaczkomaty'}</strong>
			<table class="table">
				<tr>
					<th>{l s='Organization id' mod='pminpostpaczkomaty'}</th>
					<th>{l s='Company name' mod='pminpostpaczkomaty'}</th>
					<th>{l s='Tax id' mod='pminpostpaczkomaty'}</th>
					<th>{l s='Bank account number' mod='pminpostpaczkomaty'}</th>
					<th>{l s='Address' mod='pminpostpaczkomaty'}</th>
					<th>{l s='Available carriers' mod='pminpostpaczkomaty'}</th>
					<th>{l s='Available services' mod='pminpostpaczkomaty'}</th>
				</tr>
				<tr>
					<td>{$company_informations['id']|escape:'htmlall':'UTF-8'}</td>
					<td>{$company_informations['name']|escape:'htmlall':'UTF-8'}</td>
					<td>{$company_informations['tax_id']|escape:'htmlall':'UTF-8'}</td>
					<td>{$company_informations['bank_account_number']|escape:'htmlall':'UTF-8'}</td>
					<td>
						{if isset($company_informations['address']['street'])}{$company_informations['address']['street']|escape:'htmlall':'UTF-8'}{/if}
						{if isset($company_informations['address']['building_number'])}{$company_informations['address']['building_number']|escape:'htmlall':'UTF-8'}{/if}<br/>
						{if isset($company_informations['address']['line1'])}{$company_informations['address']['line1']|escape:'htmlall':'UTF-8'}{/if}
						{if isset($company_informations['address']['line2'])}{$company_informations['address']['line2']|escape:'htmlall':'UTF-8'}{/if}
						{if isset($company_informations['address']['city'])}{$company_informations['address']['city']|escape:'htmlall':'UTF-8'}{/if}
						{if isset($company_informations['address']['post_code'])}{$company_informations['address']['post_code']|escape:'htmlall':'UTF-8'}{/if}
						{if isset($company_informations['address']['country'])}{$company_informations['address']['country']|escape:'htmlall':'UTF-8'}{/if}
					</td>
					<td>
						<ul>
							{foreach $company_informations['carriers'] as $carrier}<li>{$carrier|escape:'htmlall':'UTF-8'}</li>{/foreach}
						</ul>
					</td>
					<td>
						<ul>
							{foreach $company_informations['services'] as $service}<li>{$service|escape:'htmlall':'UTF-8'}</li>{/foreach}
						</ul>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
{/if}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/2.1.2/jquery.scrollTo.min.js"></script>
<div class="panel form-horizontal pminpostpaczkomaty">
	<ul class="nav nav-tabs" role="tablist">
		<li class="active">
			<a href="#tabimport" role="tab" id="refreshstats" data-toggle="tab"><span class="icon-AdminTools"></span> {l s='Configuration' mod='pminpostpaczkomaty'}</a>
		</li>
		<li>
			<a href="#tabcategories" role="tab" id="refreshcategories" data-toggle="tab"><span class="icon-th-list"></span> {l s='Help' mod='pminpostpaczkomaty'}</a>
		</li>
		<li style="float: right;">
			<a target="_blank" href="https://presta-mod.pl">&nbsp;<img height="15" src="https://presta-mod.pl/pixel.php?month={$module_key|escape:'htmlall':'UTF-8'}&domain={$host|escape:'htmlall':'UTF-8'}"></a>
		</li>
		<li style="float: right; color: red">
			<a href="#" class="btn btn-default start-guide">
				<span class="icon-steps"></span> {l s='Guide' mod='pminpostpaczkomaty'} {l s='Configuration' mod='pminpostpaczkomaty'}
				<span class="pminpostpaczkomaty-guide-step"></span>
			</a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane box active" id="tabimport">
			<div class="tab-pane">
				<div class="row">
					<div class="sidebar navigation col-md-3">
						<nav class="list-group categorieList"> 			
							{foreach from = $form_titles item='fieldset' name='panels'}
							<a class="list-group-item module_tab{if isset($selected_menu) && (int)$selected_menu eq (int)$smarty.foreach.panels.index} active{/if}" data-target=".panel:eq({$smarty.foreach.panels.index})" href="#">
								{if isset($fieldset['required']) && $fieldset['required']} <strong>{/if}
									{$fieldset['name']|escape:'htmlall':'UTF-8'}
								{if isset($fieldset['required']) && $fieldset['required']} </strong>{/if}
							</a>															
							{/foreach}
						</nav>
					</div>
					<div id="configContainer" class="col-md-9">
						{$config_form}						
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane box" id="tabcategories">
			<div class="tab-pane">
				<iframe style="overflow-y:hidden; overflow-x:scroll;" frameBorder="0" src="https://presta-mod.pl/help/paczkomaty/?f={$host|escape:'htmlall':'UTF-8'}" width="98%" height="1024px"></iframe>	
			</div>
		</div>
	</div>
</div>