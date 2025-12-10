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

{addJsDef pm_inpostpaczkomaty_carrier=$pm_inpostpaczkomaty_carrier|escape:'htmlall':'UTF-8'}
{addJsDef pm_inpostpaczkomaty_carrier_cod=$pm_inpostpaczkomaty_carrier_cod|escape:'htmlall':'UTF-8'}
{addJsDefL name='pm_inpost_placeholder2'}{l s='Use button to select machine' mod='pminpostpaczkomaty'}{/addJsDefL}
{addJsDefL name='paczkomatySelectLabel'}{l s='Please select machine to your order' mod='pminpostpaczkomaty'}{/addJsDefL}
{addJsDefL name='pm_inpostpaczkomaty_label'}{l s='Select machine' mod='pminpostpaczkomaty'}{/addJsDefL}
{addJsDefL name='pm_inpost_placeholder'}{l s='Enter city, street, or machine name' mod='pminpostpaczkomaty'}{/addJsDefL}
{addJsDefL name='paczkomatLabel '}{l s='Machine: ' mod='pminpostpaczkomaty'}{/addJsDefL}
{addJsDef pm_inpostpaczkomaty_selected=$pm_inpostpaczkomaty_selected}
{addJsDef pm_inpostpaczkomaty_type=2}
{addJsDef pm_inpostpaczkomaty_display_place=$pm_inpostpaczkomaty_display_place|escape:'htmlall':'UTF-8'}
{addJsDef current_cart=$current_cart|escape:'htmlall':'UTF-8'}
{addJsDef modulepath=$modulepath}
{addJsDef pm_inpostpaczkomaty_token=$pm_inpostpaczkomaty_token|escape:'htmlall':'UTF-8'}
{addJsDef id_pudo_carrier=''}
{addJsDef mapkey=$mapkey}
{addJsDef pm_inpostpaczkomaty_typewpzl=$wpzl}