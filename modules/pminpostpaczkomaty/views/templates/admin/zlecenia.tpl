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

<div class="panel">
	<div class="panel-heading">
		<i class="icon-info"></i>
			{l s='Transport orders' mod='pminpostpaczkomaty'}
	</div>
	<div>
		{if isset($zlecenia['items']) && sizeof($zlecenia['items'])}
		<table class="table">
			<tr>
				<th>{l s='Id' mod='pminpostpaczkomaty'}</th>
				<th>{l s='Status' mod='pminpostpaczkomaty'}</th>
				<th>{l s='External id' mod='pminpostpaczkomaty'}</th>
				<th>{l s='Address' mod='pminpostpaczkomaty'}</th>
				<th>{l s='Shipments' mod='pminpostpaczkomaty'}</th>
				<th>{l s='Date add' mod='pminpostpaczkomaty'}</th>
			</tr>
			{foreach from=$zlecenia['items'] item='zlecenie'}	
			<tr>
				<td>
					<a href="{$link->getAdminLink('PmInpostPaczkomatyZlecenia')|escape:'htmlall':'UTF-8'}&amp;print_zlecenie={$zlecenie['id']}">{$zlecenie['id']|escape:'htmlall':'UTF-8'}</a>
				</td>
				<td>
					{$zlecenie['status']|escape:'htmlall':'UTF-8'}
				</td>
				<td>
					{$zlecenie['external_id']|escape:'htmlall':'UTF-8'}
				</td>
				<td>
					{if isset($zlecenie['address']['line1'])}
						{$zlecenie['address']['line1']|escape:'htmlall':'UTF-8'}
					{/if}
					{if isset($zlecenie['address']['line2'])}
						{$zlecenie['address']['line2']|escape:'htmlall':'UTF-8'}
					{/if}
					{if isset($zlecenie['address']['street'])}
						{$zlecenie['address']['street']|escape:'htmlall':'UTF-8'}
					{/if}

					{if isset($zlecenie['address']['building_number'])}
						{$zlecenie['address']['building_number']|escape:'htmlall':'UTF-8'}
					{/if}
					<br/>
					{if isset($zlecenie['address']['post_code'])}
						{$zlecenie['address']['post_code']|escape:'htmlall':'UTF-8'}
					{/if}						
					{if isset($zlecenie['address']['city'])}
						{$zlecenie['address']['city']|escape:'htmlall':'UTF-8'}
					{/if}
					<br/>
					{if isset($zlecenie['address']['country_code'])}
						{$zlecenie['address']['country_code']|escape:'htmlall':'UTF-8'}
					{/if}
				</td>
				<td>
					{foreach from=$zlecenie['shipments'] item='shipment'}
						{$shipment['tracking_number']|escape:'htmlall':'UTF-8'}<br/>
					{/foreach}
				</td>
				<td>{$zlecenie['created_at']|escape:'htmlall':'UTF-8'}
				</td>
			</tr>
			{/foreach}
		</table>
		{/if}
	</div>
</div>
