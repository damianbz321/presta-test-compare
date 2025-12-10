<div class="form-create-box form-create-1 form-create-1-{$ki}">
    <div class="head">
        <ul>
            <li class="active"><i class="far fa-image"></i></li>
            <li><i class="fas fa-align-left"></i></li>
            <li><i class="far fa-image"></i> <i class="fas fa-align-left"></i></li>
            <li><i class="fas fa-align-left"></i> <i class="far fa-image"></i></li>
            <li><i class="far fa-image"></i> <i class="far fa-image"></i></li>
            <li><i class="fas fa-align-left"></i> <i class="fas fa-align-left"></i></li>
        </ul>
{*        <p class="alert alert-info">max. szerokość zdjęcia to 1080px</p>*}
    </div>
    <fieldset class="form-group">
        {foreach $languages as $key => $lang}
            <div class="row langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                <div class="col-md-12">
                    <label class="image-box" for="input_file-{$id_description}" data-idp="{$id_product}" data-type="1" data-idd="{$id_description}"><i class="fas fa-image"></i></label>
{*                    <label class="form-control-label">[{$lang.iso_code|strtoupper}] {l s='Insert a image' mod='phdescription'}:</label>*}
                    <input type="file" class="form-control input_file input_file-{$id_description}" name="phdescription[image_file]" id="input_file-{$id_description}" data-action="{$addFileUrl}" data-idd="{$id_description}" data-idp="{$id_product}" data-type="1" style="display: none;visibility: hidden;" />
                    <input type="hidden" name="phdescriptionE[{$ki}][image][{$lang.id_lang}]" class="imageValue" value=""/>
                    <small class="hint">(max. szerokość zdjęcia to 1080px)</small>
                </div>
            </div>
        {/foreach}
    </fieldset>
    <input type="hidden" name="phdescriptionE[{$ki}][type]" value="1" />
    <input type="hidden" name="phdescriptionE[{$ki}][id_description]" value="{$id_description}" />
    <input type="hidden" name="phdescriptionE[{$ki}][id_product]" value="{$id_product}" />
</div>
