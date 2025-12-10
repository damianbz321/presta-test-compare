{*
* Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}

{extends file="helpers/form/form.tpl"}

{block name="input"}
    {if $input.name == 'ETS_MC_ACCESS_TOKEN'}
        {if isset($fields_value[$input.name]) && $fields_value[$input.name] !== ''}{assign var='value_text' value=$fields_value[$input.name]}{else}{assign var='value_text' value=''}{/if}
		<div class="ETS_MC_ACCESS_TOKEN_group">
			<input type="text" name="ETS_MC_ACCESS_TOKEN" id="ETS_MC_ACCESS_TOKEN"
			       value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
			       class="ets_mc_copied" {if isset($input.required) && $input.required} required="required" {/if}
			       title="{l s='Click to copy' mod='ets_migrate_connector'}"/>
			<span class="data_copied">{l s='Copied' mod='ets_migrate_connector'}</span>
		</div>
	    <span class="input-group-btn">
            <a id="ets_mc_gencode" class="btn btn-default" href="#">
                <svg class="w_14 h_14" width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path
			                d="M504.971 359.029c9.373 9.373 9.373 24.569 0 33.941l-80 79.984c-15.01 15.01-40.971 4.49-40.971-16.971V416h-58.785a12.004 12.004 0 0 1-8.773-3.812l-70.556-75.596 53.333-57.143L352 336h32v-39.981c0-21.438 25.943-31.998 40.971-16.971l80 79.981zM12 176h84l52.781 56.551 53.333-57.143-70.556-75.596A11.999 11.999 0 0 0 122.785 96H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12zm372 0v39.984c0 21.46 25.961 31.98 40.971 16.971l80-79.984c9.373-9.373 9.373-24.569 0-33.941l-80-79.981C409.943 24.021 384 34.582 384 56.019V96h-58.785a12.004 12.004 0 0 0-8.773 3.812L96 336H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h110.785c3.326 0 6.503-1.381 8.773-3.812L352 176h32z"/></svg>
             {l s='Generate' mod='ets_migrate_connector'}</a>
        </span>
     {elseif $input.name == 'ETS_MC_DOMAIN'}
        {if isset($fields_value[$input.name]) && $fields_value[$input.name] !== ''}{assign var='value_text' value=$fields_value[$input.name]}{else}{assign var='value_text' value=''}{/if}
		<div class="ETS_MC_DOMAIN_group">
			<input readonly="true" type="text" name="ETS_MC_DOMAIN" id="ETS_MC_DOMAIN"
			       value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
			       class="ets_mc_copied" {if isset($input.required) && $input.required} required="required" {/if}
			       title="{l s='Click to copy' mod='ets_migrate_connector'}"/>
			<span class="data_copied">{l s='Copied' mod='ets_migrate_connector'}</span>
		</div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
