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

{if $t == ''}
	{if $r['nr_listu'] != ''}
		<a title="{l s='Click to refresh status' mod='pminpostpaczkomaty'}" class="refresh_status" href="{$link->getAdminLink('PmInpostPaczkomatyOrder')|escape:'htmlall':'UTF-8'}&ajax=1&refreshstatus=1&nr_listu={$r['nr_listu']|escape:'htmlall':'UTF-8'}">--</a>
	{/if}
{else}
	<a title="{l s='Click to refresh status' mod='pminpostpaczkomaty'}" class="refresh_status" href="{$link->getAdminLink('PmInpostPaczkomatyOrder')|escape:'htmlall':'UTF-8'}&ajax=1&refreshstatus=1&nr_listu={$r['nr_listu']|escape:'htmlall':'UTF-8'}">{$t|escape:'htmlall':'UTF-8'}</a>
{/if}