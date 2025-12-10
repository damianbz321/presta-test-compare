{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright  VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

{if count($fplist_features) > 0}
    <div class="fplist_block">
        {foreach from=$fplist_features name=foo item=fplist}
            <span class="fplist_name">{$fplist.feature_name}:</span>
            <span class="fplist_value">{$fplist.feature_value}</span>{if not $smarty.foreach.foo.last}<span class="fplist_comma">,</span>{/if}
        {/foreach}
    </div>
{/if}