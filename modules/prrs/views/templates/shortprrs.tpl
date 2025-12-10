{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-2021 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

<span class="ratingStarsShort" title="{$ratings.avg|string_format:"%.2f"}">
    {for $foo=1 to 5}
        {if $foo<=$ratings.avg}
            <img src="{$urls.base_url}modules/prrs/views/img/star.png"/>
        {elseif $foo-0.5<=$ratings.avg}
            <img src="{$urls.base_url}modules/prrs/views/img/star-half.png"/>
        {else}
            <img src="{$urls.base_url}modules/prrs/views/img/star-empty.png"/>
        {/if}
    {/for}
</span>
