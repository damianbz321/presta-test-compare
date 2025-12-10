<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

class tinymcepro extends Module
{
    public $htmlTemplatesManager;

    function __construct()
    {
        //@ini_set('display_errors', 'on');
        //@error_reporting(E_ALL | E_STRICT);
        $this->name = 'tinymcepro';
        $this->tab = 'admin_tools';
        $this->author = 'MyPresta.eu';
        $this->module_key = '44873542187effe440a9606087efd6e7';
        $this->mypresta_link = 'https://mypresta.eu/modules/administration-tools/tinymce-pro-extended-rich-text-editor.html';
        $this->version = '2.4.5';
        $this->bootstrap = true;
        $this->tinyplugins = array(
            'image',
            'imagetools',
            'fontawesome',
            'powerpaste',
            'youtube',
            'smileys',
            'ecpicker',
            'bs_alert',
            'paragraph',
            'editattributes',
            'bootstrapaccordion',
            'bs_images',
            'addiframe',
            'bootstraplite',
            'lineheight',
            'toc',
            'tinyplusBootstrapTools',
            'tinyplusinclude',
        );
        $this->tinypluginsalter = array(
            'lineheight' => 'plugin.js',
            'link' => 'plugin.min.js',
            'table' => 'plugin.min.js',
            'contextmenu' => 'plugin.min.js'
        );
        $this->path_module_plugins = "../modules/tinymcepro/lib/plugins/";
        $this->path_module_files = "../modules/tinymcepro/lib/";
        $this->path_original_js_files = "../js/admin/";
        $this->path_original_tinymce = "../js/tiny_mce/";
        $this->path_original_tinymce_plugins = "../js/tiny_mce/plugins/";

        $tinymceproFontsDefault = Configuration::get('tinymceproFontsDefault');
        $tinymceproSizesDefault = Configuration::get('tinymceproSizesDefault');
        $tinymcepro_lineheight = Configuration::get('tinymcepro_lineheight');

        if ($tinymceproFontsDefault != false && $tinymceproFontsDefault != "" && $tinymceproFontsDefault != NULL) {
            $this->tinymceproFontsDefault = $tinymceproFontsDefault;
        } else {
            $this->tinymceproFontsDefault = 'Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Open Sans=Open Sans,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats';
        }

        if ($tinymceproFontsDefault != false && $tinymceproFontsDefault != "" && $tinymceproFontsDefault != NULL) {
            $this->tinymceproSizesDefault = $tinymceproSizesDefault;
        } else {
            $this->tinymceproSizesDefault = "8pt 10pt 12pt 14pt 18pt 24pt 36pt 42pt 48pt 52pt 58pt 62pt 68pt 72pt 86px 100px 116px 132px 148px";
        }

        //Media::addJsDef(array('tinymcepro_lineheight' => (($tinymcepro_lineheight != false && $tinymcepro_lineheight != "" && $tinymcepro_lineheight != NULL) ? $tinymcepro_lineheight : $this->tinymceproLineheightDefault)));

        if ($tinymcepro_lineheight != false && $tinymcepro_lineheight != "" && $tinymcepro_lineheight != NULL) {
            $this->tinymceproLineheightDefault = $tinymcepro_lineheight;
        } else {
            $this->tinymceproLineheightDefault = "8pt 10pt 12pt 14pt 18pt 24pt 36pt 42pt 48pt 52pt 58pt 62pt 68pt 72pt 86px 100px 116px 132px 148px";
        }

        parent::__construct();
        $this->displayName = $this->l('TinyMCE Pro (rich text editor pro)');
        $this->description = $this->l('Most extended version of tinyMCE editor for your PrestaShop!');

        $this->availablehtmlTemplateVars = array();
        $this->htmlTemplatesManager = new tinymceprohtmlTemplatesManager($this->name, $this->availablehtmlTemplateVars);

        $this->checkforupdates();
    }

    public function inconsistency($ret)
    {
        return;
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        if (Configuration::get('tinymcepro_cmscat_wysiwyg') == 1) {
            if (isset($this->context->controller->controller_name)) {
                if ($this->context->controller->controller_name == "AdminCmsContent") {
                    $this->context->controller->addJs(($this->_path) . 'js/tinymce-cmscategory.js', 'all');
                }
            }
        }

        $template_files = $this->gethtmlFilesArrayForBackOffice();
        $tinymceproSizes = Configuration::get('tinymceproSizes');
        $tinymceproFonts = Configuration::get('tinymceproFonts');
        $tinymcepro_lineheight = Configuration::get('tinymcepro_lineheight');
        $tinymcepro_contentstyle = Configuration::get('tinymcepro_contentstyle');
        Media::addJsDef(array('tinymcepro_sizes' => (($tinymceproSizes != false && $tinymceproSizes != "" && $tinymceproSizes != NULL) ? $tinymceproSizes : $this->tinymceproSizesDefault)));
        Media::addJsDef(array('tinymcepro_fonts' => (($tinymceproFonts != false && $tinymceproFonts != "" && $tinymceproFonts != NULL) ? $tinymceproFonts : $this->tinymceproFontsDefault)));
        Media::addJsDef(array('tinymcepro_lineheight' => (($tinymcepro_lineheight != false && $tinymcepro_lineheight != "" && $tinymcepro_lineheight != NULL) ? $tinymcepro_lineheight : $this->tinymceproLineheightDefault)));
        Media::addJsDef(array('tinymcepro_fullpage' => (Tools::getValue('controller') == 'AdminTranslations') ? 'fullpage,' : ''));
        Media::addJsDef(array('tinymcepro_contextmenu' => (Configuration::get('tinymcepro_contextmenu') == 1 ? 'contextmenu' : '')));
        Media::addJsDef(array('tinymcepro_adv_bootstrap' => (Configuration::get('tinymcepro_adv_bootstrap') == 1 ? 'tinyplusinclude, tinyplusBootstrapTools,' : '')));
        Media::addJsDef(array('tinymcepro_adv_bootstrap_toolbar' => (Configuration::get('tinymcepro_adv_bootstrap') == 1 && Tools::getValue('controller') !='AdminTranslations' ? 'tinyplusShowBlocks tinyplusBootstrapToolsContainerEdit tinyplusBootstrapToolsContainerAdd tinyplusBootstrapToolsContainerAddBefore tinyplusBootstrapToolsContainerAddAfter tinyplusBootstrapToolsContainerDelete tinyplusBootstrapToolsContainerMoveUp tinyplusBootstrapToolsContainerMoveDown | tinyplusBootstrapToolsRowEdit tinyplusBootstrapToolsRowAdd tinyplusBootstrapToolsRowAddBefore tinyplusBootstrapToolsRowAddAfter tinyplusBootstrapToolsRowDelete tinyplusBootstrapToolsRowMoveUp tinyplusBootstrapToolsRowMoveDown | tinyplusBootstrapToolsColEdit tinyplusBootstrapToolsColAdd tinyplusBootstrapToolsColAddBefore tinyplusBootstrapToolsColAddAfter tinyplusBootstrapToolsColDelete tinyplusBootstrapToolsColMoveLeft tinyplusBootstrapToolsColMoveRight' : '')));
        Media::addJsDef(array('tinymcepro_adv_bootstrap' => (Configuration::get('tinymcepro_adv_bootstrap') == 1 ? 'tinyplusinclude, tinyplusBootstrapTools,' : '')));
        Media::addJsDef(array('tinymcepro_adv_bootstrap_toolbar' => (Configuration::get('tinymcepro_adv_bootstrap') == 1 && Tools::getValue('controller') !='AdminTranslations' ? 'tinyplusShowBlocks tinyplusBootstrapToolsContainerEdit tinyplusBootstrapToolsContainerAdd tinyplusBootstrapToolsContainerAddBefore tinyplusBootstrapToolsContainerAddAfter tinyplusBootstrapToolsContainerDelete tinyplusBootstrapToolsContainerMoveUp tinyplusBootstrapToolsContainerMoveDown | tinyplusBootstrapToolsRowEdit tinyplusBootstrapToolsRowAdd tinyplusBootstrapToolsRowAddBefore tinyplusBootstrapToolsRowAddAfter tinyplusBootstrapToolsRowDelete tinyplusBootstrapToolsRowMoveUp tinyplusBootstrapToolsRowMoveDown | tinyplusBootstrapToolsColEdit tinyplusBootstrapToolsColAdd tinyplusBootstrapToolsColAddBefore tinyplusBootstrapToolsColAddAfter tinyplusBootstrapToolsColDelete tinyplusBootstrapToolsColMoveLeft tinyplusBootstrapToolsColMoveRight' : '')));
        Media::addJsDef(array('tinymcepro_contentstyle' => (($tinymcepro_contentstyle != '' || $tinymcepro_contentstyle != false) ? $tinymcepro_contentstyle : '')));
        Media::addJsDef(array('tinymcepro_templates' => (Configuration::get('tinymcepro_templates') == 1 ? 'template, ' : '')));
        Media::addJsDef(array('tinymcepro_templates' => (Configuration::get('tinymcepro_templates') == 1 ? 'template, ' : '')));
        Media::addJsDef(array('tinymcepro_template_files' => $template_files));
        Media::addJsDef(array('tinymcepro_force_p_newlines' => (Configuration::get('tinymcepro_force_p_newlines') == 1 ? true : false)));
        Media::addJsDef(array('tinymcepro_force_br_newlines' => (Configuration::get('tinymcepro_force_br_newlines') == 1 ? true : false)));
        Media::addJsDef(array('tinymcepro_newlines_to_brs' => (Configuration::get('tinymcepro_newlines_to_brs') == 1 ? true : false)));
        Media::addJsDef(array('tinymcepro_minifycode' => (Configuration::get('tinymcepro_minifycode') == 1 ? 1 : 0)));
        Media::addJsDef(array('tinymcepro_autoresize' => (Configuration::get('tinymcepro_autoresize') == 1 ? 'autoresize, ':'')));
        Media::addJsDef(array('tinymceproHeight' => (Configuration::get('tinymceproHeight') ?  Configuration::get('tinymceproHeight') : 400)));


        $tinymceproImgClassesConfig = Configuration::get('tinymceproImgClasses');
        $tinymceproImgClasses = explode(',', $tinymceproImgClassesConfig);
        $tinymceproImgClassArray = array();
        $tinymceproImgClassArray[] = array('text' => $this->l('None'), 'value' => ' ');
        $tinymceproImgClassArray[] = array('text' => $this->l('Thumbnail'), 'value' => 'img-thumbnail');
        $tinymceproImgClassArray[] = array('text' => $this->l('Circle'), 'value' => 'img-circle');
        $tinymceproImgClassArray[] = array('text' => $this->l('Rounded'), 'value' => 'img-rounded');
        if (Configuration::get('tinymceproImgClasses') != "" && Configuration::get('tinymceproImgClasses') != false) {
            if (is_array($tinymceproImgClasses)) {
                if (count($tinymceproImgClasses) > 0) {
                    foreach ($tinymceproImgClasses AS $class) {
                        $tinymceproImgClassArray[] = array('text' => trim($class), 'value' => trim($class));
                    }
                }
            }
        }
        Media::addJsDef(array('tinymceproImgClassArray' => $tinymceproImgClassArray));


        $tinymcepro_tableClasses = Configuration::get('tinymceproTableClasses');
        $tinymceproTableClasses = explode(',', $tinymcepro_tableClasses);
        $tinymcepro_taleClassesArray = array();
        $tinymcepro_taleClassesArray[] = array('text' => $this->l('None'), 'value' => ' ');
        if ($tinymcepro_tableClasses  != "" && $tinymcepro_tableClasses  != false) {
            if (is_array($tinymceproTableClasses)) {
                if (count($tinymceproTableClasses) > 0) {
                    foreach ($tinymceproTableClasses AS $class) {
                        $tinymcepro_taleClassesArray[] = array('text' => trim($class), 'value' => trim($class));
                    }
                }
            }
        }
        $tinymcepro_taleClassesArray[] = array('text' => 'table table-dark', 'value' => 'table table-dark');
        $tinymcepro_taleClassesArray[] = array('text' => 'table table-striped', 'value' => 'table table-striped');
        $tinymcepro_taleClassesArray[] = array('text' => 'table table-striped table-dark', 'value' => 'table table-striped table-dark');
        $tinymcepro_taleClassesArray[] = array('text' => 'table table-bordered', 'value' => 'table table-bordered');
        $tinymcepro_taleClassesArray[] = array('text' => 'table table-bordered table-dark', 'value' => 'table table-bordered table-dark');
        $tinymcepro_taleClassesArray[] = array('text' => 'table table-hover', 'value' => 'table table-hover');
        $tinymcepro_taleClassesArray[] = array('text' => 'table table-hover table-dark', 'value' => 'table table-hover table-dark');
        $tinymcepro_taleClassesArray[] = array('text' => 'table-active', 'value' => 'table-active');
        $tinymcepro_taleClassesArray[] = array('text' => 'table-primary', 'value' => 'table-primary');
        $tinymcepro_taleClassesArray[] = array('text' => 'table-secondary', 'value' => 'table-secondary');
        $tinymcepro_taleClassesArray[] = array('text' => 'table-success', 'value' => 'table-success');
        $tinymcepro_taleClassesArray[] = array('text' => 'table-danger', 'value' => 'table-danger');
        $tinymcepro_taleClassesArray[] = array('text' => 'table-warning', 'value' => 'table-warning');
        $tinymcepro_taleClassesArray[] = array('text' => 'table-info', 'value' => 'table-info');
        $tinymcepro_taleClassesArray[] = array('text' => 'table-light', 'value' => 'table-light');
        $tinymcepro_taleClassesArray[] = array('text' => 'table-dark', 'value' => 'table-dark');
        $tinymcepro_taleClassesArray[] = array('text' => 'table-responsive', 'value' => 'table-responsive');
        Media::addJsDef(array('tinymceproTableClassesArray' => $tinymcepro_taleClassesArray));

        $this->context->controller->addJqueryPlugin('fancybox');
        $this->context->controller->addJS(_PS_JS_DIR_ . 'admin/tinymce.inc.js');
        //for update purposes
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 16 //
        // ---------- //
        // ---------- //
        $this->mkey = "nlc";
        if (@file_exists('../modules/' . $this->name . '/key.php')) {
            @require_once('../modules/' . $this->name . '/key.php');
        } else {
            if (@file_exists(dirname(__FILE__) . $this->name . '/key.php')) {
                @require_once(dirname(__FILE__) . $this->name . '/key.php');
            } else {
                if (@file_exists('modules/' . $this->name . '/key.php')) {
                    @require_once('modules/' . $this->name . '/key.php');
                }
            }
        }
        if ($form == 1) {
            return '
            <div class="panel" id="fieldset_myprestaupdates" style="margin-top:20px;">
            ' . ($this->psversion() == 6 || $this->psversion() == 7 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->l('MyPresta updates') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->l('Check updates') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->l('Check now') . '
                                </button>
                            </div>
                            <label>' . $this->l('Updates notifications') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->l('-- select --') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->l('Enable') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->l('Disable') . '</option>
                                </select>
                                <p class="clear">' . $this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.') . '</p>
                            </div>
                            <label>' . $this->l('Module page') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->l('This is direct link to official addon page, where you can read about changes in the module (changelog)') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates"class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '
                                </button>
                            </div>
                        </form>
                    </fieldset>
                    <style>
                    #fieldset_myprestaupdates {
                        display:block;clear:both;
                        float:inherit!important;
                    }
                    </style>
                </div>
            </div>
            </div>';
        } else {
            if (defined('_PS_ADMIN_DIR_')) {
                if (Tools::isSubmit('submit_settings_updates')) {
                    Configuration::updateValue('mypresta_updates', Tools::getValue('mypresta_updates'));
                }
                if (Configuration::get('mypresta_updates') != 0 || (bool)Configuration::get('mypresta_updates') != false) {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = tinymceproUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (tinymceproUpdate::version($this->version) < tinymceproUpdate::version(Configuration::get('updatev_' . $this->name)) && Tools::getValue('ajax', 'false') == 'false') {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                } else {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = tinymceproUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                }
                if ($display_msg == 1) {
                    if (tinymceproUpdate::version($this->version) < tinymceproUpdate::version(tinymceproUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public function install()
    {
        if (parent::install() == false ||
            Configuration::updateValue('tinymceproSizesDefault', $this->tinymceproSizesDefault) == false ||
            Configuration::updateValue('tinymceproFontsDefault', $this->tinymceproFontsDefault) == false ||
            $this->install_tinymcepro(0) == false ||
            $this->registerHook('header') == false ||
            $this->alterCleanHtml() == false ||
            $this->registerHook('ActionAdminControllerSetMedia') == false) {
            return false;
        }
        return true;
    }

    public function alterCleanHtml()
    {
        $file = '../src/Core/ConstraintValidator/CleanHtmlValidator.php';
        if (file_exists($file)) {
            $data = file($file);
            if (!function_exists('replace_aline')) {
                function replace_aline($data)
                {
                    if (stristr($data, '$containsScriptTags = preg_match(\'/<[\s]*script/ims\', $value) || preg_match(\'/.*script\:/ims\', $value);')) {
                        return '$containsScriptTags = false;' . "\n";
                    }
                    return $data;
                }
            }

            $data = array_map('replace_aline', $data);
            file_put_contents($file, implode('', $data));

            if (!function_exists('replace_a_line_two')) {
                function replace_a_line_two($data)
                {
                    if (stristr($data, '$containsJavascriptEvents = preg_match(\'/(\' . $this->getJavascriptEvents() . \')[\s]*=/ims\', $value);')) {
                        return '$containsJavascriptEvents = false;' . "\n";
                    }
                    return $data;
                }
            }

            $data = array_map('replace_a_line_two', $data);
            file_put_contents($file, implode('', $data));
        }
        return true;
    }

    public function uninstall()
    {
        parent::uninstall();
        foreach ($this->tinyplugins AS $plugin) {
            if (file_exists($this->path_original_tinymce_plugins . $plugin)) {
                $this->move_plugin("uninstall", $plugin);
            }
        }

        $this->restore_from_backup(0);
        return true;
    }

    public function restore_from_backup($return = 0)
    {
        //RESTORE FROM BACKUP
        if (file_exists($this->path_original_js_files . 'tinymce.inc.js.bkp')) {
            $file = fopen($this->path_original_js_files . 'tinymce.inc.js.bkp', 'r');
            $newfile = fopen($this->path_original_js_files . 'tinymce.inc.js', 'w+');
            while (($line = fgets($file)) !== false) {
                fputs($newfile, $line);
            }
            fclose($newfile);
            fclose($file);
        };

        if (file_exists($this->path_original_tinymce . 'tinymce.min.js.bkp')) {
            $file = fopen($this->path_original_tinymce . 'tinymce.min.js.bkp', 'r');
            $newfile = fopen($this->path_original_tinymce . 'tinymce.min.js', 'w+');
            while (($line = fgets($file)) !== false) {
                fputs($newfile, $line);
            }
            fclose($newfile);
            fclose($file);
        };

        if (file_exists($this->path_original_js_files . 'tinymce_loader.js.bkp')) {
            $file = fopen($this->path_original_js_files . 'tinymce_loader.js.bkp', 'r');
            $newfile = fopen($this->path_original_js_files . 'tinymce_loader.js', 'w+');
            while (($line = fgets($file)) !== false) {
                fputs($newfile, $line);
            }
            fclose($newfile);
            fclose($file);
        };

        //RESTORE PLUGINS
        //INSTALL MODIFICATION OF PLUGINS
        foreach ($this->tinypluginsalter AS $plugin => $file) {
            if (file_exists($this->path_original_tinymce_plugins . $plugin . '/' . $file)) {
                $this->move_modification("uninstall", $plugin, $file);
            }
        }


        if ($return == 1) {
            return " <span style='color:green; font-weight:bold; font-size:16px;'>" . $this->l('Restored!') . "</span>";
        }
    }

    public function configurationForm()
    {
        return $this->display(__FILE__, 'views/admin/form.tpl');
    }

    public function generateFormEditor()
    {
        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Editor settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Cms Category rich text editor'),
                        'desc' => $this->l('Activate rich text editor for cms category page description field (by default it is not available there)'),
                        'name' => 'tinymcepro_cmscat_wysiwyg',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Predefined templates'),
                        'desc' => $this->l('Turn this option on if you want to allow to use and create predefined templates of contents') . '<br/>' . $this->htmlTemplatesManager->generatehtmlTemplatesManagerButton(),
                        'name' => 'tinymcepro_templates',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Minify code'),
                        'desc' => $this->l('Minify code of contents'),
                        'name' => 'tinymcepro_minifycode',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Enable Context menu'),
                        'desc' => $this->l('Context menu is a special plugin that creates special menu options when you will right-click inside the editor.') . ' ' . '<a href="https://i.imgur.com/3QGjEfn.png" class="tinymcepro_fancybox">[screenshot]</a>',
                        'name' => 'tinymcepro_contextmenu',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Enable advanced bootstrap tool'),
                        'desc' => $this->l('This option activates features in rich text editor to support bootstrap content edit process'),
                        'name' => 'tinymcepro_adv_bootstrap',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Force new line as') . ' &lt;br&gt;',
                        'desc' => $this->l('Each "enter" will be replaced with code') . ' &lt;br&gt;',
                        'name' => 'tinymcepro_force_br_newlines',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Force new line as'). ' &lt;p&gt;',
                        'desc' => $this->l('Each "enter" will be replaced with code'). ' &lt;p&gt;',
                        'name' => 'tinymcepro_force_p_newlines',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Convert all new lines to') . ' &lt;br&gt;',
                        'desc' => $this->l('Option when active will change all new lines to code') . ' &lt;br&gt;',
                        'name' => 'tinymcepro_newlines_to_brs',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Editor height'),
                        'name' => 'tinymceproHeight',
                        'prefix' => 'px',
                        'desc' => $this->l('Setup the height of the editor, default value: 400'),
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Activate autoresize plugin'),
                        'desc' => $this->l('Autoresize plugin changes the size of the editor (height) automatically depending on contents size. Option when active will disable "height" param.'),
                        'name' => 'tinymcepro_autoresize',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Additional image classes'),
                        'name' => 'tinymceproImgClasses',
                        'desc' => $this->l('Module will add some new image classes to classes that exists by default: ') . '<a target="blank" href="https://i.imgur.com/4i9UGrB.png" class="tinymcepro_fancybox">thumbnail, circle, rounded</a>' . '<br/>' . $this->l('Separate new classes by comma'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Table classes'),
                        'name' => 'tinymceproTableClasses',
                        'desc' => $this->l('Module will create option to select table class during table creation process: ') . '<a target="blank" href="https://i.imgur.com/UMlMkVb.png" class="tinymcepro_fancybox">thumbnail, circle, rounded</a>' . '<br/>' . $this->l('Separate new classes by comma'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Font sizes'),
                        'name' => 'tinymceproSizesDefault',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Line heights'),
                        'name' => 'tinymcepro_lineheight',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Fonts'),
                        'name' => 'tinymceproFontsDefault',
                        'desc' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'tinymcepro/views/admin/fonts.tpl'),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Additional css style'),
                        'name' => 'tinymcepro_contentstyle',
                        'desc' => $this->l('Add here styles that will be applicable to the editor contents (these styles will be used in back office only)') . ' ' . $this->l('Thanks to this option you can @import your theme css styles into the editor'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitEditorSettings',
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->default_form_language = $this->context->language->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->submit_action = 'submitFosConfiguration';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form_1));
    }

    public function generateFormFos()
    {
        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Configuration'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Include latest Font Awesome library'),
                        'name' => 'tpro_fa_new',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Include internal accordion library'),
                        'name' => 'tpro_accordion',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Include fancybox library to all pages'),
                        'name' => 'tpro_fancybox',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Include "read more" library'),
                        'name' => 'tpro_readmore',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitPrrs',
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->default_form_language = $this->context->language->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->submit_action = 'submitFosConfiguration';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form_1));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'tinymcepro_cmscat_wysiwyg' => Tools::getValue('tinymcepro_cmscat_wysiwyg', Configuration::get('tinymcepro_cmscat_wysiwyg')),
            'tinymcepro_templates' => Tools::getValue('tinymcepro_templates', Configuration::get('tinymcepro_templates')),
            'tinymcepro_contextmenu' => Tools::getValue('tinymcepro_contextmenu', Configuration::get('tinymcepro_contextmenu')),
            'tpro_accordion' => Tools::getValue('tpro_accordion', Configuration::get('tpro_accordion')),
            'tpro_fa_new' => Tools::getValue('tpro_fa_new', Configuration::get('tpro_fa_new')),
            'tpro_fancybox' => Tools::getValue('tpro_fancybox', Configuration::get('tpro_fancybox')),
            'tpro_readmore' => Tools::getValue('tpro_readmore', Configuration::get('tpro_readmore')),
            'tinymceproFontsDefault' => Tools::getValue('tinymceproFontsDefault', Configuration::get('tinymceproFontsDefault')),
            'tinymceproSizesDefault' => Tools::getValue('tinymceproSizesDefault', Configuration::get('tinymceproSizesDefault')),
            'tinymcepro_lineheight' => Tools::getValue('tinymcepro_lineheight', $this->tinymceproLineheightDefault),
            'tinymceproImgClasses' => Tools::getValue('tinymceproImgClasses', Configuration::get('tinymceproImgClasses')),
            'tinymcepro_contentstyle' => Tools::getValue('tinymcepro_contentstyle', Configuration::get('tinymcepro_contentstyle')),
            'tinymcepro_newlines_to_brs' => (bool)Tools::getValue('tinymcepro_newlines_to_brs', Configuration::get('tinymcepro_newlines_to_brs')),
            'tinymcepro_force_p_newlines' => (bool)Tools::getValue('tinymcepro_force_p_newlines', Configuration::get('tinymcepro_force_p_newlines')),
            'tinymcepro_force_br_newlines' => (bool)Tools::getValue('tinymcepro_force_br_newlines', Configuration::get('tinymcepro_force_br_newlines')),
            'tinymcepro_adv_bootstrap' => (bool)Tools::getValue('tinymcepro_adv_bootstrap', Configuration::get('tinymcepro_adv_bootstrap')),
            'tinymcepro_minifycode' => (bool)Tools::getValue('tinymcepro_minifycode', Configuration::get('tinymcepro_minifycode')),
            'tinymcepro_adv_bootstrap' => (bool)Tools::getValue('tinymcepro_adv_bootstrap', Configuration::get('tinymcepro_adv_bootstrap')),
            'tinymcepro_autoresize' => (bool)Tools::getValue('tinymcepro_autoresize', Configuration::get('tinymcepro_autoresize')),
            'tinymceproHeight' => Tools::getValue('tinymceproHeight', (Configuration::get('tinymceproHeight') ? Configuration::get('tinymceproHeight'):400)),
            'tinymceproTableClasses' => Tools::getValue('tinymceproTableClasses', Configuration::get('tinymceproTableClasses')),

        );
    }

    public function _postProcess()
    {
        if (Tools::isSubmit('tpro_fa_new')) {
            Configuration::updateValue('tpro_fa_new', Tools::getValue('tpro_fa_new'));
            Configuration::updateValue('tpro_accordion', Tools::getValue('tpro_accordion'));
            Configuration::updateValue('tpro_fancybox', Tools::getValue('tpro_fancybox'));
            Configuration::updateValue('tpro_readmore', Tools::getValue('tpro_readmore'));
        }

        if (Tools::isSubmit('submitEditorSettings')) {
            Configuration::updateValue('tinymcepro_minifycode', trim(Tools::getValue('tinymcepro_minifycode')));
            Configuration::updateValue('tinymcepro_cmscat_wysiwyg', trim(Tools::getValue('tinymcepro_cmscat_wysiwyg')));
            Configuration::updateValue('tinymcepro_templates', trim(Tools::getValue('tinymcepro_templates')));
            Configuration::updateValue('tinymcepro_contentstyle', trim(Tools::getValue('tinymcepro_contentstyle')));
            Configuration::updateValue('tinymcepro_contextmenu', trim(Tools::getValue('tinymcepro_contextmenu')));
            Configuration::updateValue('tinymceproSizesDefault', trim(Tools::getValue('tinymceproSizesDefault')));
            Configuration::updateValue('tinymceproFontsDefault', trim(Tools::getValue('tinymceproFontsDefault')));
            Configuration::updateValue('tinymcepro_lineheight', trim(Tools::getValue('tinymcepro_lineheight')));
            Configuration::updateValue('tinymceproImgClasses', trim(Tools::getValue('tinymceproImgClasses')));
            Configuration::updateValue('tinymcepro_force_p_newlines', trim(Tools::getValue('tinymcepro_force_p_newlines')));
            Configuration::updateValue('tinymcepro_force_br_newlines', trim(Tools::getValue('tinymcepro_force_br_newlines')));
            Configuration::updateValue('tinymcepro_newlines_to_brs', trim(Tools::getValue('tinymcepro_newlines_to_brs')));
            Configuration::updateValue('tinymcepro_adv_bootstrap', trim(Tools::getValue('tinymcepro_adv_bootstrap')));
            Configuration::updateValue('tinymcepro_autoresize', trim(Tools::getValue('tinymcepro_autoresize')));
            Configuration::updateValue('tinymceproHeight', trim(Tools::getValue('tinymceproHeight')));
            Configuration::updateValue('tinymceproTableClasses', trim(Tools::getValue('tinymceproTableClasses')));

        }

        if (Tools::isSubmit('submit_settings_reinstall')) {
            $this->context->controller->confirmations[] = $this->install_tinymcepro(1);
        }
        if (Tools::isSubmit('submit_settings_restore_backup')) {
            $this->context->controller->confirmations[] = $this->restore_from_backup(1);
        }
    }

    public function getContent()
    {
        $token = Tools::getAdminTokenLite('AdminModules');
        $module_link = $this->context->link->getAdminLink('AdminModules', false);
        $url = $module_link . '&configure=tinymcepro&token=' . $token . '&tab_module=administration&module_name=tinymcepro';
        $this->context->smarty->assign('tinymcepro_updates', $this->checkforupdates(0, 1));
        $this->context->smarty->assign('tinymcepro_restore', $this->config_check_backup());
        $this->context->smarty->assign('tinymcepro_form_fos', $this->generateFormFos());
        $this->context->smarty->assign('tinymcepro_form_editor', $this->generateFormEditor());
        $this->context->smarty->assign('tinymcepro_url', $url);
        $this->_postProcess();

        return $this->configurationForm();
    }

    public function psversion()
    {
        $version = _PS_VERSION_;
        $exp = $explode = explode(".", $version);
        return $exp[1];
    }

    public function config_check_backup()
    {
        if (file_exists($this->path_original_js_files . 'tinymce.inc.js.bkp')) {
            return true;
        } else {
            return false;
        }
    }

    public function recurse_copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function copy_modification($source, $target)
    {
        $new_file_contents = file_get_contents($source);
        $file = fopen($target, 'w');
        fwrite($file, $new_file_contents);
        fclose($file);
    }

    public function move_modification($what_to_do = null, $plugin, $file)
    {
        if ($what_to_do == "install") {
            if (!file_exists($this->path_original_tinymce_plugins . $plugin . '/' . $file . '.bkp')) {
                $this->copy_modification($this->path_original_tinymce_plugins . $plugin . '/' . $file, $this->path_original_tinymce_plugins . $plugin . '/' . $file . '.bkp');
            }
            $this->copy_modification($this->path_module_plugins . $plugin . '/' . $file, $this->path_original_tinymce_plugins . $plugin . '/' . $file);
        } elseif ($what_to_do == "uninstall") {
            if (file_exists($this->path_original_tinymce_plugins . $plugin . '/' . $file . '.bkp')) {
                $this->copy_modification($this->path_original_tinymce_plugins . $plugin . '/' . $file . '.bkp', $this->path_original_tinymce_plugins . $plugin . '/' . $file);
            }
        }
    }

    public function move_plugin($what_to_do = null, $plugin)
    {
        if ($what_to_do == "install") {
            $this->recurse_copy($this->path_module_plugins . $plugin, $this->path_original_tinymce_plugins . $plugin);
        } elseif ($what_to_do == "uninstall") {
            if (file_exists($this->path_original_tinymce_plugins . $plugin)) {
                $this->deletePlugin($this->path_original_tinymce_plugins . $plugin);
            }
        }
    }

    public function deletePlugin($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deletePlugin($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }
        return rmdir($dir);
    }

    public function install_tinymcepro($return = 0)
    {
        $this->alterCleanHtml();
        //BACKUP
        if (!file_exists($this->path_original_js_files . 'tinymce.inc.js.bkp')) {
            $file = fopen($this->path_original_js_files . 'tinymce.inc.js', 'r');
            $newfile = fopen($this->path_original_js_files . 'tinymce.inc.js.bkp', 'w+');
            while (($line = fgets($file)) !== false) {
                fputs($newfile, $line);
            }
            fclose($newfile);
            fclose($file);
        }

        if (!file_exists($this->path_original_tinymce . 'skins/prestashop/skin.min.css.bkp')) {
            $file = fopen($this->path_original_tinymce . 'skins/prestashop/skin.min.css', 'r');
            $newfile = fopen($this->path_original_tinymce . 'skins/prestashop/skin.min.css.bkp', 'w+');
            while (($line = fgets($file)) !== false) {
                fputs($newfile, $line);
            }
            fclose($newfile);
            fclose($file);
        }

        if (!file_exists($this->path_original_tinymce . 'tinymce.min.js.bkp')) {
            $file = fopen($this->path_original_tinymce . 'tinymce.min.js', 'r');
            $newfile = fopen($this->path_original_tinymce . 'tinymce.min.js.bkp', 'w+');
            while (($line = fgets($file)) !== false) {
                fputs($newfile, $line);
            }
            fclose($newfile);
            fclose($file);
        }

        if (!file_exists($this->path_original_js_files . 'tinymce_loader.js.bkp')) {
            $file = fopen($this->path_original_js_files . 'tinymce_loader.js', 'r');
            $newfile = fopen($this->path_original_js_files . 'tinymce_loader.js.bkp', 'w+');
            while (($line = fgets($file)) !== false) {
                fputs($newfile, $line);
            }
            fclose($newfile);
            fclose($file);
        }


        //INSTALL TINYMCE MODIFIED
        if (file_exists($this->path_original_js_files . 'tinymce.inc.js.bkp')) {
            $file_module = fopen($this->path_module_files . 'tinymce.inc.js', 'r');
            $file_original = fopen($this->path_original_js_files . 'tinymce.inc.js', 'w+');
            while (($line = fgets($file_module)) !== false) {
                fputs($file_original, $line);
            }
            fclose($file_original);
            fclose($file_module);
        }

        if (file_exists($this->path_original_tinymce . 'tinymce.min.js.bkp')) {
            $file_module = fopen($this->path_module_files . 'tinymce.min.js', 'r');
            $file_original = fopen($this->path_original_tinymce . 'tinymce.min.js', 'w+');
            while (($line = fgets($file_module)) !== false) {
                fputs($file_original, $line);
            }
            fclose($file_original);
            fclose($file_module);
        }

        if (file_exists($this->path_original_tinymce . 'skins/prestashop/skin.min.css.bkp')) {
            $file_module = fopen($this->path_module_files . 'skins/prestashop/skin.min.css', 'r');
            $file_original = fopen($this->path_original_tinymce . 'skins/prestashop/skin.min.css', 'w+');
            while (($line = fgets($file_module)) !== false) {
                fputs($file_original, $line);
            }
            fclose($file_original);
            fclose($file_module);
        }

        if (file_exists($this->path_original_js_files . 'tinymce_loader.js.bkp')) {
            $file_module = fopen($this->path_module_files . 'tinymce_loader.js', 'r');
            $file_original = fopen($this->path_original_js_files . 'tinymce_loader.js', 'w+');
            while (($line = fgets($file_module)) !== false) {
                fputs($file_original, $line);
            }
            fclose($file_original);
            fclose($file_module);
        }


        //INSTALL PLUGINS
        foreach ($this->tinyplugins AS $plugin) {
            if (!file_exists($this->path_original_tinymce_plugins . $plugin)) {
                $this->move_plugin("install", $plugin);
            } else {
                if ($this->deletePlugin($this->path_original_tinymce_plugins . $plugin)) {
                    $this->move_plugin("install", $plugin);
                }
            }
        }

        //INSTALL MODIFICATION OF PLUGINS
        foreach ($this->tinypluginsalter AS $plugin => $file) {
            if (file_exists($this->path_original_tinymce_plugins . $plugin . '/' . $file)) {
                $this->move_modification("install", $plugin, $file);
            }
        }

        //REINSTALL MESSAGE
        if ($return == 1) {
            return " <span style='color:green; font-weight:bold; font-size:16px;'>" . $this->l('Reinstalled!') . "</span>";
        } else {
            return true;
        }
    }


    public function hookHeader()
    {
        $this->context->controller->addCss($this->_path . 'lib/plugins/codesample/css/prism.css');
        $this->context->controller->addCss($this->_path . 'css/tinymcepro.css');
        $this->context->controller->addJS($this->_path . 'js/alerts.js');
        if (Configuration::get('tpro_accordion') == 1) {
            $this->context->controller->addJS($this->_path . 'js/accordion.js');
        }
        if (Configuration::get('tpro_fancybox') == 1) {
            $this->context->controller->addJqueryPlugin('fancybox');
        }
        if (Configuration::get('tpro_readmore') == 1) {
            $this->context->controller->addJS($this->_path . 'js/readmore.js');
        }
        if (Configuration::get('tpro_fa_new') == 1) {
            return $this->display(__file__, 'header.tpl');
        }
    }

    public function gethtmlFilesArrayForBackOffice()
    {
        $dir = "../modules/" . $this->name . "/html/";
        $dh = opendir($dir);
        $files = array();
        $exists = array();
        while (false !== ($filename = readdir($dh))) {
            if ($filename != ".." && $filename != "." && $filename != "" && $filename != "index.php") {
                $explode = explode(".", $filename);
                if (!isset($exists[$explode[0]])) {
                    $exists[$explode[0]] = true;
                    $files[]['name'] = $explode[0];
                }
            }
        }
        $array_to_return = array();
        if (count($files) > 0) {
            foreach ($files as $file) {
                $array_to_return[] = array('title' => $file['name'], 'url' => $this->context->shop->getBaseURL(true, true) . "modules/tinymcepro/html/" . $file['name'] . ".txt");
            }
            $files = json_encode($array_to_return, JSON_UNESCAPED_SLASHES);
        } else {
            $files = '[]';
        }
        return $files;
    }
}

class tinymceproUpdate extends tinymcepro
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3) {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2) {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1) {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0) {
            $version = (int)$version . "0000";
        }
        return (int)$version;
    }

    public static function encrypt($string)
    {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {
        if (ini_get("allow_url_fopen")) {
            if (function_exists("file_get_contents")) {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);
        return $actual_version;
    }
}

require_once _PS_MODULE_DIR_ . 'tinymcepro/lib/htmlTemplatesManager/htmlTemplatesManager.php';


?>