{**
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-9999 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

{foreach from=$blocks item=block name='for'}
    <li class="nav-item"><a class="nav-link" id="ppbtab{$block->id}" href="#ppbtabcontents{$block->id}" data-toggle="tab">{$block->name|escape:'html':'utf-8'}</a></li>
{/foreach}