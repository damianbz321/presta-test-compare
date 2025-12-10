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

{if $r['nr_listu'] == ''}
<a href="{$link->getAdminLink('PmInpostPaczkomatyOrder')|escape:'htmlall':'UTF-8'}&send2=1&pminpostorder_size=A&id_order={$r['id_order']|escape:'htmlall':'UTF-8'}" class="pminpostbutton-ajax send2 btn btn-default" id="send_order_a{$r['id_order']|escape:'htmlall':'UTF-8'}"><span class="icon-send"></span> A</a>

<a href="{$link->getAdminLink('PmInpostPaczkomatyOrder')|escape:'htmlall':'UTF-8'}&send2=1&pminpostorder_size=B&id_order={$r['id_order']|escape:'htmlall':'UTF-8'}" class="pminpostbutton-ajax send2 btn btn-default" id="send_order_b{$r['id_order']|escape:'htmlall':'UTF-8'}"><span class="icon-send"></span> B</a>

<a href="{$link->getAdminLink('PmInpostPaczkomatyOrder')|escape:'htmlall':'UTF-8'}&send2=1&pminpostorder_size=C&id_order={$r['id_order']|escape:'htmlall':'UTF-8'}" class="pminpostbutton-ajax send2 btn btn-default" id="send_order_c{$r['id_order']|escape:'htmlall':'UTF-8'}"><span class="icon-send"></span> C</a>
    
<a href="{$link->getAdminLink('PmInpostPaczkomatyOrder')|escape:'htmlall':'UTF-8'}&printlabel=1&id_order={$r['id_order']|escape:'htmlall':'UTF-8'}&id={$r['id']|escape:'htmlall':'UTF-8'}" class="hidden pminpostbutton-ajax printlabel2 btn btn-default" id="print_order{$r['id_order']|escape:'htmlall':'UTF-8'}"><span class="icon-print"></span></a>
{else}
<a href="{$link->getAdminLink('PmInpostPaczkomatyOrder')|escape:'htmlall':'UTF-8'}&printlabel=1&id_order={$r['id_order']|escape:'htmlall':'UTF-8'}&id={$r['id']|escape:'htmlall':'UTF-8'}" class="pminpostbutton-ajax printlabel2 btn btn-default" id="print_order{$r['id_order']|escape:'htmlall':'UTF-8'}"><span class="icon-print"></span></a>
{/if}