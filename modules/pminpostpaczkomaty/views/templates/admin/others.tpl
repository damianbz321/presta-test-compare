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

<div class="others">
{if $packages}
{foreach from=$packages item='package'}
{if $package['nr_listu']}
	<div>
		<a 
			title="{l s='Print label' mod='pminpostpaczkomaty'}" 
			class="btn print-package-label" 
			href="{$link->getAdminLink('PmInpostPaczkomatyOrder')|escape:'htmlall':'UTF-8'}&amp;printlabel=1&amp;id_order={$id_order|escape:'htmlall':'UTF-8'}&amp;id={$package['id']|escape:'htmlall':'UTF-8'}"
		>
			<i class="material-icons" aria-hidden="true">print</i>
			<i class="icon-print"></i>
		</a>

		<a 
			title="{l s='Show' mod='pminpostpaczkomaty'}" 
			target="_blank" 
			href="{$link->getAdminLink('PmInpostPaczkomatyList')|escape:'htmlall':'UTF-8'}&amp;id_orders={$package['id']|escape:'htmlall':'UTF-8'}&amp;vieworders"
		>
			{$package['nr_listu']|escape:'htmlall':'UTF-8'}
		</a>
	</div>
{/if}
{/foreach}
{/if}
</div>