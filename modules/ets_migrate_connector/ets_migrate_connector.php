<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once _PS_MODULE_DIR_ . 'ets_migrate_connector/classes/MCDb.php';

class Ets_migrate_connector extends Module
{
    protected static $_configs;
    protected static $currentIndex;

    public $ps13;
    public $ps14;
    public $ps15;
    public $token;
    public $content;
    public $context;

    public function __construct()
    {
        $this->name = 'ets_migrate_connector';
        $this->tab = 'front_office_features';
        $this->version = '1.1.5';
        $this->author = 'PrestaHero';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PrestaShop Connector');
        $this->description = $this->l('Connect PrestaShop websites for data migration purpose. Only use for PrestaShop migration modules made by PrestaHero');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall PrestaShop Connector?');
        $this->init();
    }

    public function init()
    {
        $this->ps13 = version_compare(_PS_VERSION_, '1.4.0.0', '<') ? 1 : 0;
        $this->ps14 = !$this->ps13 && version_compare(_PS_VERSION_, '1.5.0.0', '<') ? 1 : 0;
        if ($this->ps14 || $this->ps13) {
            require_once _PS_MODULE_DIR_ . 'ets_migrate_connector/backward_compatibility/backward.php';
            if (!$this->context) {
                $this->context = Context::getContext();
            }
            global $smarty;
            global $cookie;
            $this->context->smarty = $smarty;
            $this->context->cookie = $cookie;
        }
        $this->ps15 = !$this->ps13 && !$this->ps14 && version_compare(_PS_VERSION_, '1.6.0.0', '<') ? 1 : 0;
    }

    public function _registerHooks($register = true)
    {
        $hooks = array(
            'header',
            'backOfficeHeader',
            'displayBackOfficeHeader',
            'displayHeader',
        );
        if (count($hooks) > 0) {
            foreach ($hooks as $hook) {
                if ($register && !$this->registerHook($hook) || !$register && !$this->unregisterHook($hook)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');

        return
            parent::install()
            && $this->_installConfigs();
    }

    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');

        return
            parent::uninstall()
            && $this->_uninstallConfigs();
    }

    public function _installConfigs()
    {
        if ($configs = $this->getConfigs()) {
            $languages = Language::getLanguages(false);
            foreach ($configs as $key => $config) {
                $global = isset($config['global']) && $config['global'] ? 1 : 0;
                if (isset($config['lang']) && $config['lang']) {
                    $values = array();
                    foreach ($languages as $l) {
                        $values[$l['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    $this->setFields($key, $global, $values, true);
                } else {
                    $this->setFields($key, $global, isset($config['default']) ? $config['default'] : '', true);
                }
            }
        }

        return true;
    }

    public function _uninstallConfigs()
    {
        if ($configs = $this->getConfigs()) {
            foreach ($configs as $key => $config) {
                if (!Configuration::deleteByName($key)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getConfigs()
    {
        if (!self::$_configs) {
            if ($this->ps14 || $this->ps13) {
                $domainUrl = Tools::getHttpHost(true) . __PS_BASE_URI__;
            } else {
                $ssl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
                $shop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));
                $domainUrl = ($ssl ? 'https://' . $shop->domain_ssl : 'http://' . $shop->domain) . $shop->physical_uri . $shop->virtual_uri;
            }
            self::$_configs = array(
                'ETS_MC_ENABLED' => array(
                    'type' => $this->ps15 || $this->ps14 || $this->ps13 ? 'radio' : 'switch',
                    'is_bool' => true, //retro compat 1.5
                    'label' => $this->l('Enable Connector'),
                    'name' => 'ETS_MC_ENABLED',
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
                    'global' => true,
                    'default' => 1,
                ),
                'ETS_MC_DOMAIN' => array(
                    'type' => 'text',
                    'label' => $this->l('Store URL'),
                    'name' => 'ETS_MC_DOMAIN',
                    'col' => 5,
                    'form_group_class' => 'ets_mc_domain',
                    'class' => 'ets_mc_copied',
                    'global' => true,
                    'default' => $domainUrl,
                ),
                'ETS_MC_ACCESS_TOKEN' => array(
                    'type' => 'text',
                    'label' => $this->l('Access token'),
                    'name' => 'ETS_MC_ACCESS_TOKEN',
                    'col' => 5,
                    'form_group_class' => 'ets_mc_token',
                    'class' => 'ets_mc_copied',
                    'global' => true,
                    'default' => Tools::strtolower(Tools::passwdGen(10)),
                ),
            );
        }
        return self::$_configs;
    }

    public function getContent()
    {
        $this->token = trim(Tools::getValue('token'));
        self::$currentIndex = ($this->ps14 || $this->ps13 ? 'index.php?tab=AdminModules&token=' . $this->token : $this->context->link->getAdminLink('AdminModules')) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;

        if (Tools::isSubmit('submitConfig')) {
            $this->_postConfig();
        }

        $this->setDisplayHeader();
        $this->renderForm();

        return $this->content;
    }

    public function setDisplayHeader()
    {
        $medias = array(
            'css' =>array($this->_path . 'views/css/back.css'),
            'js' =>array($this->_path . 'views/js/back.js')
        );
        $this->context->smarty->assign($medias);
        $this->content .= $this->ps13 ? $this->setMedia($medias) : $this->display(__FILE__, 'views/templates/hook/bo-head.tpl');
    }

    protected function renderForm()
    {
        if ($configs = $this->getConfigs()) {

            $ps_helper_version = version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 1 : 0;

            $fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Settings'),
                        'icon' => 'icon-AdminAdmin',
                    ),
                    'input' => $configs,
                    'submit' => array(
                        'title' => $this->l('Save'),
                    )
                ),
            );

            $fields_value = $this->getFieldsValues();

            $tpl_vars = array(
                'fields_value' => $fields_value,
                'languages' => $ps_helper_version ? $this->context->controller->getLanguages() : Language::getLanguages(),
                'id_language' => $this->context->language->id,
            );

            // Render form:
            if ($ps_helper_version) {
                // Ps > 1.4
                $helper = new HelperForm();
                $helper->show_toolbar = false;
                $helper->table = $this->table;
                $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
                $helper->default_form_language = $lang->id;
                $helper->module = $this;
                $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
                $helper->identifier = $this->identifier;
                $helper->submit_action = 'submitConfig';
                $helper->currentIndex = self::$currentIndex;
                $helper->token = $this->token;
                $helper->tpl_vars = $tpl_vars;

                $this->content .= $helper->generateForm(array($fields_form));
            } else {
                // Ps <= 1.4
                $tpl_vars = array_merge(
                    $tpl_vars,
                    array(
                        'fields' => array($fields_form),
                        'submit_action' => 'submitConfig',
                        'current' => self::$currentIndex,
                        'token' => $this->token,
                    )
                );
                $this->context->smarty->assign($tpl_vars);
                $this->content .= $this->displayFormSettings($fields_form, $fields_value);
            }
        }
    }

    public function getFieldsValues()
    {
        $fields = array();
        $configs = $this->getConfigs();
        if ($configs) {
            $languages = Language::getLanguages(false);
            if (Tools::isSubmit('submitConfig')) {
                foreach ($configs as $config) {
                    $key = $config['name'];
                    if (isset($config['lang']) && $config['lang']) {
                        foreach ($languages as $l) {
                            $fields[$key][$l['id_lang']] = Tools::getValue($key . '_' . $l['id_lang'], isset($config['default']) ? $config['default'] : '');
                        }
                    } elseif ($config['type'] == 'select' && isset($config['multiple']) && $config['multiple']) {
                        $fields[$key . ($config['type'] == 'select' ? '[]' : '')] = Tools::getValue($key, array());
                    } elseif ($config['type'] == 'group' || $config['type'] == 'checkboxes') {
                        $fields[$key] = Tools::getValue($key, array());
                    } else
                        $fields[$key] = Tools::getValue($key, isset($config['default']) ? $config['default'] : '');
                }
            } else {
                foreach ($configs as $config) {
                    $key = $config['name'];
                    $global = !empty($config['global']) ? 1 : 0;
                    if (isset($config['lang']) && $config['lang']) {
                        foreach ($languages as $l) {
                            $fields[$key][$l['id_lang']] = $this->getFields($key, $global, $l['id_lang']);
                        }
                    } elseif ($config['type'] == 'select' && isset($config['multiple']) && $config['multiple']) {
                        $fields[$key . ($config['type'] == 'select' ? '[]' : '')] = ($result = $this->getFields($key, $global)) != '' ? explode(',', $result) : array();
                    } elseif ($config['type'] == 'group' || $config['type'] == 'checkboxes') {
                        $fields[$key] = ($result = $this->getFields($key, $global)) != '' ? explode(',', $result) : array();
                    } else
                        $fields[$key] = $this->getFields($key, $global);
                }
            }
        }

        return $fields;
    }

    public function getFields($key, $global = false, $idLang = null)
    {
        return $global && !$this->ps14 && !$this->ps13 ? Configuration::getGlobalValue($key, $idLang) : Configuration::get($key, $idLang);
    }

    protected function _postConfig()
    {
        if ($configs = $this->getConfigs()) {

            $languages = Language::getLanguages(false);
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');

            foreach ($configs as $key => $config) {
                if (isset($config['lang']) && $config['lang']) {
                    if (isset($config['required']) && $config['required'] && $config['type'] != 'switch' && trim(Tools::getValue($key . '_' . $id_lang_default) == '')) {
                        $this->_errors[] = $config['label'] . ' ' . $this->l('is required');
                    }
                } else {
                    if (isset($config['required']) && $config['required'] && $config['type'] != 'switch' && trim(Tools::getValue($key) == '')) {
                        $this->_errors[] = $config['label'] . ' ' . $this->l('is required');
                    }
                    if (isset($config['validate']) && method_exists('Validate', $config['validate'])) {
                        $validate = $config['validate'];
                        if (!Validate::$validate(trim(Tools::getValue($key))))
                            $this->_errors[] = $config['label'] . ' ' . $this->l('is invalid');
                        unset($validate);
                    } elseif (!Validate::isCleanHtml(trim(Tools::getValue($key)))) {
                        $this->_errors[] = $config['label'] . ' ' . $this->l('is invalid');
                    }
                }
            }

            if (!$this->_errors) {
                foreach ($configs as $key => $config) {
                    $global = !empty($config['global']) ? 1 : 0;
                    if (isset($config['lang']) && $config['lang']) {
                        $values = array();
                        foreach ($languages as $lang) {
                            if ($config['type'] == 'switch')
                                $values[$lang['id_lang']] = (int)trim(Tools::getValue($key . '_' . $lang['id_lang'])) ? 1 : 0;
                            else
                                $values[$lang['id_lang']] = trim(Tools::getValue($key . '_' . $lang['id_lang'])) ? trim(Tools::getValue($key . '_' . $lang['id_lang'])) : trim(Tools::getValue($key . '_' . $id_lang_default));
                        }
                        $this->setFields($key, $global, $values, true);
                    } else {
                        if ($config['type'] == 'switch') {
                            $this->setFields($key, $global, (int)trim(Tools::getValue($key)) ? 1 : 0, true);
                        } elseif ($config['type'] == 'group' || $config['type'] == 'checkboxes' || $config['type'] == 'select' && isset($config['multiple']) && $config['multiple']) {
                            $this->setFields($key, $global, implode(',', Tools::getValue($key, array())), true);
                        } else
                            $this->setFields($key, $global, trim(Tools::getValue($key)), true);
                    }
                }
            }
            if (is_array($this->_errors) && count($this->_errors) > 0) {
                $this->content .= $this->displayError($this->_errors);
            } else {
                Tools::redirectAdmin(self::$currentIndex . '&conf=4');
            }
        }
    }

    public function setFields($key, $global, $values, $html = false)
    {
        return $global && !$this->ps14 && !$this->ps13 ? Configuration::updateGlobalValue($key, $values, $html) : Configuration::updateValue($key, $values, $html);
    }

    public function setMedia($medias)
    {
        if (!is_array($medias) ||
            count($medias) <= 0
        ) {
            return '';
        }
        $html = '';
        foreach ($medias as $type => $media) {
            if (is_array($media) && count($media) > 0) {
                foreach ($media as $link) {
                    $html .= $type !== 'js' ? '<link type="text/css" rel="stylesheet" href="' . $link . '">' : '<script type="text/javascript" src="' . $link . '"></script>';
                }
            }
        }
        return $html;
    }

    public function displayFormSettings($fields_form, $fields_value)
    {
        $html_field_sets = '';
        if (isset($fields_form['form'])
            && is_array($fields_form['form'])
            && count($fields_form['form']) > 0
        ) {
            $fields = isset($fields_form['form']['input']) ? $fields_form['form']['input'] : array();
            if (is_array($fields)
                && count($fields) > 0
            ) {
                foreach ($fields as $name => $input) {
                    $html_field_sets .= '
                        <label>' . (isset($input['label']) ? $input['label'] : '') . '</label>
                        <div class="margin-form">
                    ';
                    if (isset($input['type']) && trim($input['type']) == 'radio') {
                        $html_field_sets .= '
                        <input type="radio" name="' . $name . '" id="active_on" value="1" ' . (isset($fields_value[$name]) && $fields_value[$name] > 0 ? 'checked="checked"' : '') . '>
                        <label for="active_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '"></label>
                        <input type="radio" name="' . $name . '" id="active_off" value="0" ' . (isset($fields_value[$name]) && !$fields_value[$name] ? 'checked="checked"' : '') . '>
                        <label for="active_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '"></label>
                    ';
                    } elseif (isset($input['type']) && trim($input['type']) == 'text') {
                        $html_field_sets .= '<div class="' . $name . '_group">';
                        $html_field_sets .= '<input type="text" name="' . $name . '" id="' . $name . '" value="' . (isset($fields_value[$name]) ? $fields_value[$name] : '') . (isset($input['class']) ? '" class="' . $input['class'] . '"' : '') . '">';
                        $html_field_sets .= '<span class="data_copied">' . $this->l('Copied') . '</span>
                        </div>
                        ' . (trim($name) == 'ETS_MC_ACCESS_TOKEN' ? '<span class="input-group-btn">
                            <a id="ets_mc_gencode" class="btn btn-default" href="#">
                                <svg class="w_14 h_14" width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M504.971 359.029c9.373 9.373 9.373 24.569 0 33.941l-80 79.984c-15.01 15.01-40.971 4.49-40.971-16.971V416h-58.785a12.004 12.004 0 0 1-8.773-3.812l-70.556-75.596 53.333-57.143L352 336h32v-39.981c0-21.438 25.943-31.998 40.971-16.971l80 79.981zM12 176h84l52.781 56.551 53.333-57.143-70.556-75.596A11.999 11.999 0 0 0 122.785 96H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12zm372 0v39.984c0 21.46 25.961 31.98 40.971 16.971l80-79.984c9.373-9.373 9.373-24.569 0-33.941l-80-79.981C409.943 24.021 384 34.582 384 56.019V96h-58.785a12.004 12.004 0 0 0-8.773 3.812L96 336H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h110.785c3.326 0 6.503-1.381 8.773-3.812L352 176h32z"></path></svg>
                            ' . $this->l('Generate') . '</a>
                        </span>' : '') . '';
                    }
                    $html_field_sets .= '
                        </div>
                        <div class="clear"></div>
                    ';
                }
            }
        }
        return '
            <form id="configuration_form" action="' . self::$currentIndex . '&token=' . $this->token . '" method="post" enctype="multipart/form-data">
                <fieldset id="fieldset_0">
                    <legend>' . (isset($fields_form['legend']['title']) ? $fields_form['legend']['title'] : $this->l('Settings')) . '</legend>
                    ' . $html_field_sets . '
                    <div class="margin-form">
                        <input type="submit" id="form_submit_btn" value="' . $this->l('Save') . '" name="submitConfig">
                    </div>
                </fieldset>
            </form>
        ';
    }
}
