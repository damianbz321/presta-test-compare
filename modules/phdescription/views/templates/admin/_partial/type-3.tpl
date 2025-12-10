<div class="form-create-box form-create-3 form-create-3-{$ki}">
    <div class="head">
        <ul>
            <li><i class="far fa-image"></i></li>
            <li><i class="fas fa-align-left"></i></li>
            <li class="active"><i class="far fa-image"></i> <i class="fas fa-align-left"></i></li>
            <li><i class="fas fa-align-left"></i> <i class="far fa-image"></i></li>
            <li><i class="far fa-image"></i> <i class="far fa-image"></i></li>
            <li><i class="fas fa-align-left"></i> <i class="fas fa-align-left"></i></li>
        </ul>
{*        <p class="alert alert-info">max. szerokość zdjęcia to 540px</p>*}
    </div>
    <fieldset class="form-group">
        <div class="row">
            <div class="col-md-6">
                {foreach $languages as $key => $lang}
                    <div class="langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                        <label class="image-box" for="input_file-{$id_description}" data-idp="{$id_product}" data-type="1" data-idd="{$id_description}"><i class="fas fa-image"></i></label>
{*                        <label class="form-control-label">[{$lang.iso_code|strtoupper}] {l s='Insert a image' mod='phdescription'}:</label>*}
                        <input type="file" class="form-control input_file input_file-{$id_description}" name="phdescription[image_file3]" id="input_file-{$id_description}" data-action="{$addFileUrl}" data-idp="{$id_product}" data-idd="{$id_description}" data-type="3" style="display: none;visibility: hidden;" />
                        <input type="hidden" name="phdescriptionE[{$ki}][image3][{$lang.id_lang}]" class="imageValue" value=""/>
                        <small class="hint">(max. szerokość zdjęcia to 540px)</small>
                    </div>
                {/foreach}
            </div>
            <div class="col-md-6">
                {foreach $languages as $key => $lang}
                    <div class="langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                        <label class="form-control-label">[{$lang.iso_code|strtoupper}] {l s='Insert a text' mod='phdescription'}:</label>
                        <textarea name="phdescriptionE[{$ki}][text3][{$lang.id_lang}]" class="form-control autoload_rte3-3-{$ki}" ></textarea>
                    </div>
                {/foreach}
            </div>
        </div>
    </fieldset>
    <input type="hidden" name="phdescriptionE[{$ki}][type]" value="3" />
    <input type="hidden" name="phdescriptionE[{$ki}][id_description]" value="{$id_description}" />
    <input type="hidden" name="phdescriptionE[{$ki}][id_product]" value="{$id_product}" />
</div>
