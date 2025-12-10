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
			{l s='Label view' mod='pminpostpaczkomaty'}
	</div>
	<div class="row">
		<div class="col-md-6">
		{if $label}
			<table class="table">		
				<tr>
					<td>
						{l s='E-mail' mod='pminpostpaczkomaty'}
					</td>
					<td>
						{if isset($post_info['pminpostorder_email'])}
						{$post_info['pminpostorder_email']|escape:'htmlall':'UTF-8'}
						{/if}
					</td>
				</tr>
				<tr>
					<td>
						{l s='Phone' mod='pminpostpaczkomaty'}
					</td>
					<td>
						{if isset($post_info['pminpostorder_phone'])}
						{$post_info['pminpostorder_phone']|escape:'htmlall':'UTF-8'}
						{/if}
					</td>
				</tr>
				<tr>
					<td>
						{l s='Parcel' mod='pminpostpaczkomaty'}
					</td>
					<td>
						{if isset($post_info['pminpostorder_selected'])}
						{$post_info['pminpostorder_selected']|escape:'htmlall':'UTF-8'}
						{/if}
					</td>
				</tr>
				<tr>
					<td>
						{l s='Size' mod='pminpostpaczkomaty'}
					</td>
					<td>
						{if isset($post_info['pminpostorder_size'])}
						{$post_info['pminpostorder_size']|escape:'htmlall':'UTF-8'}
						{/if}
					</td>
				</tr>
				<tr>
					<td>
						{l s='Reference' mod='pminpostpaczkomaty'}
					</td>
					<td>
						{if isset($post_info['pminpostorder_reference'])}
						{$post_info['pminpostorder_reference']|escape:'htmlall':'UTF-8'}
						{/if}
					</td>
				</tr>
				<tr>
					<td>
						{l s='To machine' mod='pminpostpaczkomaty'}
					</td>
					<td>
						{if isset($post_info['pminpostorder_machine'])}
							{if $post_info['pminpostorder_machine']}
								{l s='Yes' mod='pminpostpaczkomaty'}
							{else}
								{l s='No' mod='pminpostpaczkomaty'}
							{/if}
						{/if}
					</td>
				</tr>
				<tr>
					<td>
						{l s='Sender parcel' mod='pminpostpaczkomaty'}
					</td>
					<td>
						{if isset($post_info['pminpostorder_selected_from']) && isset($post_info['pminpostorder_machine']) && $post_info['pminpostorder_machine']}
						{$post_info['pminpostorder_selected_from']|escape:'htmlall':'UTF-8'}
						{/if}
					</td>
				</tr>
				<tr>
					<td>
						{l s='Cash on delivery' mod='pminpostpaczkomaty'}
					</td>
					<td>
						{if isset($post_info['pminpostorder_pobranie'])}
							{if $post_info['pminpostorder_pobranie']}
								{l s='Yes' mod='pminpostpaczkomaty'}
							{else}
								{l s='No' mod='pminpostpaczkomaty'}
							{/if}
						{/if}
					</td>
				</tr>
				<tr>
					<td>
						{l s='Cash on delivery value' mod='pminpostpaczkomaty'}
					</td>
					<td>
						{if isset($post_info['pminpostorder_pobranie_value']) && isset($post_info['pminpostorder_pobranie']) && $post_info['pminpostorder_pobranie']}
						{$post_info['pminpostorder_pobranie_value']|replace:'.':','|escape:'htmlall':'UTF-8'}
						{/if}
					</td>
				</tr>
			</table>
			{/if}
		</div>
	</div>
	<div class="panel-footer">
		<a href="{$link->getAdminLink('PmInpostPaczkomatyList')|escape:'htmlall':'UTF-8'}" class="btn btn-default">
			<i class="process-icon-back"></i> {l s='Back' mod='pminpostpaczkomaty'}
		</a>
		<a href="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'UTF-8'}&amp;id_order={$id_order}&amp;vieworder" class="btn btn-default">
			<i class="process-icon-back"></i> {l s='Go to order' mod='pminpostpaczkomaty'}
		</a>		
		{*
			<a class="pull-right btn btn-primary" href="{$link->getAdminLink('PmInpostPaczkomatyOrder')|escape:'htmlall':'UTF-8'}&amp;printlabel=1&amp;id_order={$id_order|escape:'htmlall':'UTF-8'}&amp;id={$label->id|escape:'htmlall':'UTF-8'}" id="print_order{$r['id_order']|escape:'htmlall':'UTF-8'}">
				<i class="icon-print"></i> {l s='Print' mod='pminpostpaczkomaty'}
			</a>
		*}
	</div>
</div>