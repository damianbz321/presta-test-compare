<div class="panel">

    <div class="panel-heading">
        {l s='Multistore settings' mod='pshowconversion'}
    </div>

    {if !$isShopContext}
        <div class="alert alert-warning">
            {l s='You must switch to single shop context to configure this module if you do not have multistore addon.'}
        </div>
    {else}
        <div class="alert alert-warning">
            {l s='You do not have the multistore extension, so you may only have this module enabled for one store.'}
        </div>
    {/if}

    <div class="alert alert-info">
        You can buy multistore extension at
        <a href="https://prestashow.pl//65-.html">
            https://prestashow.pl//65-.html
        </a>
    </div>

    <div class="alert alert-info">
        If you have purchased a multi-store extension and assigned it to the same domain as the module, perform an update to activate the new extension.
    </div>

</div>
