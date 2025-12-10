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

{if $IMGs}
<div class="ybc_instagram block_instagram">
        <div class="top_instagram">
            <div class="top_instagram_follow">
                <h3 class="h3 title-block">{l s='Instagram' mod='ph_instagram'}</h3>
                {if $PH_INSTAGRAM_DISPLAY_NAME}
                    {if $PH_INSTAGRAM_PROFILE_URL}
                        <a class="ph-insta-display-name" href="{$PH_INSTAGRAM_PROFILE_URL|escape:'quotes':'UTF-8'}" target="_blank">@{$PH_INSTAGRAM_DISPLAY_NAME|escape:'html':'UTF-8'}</a>
                    {else}
                        <span class="ph-insta-display-name">@{$PH_INSTAGRAM_DISPLAY_NAME|escape:'html':'UTF-8'}</span>
                    {/if}
                {/if}
            </div>
            {if $PH_INSTAGRAM_FOLLOW_US}
                {if $PH_INSTAGRAM_PROFILE_URL}
                    <a class="ph-insta-follow-us" href="{$PH_INSTAGRAM_PROFILE_URL|escape:'quotes':'UTF-8'}" target="_blank">{l s='Follow us' mod='ph_instagram'}</a>
                {else}
                    <span class="ph-insta-follow-us">{l s='Follow us' mod='ph_instagram'}</span>
                {/if}
            {/if}
        </div>
        <ul id="Home_instagram" class="instagram_list_img">
            {assign var='ik' value=0}
            {foreach $IMGs as $key=>$img}
                {assign var='ik' value=$ik+1}
                {if $ik <= $PH_INSTAGRAM_IMG_NUMBER}
                    <li class="instagram_item_img col-xs-4 col-sm-4 col-md-3 col-lg-2">
                        <a class="ybc_instagram_fancy"
                           href="{if $img.is_video}#ph_insta_video_{$key+1|escape:'quotes':'UTF-8'}{else}{$img.standard_resolution|escape:'quotes':'UTF-8'}{/if}"
                           data-media-type="{if $img.is_video}video{else}image{/if}"
                           title="{if $img.is_video}{l s='Click to view full video' mod='ph_instagram'}{else}{l s='Click to view full image' mod='ph_instagram'}{/if}"
                        >
                            <img {if $img.caption}alt="{$img.caption|escape:'html':'UTF-8'}"{/if} src="{$img.thumbnail|escape:'quotes':'UTF-8'}" alt=""/>
                        </a>
                        {if $img.is_video}
                            <video controls style="display: none; padding: 0; width: auto;" id="ph_insta_video_{$key+1|escape:'quotes':'UTF-8'}">
                                <source src="{$img.standard_resolution|escape:'quotes':'UTF-8'}" type="video/mp4">
                                Your browser doesn't support HTML5 video tag.
                            </video>
                        {/if}
                    </li>
                {/if}
            {/foreach}
        </ul>
</div>
{/if}