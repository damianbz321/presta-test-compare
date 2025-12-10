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

{foreach from=$blocksProductsOrder item=block}
    {if $block->custom_path == 1 && file_exists($block->path)}
        {include file=$block->path block=$block}
    {else}
        <div id="ppbContainer{$block->id|escape:'int':'utf-8'}" class="featured-products block products_block clearfix {if $block->carousell==1}ppbContainerBlockMainDiv ppbCarouselBlock{/if}" {if $block->block_position == 2}style="display:none;"{/if}>
            <h2 class="h2 products-section-title text-uppercase ">{if $block->titleURL != "" && $block->titleURL != NULL}<a href="{$block->titleURL}">{$block->name|escape:'html':'utf-8'}</a>{else}{$block->name|escape:'html':'utf-8'}{/if}{if $block->carousell_controls==1 && $block->carousell==1}<i class="ppbback ppbb{$block->id} material-icons"></i><i class="material-icons ppbf{$block->id} ppbforward"></i>{/if}</h2>
            {if isset($block->products) && $block->products}
                <div class="ppb{$block->id} featured-products" id="ppb{$block->id}">
                    <div id="js-product-list">
                        {include file='catalog/_partials/miniatures/list-item.tpl' products=$block->products}
                    </div>
                </div>
            {else}
                <ul class="ppbContainer{$block->id|escape:'int':'utf-8'}_noProducts tab-pane">
                    <li class="alert alert-info">{l s='No products at this time.' mod='ppb'}</li>
                </ul>
            {/if}
        </div>
        {if $block->block_position == 2 && $block->carousell==1}
            <script>
                {literal}
                document.addEventListener("DOMContentLoaded", function (event) {
                    $.fancybox({
                        'maxWidth': 1200,
                        'scrolling': 'no',
                        'closeClick': false,
                        'nextClick': false,
                        'margin': 20,
                        'hideOnOverlayClick': false,
                        'hideOnContentClick': false,
                        'autoscale': true,
                        'content': $("#ppbContainer{/literal}{$block->id|escape:'int':'utf-8'}{literal}").html(),
                        'helpers': {
                            overlay: {
                                closeClick: false,
                                css: {'overflow': 'hidden'}
                            }
                        },
                        'afterShow': function () {
                            $('.fancybox-inner').addClass('block');
                            $('.fancybox-skin').css("background-color", "white");
                            var {/literal}ppb{$block->id|escape:'int':'utf-8'}{literal} = $('.fancybox-inner #{/literal}ppb{$block->id}{literal} .products').lightSlider(
                                {
                                    item: {/literal}{$block->carousell_nb}{literal},
                                    loop: false,
                                    slideMove: 1,
                                    speed: 600,
                                    pager: {/literal}{if $block->carousell_pager==1}true{else}false{/if}{literal},
                                    loop: {/literal}{if $block->carousell_loop==1}true{else}false{/if}{literal},
                                    controls: false,
                                    pauseOnHover: true,
                                    slideMargin: 0,
                                    auto: {/literal}{if $block->carousell_auto==1}true{else}false{/if}{literal},
                                    responsive: [
                                        {
                                            breakpoint: 800,
                                            settings: {
                                                item: {/literal}{$block->carousell_nb_tablet}{literal},
                                                slideMove: 1,
                                                slideMargin: 6,
                                            }
                                        },
                                        {
                                            breakpoint: 480,
                                            settings: {
                                                item: {/literal}{$block->carousell_nb_mobile}{literal},
                                                slideMove: 1
                                            }
                                        }
                                    ]
                                }
                            );
                            {/literal}
                            {if $block->carousell_controls==1 && $block->carousell==1}
                            {literal}
                            $('.fancybox-inner .ppbb{/literal}{$block->id}{literal}').click(function () {
                                {/literal}ppb{$block->id}{literal}.goToPrevSlide();
                            });
                            $('.fancybox-inner .ppbf{/literal}{$block->id}{literal}').click(function () {
                                {/literal}ppb{$block->id}{literal}.goToNextSlide();
                            });
                            {/literal}
                            {/if}
                            {literal}
                        }
                    });
                });
                {/literal}
            </script>
        {/if}

        {if $block->carousell==1 && $block->block_position != 2}
        {literal}
            <script>
                document.addEventListener("DOMContentLoaded", function (event) {
                    var {/literal}ppb{$block->id}{literal} = $('.{/literal}ppb{$block->id}{literal} .products').lightSlider(
                        {
                            item: {/literal}{$block->carousell_nb}{literal},
                            loop: false,
                            slideMove: 1,
                            slideMargin: 10,
                            speed: 600,
                            pager: {/literal}{if $block->carousell_pager==1}true{else}false{/if}{literal},
                            loop: {/literal}{if $block->carousell_loop==1}true{else}false{/if}{literal},
                            controls: false,
                            pauseOnHover: true, adaptiveHeight: true,
                            auto: {/literal}{if $block->carousell_auto==1}true{else}false{/if}{literal},
                            responsive: [
                                {
                                    breakpoint: 800,
                                    settings: {
                                        item: {/literal}{$block->carousell_nb_tablet}{literal},
                                        slideMove: 1,
                                        slideMargin: 10,
                                    }
                                },
                                {
                                    breakpoint: 480,
                                    settings: {
                                        item: {/literal}{$block->carousell_nb_mobile}{literal},
                                        slideMove: 1,
                                        slideMargin: 10
                                    }
                                }
                            ]
                        }
                    );
                    {/literal}
                    {if $block->carousell_controls==1 && $block->carousell==1}
                    {literal}
                    $('.ppbb{/literal}{$block->id}{literal}').click(function () {
                        {/literal}ppb{$block->id}{literal}.goToPrevSlide();
                    });
                    $('.ppbf{/literal}{$block->id}{literal}').click(function () {
                        {/literal}ppb{$block->id}{literal}.goToNextSlide();
                    });
                    {/literal}
                    {/if}
                    {literal}

                });
            </script>
        {/literal}
        {/if}
    {/if}
{/foreach}


{literal}
    <style>
        .js-product-list .lSSlideWrapper {
            padding: 5px;
        }
    </style>
{/literal}