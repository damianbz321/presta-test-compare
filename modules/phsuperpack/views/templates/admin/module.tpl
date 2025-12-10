
{include file=$phelpTop}
<style>
    #pack_category div {
        padding: 10px 5px;
        border:1px solid #cccccc;
        cursor: pointer;
    }
</style>
<div class="panel" style="margin-top: 25px;">

    <div role="tabpanel" class="tab-pane active" id="home">
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-cogs"></i> Konfiguracja
            </div>
            <form method="post" enctype="multipart/form-data" class="defaultForm form-horizontal">
                <div class="form-wrapper">

                    <div class="form-group">
                        <label class="control-label col-lg-3">
                            Zamieniaj zestawy na pojedyncze produkty
                        </label>
                        <div class="col-lg-9">
								<span class="switch prestashop-switch fixed-width-lg">
									<input type="radio" name="PH_PACK_UNZIP" id="PH_PACK_UNZIP_on" value="1" {if $product_pack_unzip == 1}checked="checked"{/if}>
									<label for="PH_PACK_UNZIP_on">Tak</label>
									<input type="radio" name="PH_PACK_UNZIP" id="PH_PACK_UNZIP_off" value="0" {if $product_pack_unzip == 0}checked="checked"{/if}>
									<label for="PH_PACK_UNZIP_off">Nie</label>
									<a class="slide-button btn"></a>
								</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">
                            Czy aktualizować cenę zestawu gdy zmieni się cena produktu?
                        </label>
                        <div class="col-lg-9">
								<span class="switch prestashop-switch fixed-width-lg">
									<input type="radio" name="PH_CHECK_PRICE" id="PH_CHECK_PRICE_on" value="1" {if $product_check_price == 1}checked="checked"{/if}>
									<label for="PH_CHECK_PRICE_on">Tak</label>
									<input type="radio" name="PH_CHECK_PRICE" id="PH_CHECK_PRICE_off" value="0" {if $product_check_price == 0}checked="checked"{/if}>
									<label for="PH_CHECK_PRICE_off">Nie</label>
									<a class="slide-button btn"></a>
								</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-3">
                            Wybierz rodzaj podatku dla pakietów
                        </label>
                        <div class="col-lg-9">
                            <select class="form-control" name="ph_superpack_taxes">
                                {foreach from=$taxes item=tax}
                                    <option value="{$tax.id_tax}"
                                    {if $tax.id_tax == $choosed_tax}
                                        selected
                                    {/if}
                                    >{$tax.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-control-label">{l s='Ikona domyślna zestawów' mod='phsuperpack'}</label>
                        <div>
                            <input id="product_image_mod" type="file" name="product_image_mod" />
                        </div>
                    </div>
                    {if $product_image_value != ''}
                        <div class="form-group">
                            <div id="images-thumbnails" class="col-lg-12">
                                <img src="{$uri}img/{$product_image_value}" class="img-thumbnail"/>
                            </div>
                        </div>
                    {/if}

                </div>
                <div class="form-group">
                    <label class="control-label col-lg-2">
                       <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="Zaznacz wszystkie grupy, którym chcesz przydzielić dostęp do tego sposobu dostawy.">
                       Dostęp grupowy
                       </span>
                    </label>
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-lg-6">

                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th class="fixed-width-xs">
                        <span class="title_box">
                        </span>
                                        </th>
                                        <th class="fixed-width-xs"><span class="title_box">ID</span></th>
                                        <th>
                        <span class="title_box">
                        Nazwa grupy
                        </span>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    {foreach $groups_client as $group_client}

                                        <tr>
                                            <td>
                                                <input type="checkbox" name="groupbox_fields[]" class="groupbox_fields" id="groupbox_fields_{$group_client["id_group"]}" value="{$group_client["id_group"]}" {if $groups_client_choose[$group_client["id_group"]-1] != ''}checked="checked"{/if}>
                                            </td>
                                            <td>{$group_client["id_group"]}</td>
                                            <td>
                                                <label for="groupbox_fields_{$group_client["id_group"]}">{$group_client["name"]}</label>
                                            </td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
{*                <div class="form-group">*}
{*                    <div class="control-label  col-lg-2">*}
{*                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" >Wybierz kategorię dla swoich zestawów</span>*}
{*                    </div>*}
{*                    <div class="col-lg-10">*}
{*                        <select name="pack_category" id="pack_category">*}
{*                            <option value="0">*}
{*                                Nie wybrano*}
{*                            </option>*}
{*                            {foreach $all_category as $cat}*}
{*                                <option value="{$cat["id_category"]}" {if $choosed_category == $cat["id_category"]} selected{/if}>*}
{*                                    {$cat["name"]}*}
{*                                </option>*}
{*                            {/foreach}*}
{*                        </select>*}
{*                    </div>*}
{*                </div>*}
                <div>
                    <label for="searchTerm">Wyszukaj kategorie:</label>
                    <input type="text" id="searchTerm" name="searchTerm" placeholder="Wyszukaj" onkeyup="searchCategories()" value="{if $choosed_category_name[1]} {$choosed_category_name[1]} {/if}"/>

                    <div id="pack_category" class="tt-menu" style="display:none;">

                    </div>
                    <input type="hidden" name="pack_category" id="pack_category_input">
                </div>
                <div id="categoryList"></div>
                {literal}
                <script type="text/javascript">
                    function addToInput(name,id) {
                        document.getElementById("pack_category_input").value=id;
                        document.getElementById("searchTerm").value=name;
                        document.getElementById("pack_category").style.display = "none";
                    }
                    function searchCategories() {
                        var searchTerm = document.getElementById("searchTerm").value;
                        if (searchTerm.length >= 3) {
                            var xmlhttp = new XMLHttpRequest();
                            xmlhttp.onreadystatechange = function() {
                                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                    var categoryList = JSON.parse(xmlhttp.responseText);
                                    var html = "";
                                    for (var i = 0; i < categoryList.length; i++) {
                                        html += '<div><a onclick="addToInput(\''+categoryList[i].name+'\',' + categoryList[i].id + ')" value="' + categoryList[i].id + '">' + categoryList[i].name + '</a></div>';
                                    }
                                    document.getElementById("pack_category").innerHTML = html;
                                    console.log(categoryList.length );
                                    if(categoryList.length > 0)
                                        document.getElementById("pack_category").style.display = "block";

                                    else
                                        document.getElementById("pack_category").style.display = "none";
                                }
                            };

                            xmlhttp.open("GET", "{/literal}{$url_ajax_controller_search}{literal}&searchTerm=" + searchTerm, true);
                            xmlhttp.send();
                        }

                    }
                </script>
                {/literal}
                <div class="panel-footer">
                    <button class="btn btn-default pull-right" type="submit" value="1" name="submitPhSuperPackModule"><i class="process-icon-save"></i> {l s='Zapisz'}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="panel-heading">
        Jak użyć zestawów?
    </div>
    <div class="panel-wraper">
        {if $psVersion}
            <p>
                1. Aby wyświetlić zestaw w koszyku wstaw <b>themes/{$themeName}/shopping-cart-product-line.tpl</b>:
            </p>
            <textarea name="area_mail" id="area_mail" class="textarea-autosize" disabled style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 65px;"><tr><td colspan="7">&#123;hook h='displayPack' id_product=$product.id_product&#125;</td></tr></textarea>
            <p></p>
            <p class="alert-{if $ready1}success{else}danger{/if}" style="display: inline-block;padding: 5px 10px;">
                umieść go na końcu pliku
            </p>
        {else}
            <p>
                Aby wyświetlić zestaw dla prduktu wstaw <b>themes/{$themeName}/templates/checkout/_partials/cart-detailed-product-line.tpl</b>:
            </p>
            {*<code>&#123;hook h='displayPack' id_product=$product.id_product&#125;</code>*}
            <textarea name="area_mail" id="area_mail" class="textarea-autosize" disabled style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 65px;">&#123;hook h='displayPack' id_product=$product.id_product&#125;</textarea>
            <p></p>
            <p class="alert-{if $ready1}success{else}danger{/if}" style="display: inline-block;padding: 5px 10px;">
                umieść go przed zamknięciem elementu 'div' o klasie 'product-line-grid'
            </p>
        {/if}
    </div>
</div>

{$banersHtml}
{include file=$phelpBtm}
