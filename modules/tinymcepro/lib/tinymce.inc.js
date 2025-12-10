/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 *
 *
 * NEW TINYMCE FILE !
 *
 */

var tinyMceLangLoaded = false;

function UrlExistsTinyMcePro(url) {
    var http = new XMLHttpRequest();
    http.open('HEAD', url, false);
    http.send();
    return http.status != 404;
}

$(document).ready(function () {
    if (window.location.href.indexOf("AdminTranslations") > -1) {
        if (typeof tinyMCE === 'undefined') {
            setTimeout(function () {
                $('.mails_field').off();
                run_new_mail_editor();
            }, 100);
            return;
        }
    }
});


function run_new_mail_editor() {
    $('.mails_field').on('shown.bs.collapse', function () {
        var active_email = $(this).find('.email-collapse.in');
        var frame = active_email.find('.email-html-frame');
        var src = frame.data('email-src');
        var rte_mail_selector = active_email.find('textarea.rte-mail').data('rte');

        var rte_mail_config = {};
        rte_mail_config['editor_selector'] = 'rte-mail-' + rte_mail_selector;
        $('#translation_mails-control-actions').appendTo($(this).find('.panel-collapse.in'));

        if (frame.find('iframe.email-frame').length == 0) {
            frame.append('<iframe class="email-frame" />');
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                dataType: 'html',
                data: {
                    getEmailHTML: true,
                    email: src,
                    token: window.token
                },
                success: function (result) {
                    var doc = frame.find('iframe')[0].contentWindow.document;
                    doc.open();
                    doc.write(result);
                    doc.close();
                    tinySetup(rte_mail_config);
                }
            });

        }
    });
}

function tinySetup(config) {
    if (typeof tinyMCE === 'undefined') {
        setTimeout(function () {
            tinySetup(config);
        }, 100);
        return;
    }

    if (!config) {
        config = {};
    }

    if (typeof config.editor_selector != 'undefined') {
        config.selector = '.' + config.editor_selector;
    }


    var cssId = 'cssIdFontAwesomePro';  // you could encode the css path itself to generate id..
    if (!document.getElementById('cssIdFontAwesomePro')) {
        var head = document.getElementsByTagName('head')[0];
        var link = document.createElement('link');
        link.id = cssId;
        link.rel = 'stylesheet';
        link.type = 'text/css';
        link.href = '//netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css';
        link.media = 'all';
        head.appendChild(link);
    }

    var path_array = baseAdminDir.split('/');
    path_array.splice((path_array.length - 2), 2);
    var final_path = path_array.join('/');
    window.tinyMCEPreInit = {};
    window.tinyMCEPreInit.base = final_path + '/js/tiny_mce';
    window.tinyMCEPreInit.suffix = '.min';
    var head = document.getElementsByTagName('head')[0];
    var link = document.createElement('link');
    link.id = cssId;
    link.rel = 'stylesheet';
    link.type = 'text/css';
    link.href = final_path + '/modules/tinymcepro/lib/iconset.css';
    link.media = 'all';
    head.appendChild(link);

    if (typeof config.editor_selector != 'undefined')
        config.selector = '.' + config.editor_selector;

    default_config = {
        editor_selector: ".rte",
        selector: ".rte",
        content_style: tinymcepro_contentstyle,
        content_css: final_path + '/modules/tinymcepro/lib/editor.css',
        plugins: tinymcepro_autoresize + tinymcepro_adv_bootstrap +" lineheight, lists, paragraph, template, toc, bootstraplite, addiframe, imagetools, ecpicker, bootstrapaccordion, editattributes, bs_alert, bs_images, powerpaste, advlist, smileys, youtube, fullscreen, fontawesome, visualblocks, preview searchreplace print insertdatetime, hr charmap anchor code link image pagebreak table " + tinymcepro_contextmenu + " filemanager table code media textcolor editattributes",
        toolbar1: tinymcepro_templates + " newdocument,print,|,undo,redo,|,lineheightselect,|,styleselect,|,formatselect,|,fontselect,|,fontsizeselect,|,editattributes,|,toc",
        toolbar2: "cut,copy,paste,searchreplace,|,bold,italic,underline,strikethrough,superscript,subscript,|,forecolor,ecpicker,backcolor,|,bullist,numlist,alignleft,aligncenter,alignright,alignjustify,outdent,indent,blockquote,|,removeformat,|,link,unlink,anchor,",
        toolbar3: "bootstraplite,bs_alert,bootstrapaccordion,|,"+ tinymcepro_adv_bootstrap_toolbar,
        toolbar4: "code,preview,visualblocks,fullscreen,|,table,|,youtube,|,fontawesome,|,image,bs_images,|,addiframe,media,smileys,charmap,hr,inserttime,",
        browser_spellcheck: true,
        templates: JSON.parse(tinymcepro_template_files),
        image_advtab: true,
        table_row_advtab: true,
        external_filemanager_path: baseAdminDir + "filemanager/",
        filemanager_title: "File manager",
        external_plugins: {"filemanager": baseAdminDir + "filemanager/plugin.min.js"},
        contextmenu: "paragraph link image inserttable | cell row column deletetable",
        language: iso_user,
        skin: "prestashop",
        statusbar: false,
        relative_urls: false,
        convert_urls: false,
        font_formats: tinymcepro_fonts,
        fontsize_formats: tinymcepro_sizes,
        lineheight_formats: tinymcepro_lineheight,
        table_class_list: tinymceproTableClassesArray,
        allow_script_urls: true,
        entity_encoding: "raw",
        link_title: true,
        verify_html: false,
        forced_root_block: false,
        force_br_newlines: tinymcepro_force_br_newlines,
        convert_newlines_to_brs: tinymcepro_newlines_to_brs,
        force_p_newlines: tinymcepro_force_p_newlines,
        height: tinymceproHeight,
        autoresize_max_height: 999999,
        tinyplusInclude: {
            framework: "b3",
            includeCssToGlobalDoc: false
        },
        menu: {
            edit: {title: 'Edit', items: 'undo redo | cut copy paste | selectall'},
            insert: {title: 'Insert element', items: 'charmap fontawesome image media emoticons hr inserttime link anchor | pagebreak'},
            view: {title: 'View', items: 'visualaid'},
            format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
            table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'}
        },
        valid_children: "+body[meta|style|script|iframe|section|link],+pre[iframe|section|script|div|p|br|span|img|style|h1|h2|h3|h4|h5|link],+div[meta|video|source],+video[source],*[*],+label[embed|sub|sup|textarea|strong|strike|small|em|form|frame|iframe|input|select|legend|button|div|img|h1|h2|h3|h4|h5|h6|h7|span|p|section|pre|b|u|i|a|ol|ul|li|table|td|tr|th|tbody|thead]",
        valid_elements: '+*[*],a[accesskey|charset|class|coords|dir<ltr?rtl|href|hreflang|id|lang|name'
            + '|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup'
            + '|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rel|rev'
            + '|shape<circle?default?poly?rect|style|tabindex|title|target|type],'
            + 'abbr[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'acronym[class|dir<ltr?rtl|id|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'address[class|align|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown'
            + '|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover'
            + '|onmouseup|style|title],'
            + 'applet[align<bottom?left?middle?right?top|alt|archive|class|code|codebase'
            + '|height|hspace|id|name|object|style|title|vspace|width],'
            + 'area[accesskey|alt|class|coords|dir<ltr?rtl|href|id|lang|nohref<nohref'
            + '|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup'
            + '|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup'
            + '|shape<circle?default?poly?rect|style|tabindex|title|target],'
            + 'article[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'aside[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'audio[autoplay|class|controls|dir<ltr?rtl|id|lang|loop|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|preload|src|style'
            + '|title],'
            + 'base[href|target],'
            + 'basefont[color|face|id|size],'
            + 'bdo[class|dir<ltr?rtl|id|lang|style|title],'
            + 'big[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'blockquote[dir|style|cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick'
            + '|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout'
            + '|onmouseover|onmouseup|style|title],'
            + 'body[alink|background|bgcolor|class|dir<ltr?rtl|id|lang|link|onclick'
            + '|ondblclick|onkeydown|onkeypress|onkeyup|onload|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|onunload|style|title|text|vlink],'
            + 'br[class|clear<all?left?none?right|id|style|title],'
            + 'button[accesskey|class|dir<ltr?rtl|disabled<disabled|id|lang|name|onblur'
            + '|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown'
            + '|onmousemove|onmouseout|onmouseover|onmouseup|style|tabindex|title|type'
            + '|value],'
            + 'canvas[class|dir<ltr?rtl|height|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title|width],'
            + 'caption[align<bottom?left?right?top|class|dir<ltr?rtl|id|lang|onclick'
            + '|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|style|title],'
            + 'center[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'cite[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'code[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'col[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id'
            + '|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown'
            + '|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title'
            + '|valign<baseline?bottom?middle?top|width],'
            + 'colgroup[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl'
            + '|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown'
            + '|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title'
            + '|valign<baseline?bottom?middle?top|width],'
            + 'command[class|dir<ltr?rtl|disabled|icon|id|label|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title|type],'
            + 'datalist[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'dd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup'
            + '|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],'
            + 'del[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown'
            + '|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover'
            + '|onmouseup|style|title],'
            + 'details[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|open|style'
            + '|title],'
            + 'dfn[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'dir[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown'
            + '|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover'
            + '|onmouseup|style|title],'
            + 'div[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick'
            + '|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|style|title],'
            + 'dl[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown'
            + '|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover'
            + '|onmouseup|style|title],'
            + 'dt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup'
            + '|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],'
            + 'em/i[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'embed[class|dir<ltr?rtl|height|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|src|style'
            + '|title|type|width],'
            + 'fieldset[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'figcaption[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'figure[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'footer[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'font[class|color|dir<ltr?rtl|face|id|lang|size|style|title],'
            + 'form[accept|accept-charset|action|class|dir<ltr?rtl|enctype|id|lang'
            + '|method<get?post|name|onclick|ondblclick|onkeydown|onkeypress|onkeyup'
            + '|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onreset|onsubmit'
            + '|style|title|target],'
            + 'frame[class|frameborder|id|longdesc|marginheight|marginwidth|name'
            + '|noresize<noresize|scrolling<auto?no?yes|src|style|title],'
            + 'frameset[class|cols|id|onload|onunload|rows|style|title],'
            + 'h1[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick'
            + '|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|style|title],'
            + 'h2[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick'
            + '|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|style|title],'
            + 'h3[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick'
            + '|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|style|title],'
            + 'h4[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick'
            + '|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|style|title],'
            + 'h5[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick'
            + '|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|style|title],'
            + 'h6[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick'
            + '|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|style|title],'
            + 'head[dir<ltr?rtl|lang|profile],'
            + 'header[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'hgroup[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'hr[align<center?left?right|class|dir<ltr?rtl|id|lang|noshade<noshade|onclick'
            + '|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|size|style|title|width],'
            + 'html[dir<ltr?rtl|lang|version],'
            + 'iframe[align<bottom?left?middle?right?top|class|frameborder|allowfullscreen|allowFullScreen|height|id'
            + '|longdesc|marginheight|marginwidth|name|scrolling<auto?no?yes|src|style'
            + '|title|width],'
            + 'img[align<bottom?left?middle?right?top|alt|border|class|dir<ltr?rtl|height'
            + '|hspace|id|ismap<ismap|lang|longdesc|name|onclick|ondblclick|onkeydown'
            + '|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover'
            + '|onmouseup|src|style|title|usemap|vspace|width],'
            + 'input[accept|accesskey|align<bottom?left?middle?right?top|alt|autocomplete|autofocus'
            + '|checked<checked|class|dir<ltr?rtl|disabled<disabled|form|id|ismap<ismap|lang|list'
            + '|max|maxlength|min|name|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect'
            + '|pattern|placeholder|readonly<readonly|required<required|size|src|style|tabindex|title'
            + '|type<button?checkbox?file?hidden?image?password?radio?reset?submit?text'
            + '?datetime?datetime-local?date?month?time?week?number?range?email?url?search?tel?color'
            + '|usemap|value],'
            + 'ins[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown'
            + '|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover'
            + '|onmouseup|style|title],'
            + 'isindex[class|dir<ltr?rtl|id|lang|prompt|style|title],'
            + 'kbd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'keygen[autofocus|challenge|class|dir<ltr?rtl|disabled<disabled|form|id|keytype|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'label[accesskey|class|dir<ltr?rtl|for|id|lang|onblur|onclick|ondblclick'
            + '|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout'
            + '|onmouseover|onmouseup|style|title],'
            + 'legend[align<bottom?left?right?top|accesskey|class|dir<ltr?rtl|id|lang'
            + '|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|style|title],'
            + 'li[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup'
            + '|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title|type'
            + '|value],'
            + 'link[charset|class|dir<ltr?rtl|href|hreflang|id|lang|media|onclick'
            + '|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|rel|rev|style|title|target|type],'
            + 'map[class|dir<ltr?rtl|id|lang|name|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'mark[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'menu[class|compact<compact|dir<ltr?rtl|id|label|lang|onclick|ondblclick|onkeydown'
            + '|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover'
            + '|onmouseup|style|title|type],'
            + 'meta[content|dir<ltr?rtl|http-equiv|lang|name|scheme],'
            + 'meter[class|dir<ltr?rtl|high|id|lang|low|max|min|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|optimum|style'
            + '|title|value],'
            + 'nav[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'noframes[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'noscript[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'object[align<bottom?left?middle?right?top|archive|border|class|classid'
            + '|codebase|codetype|data|declare|dir<ltr?rtl|height|hspace|id|lang|name'
            + '|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|standby|style|tabindex|title|type|usemap'
            + '|vspace|width],'
            + 'ol[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown'
            + '|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover'
            + '|onmouseup|start|style|title|type],'
            + 'optgroup[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick'
            + '|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|style|title],'
            + 'option[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick|ondblclick'
            + '|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout'
            + '|onmouseover|onmouseup|selected<selected|style|title|value],'
            + 'output[class|dir<ltr?rtl|for|form|id|lang|name|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'p[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick'
            + '|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|style|title],'
            + 'param[id|name|type|value|valuetype<DATA?OBJECT?REF],'
            + 'pre/listing/plaintext/xmp[align|class|dir<ltr?rtl|id|lang|onclick|ondblclick'
            + '|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout'
            + '|onmouseover|onmouseup|style|title|width],'
            + 'progress[class|dir<ltr?rtl|id|lang|max|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title|value],'
            + 'q[cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'rp[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'rt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'ruby[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 's[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup'
            + '|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],'
            + 'samp[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'script[charset|defer|language|src|type],'
            + 'section[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'select[class|dir<ltr?rtl|disabled<disabled|id|lang|multiple<multiple|name'
            + '|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup'
            + '|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|size|style'
            + '|tabindex|title],'
            + 'small[class|dir<ltr?rtl|id|lang|media|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'source[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|src|style'
            + '|title|type],'
            + 'span[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown'
            + '|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover'
            + '|onmouseup|style|title],'
            + 'strike[class|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown'
            + '|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover'
            + '|onmouseup|style|title],'
            + 'strong/b[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'style[dir<ltr?rtl|lang|media|title|type],'
            + 'sub[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'summary[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|open|style'
            + '|title],'
            + 'sup[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title],'
            + 'table[align<center?left?right|bgcolor|border|cellpadding|cellspacing|class'
            + '|dir<ltr?rtl|frame|height|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rules'
            + '|style|summary|title|width],'
            + 'tbody[align<center?char?justify?left?right|char|class|charoff|dir<ltr?rtl|id'
            + '|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown'
            + '|onmousemove|onmouseout|onmouseover|onmouseup|style|title'
            + '|valign<baseline?bottom?middle?top],'
            + 'td[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class'
            + '|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick'
            + '|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup'
            + '|style|title|valign<baseline?bottom?middle?top|width],'
            + 'textarea[accesskey|class|cols|dir<ltr?rtl|disabled<disabled|id|lang|name'
            + '|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup'
            + '|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect'
            + '|readonly<readonly|rows|style|tabindex|title],'
            + 'tfoot[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id'
            + '|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown'
            + '|onmousemove|onmouseout|onmouseover|onmouseup|style|title'
            + '|valign<baseline?bottom?middle?top],'
            + 'th[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class'
            + '|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick'
            + '|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
            + '|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup'
            + '|style|title|valign<baseline?bottom?middle?top|width],'
            + 'thead[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id'
            + '|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown'
            + '|onmousemove|onmouseout|onmouseover|onmouseup|style|title'
            + '|valign<baseline?bottom?middle?top],'
            + 'time[class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|pubdate|style'
            + '|title],'
            + 'title[dir<ltr?rtl|lang],'
            + 'tr[abbr|align<center?char?justify?left?right|bgcolor|char|charoff|class'
            + '|rowspan|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title|valign<baseline?bottom?middle?top],'
            + 'tt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup'
            + '|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],'
            + 'u[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup'
            + '|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],'
            + 'ul[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown'
            + '|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover'
            + '|onmouseup|style|title|type],'
            + 'var[class|dir<ltr?rtl|height|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style],'
            + 'video[autoplay|class|controls|dir<ltr?rtl|id|lang|loop|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|preload|poster|src|style'
            + '|title|width],'
            + 'wbr[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress'
            + '|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style'
            + '|title]',
        init_instance_callback: function (editor) {
            file_not_found = 0;
            editor.on('keyup change undo redo', function () {
                editor.save();
                $('#' + editor.id).keyup();
            });
            editor.on('PostProcess', function (e) {
                if (tinymcepro_minifycode == 1) {
                    e.content = e.content.replace(/\n/g, '');
                }
            });
            var tiny_path_array = baseAdminDir.split('/');
            tiny_path_array.splice((tiny_path_array.length - 2), 2);
            var tiny_final_path = tiny_path_array.join('/');
            window.tinyMCEPreInit = {};
            window.tinyMCEPreInit.base = tiny_final_path + '/js/tiny_mce';
            window.tinyMCEPreInit.suffix = '.min';
            tinyMceProPathLang = tiny_final_path + '/js/tiny_mce/langs/' + iso_user + '.js';
            if (UrlExistsTinyMcePro(tinyMceProPathLang) == true && tinyMceLangLoaded === false) {
                if (tinyMceLangLoaded === false) {
                    tinyMceLangLoaded = true;
                    $.getScript(tiny_final_path + '/js/tiny_mce/langs/' + iso_user + '.js');
                }
            }
        },
        menu: {
            edit: {title: 'Edit', items: 'undo redo | cut copy paste | selectall'},
            insert: {title: 'Insert', items: 'charmap fontawesome image media emoticons hr inserttime link anchor | pagebreak'},
            view: {title: 'View', items: 'visualaid'},
            format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
            table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'}
        },
        style_formats: [
            {
                title: 'Headers', items: [
                    {title: 'h1', block: 'h1'},
                    {title: 'h2', block: 'h2'},
                    {title: 'h3', block: 'h3'},
                    {title: 'h4', block: 'h4'},
                    {title: 'h5', block: 'h5'},
                    {title: 'h6', block: 'h6'}
                ]
            },

            {
                title: 'Blocks', items: [
                    {title: 'p', block: 'p'},
                    {title: 'div', block: 'div'},
                    {title: 'pre', block: 'pre'}
                ]
            },

            {
                title: 'Containers', items: [
                    {title: 'section', block: 'section', wrapper: true, merge_siblings: false},
                    {title: 'article', block: 'article', wrapper: true, merge_siblings: false},
                    {title: 'blockquote', block: 'blockquote', wrapper: true},
                    {title: 'hgroup', block: 'hgroup', wrapper: true},
                    {title: 'aside', block: 'aside', wrapper: true},
                    {title: 'figure', block: 'figure', wrapper: true}
                ]
            }
        ],
    };

    $.each(default_config, function (index, el) {
        if (config[index] === undefined)
            config[index] = el;
    });
    tinyMCE.init(config);
}