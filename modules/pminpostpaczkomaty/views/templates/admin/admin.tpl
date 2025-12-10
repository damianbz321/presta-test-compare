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
		Dziękujemy za zainteresowanie naszymi modułami
	</div>
{if $version}	
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-info">				
				<p><strong>{$xml->description} {* Can not be escaped *} {$xml->version|escape:'htmlall':'UTF-8'}</strong></p>
			</div>
		</div>
	</div>
{/if}
	<div class="row">
		{$xml->adds} {* Can not be escaped *}
	</div>
	<div class="row">
		<div class="col-md-12">
			<p><strong>Zapraszamy po więcej na <a href="https://presta-mod.pl" target="_blank" alt="Presta-Mod.pl">Presta-Mod.pl</a></strong></p>
		</div>
	</div>
</div>