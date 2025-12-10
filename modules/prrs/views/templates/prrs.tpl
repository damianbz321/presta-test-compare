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
<style>
    .ratingStars {
        cursor: pointer;
    }
</style>
<div class="total-rating-items-block-footer card">
    <div class="clearfix row">
        <div class="col-md-6">
            <span class="h3reviews">
                OPINIE
            </span>
        </div>
        {if Configuration::get('prrs_addr') == 1 && $prrs_hide_add_review != true}
            <div class="col-md-6 addcomment">
                <a onclick="{if $comments_module == 'revws'}$('[data-revws-create-trigger]').click();{elseif $comments_module == 'productcomments' || $comments_module == 'myprestacomments'}$('.open-comment-form').click();{/if}" class="btn btn-small btn-primary">DODAJ OPINIE</a>
            </div>
        {/if}
    </div>
    {if $prrs_ratings_counter > 0}
        <meta itemprop="name" content="{$prrs_product->name}">
        <meta itemprop="url" content="{Context::getContext()->link->getProductLink($prrs_product)}">
        <div>
            <span class="ratingStars">
            {for $foo=1 to 5}
                {if $foo<=$ratings.avg}
                    <img width="32" height="30" src="{$urls.base_url}modules/prrs/views/img/star.png"/>

{elseif $foo-0.5<=$ratings.avg}

                    <img width="32" height="30" src="{$urls.base_url}modules/prrs/views/img/star-half.png"/>

{else}

                    <img width="32" height="30" src="{$urls.base_url}modules/prrs/views/img/star-empty.png"/>
                {/if}
            {/for}
            </span>
            <span class="ratingValue" itemprop="ratingValue">{$ratings.avg|string_format:"%.2f"}</span>
            {if $prrs_nbc}
                <span class="ratingValue">({if $ratings_counter>0}{$prrs_ratings_counter}{else}0{/if})</span>
            {/if}
            <meta itemprop="worstRating" content="0">
            <meta itemprop="ratingCount" content="{$ratings_counter}">
            <meta itemprop="bestRating" content="5">
        </div>
    {else}
        {l s='No reviews at the moment' mod='prrs'}
    {/if}
</div>

<script>
    $(document).ready(function (){
       $('body').on('click','.ratingStars', function (){
           $('.nav-tabs ul li:last-child a').click();
       });
    });
</script>