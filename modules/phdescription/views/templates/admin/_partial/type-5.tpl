<div class="form-create-box form-create-5 form-create-5-{$ki}">
    <div class="head">
        <ul>
            <li><i class="far fa-image"></i></li>
            <li><i class="fas fa-align-left"></i></li>
            <li><i class="far fa-image"></i> <i class="fas fa-align-left"></i></li>
            <li><i class="fas fa-align-left"></i> <i class="far fa-image"></i></li>
            <li class="active"><i class="far fa-image"></i> <i class="far fa-image"></i></li>
            <li><i class="fas fa-align-left"></i> <i class="fas fa-align-left"></i></li>
        </ul>
{*        <p class="alert alert-info">max. szerokość zdjęcia to 540px</p>*}
    </div>
    <fieldset class="form-group">
        <div class="row">
            <div class="col-md-6">
                {foreach $languages as $key => $lang}
                    <div class="langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                        <label class="image-box" for="input_file-{$id_description}a" data-idp="{$id_product}" data-type="5" data-idd="{$id_description}" data-ty="a"><i class="fas fa-image"></i></label>
{*                        <label class="form-control-label">[{$lang.iso_code|strtoupper}] {l s='Insert a image' mod='phdescription'}:</label>*}
{*                        <input type="file" class="form-control" name="phdescription[image_file5]" id="input_file" data-action="{$addFileUrl}" data-idp="{$id_product}" data-type="5" />*}
                        <input type="file" class="form-control input_file input_file-{$id_description}a" name="phdescription[image_file5]" id="input_file-{$id_description}a" data-ty="a" data-action="{$addFileUrl}" data-idp="{$id_product}" data-idd="{$id_description}" data-type="5" style="display: none;visibility: hidden;" />
                        <input type="hidden" name="phdescriptionE[{$ki}][image5][{$lang.id_lang}]" class="imageValue" value=""/>
                        <small class="hint">(max. szerokość zdjęcia to 540px)</small>
                    </div>
                {/foreach}
            </div>
            <div class="col-md-6">
                {foreach $languages as $key => $lang}
                    <div class="langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                        <label class="image-box" for="input_file-{$id_description}b" data-idp="{$id_product}" data-type="6" data-idd="{$id_description}" data-ty="b"><i class="fas fa-image"></i></label>
{*                        <label class="form-control-label">[{$lang.iso_code|strtoupper}] {l s='Insert a image' mod='phdescription'}:</label>*}
{*                        <input type="file" class="form-control" name="phdescription[image_file6]" id="input_file" data-action="{$addFileUrl}" data-idp="{$id_product}" data-type="6" />*}
                        <input type="file" class="form-control input_file input_file-{$id_description}b" name="phdescription[image_file6]" id="input_file-{$id_description}b" data-ty="b" data-action="{$addFileUrl}" data-idd="{$id_description}" data-idp="{$id_product}" data-type="6" style="display: none;visibility: hidden;" />
                        <input type="hidden" name="phdescriptionE[{$ki}][image6][{$lang.id_lang}]" class="imageValue" value=""/>
                        <small class="hint">(max. szerokość zdjęcia to 540px)</small>
                    </div>
                {/foreach}
            </div>
        </div>
    </fieldset>
    <input type="hidden" name="phdescriptionE[{$ki}][type]" value="5" />
    <input type="hidden" name="phdescriptionE[{$ki}][id_description]" value="{$id_description}" />
    <input type="hidden" name="phdescriptionE[{$ki}][id_product]" value="{$id_product}" />
</div>
