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
<div class="clearfix panel">
    <h3>{l s='Add new font' mod='tinymcepro'}</h3>
    <div class="clearfix">
        <div class='col-md-4'>
            <div class='input-group'><span class='input-group-addon'>{l s='Name' mod='tinymcepro'}</span><input type="text" name="tpro_new_name"/></div>
        </div>
        <div class='col-md-4'>
            <div class='input-group'><span class='input-group-addon'>{l s='Definition' mod='tinymcepro'}</span><input type="text" name="tpro_new_definition"/></div>
        </div>
    </div>
    <div class="panel-footer">
        <button type="button" class="btn btn-default pull-right" id="tproAddNewButton">
            <i class="process-icon-plus"></i> {l s='add new font' mod='tinymcepro'}
        </button>
        <button type="button" class="btn btn-default pull-left" id="tproRestoreFonts">
            <i class="process-icon-refresh"></i> {l s='restore default fonts' mod='tinymcepro'}
        </button>

    </div>
</div>
<div id="tinymceproFontsDefaultDiv">

</div>

{literal}
<style>
    #tinymceproFontsDefault {
        display:none;
    }

    .tproFontDiv div {
        opacity:0.7;
    }

    .tproFontDiv {
        margin-bottom:2px;
    }

    .tproFontDiv:hover div {
        opacity:1.0;
        background: #fff5b5;
    }
</style>
<script>
    function recreateListOfFonts() {
        $('#tinymceproFontsDefaultDiv').html('');
        var tpro_fonts = $('#tinymceproFontsDefault').val();
        var tpro_fonts_array = tpro_fonts.split(';');
        $.each(tpro_fonts_array, function (index, value) {
            var tpro_fonts_array_font = value.split('=');
            $('#tinymceproFontsDefaultDiv').append("<div class='tproFontDiv tproFont" + index + " clearfix'><div class='col-md-4'><div class='input-group'><span class='input-group-addon'>{/literal}{l s='Name' mod='tinymcepro'}{literal}</span><input class='tname' type='text' data-previous='" + tpro_fonts_array_font[0] + "' value='" + tpro_fonts_array_font[0] + "'/></div></div><div class='col-md-4'><div class='input-group'><span class='input-group-addon'>{/literal}{l s='Definition' mod='tinymcepro'}{literal}</span><input class='tdef' data-previous='" + tpro_fonts_array_font[1] + "' type='text' value='" + tpro_fonts_array_font[1] + "'/></div></div><button type='button' class='btn btn-default tproDelete'><i class='icon-remove'></i></button></div>");
        });
        tinymceProBindFonts();
    }

    function tinymceProBindFonts() {
        $('.tproFontDiv input').off().keyup(function () {
            var tinymceproFontsDefaultVal = $('#tinymceproFontsDefault').val();
            $('#tinymceproFontsDefault').val(tinymceproFontsDefaultVal.replace($(this).data('previous'), $(this).val()));
            $(this).data('previous', $(this).val());
        });

        $('.tproFontDiv button').off().click(function () {
            var tdefinition = $(this).parent().find('.tname').val()+"="+$(this).parent().find('.tdef').val()+";";
            var tinymceproFontsDefaultVal = $('#tinymceproFontsDefault').val();
            var tnew = tinymceproFontsDefaultVal.replace(tdefinition, ' ');
            $('#tinymceproFontsDefault').val(tnew);
            $(this).parent().fadeOut("slow");
        });
    }

    $('#tproRestoreFonts').click(function(){
       $('#tinymceproFontsDefault').val('Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Open Sans=Open Sans,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats');
       recreateListOfFonts();
       showSuccessMessage('{/literal}{l s='OK!' mod='tinymcepro'}{literal}');
    });

    $('#tproAddNewButton').click(function () {
        var fontDefinition = $('input[name="tpro_new_name"]').val() + "=" + $('input[name="tpro_new_definition"]').val();
        $('#tinymceproFontsDefault').val(fontDefinition + ";" + $('#tinymceproFontsDefault').val());
        $('input[name="tpro_new_name"]').val('');
        $('input[name="tpro_new_definition"]').val('');
        showSuccessMessage('{/literal}{l s='OK!' mod='tinymcepro'}{literal}');
        recreateListOfFonts();
    });

    $(document).ready(function () {
        recreateListOfFonts();
    });
</script>
{/literal}