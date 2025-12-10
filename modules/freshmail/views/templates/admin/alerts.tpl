<div class="bootstrap">
{if isset($error)}
    <div class="alert alert-success">{$error|escape:'htmlall':'UTF-8'}</div>
{/if}
{if isset($success)}
    <div class="alert alert-success">{$success|escape:'htmlall':'UTF-8'}</div>
{/if}
{if isset($successUpdate)}
    <div class="alert alert-success">{$successUpdate|escape:'htmlall':'UTF-8'}</div>
{/if}

{if isset($errorEmptyKeys)}
    <div class="alert alert-warning">
        <button data-dismiss="alert" class="close" type="button">×</button>
        {l s='Please connect to your FreshMail account on the: ' mod='freshmail'}
    </div>
{/if}
</div>