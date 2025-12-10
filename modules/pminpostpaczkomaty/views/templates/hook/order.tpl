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
{if $pminpostview eq 'all'}
<div id="paczkomaty-alert" class="alert alert-paczkomaty" role="alert">

</div>
{if $selectedpm == false}
<button class="btn btn-default show-paczkomaty-form">
	<img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/inpost-logotyp.jpg" height="50" />{l s='Show Inpost Paczkomaty Form' mod='pminpostpaczkomaty'}
</button>
{/if}
<div class="paczkomaty-form" {if $selectedpm == false}style="display: none;"{/if}>
{$form nofilter}
</div>
{elseif $pminpostview eq 'content'}
<div class="tab-pane d-print-block fade" id="tabinpost" role="tabpanel" aria-labelledby="tabinpostpaczkomatybtn">
	<div id="paczkomaty-alert" class="alert alert-paczkomaty" style="display: none;" role="alert">

	</div>
	<div class="paczkomaty-form hidden-print">
		{$form nofilter}
	</div>
</div>
{else}
	<li class="nav-item">
		<a class="nav-link"
		    id="tabinpostpaczkomatybtn"
			data-toggle="tab"
			href="#tabinpost"
			role="tab"			
	 	>

			<img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/inpost_logotyp.jpg" height="20" />{l s='Inpost Paczkomaty' mod='pminpostpaczkomaty'} <span class="badge">({if $selectedpm == false}0{else}1{/if})</span>
		</a>
	</li>
{/if}