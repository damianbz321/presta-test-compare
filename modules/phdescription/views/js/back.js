$(document).ready(function() {

    if (typeof tinySetup != 'undefined') {
        tinySetup({
            editor_selector: "autoload_rte2",
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

                    // tinyMCE.execCommand('mceInsertContent', false, pastedData);
                    // tinymce.activeEditor.selection.setContent(pastedData);
                    // console.log(tinyMCE.activeEditor.setContent(pastedData));
                    tinyMCE.triggerSave();
                    // ed.dblClick();
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
                        // clipboardData = e.clipboardData || window.clipboardData;
                        // pastedData = clipboardData.getData('text/html');
                        // var content = tinyMCE.activeEditor.getData('Text');
                        // Let TinyMCE do the heavy lifting for inserting that content into the editor.
                        // ed.execCommand('mceInsertContent', false, content);
                        // tinymce.triggerSave(true, true);
                        // console.log($('.mce-edit-area iframe .mce-content-body').html())
                        // $('.mce-edit-area iframe').click();
                        // tinyMCE.activeEditor.focus();
                        // console.log('tiny ctrl + v - paste !');
                        // tinyMCE.activeEditor.getContainer().click();
                        // console.log(tinyMCE.activeEditor.getElement());
                        // console.log(tinyMCE.activeEditor.getContainer());
                        // console.log(tinyMCE.activeEditor.save());
                        // console.log(tinyMCE.activeEditor.getWin());
                    }
                });
            }
        });

        tinySetup({
            editor_selector: "autoload_rte3",
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

                    // tinyMCE.execCommand('mceInsertContent', false, pastedData);
                    // tinymce.activeEditor.selection.setContent(pastedData);
                    // console.log(tinyMCE.activeEditor.setContent(pastedData));
                    tinyMCE.triggerSave();
                    // ed.dblClick();
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
                        // clipboardData = e.clipboardData || window.clipboardData;
                        // pastedData = clipboardData.getData('text/html');
                        // var content = tinyMCE.activeEditor.getData('Text');
                        // Let TinyMCE do the heavy lifting for inserting that content into the editor.
                        // ed.execCommand('mceInsertContent', false, content);
                        // tinymce.triggerSave(true, true);
                        // console.log($('.mce-edit-area iframe .mce-content-body').html())
                        // $('.mce-edit-area iframe').click();
                        // tinyMCE.activeEditor.focus();
                        // console.log('tiny ctrl + v - paste !');
                        // tinyMCE.activeEditor.getContainer().click();
                        // console.log(tinyMCE.activeEditor.getElement());
                        // console.log(tinyMCE.activeEditor.getContainer());
                        // console.log(tinyMCE.activeEditor.save());
                        // console.log(tinyMCE.activeEditor.getWin());
                    }
                });
            }
        });
    }

    if ($('.summary-description-container').length == 1) {
        $('#form_step1_description').before('<p class="alert alert-info" style="margin: 15px;">'+trans6+' <span class="goToMOdule btn btn-info">'+trans7+'</span></p>');
    }

    $('.goToMOdule').on('click', function() {
        $('#tab_hooks a').click();
        setTimeout(function() {
            $('.modules-list-button[data-target=module-phdescription]').click();
        }, 500);
    });

    var start = 0;
    var end = -1;

    $( "#sortableBox" ).sortable({
        sort: function( event, ui ) {

        },
        start: function (event, ui) {
            start = ui.placeholder.index();
            end = -1;
        },
        change: function( event, ui ) {
            end = ui.placeholder.index();
        },
        stop: function( event, ui ) {
            var idp = ui.item.data('idp');
            var idd = ui.item.data('idd');
            $('.sortableBox').css({
                'opacity' : '0.5',
                'cursor': 'not-allowed'
            })
            $.ajax({
                type : "POST",
                url : changePositionUrl,
                data : 'id_product='+id_product+'&id_description='+idd+'&end='+end,
                dataType: 'json',
                async: false,
                cache: false,
                success:function(result){
                    if (result.status) {
                        $('.sortableItem').each(function(index, value) {
                            var item = $(value);
                            var current = $(this).data('position');
                            if (index != current) {
                                $(this).find('.position').text(index+1)
                            }
                        });
                        $('.sortableBox').css({
                            'opacity' : '1',
                            'cursor': 'pointer'
                        })
                    }
                    getDescriptionAjax();
                }
            });
        }
    });

    $(document).on('click', '.addNewElement', function() {
        $('.phdescription-admin-form-add').show();
        $("html, body").animate({
            scrollTop: $('.phdescription-admin-form-add').offset().top+100
        }, 1000);
    });

    $(document).on('click', '.createNewElement', function() {
        var element = $(this).data('type');$('.elementType').val();
        $('.phdescription-admin-form-add').hide();
        // $('.addNewElement').hide();
        // $('.phdescription-admin-body .form-create-box').each(function() {
        //     $(this).hide();
        // });
        $('.phdescription-admin-body .phdescription-admin-form-create').before('<p class="showLoading alert alert-info"> '+trans2+' </p>');
        $.ajax({
            type : "POST",
            url : addDescriptionUrl,
            data : 'id_product='+id_product+'&type='+element+'&id_shop='+id_shop,
            dataType: 'json',
            async: false,
            cache: false,
            success:function(result){
                if (result.id > 0) {
                    if ($('.showAlertInfo').length < 1) {
                        $('.phdescription-admin-body .phdescription-admin-form-create').before('<p class="showAlertInfo alert alert-warning"> UWAGA! Masz niezapisane elementy. Zapisz produkt aby nie stracić informacji </p>');
                    }

                    $('.phdescription-admin-body .phdescription-admin-form-create').append(result.tpl);
                    tinymce_start(element, result.ki);
                    // $('.phdescription-admin-header .phdescription-admin-form-add').hide();
                    $('.phtype').val(element);
                    $('.ph_id_description').val(result.id);
                    $('#form').attr('enctype', 'multipart/form-data');
                    var p = $('.form-create-'+element).find('p').html();
                    $('.phdescription-admin-body .form-create-'+element).find('p.head').html('[ID: '+result.id+'] '+p);
                    $('.phdescription-admin-body .form-create-'+element).find('input[type=file]').attr('data-idd', result.id);
                    $('.phdescription-admin-body .form-create-'+element).slideDown();
                    $('.showLoading').remove();
                    $('.phdescription-admin-body .phdescription-admin-form-create').show();
                    // $("html, body").animate({
                    //     scrollTop: $('.phdescription-admin-form-add').offset().top+100
                    // }, 1000);
                    $("html, body").animate({
                        scrollTop: $('.phdescription-admin-body .form-create-'+element+'-'+result.ki).offset().top+100
                    }, 1000);
                }
            },
            error:function(error){

            }
        });
    });

    $(document).on('click', '.deleteElement', function() {
        if (confirm(trans1)) {
            var id_description = $(this).data('idd'),
                obj = $(this);
            $.ajax({
                type: "POST",
                url: deleteDescription,
                data: 'id_description=' + id_description,
                dataType: 'json',
                async: false,
                cache: false,
                success: function (result) {
                    if (result.status) {
                        obj.closest('.element').remove();
                        // add change position

                        // remove header bar
                        var length = 0;
                        length = $('.sortableItem').length;
                        if (length == 0) {
                            $('.phdescription-admin-footer .item.header').hide();
                        }
                    }
                },
                error: function (error) {

                }
            });
        }
    });

    $('body').on('change', '.input_file', function(){
        var file = this.files[0],
            formData = new FormData(),
            link = $(this).data('action'),
            id_product = $(this).data('idp'),
            id_description2 = $(this).data('idd'),
            id_description = $(this).attr('data-idd'),
            type = $(this).data('type'),
            element2 = $(this),
            element = $('.input_file-'+id_description);

        if (type == 5) {
            var ty = $(this).data('ty');
            element = $('.input_file-'+id_description+ty);
        }
        if (type == 6) {
            var ty = $(this).data('ty');
            element = $('.input_file-'+id_description+ty);
        }

        formData.append('uploadFiles', '1');
        formData.append('file', file);
        formData.append('id_product', id_product);
        formData.append('type', type);
        formData.append('id_description', id_description);
        doUpload(formData, link, element, id_product, id_description);
    });

    var doUpload = function($file, $link, $element, $idp, $id_description)
    {
        $element.attr('disabled', 'disabled');
        $element.after('<p class="showLoadingFile alert alert-info"> '+trans3+' </p>');
        $.ajax({
            type:"POST",
            url:$link,
            data:$file,
            contentType: false,
            processData:false,
            success:function(result){
                var data = $.parseJSON(result);
                $('.showLoadingFile').remove();
                if (data.success) {
                    $element.next().val(data.filepath);
                    // $element.parent().hide();
                    $element.parent().after('<div class="col-md-12"><p class="alert alert-info"><b>'+trans4+'</b> '+trans5+'</p></div>');
                    if ($element.closest('.langBox').find('img').length > 0) {
                        $element.closest('.langBox').find('img').attr('src', data.filepath);
                    } else {
                        $element.closest('.langBox').append('<div class="col-md-12"><img src="'+data.filepath+'" alt="" /></div>');
                    }
                    $('.product-footer input#submit').addClass('btnAnimated');
                } else if(data.error){
                    alert(data.error);
                }
            },
            error:function(error){

            }
        });
    }

    $(document).on('change', '#form_switch_language', function() {
        var lang = $(this).val();
        $('.langBox').each(function() {
            $(this).hide();
        });
        $('.langBox.lang-'+lang).show();
    });

    $(document).on('click', '.editBtn', function () {
        var type = $(this).attr('data-type');
        if (type == 'hide') {
            $(this).closest('.sortableItem').find('.editElement').show();
            $(this).attr('data-type', 'show');
            $(this).text('Zamknij');
        } else {
            $(this).closest('.sortableItem').find('.editElement').hide();
            $(this).attr('data-type', 'hide');
            $(this).text('Edytuj');
        }
    });

    var xclick = 0;
    var show = 0;
    $(document).on('click', '.product-footer input#submit', function() {
        xclick = 0;
        show = 0;
        $('.phdescription-admin-form-create').click();
        $(this).removeClass('btnAnimated');
        $('.phdescription-admin-footer').css('opacity', '0.5')
        $('.phdescription-admin-body').css('opacity', '0.5')
        $('.showAlertInfo').remove();
        $( document ).ajaxComplete(function() {
            if (xclick == 0) {
                // console.log("Triggered ajaxComplete handler.")
                xclick = 1;
            }
        });
        setInterval(function() {
            if (xclick == 1 && show == 0) {
                saveproduct();
            }
        }, 500);
    })

    function saveproduct()
    {
        show = 1;
        // console.log('save products now!');
        $.ajax({
            type: "POST",
            url: getDescription,
            data: 'id_product=' + id_product,
            dataType: 'json',
            async: false,
            cache: false,
            success: function (result) {
                $('.addNewElement').show();
                if ($('#sortableBox').length < 1) {
                    $('.phdescription-admin-footer').append('<div id="sortableBox"></div>');
                }
                if (result.counts == 1) {
                    // $('.phdescription-admin-footer').before(result.head);
                    $('#sortableBox').html(result.tpl)
                } else {
                    $('#sortableBox').empty().html(result.tpl)
                }
                xclick = 0;
                tinySetup({
                    editor_selector :"autoload_rte2",
                    height: 400,
                    paste_as_text: true,
                    setup : function(ed) {
                        ed.on('init', function(ed)
                        {
                            tinyMCE.get(ed.target.id).show();
                            ed.pasteAsPlainText = true;
                        });
                        ed.on('blur', function(ed) {
                            tinyMCE.triggerSave();
                        });
                        ed.on('Paste', function(ed)
                        {
                            clipboardData = ed.clipboardData || window.clipboardData;
                            pastedData = clipboardData.getData('text/html');

                            // tinyMCE.execCommand('mceInsertContent', false, pastedData);
                            // tinymce.activeEditor.selection.setContent(pastedData);
                            // console.log(tinyMCE.activeEditor.setContent(pastedData));
                            tinyMCE.triggerSave();
                        });
                        ed.on('keydown', function(ed, e) {
                            tinyMCE.triggerSave();
                            textarea = $('#'+tinymce.activeEditor.id);
                            var max = textarea.parent('div').find('span.counter').data('max');
                            if (max != 'none')
                            {
                                count = tinyMCE.activeEditor.getBody().textContent.length;
                                rest = max - count;
                                if (rest < 0)
                                    textarea.parent('div').find('span.counter').html('<span style="color:red;">Maximum '+ max +' characters : '+rest+'</span>');
                                else
                                    textarea.parent('div').find('span.counter').html(' ');
                            }
                        });
                    },
                    handleEvent : function(e) {
                        // Force paste dialog if non IE browser
                        if (!tinyMCE.isRealIE && tinyMCE.getParam("paste_auto_cleanup_on_paste", false) && e.ctrlKey && e.keyCode == 86 && e.type == "keydown") {
                            console.log('riny ctrl + v')
                        }

                        return true;
                    }
                });
                $('.phdescription-admin-footer').css('opacity', '1');
                $('.phdescription-admin-body').css('opacity', '1')
                $('.phdescription-admin-form-create .form-create-box').hide();
                $('.phdescription-admin-form-create .form-create-box #input_file').removeAttr('disabled');
                $('.phdescription-admin-body .phdescription-admin-form-create').empty();
                $('.phdescription-admin-form-create .alert').remove();
                setTimeout(function() {
                    $('.phdescription-admin-footer .item.header').show();
                }, 500);

                $( "#sortableBox" ).sortable({
                    sort: function( event, ui ) {

                    },
                    start: function (event, ui) {
                        start = ui.placeholder.index();
                        end = -1;
                    },
                    change: function( event, ui ) {
                        end = ui.placeholder.index();
                    },
                    stop: function( event, ui ) {
                        var idp = ui.item.data('idp');
                        var idd = ui.item.data('idd');
                        $('.sortableBox').css({
                            'opacity' : '0.5',
                            'cursor': 'not-allowed'
                        })
                        $.ajax({
                            type : "POST",
                            url : changePositionUrl,
                            data : 'id_product='+id_product+'&id_description='+idd+'&end='+end,
                            dataType: 'json',
                            async: false,
                            cache: false,
                            success:function(result){
                                if (result.status) {
                                    $('.sortableItem').each(function(index, value) {
                                        var item = $(value);
                                        var current = $(this).data('position');
                                        if (index != current) {
                                            $(this).find('.position').text(index+1)
                                        }
                                    });
                                    $('.sortableBox').css({
                                        'opacity' : '1',
                                        'cursor': 'pointer'
                                    })
                                }
                                getDescriptionAjax();
                            }
                        });
                    }
                });
            },
            error: function (error) {
                xclick = 0;
            },
            complete: function () {
                xclick = 0;
            }
        })
    }

    var oldXHR = window.XMLHttpRequest;

    function newXHR() {
        var realXHR = new oldXHR();
        realXHR.addEventListener("readystatechange", function() {
            var url = realXHR.responseURL;
            if (url.indexOf('sell/catalog/products/'+id_product+'?')) {
                if (url.indexOf('#tab-hooks')) {
                    if (realXHR.readyState == 4) {
                        if ($('#tab_hooks a.active').length > 0) {
                            console.log(xclick);
                            if ($('#module_phdescription').is(':visible') && xclick == 1) {
                                getDescriptionAjax();
                            }
                        }
                    }
                }
            }
            /*if(realXHR.readyState==1){
                alert('server connection established');
            }
            if(realXHR.readyState==2){
                alert('request received');
            }
            if(realXHR.readyState==3){
                alert('processing request');
            }*/
        }, false);
        return realXHR;
    }

    function getDescriptionAjax()
    {
        console.log('getDescriptionAjax')
        setTimeout(function() {
            $.ajax({
                type: "POST",
                url: getDescription,
                data: 'id_product=' + id_product,
                dataType: 'json',
                async: false,
                cache: false,
                success: function (result) {
                    getDescriptionTpl(result.tpl);
                },
                error: function (error) {
                    xclick = 0;
                },
                complete: function () {
                    xclick = 0;
                }
            })
        }, 500)
    }

    function getDescriptionTpl(tpl)
    {
        $('#sortableBox').empty().html(tpl)
        xclick = 0;
        tinySetup({
            height: 400,
            editor_selector :"autoload_rte2",
            paste_as_text: true,
            setup : function(ed) {
                ed.on('init', function(ed) {
                    tinyMCE.get(ed.target.id).show();
                    ed.pasteAsPlainText = true;
                });
                ed.on('blur', function(ed) {
                    tinyMCE.triggerSave();
                });
                ed.on('Paste', function(ed)
                {
                    clipboardData = ed.clipboardData || window.clipboardData;
                    pastedData = clipboardData.getData('text/html');

                    // tinyMCE.execCommand('mceInsertContent', false, pastedData);
                    // tinymce.activeEditor.selection.setContent(pastedData);
                    // console.log(tinyMCE.activeEditor.setContent(pastedData));
                    tinyMCE.triggerSave();
                });
                ed.on('keydown', function(ed, e) {
                    tinyMCE.triggerSave();
                    textarea = $('#'+tinymce.activeEditor.id);
                    var max = textarea.parent('div').find('span.counter').data('max');
                    if (max != 'none') {
                        count = tinyMCE.activeEditor.getBody().textContent.length;
                        rest = max - count;
                        if (rest < 0)
                            textarea.parent('div').find('span.counter').html('<span style="color:red;">Maximum '+ max +' characters : '+rest+'</span>');
                        else
                            textarea.parent('div').find('span.counter').html(' ');
                    }
                });
            }
        });
    }

});

function tinymce_start(id, ki)
{
    tinySetup({
        editor_selector :"autoload_rte3-"+id+"-"+ki,
        height: 400,
        paste_as_text: true,
        entity_encoding : 'raw',
        force_br_newlines : true,
        force_p_newlines : false,
        forced_root_block : false,
        setup : function(ed) {
            ed.on('init', function(ed)
            {
                tinyMCE.get(ed.target.id).show();
                ed.pasteAsPlainText = true;
            });
            ed.on('blur', function(ed) {
                tinyMCE.triggerSave();
            });
            ed.on('Paste', function(ed)
            {
                clipboardData = ed.clipboardData || window.clipboardData;
                pastedData = clipboardData.getData('text/html');

                // tinyMCE.execCommand('mceInsertContent', false, pastedData);
                // tinymce.activeEditor.selection.setContent(pastedData);
                // console.log(tinyMCE.activeEditor.setContent(pastedData));
                tinyMCE.triggerSave();
            });
            ed.on('keydown', function(ed, e) {
                tinyMCE.triggerSave();
                textarea = $('#'+tinymce.activeEditor.id);
                var max = textarea.parent('div').find('span.counter').data('max');
                if (max != 'none')
                {
                    count = tinyMCE.activeEditor.getBody().textContent.length;
                    rest = max - count;
                    if (rest < 0)
                        textarea.parent('div').find('span.counter').html('<span style="color:red;">Maximum '+ max +' characters : '+rest+'</span>');
                    else
                        textarea.parent('div').find('span.counter').html(' ');
                }
            });
        },
        handleEvent : function(e) {
            // Force paste dialog if non IE browser
            if (!tinyMCE.isRealIE && tinyMCE.getParam("paste_auto_cleanup_on_paste", false) && e.ctrlKey && e.keyCode == 86 && e.type == "keydown") {
                console.log('riny ctrl + v')
            }

            return true;
        }
    });
}
