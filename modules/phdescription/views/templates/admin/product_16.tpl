<div class="panel product-tab" id="product-phdescription">
    <h3>{l s='Modułowy opis produktu' mod='phdescription'}</h3>
    <div class="row">
        <div class="phdescription-admin-extra">
            <div class="phdescription-admin-footer">
                {if !empty($list)}
                    <div class="sortableBox" id="sortableBox">
                        {foreach $list as $ki => $l}
                            <div class="item element sortableItem" data-idd="{$l.id_description}" data-idp="{$l.id_product}" data-position="{$l.position}" data-current="{$l.position}">
                                <div class="row itemElement{if $edit == 1} showElement{/if}">
                                    <div class="col-lg-8 type">
                                        <ul>
                                            <li class="{if $l.type == 1}active{/if}"><i class="far fa-image"></i></li>
                                            <li class="{if $l.type == 2}active{/if}"><i class="fas fa-align-left"></i></li>
                                            <li class="{if $l.type == 3}active{/if}"><i class="far fa-image"></i> <i class="fas fa-align-left"></i></li>
                                            <li class="{if $l.type == 4}active{/if}"><i class="fas fa-align-left"></i> <i class="far fa-image"></i></li>
                                            <li class="{if $l.type == 5}active{/if}"><i class="far fa-image"></i> <i class="far fa-image"></i></li>
                                            <li class="{if $l.type == 6}active{/if}"><i class="fas fa-align-left"></i> <i class="fas fa-align-left"></i></li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-4 actions">
                                        <button type="button" class="btn btn-danger deleteElement" data-idd="{$l.id_description}">{l s='Delete item' mod='phdescription'}</button>
                                        <button type="button" class="btn btn-primary editBtn" data-type="hide" data-idd="{$l.id_description}" data-idp="{$l.id_product}">{l s='Edit item' mod='phdescription'}</button>
                                    </div>
                                </div>
                                <div class="row editElement{if $edit == 1} showElement{/if}">
                                    {if $l.type == 1}
                                        <input type="hidden" name="phdescriptionE[{$ki}][type]" value="1" />
                                        <input type="hidden" name="phdescriptionE[{$ki}][id_description]" value="{$l.id_description}" />
                                        <input type="hidden" name="phdescriptionE[{$ki}][id_product]" value="{$id_product}" />
                                        <div class="form-create-box form-create-1 col-lg-12">
                                            {*                                    <p class="head">{l s='1 column - big image' mod='phdescription'} (max. szerokość zdjęcia to 1080px)</p>*}
                                            <fieldset class="form-group">
                                                {foreach $languages as $key => $lang}
                                                    <div class="row langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                                                        <div class="col-md-12">
                                                            <label class="image-box" for="input_file-{$l.id_description}" data-idp="{$id_product}" data-type="1" data-idd="{$l.id_description}"><i class="fas fa-image"></i></label>
                                                            <input type="file" class="form-control input_file input_file-{$l.id_description}" name="phdescription[image_file]" id="input_file-{$l.id_description}" data-idd="{$l.id_description}" data-action="{$addFileUrl}" data-idp="{$id_product}" data-type="1" style="display: none;visibility: hidden;" />
                                                            <input type="hidden" name="phdescriptionE[{$ki}][image][{$lang.id_lang}]" class="imageValue" value="{$l.lang[$lang.id_lang].image}"/>
                                                            <small class="hint">(max. szerokość zdjęcia to 1080px)</small>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <br />
                                                            <img src="{$l.lang[$lang.id_lang].image}" alt="" />
                                                        </div>
                                                    </div>
                                                {/foreach}
                                            </fieldset>
                                        </div>
                                    {/if}
                                    {if $l.type == 2}
                                        <input type="hidden" name="phdescriptionE[{$ki}][type]" value="2" />
                                        <input type="hidden" name="phdescriptionE[{$ki}][id_description]" value="{$l.id_description}" />
                                        <input type="hidden" name="phdescriptionE[{$ki}][id_product]" value="{$id_product}" />
                                        <div class="form-create-box form-create-2 col-lg-12">
                                            {*                                    <p class="head">{l s='1 column - big image' mod='phdescription'}</p>*}
                                            <fieldset class="form-group">
                                                <div class="row">
                                                    {foreach $languages as $key => $lang}
                                                        <div class="col-md-12 langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                                                            <label class="form-control-label">[{$lang.iso_code|strtoupper}] {l s='Insert a text' mod='phdescription'}:</label>
                                                            <textarea name="phdescriptionE[{$ki}][text][{$lang.id_lang}]" class="form-control autoload_rte" >{$l.lang[$lang.id_lang].text}</textarea>
                                                        </div>
                                                    {/foreach}
                                                </div>
                                            </fieldset>
                                        </div>
                                    {/if}
                                    {if $l.type == 3}
                                        <input type="hidden" name="phdescriptionE[{$ki}][type]" value="3" />
                                        <input type="hidden" name="phdescriptionE[{$ki}][id_description]" value="{$l.id_description}" />
                                        <input type="hidden" name="phdescriptionE[{$ki}][id_product]" value="{$id_product}" />
                                        <div class="form-create-box form-create-3 col-lg-12">
                                            {*                                    <p class="head">{l s='2 column - image / text' mod='phdescription'} (max. szerokość zdjęcia to 540px)</p>*}
                                            <fieldset class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        {foreach $languages as $key => $lang}
                                                            <div class="langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                                                                <label class="image-box" for="input_file-{$l.id_description}" data-idp="{$id_product}" data-type="3" data-idd="{$l.id_description}"><i class="fas fa-image"></i></label>
                                                                <input type="file" class="form-control input_file input_file-{$l.id_description}" name="phdescription[image_file3]" id="input_file-{$l.id_description}" data-action="{$addFileUrl}" data-idp="{$id_product}" data-idd="{$l.id_description}" data-type="3" style="display: none;visibility: hidden;" />
                                                                <input type="hidden" name="phdescriptionE[{$ki}][image3][{$lang.id_lang}]" class="imageValue" value="{$l.lang[$lang.id_lang].image}"/>
                                                                <small class="hint">(max. szerokość zdjęcia to 540px)</small>
                                                                <br />
                                                                <img src="{$l.lang[$lang.id_lang].image}" alt="" />
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                    <div class="col-md-6">
                                                        {foreach $languages as $key => $lang}
                                                            <div class="langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                                                                <label class="form-control-label">[{$lang.iso_code|strtoupper}] {l s='Insert a text' mod='phdescription'}:</label>
                                                                <textarea name="phdescriptionE[{$ki}][text3][{$lang.id_lang}]" class="form-control autoload_rte" >{$l.lang[$lang.id_lang].text}</textarea>
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                    {/if}
                                    {if $l.type == 4}
                                        <input type="hidden" name="phdescriptionE[{$ki}][type]" value="4" />
                                        <input type="hidden" name="phdescriptionE[{$ki}][id_description]" value="{$l.id_description}" />
                                        <input type="hidden" name="phdescriptionE[{$ki}][id_product]" value="{$id_product}" />
                                        <div class="form-create-box form-create-4 col-lg-12">
                                            {*                                    <p class="head">{l s='2 column - text / image' mod='phdescription'} (max. szerokość zdjęcia to 540px)</p>*}
                                            <fieldset class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        {foreach $languages as $key => $lang}
                                                            <div class="langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                                                                <label class="form-control-label">[{$lang.iso_code|strtoupper}] {l s='Insert a text' mod='phdescription'}:</label>
                                                                <textarea name="phdescriptionE[{$ki}][text4][{$lang.id_lang}]" class="form-control autoload_rte" >{$l.lang[$lang.id_lang].text}</textarea>
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                    <div class="col-md-6">
                                                        {foreach $languages as $key => $lang}
                                                            <div class="langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                                                                <label class="image-box" for="input_file-{$l.id_description}" data-idp="{$id_product}" data-type="4" data-idd="{$l.id_description}"><i class="fas fa-image"></i></label>
                                                                <input type="file" class="form-control input_file input_file-{$l.id_description}" name="phdescription[image_file4]" id="input_file-{$l.id_description}" data-action="{$addFileUrl}" data-idp="{$id_product}" data-idd="{$l.id_description}" data-type="4" style="display: none;visibility: hidden;" />
                                                                <input type="hidden" name="phdescriptionE[{$ki}][image4][{$lang.id_lang}]" class="imageValue" value="{$l.lang[$lang.id_lang].image}"/>
                                                                <small class="hint">(max. szerokość zdjęcia to 540px)</small>
                                                                <br />
                                                                <img src="{$l.lang[$lang.id_lang].image}" alt="" />
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                    {/if}
                                    {if $l.type == 5}
                                        <input type="hidden" name="phdescriptionE[{$ki}][type]" value="5" />
                                        <input type="hidden" name="phdescriptionE[{$ki}][id_description]" value="{$l.id_description}" />
                                        <input type="hidden" name="phdescriptionE[{$ki}][id_product]" value="{$id_product}" />
                                        <div class="form-create-box form-create-5 col-lg-12">
                                            {*                                    <p class="head">{l s='2 column - image / image' mod='phdescription'} (max. szerokość zdjęcia to 540px)</p>*}
                                            <fieldset class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        {foreach $languages as $key => $lang}
                                                            <div class="langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                                                                <label class="image-box" for="input_file-{$id_description}a" data-idp="{$id_product}" data-type="5" data-idd="{$id_description}" data-ty="a"><i class="fas fa-image"></i></label>
                                                                <input type="file" class="form-control input_file input_file-{$l.id_description}a" name="phdescription[image_file5]" id="input_file-{$id_description}a" data-idd="{$l.id_description}" data-ty="a" data-action="{$addFileUrl}" data-idp="{$id_product}" data-type="5" style="display: none;visibility: hidden;" />
                                                                <input type="hidden" name="phdescriptionE[{$ki}][image5][{$lang.id_lang}]" class="imageValue" value="{$l.lang[$lang.id_lang].image}"/>
                                                                <small class="hint">(max. szerokość zdjęcia to 540px)</small>
                                                                <br />
                                                                <img src="{$l.lang[$lang.id_lang].image}" alt="" />
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                    <div class="col-md-6">
                                                        {foreach $languages as $key => $lang}
                                                            <div class="langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                                                                <label class="image-box" for="input_file-{$id_description}b" data-idp="{$id_product}" data-type="6" data-idd="{$id_description}" data-ty="a"><i class="fas fa-image"></i></label>
                                                                <input type="file" class="form-control input_file input_file-{$l.id_description}b" name="phdescription[image_file6]" id="input_file-{$id_description}b" data-idd="{$l.id_description}" data-ty="b" data-action="{$addFileUrl}" data-idp="{$id_product}" data-type="6" style="display: none;visibility: hidden;" />
                                                                <input type="hidden" name="phdescriptionE[{$ki}][image6][{$lang.id_lang}]" class="imageValue" value="{$l.lang[$lang.id_lang].image2}"/>
                                                                <small class="hint">(max. szerokość zdjęcia to 540px)</small>
                                                                <br />
                                                                <img src="{$l.lang[$lang.id_lang].image2}" alt="" />
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                    {/if}
                                    {if $l.type == 6}
                                        <input type="hidden" name="phdescriptionE[{$ki}][type]" value="6" />
                                        <input type="hidden" name="phdescriptionE[{$ki}][id_description]" value="{$l.id_description}" />
                                        <input type="hidden" name="phdescriptionE[{$ki}][id_product]" value="{$id_product}" />
                                        <div class="form-create-box form-create-6 col-lg-12">
                                            {*                                    <p class="head">{l s='2 column - text / text' mod='phdescription'}</p>*}
                                            <fieldset class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        {foreach $languages as $key => $lang}
                                                            <div class="langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                                                                <label class="form-control-label">[{$lang.iso_code|strtoupper}] {l s='Insert a text' mod='phdescription'}:</label>
                                                                <textarea name="phdescriptionE[{$ki}][text6][{$lang.id_lang}]" class="form-control autoload_rte" >{$l.lang[$lang.id_lang].text}</textarea>
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                    <div class="col-md-6">
                                                        {foreach $languages as $key => $lang}
                                                            <div class="langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                                                                <label class="form-control-label">[{$lang.iso_code|strtoupper}] {l s='Insert a text' mod='phdescription'}:</label>
                                                                <textarea name="phdescriptionE[{$ki}][text7][{$lang.id_lang}]" class="form-control autoload_rte" >{$l.lang[$lang.id_lang].text2}</textarea>
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        {/foreach}
                    </div>
                {/if}
            </div>
            <div class="phdescription-admin-body">
                <div class="phdescription-admin-form-create"></div>
            </div>
            <div class="phdescription-admin-header">
                <button type="button" class="btn btn-success addNewElement"><i class="fas fa-plus-circle"></i> {l s='Add new element' mod='phdescription'}</button>
                <div class="phdescription-admin-form-add">
                    <div class="">
                        <fieldset class="form-group">
                            <div class="row">
                                <div class="col-md-8">
                                    <label class="form-control-label">{l s='Type of element' mod='phdescription'}:</label>
                                    <div class="head">
                                        <ul>
                                            <li class="createNewElement" data-type="1" title="{l s='1 column - big image' mod='phdescription'}"><i class="far fa-image"></i></li>
                                            <li class="createNewElement" data-type="2" title="{l s='1 column - big text' mod='phdescription'}"><i class="fas fa-align-left"></i></li>
                                            <li class="createNewElement" data-type="3" title="{l s='2 column - image / text' mod='phdescription'}"><i class="far fa-image"></i> <i class="fas fa-align-left"></i></li>
                                            <li class="createNewElement" data-type="4" title="{l s='2 column - text / image' mod='phdescription'}"><i class="fas fa-align-left"></i> <i class="far fa-image"></i></li>
                                            <li class="createNewElement" data-type="5" title="{l s='2 column - image / image' mod='phdescription'}"><i class="far fa-image"></i> <i class="far fa-image"></i></li>
                                            <li class="createNewElement" data-type="6" title="{l s='2 column - text / text' mod='phdescription'}"><i class="fas fa-align-left"></i> <i class="fas fa-align-left"></i></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>

        </div>
        <script type="text/javascript">
            var addDescriptionUrl = '{$addDescriptionUrl}';
            var deleteDescription = '{$deleteDescription}';
            var changePositionUrl = '{$changePositionUrl}';
            var getDescription = '{$getDescriptionUrl}';
            var id_product = {$id_product};
            var id_shop = {$id_shop};
            var trans1 = '{l s='Are you sure?' mod='phdescription'}';
            var trans2 = '{l s='Loading...' mod='phdescription'}';
            var trans3 = '{l s='Uploading file ...' mod='phdescription'}';
            var trans4 = '{l s='Save product' mod='phdescription'}';
            var trans5 = '{l s='before end or refresh page!' mod='phdescription'}';
            var trans6 = '{l s='Edit product description in module Product Description' mod='phdescription'}';
            var trans7 = '{l s='click here' mod='phdescription'}';

            tinySetup({
                editor_selector: "autoload_rte",
                height: 200,
                paste_text_sticky: true,
                paste_text_sticky_default: true,
                paste_as_text: true,
                entity_encoding : 'raw',
                force_br_newlines : true,
                force_p_newlines : false,
                forced_root_block : false,
                setup: function (ed) {
                    ed.pasteAsPlainText = true;
                    ed.on('init', function (ed) {
                        tinyMCE.get(ed.target.id).show();
                        ed.pasteAsPlainText = true;
                    });
                    ed.on('blur', function (ed) {
                        tinyMCE.triggerSave();
                    });
                    ed.on('Paste', function (ed) {
                        clipboardData = ed.clipboardData || window.clipboardData;
                        pastedData = clipboardData.getData('text/html');

                        tinyMCE.triggerSave();
                    });
                    ed.on('keydown', function (ed, e) {
                        tinyMCE.triggerSave();
                        textarea = $('#' + tinymce.activeEditor.id);
                        var max = textarea.parent('div').find('span.counter').data('max');
                        if (max != 'none') {
                            count = tinyMCE.activeEditor.getBody().textContent.length;
                            rest = max - count;
                            if (rest < 0)
                                textarea.parent('div').find('span.counter').html('<span style="color:red;">Maximum ' + max + ' characters : ' + rest + '</span>');
                            else
                                textarea.parent('div').find('span.counter').html(' ');
                        }
                        if (ed.ctrlKey && ed.keyCode == 86 && ed.type == "keydown") {

                        }
                    });
                }
            });
        </script>

    </div>
    <div class="panel-footer">
        <a class="btn btn-default">
            <i class="process-icon-cancel"></i> Anuluj
        </a>
        <button class="btn btn-default pull-right" name="submitAddproduct" type="submit">
            <i class="process-icon-save"></i> Zapisz
        </button>
        <button class="btn btn-default pull-right" name="submitAddproductAndStay" type="submit">
            <i class="process-icon-save"></i> Zapisz i zostań
        </button>
    </div>
</div>
