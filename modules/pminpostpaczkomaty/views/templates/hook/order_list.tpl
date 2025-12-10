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

<div id="kurier-alert" class="alert alert-kurier {if isset($alerttype)}alert-{$alerttype|escape:'htmlall':'UTF-8'}{/if}" style="{if !isset($alertpaczkomaty) || $alertpaczkomaty === false}display: none;{/if}" role="alert">
{if isset($alertpaczkomaty)}
{$alertpaczkomaty}
{/if}
</div>

<div id="paczkomaty-alert" class="alert alert-paczkomaty" style="display: none;" role="alert">
</div>
<div class="panel">
	<div class="panel-heading">
		{l s='Mass actions' mod='pminpostpaczkomaty'}
	</div>
	<button class="btn btn-primary refresh_all_packages">{l s='Refresh all visible packages' mod='pminpostpaczkomaty'}</button>
	<button class="btn btn-primary refresh_all_packages">{l s='Change order state for delivered packages' mod='pminpostpaczkomaty'}</button>
	<a class="btn btn-primary" href="{$link->getAdminLink('PmInpostPaczkomatyList')|escape:'htmlall':'UTF-8'}&amp;without_transport_order=1">{l s='Show without transport order' mod='pminpostpaczkomaty'}</a>
</div>