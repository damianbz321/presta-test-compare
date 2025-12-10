{*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA Miłosz Myszczuk VATEU: PL9730945634
 * @copyright 2010-2022 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
*}
<div class="panel">
    <div class="panel-heading"><i class="icon-cogs"></i> {l s='Possible collisions' mod='purls'}</div>
    <div class="bootstrap">
        <div class="alert alert-info">
            {l s='Add this url to your cron job table to automatically fix collisions' mod='purls'}<br/>
            {$cron_purls}
        </div>
    </div>
    <div class="bootstrap">
        <div class="alert alert-warning">
            <ul class="list-unstyled">
                <li>
                    <h4>{l s='Please check these duplicated URLs below (if any).' mod='purls'}</h4>
                    {l s='If products from the same category will have exactly the same link rewrite then only one product page will be accessible because PrestaShop will not be able to distinct these products' mod='purls'}
                </li>
            </ul>
        </div>
    </div>

    <div class="alert alert-info">{l s='The following tables shows products and categories sharing same URL which should NOT happen if products / categories have the the same parent category' mod='purls'}</div>

    <div class="panel">
        <div class="panel-heading"><i class="icon-cogs"></i> {l s='Product URLs' mod='purls'}</div>
        <div class="row row-margin-bottom">
            <table class="table productsCollisionsTable">
                <thead>
                <tr>
                    <th class="text-left"><span class="title_box active">{l s='ID' mod='purls'}</span></th>
                    <th class="text-left"><span class="title_box active">{l s='Name' mod='purls'}</span></th>
                    <th class="text-left"><span class="title_box active" style="color: red">{l s='Link Rewrite' mod='purls'}</span></th>
                    <th class="text-center"><span class="title_box active">{l s='Occurance' mod='purls'}</span></th>
                    <th class="text-center"><span class="title_box active">{l s='Action' mod='purls'}</span></th>
                </tr>
                </thead>
                <tbody>
                {if 1==2}
                    <tr>
                        <td class="text-center" colspan="5">{l s='No Collisions found' mod='purls'}</td>
                    </tr>
                {else}
                    {foreach Language::getLanguages(true) AS $language}
                        <tr>
                            <td colspan="2"><h2>{$language.name}</h2></td>
                            <td colspan="2">
                                <div class="alert alert-info" style="margin:0px 10px;">
                                    {l s='You can update rewrite field for products with one mouse click.' mod='purls'}<br/>
                                    {l s='For large list of potential collisions use partial feature, especially if your hosting environment has limited resources' mod='purls'}
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        {l s='Fix all collisions' mod='purls'}
                                        <i class="icon-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                        <li>
                                            <form method="post" name="CollisionsProductsForm{$language.id_lang}" id=" name="CollisionsProductsForm{$language.id_lang}"">
                                                <input type="hidden" name="makeUnique" value="products" />
                                                <input type="hidden" name="makeUniqueLanguage" value="{$language.id_lang}" />
                                            </form>
                                            <a href="" class="" onClick="CollisionsProductsForm{$language.id_lang}.submit(); return false;"><i class="icon-cogs"></i> {l s='Fix all at once' mod='purls'}</a>
                                        </li>
                                        <li>
                                            <a href="" onClick="FixProductsCollisions({$language.id_lang}); return false;" class="startUniqueTask"><i class="icon-cogs"></i> {l s='Fix partially' mod='purls'}</a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        {capture name="products_loop"}0{/capture}
                        {capture name="rewrite_capture"}&id_lang={Tools::getValue('id_lang')}&product_rewrite={Tools::getValue('product_rewrite','false')}{/capture}
                        {foreach from=$purls->getAllProductCollisions($language.id_lang) item=collision}
                            {capture name="products_loop"}1{/capture}
                            <tr class="ProductCollision-{$language.id_lang}" data-id-lang="{$language.id_lang}" data-id-product="{$collision.id_product|escape:'htmlall':'UTF-8'}" id="ProductCollision-{$language.id_lang}-{$collision.id_product|escape:'htmlall':'UTF-8'}">
                                <td {if Tools::getValue('product_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-left">{$collision.id_product|escape:'htmlall':'UTF-8'}</td>
                                <td {if Tools::getValue('product_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-left">{$collision.name|escape:'htmlall':'UTF-8'}</td>
                                <td {if Tools::getValue('product_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-left">{$collision.link_rewrite|escape:'htmlall':'UTF-8'}</td>
                                <td {if Tools::getValue('product_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-center">{$collision.times|escape:'htmlall':'UTF-8'}</td>
                                <td {if Tools::getValue('product_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-center"><a href="{$smarty.server.REQUEST_URI|replace:$smarty.capture.rewrite_capture:''}&id_lang={$language.id_lang}&product_rewrite={$collision.link_rewrite}" class="button btn btn-default">{l s='Show products' mod='purls'}</a></td>
                            </tr>
                            {if Tools::getValue('product_rewrite') == $collision.link_rewrite}
                                {if $exact_coll|is_array}
                                    {foreach name="fooo" from=$exact_coll item=exact_collision}
                                        {if $smarty.foreach.fooo.first != true}
                                            <tr>
                                                <td {if Tools::getValue('product_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-left">{$exact_collision.id_product|escape:'htmlall':'UTF-8'}</td>
                                                <td {if Tools::getValue('product_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-left">{$exact_collision.name|escape:'htmlall':'UTF-8'}</td>
                                                <td {if Tools::getValue('product_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-left">{$exact_collision.link_rewrite|escape:'htmlall':'UTF-8'}</td>
                                                <td {if Tools::getValue('product_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-center"></td>
                                                <td {if Tools::getValue('product_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} ></td>
                                            </tr>
                                        {/if}
                                    {/foreach}
                                {/if}
                            {/if}
                        {/foreach}
                        {if $smarty.capture.products_loop == 0}
                            <tr>
                                <td class="text-center" colspan="5">{l s='No Collisions found' mod='purls'}</td>
                            </tr>
                        {/if}
                        <tr>
                            <td colspan="5"><h2>&nbsp;</h2></td>
                        </tr>
                    {/foreach}
                {/if}
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading"><i class="icon-cogs"></i> {l s='Category URLs' mod='purls'}</div>
        <div class="row row-margin-bottom">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left"><span class="title_box active">{l s='ID' mod='purls'}</span></th>
                    <th class="text-left"><span class="title_box active">{l s='Name' mod='purls'}</span></th>
                    <th class="text-left"><span class="title_box active" style="color: red">{l s='Link Rewrite' mod='purls'}</span></th>
                    <th class="text-center"><span class="title_box active">{l s='Occurance' mod='purls'}</span></th>
                    <th class="text-center"><span class="title_box active">{l s='Action' mod='purls'}</span></th>
                </tr>
                </thead>
                <tbody>
                {if 1==2}
                {else}
                    {foreach Language::getLanguages(true) AS $language}
                        <tr>
                            <td colspan="4"><h2>{$language.name}</h2></td>
                            <td class="text-center">
                                <form method="post">
                                    <input type="hidden" name="makeUnique" value="categories" />
                                    <input type="hidden" name="makeUniqueLanguage" value="{$language.id_lang}" />
                                    <button class="button btn btn-default">{l s='Make rewrite fields unique' mod='purls'}</button>
                                </form>
                            </td>
                        </tr>
                        {capture name="categories_loop"}0{/capture}
                        {capture name="categories_capture"}&id_lang={$language.id_lang}&category_rewrite={Tools::getValue('category_rewrite','false')}{/capture}
                        {foreach from=$purls->getAllCategoryCollisions($language.id_lang) item=collision}
                            {capture name="categories_loop"}1{/capture}
                            <tr>
                                <td {if Tools::getValue('category_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if}  class="text-left">{$collision.id_category|escape:'htmlall':'UTF-8'}</td>
                                <td {if Tools::getValue('category_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-left">{$collision.name|escape:'htmlall':'UTF-8'}</td>
                                <td {if Tools::getValue('category_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-left">{$collision.link_rewrite|escape:'htmlall':'UTF-8'}</td>
                                <td {if Tools::getValue('category_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-center">{$collision.times|escape:'htmlall':'UTF-8'}</td>
                                <td {if Tools::getValue('category_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-center"><a href="{$smarty.server.REQUEST_URI|replace:$smarty.capture.rewrite_capture:''}&id_lang={$language.id_lang}&category_rewrite={$collision.link_rewrite}" class="button btn btn-default">{l s='Show categories' mod='purls'}</a></td>
                            </tr>
                            {if Tools::getValue('category_rewrite') == $collision.link_rewrite}
                                {if $exact_coll_cat|is_array}
                                    {foreach name=foo from=$exact_coll_cat item=exact_collision}
                                        {if $smarty.foreach.foo.first != true}
                                            <tr>
                                                <td {if Tools::getValue('category_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-left">{$exact_collision.id_category|escape:'htmlall':'UTF-8'}</td>
                                                <td {if Tools::getValue('category_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-left">{$exact_collision.name|escape:'htmlall':'UTF-8'}</td>
                                                <td {if Tools::getValue('category_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-left">{$exact_collision.link_rewrite|escape:'htmlall':'UTF-8'}</td>
                                                <td {if Tools::getValue('category_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} class="text-center"></td>
                                                <td {if Tools::getValue('category_rewrite') == $collision.link_rewrite}style="background:#fffac8!important;"{/if} ></td>
                                            </tr>
                                        {/if}
                                    {/foreach}
                                {/if}
                            {/if}
                        {/foreach}

                        {if $smarty.capture.categories_loop == 0}
                            <tr>
                                <td class="text-center" colspan="5">{l s='No Collisions found' mod='purls'}</td>
                            </tr>
                        {/if}
                        <tr>
                            <td colspan="5"><h2>&nbsp;</h2></td>
                        </tr>
                    {/foreach}
                {/if}
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    {literal}
    function FixProductsCollisions(id_lang)
    {
        $('table.productsCollisionsTable tr.ProductCollision-'+id_lang).each(function(index, element){
            var self = this;
            setTimeout(function () {
                $.ajax({url: "{/literal}{$purls_url}&AjaxCollisions&ajax=1&id_lang={literal}"+id_lang+'&id_product='+$(self).attr('data-id-product'),
                    success: function(result){
                        $(self).fadeOut('2500');
                    },
                });
            }, index*500);
        });
    }
    {/literal}
</script>