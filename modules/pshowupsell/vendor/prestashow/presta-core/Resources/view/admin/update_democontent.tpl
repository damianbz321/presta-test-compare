<h3>{l s='Demo content'}</h3>

{if $isDemoContentAvailable}

  {if isset($log)}
    <h4>{l s='Demo content installation log'}</h4>
    <ul>
      {foreach from=$log item='item'}
        <li>{$item}</li>
      {/foreach}
    </ul>
    <hr>
    {if $installationResult}
      <p class="alert alert-success">
        {l s='Demo content installed successfully'}
      </p>
    {else}
      <p class="alert alert-danger">
        {l s='Demo content installation failed'}
      </p>
    {/if}
  {else}
    <p>
      {l s='Use button bellow to install demo content of the module.'}
    </p>
    <p>
      <a href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Update", true)}&page=demoContent&installDemoContent=1"
         class="btn btn-default">
        {l s='Install demo content' mod=''}
      </a>
    </p>
  {/if}
{else}
  <p>
    {l s='This module do not contains any demo content.'}
  </p>
{/if}
