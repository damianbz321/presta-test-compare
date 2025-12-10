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

<script type="text/javascript">
	var pm_inpostpaczkomaty_carrier={$pm_inpostpaczkomaty_carrier|escape:'htmlall':'UTF-8'};
	var pm_inpostpaczkomaty_carrier_cod={$pm_inpostpaczkomaty_carrier_cod|escape:'htmlall':'UTF-8'};
	var pm_inpostpaczkomaty_selected='{$pm_inpostpaczkomaty_selected|escape:'htmlall':'UTF-8'}';
	var pm_inpostpaczkomaty_type=2;
	var pm_inpostpaczkomaty_display_place={$pm_inpostpaczkomaty_display_place|escape:'htmlall':'UTF-8'};
	var current_cart={$current_cart|escape:'htmlall':'UTF-8'};
	var modulepath='{$modulepath nofilter}'
	var pm_inpostpaczkomaty_token='{$pm_inpostpaczkomaty_token|escape:'htmlall':'UTF-8'}';
	var pm_inpostpaczkomaty_label = '{l s='Select machine' mod='pminpostpaczkomaty'}';
	var id_pudo_carrier = '';
	var paczkomatySelectLabel = "{l s='Please select machine to your order' mod='pminpostpaczkomaty'}";
	var pm_inpostpaczkomaty_label = "{l s='Select machine' mod='pminpostpaczkomaty'}";
	var pm_inpost_placeholder = "{l s='Enter city, street, or machine name' mod='pminpostpaczkomaty'}";	
	var pm_inpost_placeholder2 = "{l s='Use button to select machine name' mod='pminpostpaczkomaty'}";
	var paczkomatLabel = "{l s='Machine: ' mod='pminpostpaczkomaty'}";
	var mapkey='{$mapkey|escape:'htmlall':'UTF-8'}';
	var pm_inpostpaczkomaty_typewpzl={if $wpzl}true{else}false{/if};
</script>