<div class="form-create-box form-create-6 form-create-6-{$ki} ">
    <div class="head">
        <ul>
            <li><i class="far fa-image"></i></li>
            <li><i class="fas fa-align-left"></i></li>
            <li><i class="far fa-image"></i> <i class="fas fa-align-left"></i></li>
            <li><i class="fas fa-align-left"></i> <i class="far fa-image"></i></li>
            <li><i class="far fa-image"></i> <i class="far fa-image"></i></li>
            <li class="active"><i class="fas fa-align-left"></i> <i class="fas fa-align-left"></i></li>
        </ul>
    </div>
    <fieldset class="form-group">
        <div class="row">
            <div class="col-md-6">
                {foreach $languages as $key => $lang}
                    <div class="langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                        <label class="form-control-label">[{$lang.iso_code|strtoupper}] {l s='Insert a text' mod='phdescription'}:</label>
                        <textarea name="phdescriptionE[{$ki}][text6][{$lang.id_lang}]" class="form-control autoload_rte3-6-{$ki}" ></textarea>
                    </div>
                {/foreach}
            </div>
            <div class="col-md-6">
                {foreach $languages as $key => $lang}
                    <div class="langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                        <label class="form-control-label">[{$lang.iso_code|strtoupper}] {l s='Insert a text' mod='phdescription'}:</label>
                        <textarea name="phdescriptionE[{$ki}][text7][{$lang.id_lang}]" class="form-control autoload_rte3-6-{$ki}" ></textarea>
                    </div>
                {/foreach}
            </div>
        </div>
    </fieldset>
    <input type="hidden" name="phdescriptionE[{$ki}][type]" value="6" />
    <input type="hidden" name="phdescriptionE[{$ki}][id_description]" value="{$id_description}" />
    <input type="hidden" name="phdescriptionE[{$ki}][id_product]" value="{$id_product}" />
</div>
