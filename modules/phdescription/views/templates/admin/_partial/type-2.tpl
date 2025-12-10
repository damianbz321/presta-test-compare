<div class="form-create-box form-create-2 form-create-2-{$ki}">
    <div class="head">
        <ul>
            <li ><i class="far fa-image"></i></li>
            <li class="active"><i class="fas fa-align-left"></i></li>
            <li><i class="far fa-image"></i> <i class="fas fa-align-left"></i></li>
            <li><i class="fas fa-align-left"></i> <i class="far fa-image"></i></li>
            <li><i class="far fa-image"></i> <i class="far fa-image"></i></li>
            <li><i class="fas fa-align-left"></i> <i class="fas fa-align-left"></i></li>
        </ul>
    </div>
    <fieldset class="form-group">
        <div class="row">
            {foreach $languages as $key => $lang}
                <div class="col-md-12 langBox lang-{$lang.iso_code}{if $key > 0} hidden{/if}">
                    <label class="form-control-label">[{$lang.iso_code|strtoupper}] {l s='Insert a text' mod='phdescription'}:</label>
                    <textarea name="phdescriptionE[{$ki}][text][{$lang.id_lang}]" class="form-control autoload_rte3-2-{$ki}" ></textarea>
                </div>
            {/foreach}
        </div>
    </fieldset>
    <input type="hidden" name="phdescriptionE[{$ki}][type]" value="2" />
    <input type="hidden" name="phdescriptionE[{$ki}][id_description]" value="{$id_description}" />
    <input type="hidden" name="phdescriptionE[{$ki}][id_product]" value="{$id_product}" />
</div>
