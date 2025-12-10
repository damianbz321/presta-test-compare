/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2023 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 *
 * version 1.0.0
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

$('document').ready(function () {
    $('.smartyTemplatesManager').click(function (e) {
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
    $('.smartyTemplatesManager').click(function (e) {
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

function displayModulePageTab(tab) {
    $('.module_page_tab').hide();
    $('.tab-row.active').removeClass('active');
    $('#module_page_' + tab).show();
    $('#module_page_link_' + tab).parent().addClass('active');
}

function BindEtmScripts() {
    $('#table-etm a.edit').off().click(function (e) {
        e.preventDefault();
        $.ajax({
            url: etm_module_url + $(this).attr('href'),
            cache: false,
            success: function (response) {
                $('#etm_manageTemplates').html(response);
                BindEtmScripts();
                ;
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

}