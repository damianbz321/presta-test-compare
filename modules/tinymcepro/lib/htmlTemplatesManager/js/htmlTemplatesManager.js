/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * html Templates Manager
 * version 1.7.2
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

$(function() {
    $('.htmlTemplatesManager').click(function (e) {
        e.preventDefault();
        clicked = $(this);
        $.fancybox({
            'helpers': {
                media: true
            },
            'autoSize': false,
            'type': 'ajax',
            'showCloseButton': true,
            'enableEscapeButton': true,
            'href': clicked.attr('href'),
            'width': '95%',
            'height': '95%',
        });
    });
});

/*
$('document').ready(function () {
    $('.htmlTemplatesManager').click(function (e) {
        e.preventDefault();
        clicked = $(this);
        $.ajax({
            url: clicked.attr('href'),
            cache: false,
            success: function (response) {
                $.fancybox({
                    'type': 'html',
                    'showCloseButton': true,
                    'enableEscapeButton': true,
                    'href': clicked.attr('href'),
                    'content': response
                });
            }
        });
    });
});
*/


function displayptmTab(tab) {
    $('.ptm_tab').hide();
    $('.ptm_tab_page').removeClass('selected');
    $('#ptm_' + tab).show();
    $('#ptm_link_' + tab).addClass('selected');
}

function displayModulePageTabptm(tab) {
    $('.module_page_tabptm').hide();
    $('.tab-row.active').removeClass('active');
    $('#module_page_' + tab).show();
    $('#module_page_link_' + tab).parent().addClass('active');
}

function BindptmScripts() {
    $('#table-ptm .btn-group-action a.edit').off().click(function (e) {
        e.preventDefault();
        $.ajax({
            url: ptm_module_url + $(this).attr('href'),
            cache: false,
            success: function (response) {
                $('#ptm_manageTemplates').html(response);
                BindptmScripts();
                runTinyMce();
            }
        });
    });

    $('#ptm_button_templateSave').off().click(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: ptm_module_url + $(this).attr('href'),
            data: $('#editTemplateForm').serialize(),
            cache: false,
            success: function (response) {
                showNoticeMessage('Templates saved with success');
            }
        });
    });

    $('#table-ptm .btn-group-action a.delete').off().click(function (e) {
        clicked = $(this);
        e.preventDefault();
        var url_edit = $(this).parent().parent().parent().find('.edit').attr('href');
        var url_delete = url_edit.replace('updateconfiguration', 'deleteconfiguration');
        if (url_delete.includes('name=readmore-template&') || url_delete.includes('name=mypresta-example&')) {
            showErrorMessage('Default template cant be removed');
        } else {
            $.ajax({
                url: ptm_module_url + url_delete,
                cache: false,
                success: function (response) {
                    clicked.parents('tr').find('td').parent().hide();
                    showNoticeMessage('Template removed with success');
                    $.ajax({
                        url: ptm_module_url,
                        data: 'refreshListOfTemplateshtmlSelecthtml=1',
                        cache: false,
                        success: function (response) {
                            $('.htmlTemplateManager_selectBox').html(response);
                            showNoticeMessage('Select input form templates reloaded');
                        }
                    });
                }
            });
        }
    });

    $('.ptm_button_createNew').off().click(function (e) {
        clicked = $(this);
        e.preventDefault();
        if ($('#ptm_newname').val().length < 4) {
            showErrorMessage('Template must have at least 5 characters');
        } else {
            $.ajax({
                url: ptm_module_url + $(this).attr('href'),
                data: 'name=' + $('#ptm_newname').val(),
                cache: false,
                success: function (resp) {
                    showNoticeMessage('Template created with success');
                    $.ajax({
                        url: ptm_module_url,
                        data: 'refreshListOfTemplateshtml=1',
                        cache: false,
                        success: function (response) {
                            $('#ptm_manageTemplates').html(response);
                            BindptmScripts();
                            showNoticeMessage('List of templates reloaded');
                            $.ajax({
                                url: ptm_module_url,
                                data: 'refreshListOfTemplateshtmlSelecthtml=1',
                                cache: false,
                                success: function (response) {
                                    $('.htmlTemplateManager_selectBox').html(response);
                                    showNoticeMessage('Select input form templates reloaded');
                                }
                            });
                        }
                    });
                }
            });
        }
    });

    $('#ptm_button_backToList').off().click(function (e) {
        e.preventDefault();
        $.ajax({
            url: ptm_module_url,
            data: 'refreshListOfTemplateshtml=1',
            cache: false,
            success: function (response) {
                $('#ptm_manageTemplates').html(response);
                BindptmScripts();
                showNoticeMessage('List of templates reloaded');
            }
        });
    });

    $('.ptm_maximize_html, .ptm_maximize_txt').click(function (e) {
        e.preventDefault();
        $('.ptm_html_code').removeClass('col-lg-6 col-md-6 col-sm-12');
        $('.ptm_html_code').addClass('col-lg-12 col-md-12 col-sm-12');
        $('.ptm_txt_code').removeClass('col-lg-6 col-md-6 col-sm-12');
        $('.ptm_txt_code').addClass('col-lg-12 col-md-12 col-sm-12');
    });
}

function runTinyMce(){
    tinySetup({
        editor_selector: "rte",
        setup: function (ed) {
            ed.on('blur', function (ed) {
                tinyMCE.triggerSave();
            });
        }
    });
}