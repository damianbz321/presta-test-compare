/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * Email Templates Manager
 * version 1.6.1
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

$(function() {
    $('.emailTemplatesManager').click(function (e) {
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
    $('.emailTemplatesManager').click(function (e) {
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


function displayEtmTab(tab) {
    $('.etm_tab').hide();
    $('.etm_tab_page').removeClass('selected');
    $('#etm_' + tab).show();
    $('#etm_link_' + tab).addClass('selected');
}

function displayModulePageTabEtm(tab) {
    $('.module_page_tabEtm').hide();
    $('.tab-row.active').removeClass('active');
    $('#module_page_' + tab).show();
    $('#module_page_link_' + tab).parent().addClass('active');
}

function BindEtmScripts() {
    $('#table-etm .btn-group-action a.edit').off().click(function (e) {
        e.preventDefault();
        $.ajax({
            url: etm_module_url + $(this).attr('href'),
            cache: false,
            success: function (response) {
                $('#etm_manageTemplates').html(response);
                BindEtmScripts();
                runTinyMce();
            }
        });
    });

    $('#etm_button_templateSave').off().click(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: etm_module_url + $(this).attr('href'),
            data: $('#editTemplateForm').serialize(),
            cache: false,
            success: function (response) {
                showNoticeMessage('Templates saved with success');
            }
        });
    });

    $('#table-etm .btn-group-action a.delete').off().click(function (e) {
        clicked = $(this);
        e.preventDefault();
        if ($(this).attr('href').includes('name=reminder&') || $(this).attr('href').includes('name=reminder_status_change&')) {
            showErrorMessage('Default template cant be removed');
        } else {
            $.ajax({
                url: etm_module_url + $(this).attr('href'),
                cache: false,
                success: function (response) {
                    clicked.parents('tr').find('td').parent().hide();
                    showNoticeMessage('Template removed with success');
                    $.ajax({
                        url: etm_module_url,
                        data: 'refreshListOfTemplatesSelect=1',
                        cache: false,
                        success: function (response) {
                            $('.emailTemplateManager_selectBox').html(response);
                            showNoticeMessage('Select input form templates reloaded');
                        }
                    });
                }
            });
        }
    });

    $('.etm_button_createNew').off().click(function (e) {
        clicked = $(this);
        e.preventDefault();
        if ($('#etm_newname').val().length < 4) {
            showErrorMessage('Template must have at least 5 characters');
        } else {
            $.ajax({
                url: etm_module_url + $(this).attr('href'),
                data: 'name=' + $('#etm_newname').val(),
                cache: false,
                success: function (resp) {
                    showNoticeMessage('Template created with success');
                    $.ajax({
                        url: etm_module_url,
                        data: 'refreshListOfTemplates=1',
                        cache: false,
                        success: function (response) {
                            $('#etm_manageTemplates').html(response);
                            BindEtmScripts();
                            showNoticeMessage('List of templates reloaded');
                            $.ajax({
                                url: etm_module_url,
                                data: 'refreshListOfTemplatesSelect=1',
                                cache: false,
                                success: function (response) {
                                    $('.emailTemplateManager_selectBox').html(response);
                                    showNoticeMessage('Select input form templates reloaded');
                                }
                            });
                        }
                    });
                }
            });
        }
    });

    $('#etm_button_backToList').off().click(function (e) {
        e.preventDefault();
        $.ajax({
            url: etm_module_url,
            data: 'refreshListOfTemplates=1',
            cache: false,
            success: function (response) {
                $('#etm_manageTemplates').html(response);
                BindEtmScripts();
                showNoticeMessage('List of templates reloaded');
            }
        });
    });

    $('.etm_maximize_html, .etm_maximize_txt').click(function (e) {
        e.preventDefault();
        $('.etm_html_code').removeClass('col-lg-6 col-md-6 col-sm-12');
        $('.etm_html_code').addClass('col-lg-12 col-md-12 col-sm-12');
        $('.etm_txt_code').removeClass('col-lg-6 col-md-6 col-sm-12');
        $('.etm_txt_code').addClass('col-lg-12 col-md-12 col-sm-12');
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