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

<div class="panel" id="Ppb">

    <input type="hidden" name="submitted_tabs[]" value="ppb"/>
    {if isset($smarty.get.addtab)}
        <a class="btn btn-primary" style="cursor:pointer;" style="cursor:pointer;" href="{$bolink}?ppbtab&updateproduct&_token={Tools::getValue('_token')}#hooks">
            <i class="material-icons">arrow_back</i><span>{l s='back to list' mod='ppb'}</span>
        </a>
        <div class="card">
            <div class="card-header"><i class="material-icons">view_module</i>{l s='Create product list' mod='ppb'}</div>
            <div class="separation"></div>
            <div name="globalsettings" id="PPBglobalsettings" method="post" action="{$bolink}?ppbtab&updateproduct&_token={Tools::getValue('_token')}#hooks">
                {$addform|replace:'../':'../../../../'}
            </div>
        </div>
    {elseif isset($smarty.get.editblock)}
        <a class="btn btn-primary" style="cursor:pointer;" style="cursor:pointer;" href="{$bolink}?ppbtab&updateproduct&_token={Tools::getValue('_token')}#hooks">
            <i class="material-icons">arrow_back</i><span>{l s='back to list' mod='ppb'}</span>
        </a>
        <div class="card">
            <div class="card-header"><i class="material-icons">border_color</i>{l s='Edit list of products' mod='ppb'}</div>
            <div class="separation"></div>
            <div name="globalsettings" id="PPBglobalsettings" method="post" action="{$bolink}?ppbtab&updateproduct&_token={Tools::getValue('_token')}&editblock={Tools::getValue('editblock')}#hooks">
                {$editform}
            </div>
        </div>
    {else}
        <div style="overflow:hidden;">
            <div style="display:block; margin-right:10px;">
                <h1 class="tab"><i class="material-icons">view_module</i> {l s='Related products lists' mod='ppb'}</h1>
                <div class="separation"></div>
                {if Tools::getValue('showall') == 1}
                    <div class="alert alert-info">
                        {l s='Module shows now all available \'related products\' instances, some of them may be not associated with product you currently edit' mod='ppb'}
                    </div>
                {else}
                    <div class="alert alert-info">
                        {l s='This section shows \'related products\' instances that are associated with this product only. If you want to show all created \'related products\' hit button  \'Show all created product lists\'' mod='ppb'}
                    </div>
                {/if}
                <a class="btn btn-primary uppercase " style="cursor:pointer;" style="cursor:pointer;" href="{$bolink}?ppbtab&addtab&_token={Tools::getValue('_token')}#hooks">
                    <i class="material-icons">add_circle</i> <span>{l s='Create new product list' mod='ppb'}</span>
                </a>
                {if Tools::getValue('showall') == 1}
                    <a class="btn btn-primary-outline uppercase " style="cursor:pointer;" style="cursor:pointer;" href="{$bolink}?ppbtab&_token={Tools::getValue('_token')}#hooks">
                        <i class="material-icons">visibility_off</i> <span> {l s='Hide product lists not associated with this product' mod='ppb'}</span>
                    </a>
                {else}
                    <a class="btn btn-primary-outline uppercase " style="cursor:pointer;" style="cursor:pointer;" href="{$bolink}?ppbtab&showall=1&_token={Tools::getValue('_token')}#hooks">
                        <i class="material-icons">visibility</i> <span> {l s='Show all created product lists' mod='ppb'}</span>
                    </a>
                {/if}
                <ul class="slides" id="ppblist">
                    {if isset($product_ppb)}
                        {foreach $product_ppb AS $ppb}
                            <li id="ppbp_{$ppb->id}" class="{if $ppb->global_man==1}global_manufacturers_block{/if}{if $ppb->global_cat==1}global_categories_block{/if}">
                                <span class="name">{$ppb->name} {if $ppb->internal_name != ''}({$ppb->internal_name}){else}{/if} | #ppbContainer{$ppb->id} | <font style="color:#c0c0c0">{if $ppb->block_position == 1} [{l s='block' mod='ppb'}]{elseif $ppb->block_position==0} [{l s='tab' mod='ppb'}]{elseif $ppb->block_position==3} [{l s='Show in cart during checkout' mod='ppb'}]{elseif $ppb->block_position==2} [{l s='PopUp' mod='ppb'}]{elseif $ppb->block_position==4} [{l s='Product\'s page right column' mod='ppb'}]{elseif $ppb->block_position==5} [{l s='Product\'s page left column' mod='ppb'}]{elseif $ppb->block_position==6} [{l s='Custom hook displayPpbModule' mod='ppb'}]{/if}</font></span>
                                <span class="label-tooltip remove" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Remove this list of products' mod='ppb'}" onclick="ppbp_remove({$ppb->id})"></span>
                                <span class="label-tooltip duplicate" onclick="ppbp_duplicate({$ppb->id})" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Duplicate this list of products' mod='ppb'}"></span>
                                <span class="edit"><a data-toggle="tooltip" data-original-title="{l s='Edit this list of products' mod='ppb'}" class="label-tooltip edit" href="{$bolink}?ppbtab&editblock={$ppb->id}&_token={Tools::getValue('_token')}#hooks"></a></span>
                                <span data-toggle="tooltip" data-original-title="{l s='Turn on / off this list of products' mod='ppb'}" class="label-tooltip {if $ppb->active==1}on{else}off{/if}" onclick="ppbp_toggle({$ppb->id})"></span>
                            </li>
                        {/foreach}
                    {/if}
                </ul>
            </div>
        </div>
    {/if}
</div>

<script>
    var ppb_url = '{$ppb_config_page}';
</script>

<script>
    {literal}
    var ppb_link = "{/literal}{$PPBLink}{literal}";
    var ppb_lang = {/literal}{Context::getContext()->language->id}{literal};
    {/literal}
</script>
