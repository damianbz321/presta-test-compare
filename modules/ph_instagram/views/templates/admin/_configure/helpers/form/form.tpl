{*
* 2007-2020 ETS-Soft
*
* NOTICE OF LICENSE
*
* This file is not open source! Each license that you purchased is only available for 1 wesite only.
* If you want to use this file on more websites (or projects), you need to purchase additional licenses.
* You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please, contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <contact@etssoft.net>
*  @copyright  2007-2020 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
{extends file="helpers/form/form.tpl"}
{block name="input"}
    {if $input.name == 'PH_INSTAGRAM_ACCESS_TOKEN'}
        {$smarty.block.parent}
        <a class="ph_get_access_token" target="_blank" href="https://www.youtube.com/watch?time_continue=9&v=rWUcb8jXgVA&feature=emb_logo">{l s='How to get access token?' mod='ph_instagram'}</a>
        <br /><button class="btn btn-default btn-ph-insta-refresh-token js-btn-ph-insta-refresh-token"><i class="fa fa-refresh"></i> {l s='Refresh token' mod='ph_instagram'}</button>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name="description"}
    {if $input.name == 'PH_INSTAGRAM_CRONJOB_TOKEN'}
        {$smarty.block.parent}
        <p class="set-cronjob">{l s='Setup a cronjob as below on your server to refresh access token of Instagram:' mod='ph_instagram'}<br /><code>{$codeCronjob|escape:'quotes':'UTF-8'}</code></p>
        {l s='Manually refresh access token of Instagram by running the following URL on your web browser:' mod='ph_instagram'}<br />
        <a class="cronjob_link" href="{$linkCronjob|escape:'quotes':'UTF-8'}" target="_blank">{$linkCronjob|escape:'quotes':'UTF-8'}</a>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}