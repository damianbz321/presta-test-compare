<a class="list-group-item {if $smarty.get.controller == "{$PSHOW_MODULE_CLASS_NAME_}Main" and (!isset($smarty.get.page) || !$smarty.get.page)}active{/if}"
   href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Main", true)}">
    {l s='Cross-sell rules' mod='pshowupsell'}
</a>

<style>
    a.list-group-item[href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Hook", true)}"] { display: none; }
</style>
