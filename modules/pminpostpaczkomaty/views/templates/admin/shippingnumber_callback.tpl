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
<span id="nr_listu{$r['id_order']|escape:'htmlall':'UTF-8'}">
    <a title="{l s='Go to tracking' mod='pminpostpaczkomaty'}" target="_blank" href="https://inpost.pl/sledzenie-przesylek?number={$r['nr_listu']|escape:'htmlall':'UTF-8'}">{$r['nr_listu']|escape:'htmlall':'UTF-8'}</a>
</span>