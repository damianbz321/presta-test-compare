<?php
/**
* 2012-2020 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Pop-Ups Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2020 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD PopUps Pro - PrestaShop 1.6.x and 1.7.x Module
* @version   1.3.1
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      15-12-2020
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(dirname(__FILE__).'/models/PopUpModel.php');
require_once(_PS_MODULE_DIR_ . 'pdpopupspro/lib/Api.php');

class PdPopUpsPro extends Module
{
    const GUEST_NOT_REGISTERED = 3;
    const CUSTOMER_NOT_REGISTERED = 4;
    const GUEST_REGISTERED = 1;
    const CUSTOMER_REGISTERED = 2;


    public $_html = '';

    public function __construct()
    {
        $this->name = 'pdpopupspro';
        $this->tab = 'front_office_features';
        $this->version = '1.3.6';
        $this->author = 'PrestaDev.pl';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '415227f51058782abd5edd56c13807bc';
        $this->secure_key = Tools::encrypt(_COOKIE_KEY_);
        $this->module_dir = _MODULE_DIR_.$this->name.'/';
        $this->salt = Configuration::get('PDPOPUPSPRO_SALT');
        $this->controllers = array('verification', 'ajax');

        parent::__construct();

        $this->displayName = $this->l('PD Pop-up Pro');
        $this->description = $this->l('Show custom popup with optional newsletter subscription and time scheduling in your prestashop store.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->uri_location = _MODULE_DIR_.$this->name.'/';


        $this->freshmail_api_key = Configuration::get('PD_PP_FM_API_KEY');
        $this->freshmail_api_secret = Configuration::get('PD_PP_FM_API_SECRET');
        $this->freshmail_hash_list = Configuration::get('PD_PP_FM_HASH_LIST');
        $this->freshmail_enabled = Configuration::get('PD_PP_FM_ENABLED');
        $this->freshmail_confirm = Configuration::get('PD_PP_FM_CONFIRM');
        $this->freshmail_status = Configuration::get('PD_PP_FM_STATUS');

        $this->confirm_email = Configuration::get('PD_PP_SEND_CONFIRMATION_EMAIL');
        $this->skip_validation = Configuration::get('PD_PP_SEND_SKIP_VALIDATION');

        $this->ps_version_16 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.6', '=')) ? true : false;
        $this->ps_version_17 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.7', '=')) ? true : false;
        $this->ps_version_8 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '8.0', '>=')) ? true : false;
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install()
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayCustomFooter')
            || !$this->registerHook('displayFooter')
            || !$this->registerHook('actionCustomerAccountAdd')
            || !$this->registerHook('displayBackOfficeHeader')
            || !$this->registerHook('actionObjectPopUpModelAddAfter')
            || !$this->registerHook('actionObjectPopUpModelUpdateAfter')
            || !$this->registerHook('actionObjectPopUpModelDeleteAfter')
            || !Configuration::updateValue('PDPOPUPSPRO_SALT', Tools::passwdGen(16))
            || !$this->installModuleTabs()
            || !PopUpModel::createTables()
            || !Configuration::updateValue('PD_PP_SEND_SKIP_VALIDATION', 1)
            || !Configuration::updateValue('PD_PP_SEND_CONFIRMATION_EMAIL', 1)
            || !$this->installFixtures()) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        $this->uninstallModuleTab('AdminPopUpsPro');
        $this->uninstallModuleTab('AdminPopUpsProNew');
        PopUpModel::dropTables();

        return parent::uninstall();
    }

    public function installFixtures()
    {
        $url_path = Tools::getHttpHost(true).__PS_BASE_URI__.'modules/'.$this->name.'/views/img/';
        $data = array(
                'id_shop' => Context::getContext()->shop->id,
                'active' => 1,
                'show_title' => 1,
                'name' => array(
                    'pl' => 'Przykładowy tytuł pop-upa',
                    'en' => 'Sample pop-up',
                ),
                'content_newsletter' => array(
                    'pl' => '<h2>Newsletter - bądź na bieżąco</h2><ul><li>Zapisując się do naszego newslettera możesz korzystać z wyjątkowych promocji tylko dla subskrybentów</li><li>Będziesz informowany na bieżąco o naszych nowościach produktowych</li></ul>',
                    'en' => '<h2>Newsletter - stay up to date</h2><ul><li>By subscribing to our newsletter, you can use a special promotion for subscribers only</li><li>You will be kept informed regarding our new products</li></ul>',
                ),
                'content' => array(
                    'pl' => '<p><strong>Lorem ipsum dolor sit amet, consectetur adipiscing elit</strong>, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>',
                    'en' => '<p><strong>Lorem ipsum dolor sit amet, consectetur adipiscing elit</strong>, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>',
                                            ),
                'bg_image' => $url_path.'background_1.jpg',
                );

        $popup = new PopUpModel(1);

        $langs = Language::getLanguages(false);

        foreach ($data as $k => $v) {
            if ($k === 'name') {
                foreach ($langs as $lang) {
                    if ($lang['iso_code'] === 'pl') {
                        $popup->name[$lang['id_lang']] = $v['pl'];
                    } elseif ($lang['iso_code'] === 'en') {
                        $popup->name[$lang['id_lang']] = $v['en'];
                    } else {
                        $popup->name[$lang['id_lang']] = $v['en'];
                    }
                }
            } elseif ($k === 'content') {
                foreach ($langs as $lang) {
                    if ($lang['iso_code'] === 'pl') {
                        $popup->content[$lang['id_lang']] = $v['pl'];
                    } elseif ($lang['iso_code'] === 'en') {
                        $popup->content[$lang['id_lang']] = $v['en'];
                    } else {
                        $popup->content[$lang['id_lang']] = $v['en'];
                    }
                }
            } elseif ($k === 'content_newsletter') {
                foreach ($langs as $lang) {
                    if ($lang['iso_code'] === 'pl') {
                        $popup->content_newsletter[$lang['id_lang']] = $v['pl'];
                    } elseif ($lang['iso_code'] === 'en') {
                        $popup->content_newsletter[$lang['id_lang']] = $v['en'];
                    } else {
                        $popup->content_newsletter[$lang['id_lang']] = $v['en'];
                    }
                }
            } else {
                $popup->$k = $v;
            }
        }
        $popup->page = '2';
        if ($popup->save()) {
            $this->generateCss((int)$popup->id);
        }

        return true;
    }


    private function installModuleTabs()
    {
        $tabs = array(
            'AdminPopUpsProNew' => array(
                'en' => 'Add new / View list',
                'pl' => 'Dodaj nowy / przeglądaj'
            )
        );

        $deflang = (int)Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages();

        // main tab only in default language
        $mainTab = $this->installModuleTab('AdminPopUpsPro', array($deflang => 'Pop-ups Pro'), 0);

        if ($mainTab) {
            foreach ($tabs as $class => $tab) {
                // tabs names as array where key is an a id_language
                $tabNamesArray = array();
                foreach ($tab as $tabIso => $tabName) {
                    foreach ($languages as $language) {
                        if ($language['iso_code'] == $tabIso) {
                            $tabNamesArray[$language['id_lang']] = $tabName;
                        }
                    }
                }

                // we must add tab in default language
                if (!key_exists($deflang, $tabNamesArray)) {
                    $tabNamesArray[$deflang] = $tab['en'];
                }

                $this->installModuleTab($class, $tabNamesArray, $mainTab);
            }
        }
        return true;
    }

    private function installModuleTab($tabClass, $tabName, $id_tab_parent)
    {
        file_put_contents('../img/t/'.$tabClass.'.gif', Tools::file_get_contents('logo.gif'));

        $tab = new Tab();
        $tab->name = $tabName;
        $tab->class_name = $tabClass;
        $tab->module = $this->name;
        $tab->id_parent = $id_tab_parent;

        if (!$tab->save()) {
            return false;
        }

        return $tab->id;
    }

    private function uninstallModuleTab($tabClass)
    {
        $id_tab = Tab::getIdFromClassName($tabClass);
        if ($id_tab != 0) {
            $tab = new Tab($id_tab);
            $tab->delete();
            return true;
        }
        return false;
    }

    public function getContent()
    {
        if (Tools::isSubmit('saveConfiguration')) {
            $this->postProcess();
        } else {
            $this->_html .= '<br />';
        }

        $this->_html .= '<h2>'.$this->displayName.' (v'.$this->version.')</h2><p>'.$this->description.'</p>';
        $this->_html .= $this->renderForm();

        return $this->_html;
    }

    private function postProcess()
    {
        if (Tools::isSubmit('saveConfiguration')) {
            Configuration::updateValue('PD_PP_SEND_CONFIRMATION_EMAIL', Tools::getValue('PD_PP_SEND_CONFIRMATION_EMAIL'));
            Configuration::updateValue('PD_PP_SEND_SKIP_VALIDATION', Tools::getValue('PD_PP_SEND_SKIP_VALIDATION'));

            Configuration::updateValue('PD_PP_FM_STATUS', (bool)Tools::getValue('PD_PP_FM_STATUS'));
            Configuration::updateValue('PD_PP_FM_CONFIRM', (bool)Tools::getValue('PD_PP_FM_CONFIRM'));
            Configuration::updateValue('PD_PP_FM_API_KEY', Tools::getValue('PD_PP_FM_API_KEY'));
            Configuration::updateValue('PD_PP_FM_API_SECRET', Tools::getValue('PD_PP_FM_API_SECRET'));
            Configuration::updateValue('PD_PP_FM_HASH_LIST', Tools::getValue('PD_PP_FM_HASH_LIST'));
            Configuration::updateValue('PD_PP_FM_ENABLED', Tools::getValue('PD_PP_FM_ENABLED'));

            $this->_html .= $this->displayConfirmation($this->l('Setting was updated'));
        }
    }

    public function renderForm()
    {
        $switch = version_compare(_PS_VERSION_, '1.6.0', '>=') ? 'switch' : 'radio';

        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Module configuration'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => $switch,
                        'label' => $this->l('Skip validiation'),
                        'name' => 'PD_PP_SEND_SKIP_VALIDATION',
                        'is_bool' => true,
                        'class' => 't',
                        'desc' => $this->l('Option allows to disable validation proccess on subscription to newsletter'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => $switch,
                        'label' => $this->l('Confirmation email'),
                        'name' => 'PD_PP_SEND_CONFIRMATION_EMAIL',
                        'is_bool' => true,
                        'class' => 't',
                        'desc' => $this->l('Option allows to disable confirmation email send by module after subscription proccess'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'name' => 'saveConfiguration',
                    'title' => $this->l('Save'),
                )
            ),
        );

        $api = new FreshmailApi();
        $lists = $api->getAllList();


        $fields_form_2 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Freshmail configuration'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Freshmail subscription'),
                        'name' => 'PD_PP_FM_ENABLED',
                        'desc' => $this->l('Enable Freshmail APi subscription required to provide Ap i key and Api secret and after that select subscription list for subscription in to'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Freshmail confirm'),
                        'name' => 'PD_PP_FM_CONFIRM',
                        'desc' => $this->l('Send confirmation email from Freshmail to activate subscription in list'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Activation ststus'),
                        'desc' => $this->l('Choose subscription status when subscribint to Freshmail newsletter'),
                        'name' => 'PD_PP_FM_STATUS',
                        'options' => array(
                            'query' => array(
                                array(
                                    'action' => 1,
                                       'name' => $this->l('Active')
                                ),
                                array(
                                    'action' => 2,
                                    'name' => $this->l('To activation')
                                ),
                            ),
                                'id' => 'action',
                                'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Freshmail api key'),
                        'name' => 'PD_PP_FM_API_KEY',
                        'required' => true,
                        'class' => 'fixed-width',
                        'desc' => $this->l('Freshmail API key for signup to freshmail')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Freshmail secreet key'),
                        'name' => 'PD_PP_FM_API_SECRET',
                        'required' => true,
                        'class' => 'fixed-width',
                        'desc' => $this->l('Freshmail API secreet key for signup to freshmail')
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Freshmail subscription lists'),
                        'desc' => $this->l('Select Freshmail subscriber list for subscription created in Freshmail account'),
                        'name' => 'PD_PP_FM_HASH_LIST',
                        'multiple' => false,
                        'required' => true,
                        'options' => array(
                            'query' => $lists,
                            'id' => 'key',
                            'name' => 'name'
                        ),
                    ),
                ),
                'submit' => array(
                    'name' => 'saveConfiguration',
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form_1, $fields_form_2));
    }

    public function getConfigFieldsValues()
    {
        $return = array();
        $return['PD_PP_SEND_CONFIRMATION_EMAIL'] = Configuration::get('PD_PP_SEND_CONFIRMATION_EMAIL');
        $return['PD_PP_SEND_SKIP_VALIDATION'] = Configuration::get('PD_PP_SEND_SKIP_VALIDATION');
        $return['PD_PP_FM_STATUS'] = Tools::getValue('PD_PP_FM_STATUS', Configuration::get('PD_PP_FM_STATUS'));
        $return['PD_PP_FM_CONFIRM'] = Tools::getValue('PD_PP_FM_CONFIRM', Configuration::get('PD_PP_FM_CONFIRM'));
        $return['PD_PP_FM_API_KEY'] = Tools::getValue('PD_PP_FM_API_KEY', Configuration::get('PD_PP_FM_API_KEY'));
        $return['PD_PP_FM_API_SECRET'] = Tools::getValue('PD_PP_FM_API_SECRET', Configuration::get('PD_PP_FM_API_SECRET'));
        $return['PD_PP_FM_HASH_LIST'] = Tools::getValue('PD_PP_FM_HASH_LIST', Configuration::get('PD_PP_FM_HASH_LIST'));
        $return['PD_PP_FM_ENABLED'] = Tools::getValue('PD_PP_FM_ENABLED', Configuration::get('PD_PP_FM_ENABLED'));
        return $return;
    }

    public static function getBgRepeatCssVal($value)
    {
        switch ($value) {
            case 3:
                $repeat_option = 'repeat';
                break;
            case 2:
                $repeat_option = 'repeat-x';
                break;
            case 1:
                $repeat_option = 'repeat-y';
                break;
            default:
                $repeat_option = 'no-repeat';
        }
        return $repeat_option;
    }

    public function generateCss($id_pdpopupspro)
    {
        $css = '';
        $popup = new PopUpModel($id_pdpopupspro);
        $id_pdpopupspro = (int)$popup->id;

        // Pop-up height calcualtions = newsletter height + content height + header height pop-up higher
        $height = 30; // base height (bootom strip not show)

        $height_content    = (int)$popup->height_content;
        $height_content_newsletter    = (int)$popup->height_content_newsletter;

        if ($popup->newsletter && $popup->height_content_newsletter > 0) {
            $height += $height_content_newsletter;
        }

        if ($height_content > 0) {
            $height += $height_content;
        }

        $show_title = $popup->show_title;
        if ($show_title) {
            $height += 43;
        }

        $width_popup = (int)$popup->width_popup;


        $width_hidden = (int)$popup->width_hidden;
        $height_hidden    = (int)$popup->height_hidden;

        $bg_color = $popup->bg_color;
        $bg_image = $popup->bg_image;
        $bg_repeat = self::getBgRepeatCssVal($popup->bg_repeat);
        $txt_color = $popup->txt_color;
        $new_bg_color = $popup->new_bg_color;
        $new_bg_image = $popup->new_bg_image;
        $new_bg_repeat = self::getBgRepeatCssVal($popup->new_bg_repeat);
        $new_txt_color = $popup->new_txt_color;
        $input_txt_color = $popup->input_txt_color;
        $input_bg_color    = $popup->input_bg_color;

        // Hide on certain width and height
        if ($width_hidden > 0) {
            $css .= '
            @media (max-width: '.($width_hidden).'px) {
                #pdpopupspro, #pdpopupspro-overlay{
                    display: none !important;
                }
            }';
        }

        if ($height_hidden > 0) {
            $css .= '
            @media (max-height: '.($height_hidden).'px) {
                #pdpopupspro, #pdpopupspro-overlay{
                    display: none !important;
                }
            }';
        }

        if ($show_title == 0) {
            $css .= '
            #pdpopupspro .pdpopupspro-content {
                -webkit-border-top-left-radius: 5px;
                -webkit-border-top-right-radius: 5px;
                -moz-border-radius-topleft: 5px;
                -moz-border-radius-topright: 5px;
                border-top-left-radius: 5px;
                border-top-right-radius: 5px;
            }';
        }

        $css .= '
        #pdpopupspro {
            width: '.$width_popup.'px;

        }

        #pdpopupspro .pdpopupspro-content {
            height: inherit;
            min-height: '.$height_content.'px;
            color: '.$txt_color.';
            background-color: '.($bg_color).';
            background-image: url('.$bg_image.') !important;
            background-repeat: '.$bg_repeat.' !important;
        }

        @media (max-width: 570px) {
            #pdpopupspro .pdpopupspro-content { height:200px;}

            #pdpopupspro .promo-text > p {margin: 0 auto!important;}

            #pdpopupspro .pdpopupspro-newsletter-form .promo-text h2 {
                font-size: 21px;
                font-family: Helvetica;
                line-height: 25px;
                letter-spacing: 0px;
                font-weight: 300;
            }
        }



        #pdpopupspro .pdpopupspro-newsletter-form {
            min-height: '.$height_content_newsletter.'px;
            color: '.$new_txt_color.';
            background-color: '.$new_bg_color.';
            background-image: url('.$new_bg_image.') !important;
            background-repeat: '.$new_bg_repeat.' !important;
        }

        #pdpopupspro .pdpopupspro-newsletter-form .newsletter-input {
            color: '.$input_txt_color.';
            background-color: '.$input_bg_color.';
        }

        #pdpopupspro .pdpopupspro-newsletter-form .newsletter-inputl:-moz-placeholder {
            color: '.$input_txt_color.' !important;
        }
        #pdpopupspro .pdpopupspro-newsletter-form .newsletter-input::-moz-placeholder {
            color: '.$input_txt_color.' !important;
        }
        #pdpopupspro .pdpopupspro-newsletter-form .newsletter-input:-ms-input-placeholder {
            color: '.$input_txt_color.' !important;
        }
        #pdpopupspro .pdpopupspro-newsletter-form .newsletter-input::-webkit-input-placeholder {
            color: '.$input_txt_color.' !important;
        }


        ';

        //d($css);

        // Save css config per id_popup configuration
        $file_name = $this->local_path.'views/css/pdpopupspro_id_popup_'.(int)$id_pdpopupspro.'.css';
        $file = fopen($file_name, 'w');

        if ($file) {
            fwrite($file, $css);
            fclose($file);
        } else {
            Tools::displayError('Unable to create css file for Your configuration').' "'.addslashes($file_name).'"';
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS(($this->_path).'views/css/pdpopupspro_admin.css');
    }

    public function getMaxIdPopUpForDisplay($id_shop, $page)
    {
        // get page type for sql query
        $page_type = self::getPageTypeForSql($page);

        $id_category = (int)Tools::getValue('id_category');
        if (!$id_category || !Validate::isUnsignedId($id_category)) {
            $id_category = false;
        }

        $id_manufacturer = (int)Tools::getValue('id_manufacturer');
        if (!$id_manufacturer || !Validate::isUnsignedId($id_manufacturer)) {
            $id_manufacturer = false;
        }

        $id_product = (int)Tools::getValue('id_product');
        if ($id_product && Validate::isUnsignedId($id_product)) {
            $product = new Product($id_product);
            $id_manufacturer = $product->id_manufacturer;
        } else {
            $id_manufacturer = false;
        }


        $date_time_now = ' \''.date('Y-m-d h:i:s').'\' ';
        $date_null = "'0000-00-00 00:00:00'";

        if ($id_manufacturer) {

            // get only highest id of popup for display for that same type of page and id_manufacturer and if is date range is in time_now
            $sql = 'SELECT p.`id_pdpopupspro`
                    FROM `'._DB_PREFIX_.'pdpopupspro` p
                    WHERE p.`id_shop` = '.(int)$id_shop.'
                    AND p.from <= '.$date_time_now.'
                    AND p.to >= '.$date_time_now.'
                    AND p.active = 1
                    '.($page_type ? ' AND find_in_set("'.(int)$page_type.'", p.`page`) <> 0' : '').'
                    '.((($page_type == 3 || $page_type == 17) && $id_manufacturer) ? ' AND find_in_set("'.(int)$id_manufacturer.'", p.`selected_manufacturers`) <> 0' : '').'
                    ORDER BY p.`id_pdpopupspro` DESC';

            $popup_date_range_and_type_manu = Db::getInstance()->getValue($sql);

            if ($popup_date_range_and_type_manu && $id_manufacturer) {
                return $popup_date_range_and_type_manu;
            }

            // get only highest id of popup for display for that same type of page and id_manufacturer and if is date range is not set
            $sql = 'SELECT p.`id_pdpopupspro`
                    FROM `'._DB_PREFIX_.'pdpopupspro` p
                    WHERE p.`id_shop` = '.(int)$id_shop.'
                    AND p.from = '.$date_null.'
                    AND p.to = '.$date_null.'
                    AND p.active = 1
                    AND find_in_set("'.(int)$id_manufacturer.'", p.`selected_manufacturers`) <> 0
                    '.($page_type ? ' AND find_in_set("'.(int)$page_type.'", p.`page`) <> 0' : '').'
                    '.((($page_type == 3 || $page_type == 17) && $id_manufacturer) ? ' AND find_in_set("'.(int)$id_manufacturer.'", p.`selected_manufacturers`) <> 0' : '').'
                    ORDER BY p.`id_pdpopupspro` DESC';



            $popup_no_date_and_type_man = Db::getInstance()->getValue($sql);
            //dump($popup_no_date_and_type_man );

            if ($popup_no_date_and_type_man && $id_manufacturer) {
                return $popup_no_date_and_type_man;
            }

        } elseif ($id_category) {

            // get only highest id of popup for display for that same type of page and if is date range is in time_now
            $sql = 'SELECT p.`id_pdpopupspro`
                    FROM `'._DB_PREFIX_.'pdpopupspro` p
                    WHERE p.`id_shop` = '.(int)$id_shop.'
                    AND p.from <= '.$date_time_now.'
                    AND p.to >= '.$date_time_now.'
                    AND p.active = 1
                    '.($page_type ? ' AND find_in_set("'.(int)$page_type.'", p.`page`) <> 0' : '').'
                    '.(($page_type == 4 && $id_category) ? ' AND find_in_set("'.(int)$id_category.'", p.`selected_categories`) <> 0' : '').'
                    ORDER BY p.`id_pdpopupspro` DESC';

            $popup_date_range_and_type_cat = Db::getInstance()->getValue($sql);


            if ($popup_date_range_and_type_cat && $id_category) {
                return $popup_date_range_and_type_cat;
            }

            // get only highest id of popup for display for that same type of page and id_category and if is date range is not set
            $sql = 'SELECT p.`id_pdpopupspro`
                    FROM `'._DB_PREFIX_.'pdpopupspro` p
                    WHERE p.`id_shop` = '.(int)$id_shop.'
                    AND p.from = '.$date_null.'
                    AND p.to = '.$date_null.'
                    AND p.active = 1
                    '.($page_type ? ' AND find_in_set("'.(int)$page_type.'", p.`page`) <> 0' : '').'
                     '.(($page_type == 4 && $id_category) ? ' AND find_in_set("'.(int)$id_category.'", p.`selected_categories`) <> 0' : '').'
                    ORDER BY p.`id_pdpopupspro` DESC';

            $popup_no_date_and_type_cat = Db::getInstance()->getValue($sql);
            //d($popup_no_date_and_type_cat);

            if ($popup_no_date_and_type_cat && $id_category) {
                return $popup_no_date_and_type_cat;
            }

        } elseif (!in_array($page_type, [3,4,17])) {

            // get only highest id of popup for display for all pages and if is date range is in time_now
            $sql = 'SELECT p.`id_pdpopupspro`
                    FROM `'._DB_PREFIX_.'pdpopupspro` p
                    WHERE p.`id_shop` = '.(int)$id_shop.'
                    AND p.from <= '.$date_time_now.'
                    AND p.to >= '.$date_time_now.'
                    AND p.active = 1
                    AND p.`page` = 1
                    ORDER BY p.`id_pdpopupspro` DESC';

            $popup_date_range_all_type = Db::getInstance()->getValue($sql);

            if ($popup_date_range_all_type) {
                return $popup_date_range_all_type;
            }

            // get only highest id of popup for display for all pages and if is date range is not set
            $sql = 'SELECT p.`id_pdpopupspro`
                    FROM `'._DB_PREFIX_.'pdpopupspro` p
                    WHERE p.`id_shop` = '.(int)$id_shop.'
                    AND p.from = '.$date_null.'
                    AND p.to = '.$date_null.'
                    AND p.active = 1
                    AND p.`page` = 1
                    ORDER BY p.`id_pdpopupspro` DESC';

            $popup_no_date_range_all_type = Db::getInstance()->getValue($sql);

            if ($popup_no_date_range_all_type) {
                return $popup_no_date_range_all_type;
            }


            // get only highest id of popup for display for all pages and and if is date range is set
            $sql = 'SELECT p.`id_pdpopupspro`
                    FROM `'._DB_PREFIX_.'pdpopupspro` p
                    WHERE p.`id_shop` = '.(int)$id_shop.'
                    AND p.from <= '.$date_time_now.'
                    AND p.to >= '.$date_time_now.'
                    AND p.active = 1
                    '.($page_type ? ' AND find_in_set("'.(int)$page_type.'", p.`page`) <> 0' : '').'
                    ORDER BY p.`id_pdpopupspro` DESC';

            $popup_date_and_type = Db::getInstance()->getValue($sql);

            if ($popup_date_and_type && $page_type && (!$id_manufacturer && !$id_category)) {
                return $popup_date_and_type;
            }

            // get only highest id of popup for display for all pages and and if is date range is not set
            $sql = 'SELECT p.`id_pdpopupspro`
                    FROM `'._DB_PREFIX_.'pdpopupspro` p
                    WHERE p.`id_shop` = '.(int)$id_shop.'
                    AND p.from = '.$date_null.'
                    AND p.to = '.$date_null.'
                    AND p.active = 1
                    '.($page_type ? ' AND find_in_set("'.(int)$page_type.'", p.`page`) <> 0' : '').'
                    ORDER BY p.`id_pdpopupspro` DESC';

            $popup_no_date_only_type = Db::getInstance()->getValue($sql);

            if ($popup_no_date_only_type && $page_type && (!$id_manufacturer && !$id_category)) {
                return $popup_no_date_only_type;
            }

            // get only highest id of popup for display for all pages and and if is date range is set
            $sql = 'SELECT p.`id_pdpopupspro`
                    FROM `'._DB_PREFIX_.'pdpopupspro` p
                    WHERE p.`id_shop` = '.(int)$id_shop.'
                    AND p.from <= '.$date_time_now.'
                    AND p.to >= '.$date_time_now.'
                    AND p.active = 1
                    AND p.`page` = 1
                    ORDER BY p.`id_pdpopupspro` DESC';

            $popup_date_and_no_type = Db::getInstance()->getValue($sql);

            if ($popup_date_and_no_type) {
                return $popup_date_and_no_type;
            }

            // get only highest id of popup for display for all pages and and if is date range is not set
            $sql = 'SELECT p.`id_pdpopupspro`
                    FROM `'._DB_PREFIX_.'pdpopupspro` p
                    WHERE p.`id_shop` = '.(int)$id_shop.'
                    AND p.from = '.$date_null.'
                    AND p.to = '.$date_null.'
                    AND p.active = 1
                    AND p.`page` = 1
                    ORDER BY p.`id_pdpopupspro` DESC';

            $popup_no_date_and_type = Db::getInstance()->getValue($sql);

            if ($popup_no_date_and_type) {
                return $popup_no_date_and_type;
            }
        }
    }

    public static function getPageTypeForSql($value)
    {
        switch ($value) {
            case 'index':
                $type = 2;
                break;
            case 'product':
                $type = 3;
                break;
            case 'category':
                $type = 4;
                break;
            case 'order-opc':
                $type = 5;
                break;
            case 'order':
                $type = 6;
                break;
            case 'contact':
                $type = 7;
                break;
            case 'prices-drop':
                $type = 8;
                break;
            case 'new-products':
                $type = 9;
                break;
            case 'best-sales':
                $type = 10;
                break;
            case 'authentication':
                $type = 11;
                break;
            case 'cms':
                $type = 12;
                break;
            case 'address':
                $type = 13;
                break;
            case 'addresses':
                $type = 14;
                break;
            case 'my-account':
                $type = 15;
                break;
            case 'order-confirmation':
                $type = 16;
                break;
            case 'manufacturer':
                $type = 17;
                break;
            case 'sitemap':
                $type = 18;
                break;
            case 'history':
                $type = 19;
                break;
            case 'search':
                $type = 20;
                break;
            case 'products-comparison':
                $type = 21;
                break;
            case 'pagenotfound':
                $type = 22;
                break;
            case 'password':
                $type = 23;
                break;
            default:
                $type = 1;
        }
        return $type;
    }

    public function hookHeader()
    {
        $page = self::getControlerName();
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;

        // Get popup for display
        $id_pdpopupspro = $this->getMaxIdPopUpForDisplay($id_shop, $page);
        $popup = new PopUpModel($id_pdpopupspro, $id_lang);
        if ($popup->active) {
            Media::addJsDef(array(
                'pdpopupspro_secure_key' => $this->secure_key,
                'pdpopupspro_ajax_link' => $this->context->link->getModuleLink('pdpopupspro', 'ajax', array()),
                'pdpopupspro_module_dir' => $this->module_dir,
                'pdpopupspro_npc_enabled' => $popup->npc,
                'pdpopupspro_cookie_name' => 'pdpopupspro_'.(int)$popup->id.'-'.(int)$popup->newval,
                'pdpopupspro_time_open' => (int)$popup->time_to_open,
                'pdpopupspro_time_close' => (int)$popup->time_to_close,
                'pdpopupspro_cookie_expire' => (int)$popup->cookie,
            ));

            Media::addJsDefL('pdpopupspro_msg_required', $this->l('Please provide email address.'));
            Media::addJsDefL('pdpopupspro_npc_msg', $this->l('Please accept privacy policy.'));
            Media::addJsDefL('pdpopupspro_msg_error_0', $this->l('Unknown error occurred.'));
            Media::addJsDefL('pdpopupspro_msg_error_1', $this->l('This email address is already registered in newsletter as guest.'));
            Media::addJsDefL('pdpopupspro_msg_error_2', $this->l('This email address is already registered in newsletter as customer.'));
            Media::addJsDefL('pdpopupspro_msg_success', $this->l('You have successfully subscribed to this newsletter.'));
            Media::addJsDefL('pdpopupspro_msg_verification_email_send', $this->l('A verification email has been sent. Please check your inbox.'));

            if ($this->ps_version_17 || $this->ps_version_8) {
                $this->context->controller->registerStylesheet('modules-pdpopupspro-front-css', 'modules/'.$this->name.'/views/css/pdpopupspro_front.css', array('media' => 'all', 'priority' => 150));
                $this->context->controller->registerStylesheet('modules-pdpopupspro-front-css-dynamic', 'modules/'.$this->name.'/views/css/pdpopupspro_id_popup_'.(int)$id_pdpopupspro.'.css', array('media' => 'all', 'priority' => 160));
                $this->context->controller->registerJavascript('modules-pdpopupspro-front-js', 'modules/'.$this->name.'/views/js/front.js', array('position' => 'bottom', 'priority' => 150));
            } else {
                $this->context->controller->addCSS(($this->_path).'views/css/pdpopupspro_id_popup_'.(int)$id_pdpopupspro.'.css', 'all');
                $this->context->controller->addCSS($this->_path.'views/css/pdpopupspro_front.css', 'all');
                $this->context->controller->addJS($this->_path.'views/js/front.js');
            }
        }
    }


    public function hookactionObjectPopUpModelAddAfter($obj)
    {
        return $this->generateCss((int)$obj['object']->id);
    }

    public function hookactionObjectPopUpModelUpdateAfter($obj)
    {
        return $this->generateCss((int)$obj['object']->id);
    }

    public function hookactionObjectPopUpModelDeleteAfter($obj)
    {
        $file_name = $this->local_path.'views/css/pdpopupspro_id_popup_'.(int)$obj['object']->id.'.css';
        if (file_exists($file_name)) {
            @unlink($file_name);
        }
    }

    public static function getControlerName()
    {
        $page_name = 'index';
        if (!empty(Context::getContext()->controller->php_self)) {
            $page_name = (string)Context::getContext()->controller->php_self;
        } else {
            $page_name = (string)Tools::getValue('controller');
        }
        return $page_name;
    }


    public function hookDisplayCustomFooter()
    {
        return $this->hookFooter();
    }

    public function hookDisplayFooter()
    {
        $page = self::getControlerName();
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;

        // Get popup for display
        $id_pdpopupspro = $this->getMaxIdPopUpForDisplay($id_shop, $page);
        if ($id_pdpopupspro) {
            $popup = new PopUpModel($id_pdpopupspro, $id_lang);
            if ($popup->active) {
                $npc_cms_link = '';
                if (is_numeric($popup->npc_cms)) {
                    $cms = new CMS((int)$popup->npc_cms, (int)($this->context->language->id));
                    $npc_cms_link = $this->context->link->getCMSLink($cms, $cms->link_rewrite, true);
                    if (!strpos($npc_cms_link, '?')) {
                        $npc_cms_link .= '?content_only=1';
                    } else {
                        $npc_cms_link .= '&content_only=1';
                    }
                }

                if (!isset($_COOKIE['pdpopupspro_'.(int)$popup->newval])) {
                    if (!$this->isCached('pdpopupspro.tpl', $this->getCacheId())) {
                        $this->smarty->assign(
                            array(
                                'npc' => $popup->npc,
                                'npc_cms_link' => $npc_cms_link,
                                'name' => $popup->name,
                                'content' => $popup->content,
                                'show_title' => $popup->show_title,
                                'content_newsletter' => $popup->content_newsletter,
                                'show_newsletter' => $popup->newsletter,
                                'id_pdpopupspro' => $id_pdpopupspro,
                                'pdpopupspro_module_dir' => $this->module_dir,
                                'pdpopupspro_secure_key' => $this->secure_key,
                                'ps_version_16' => $this->ps_version_16,
                                'ps_version_17' =>$this->ps_version_17
                            )
                        );
                    }
                    return $this->display(__FILE__, 'pdpopupspro.tpl', $this->getCacheId());
                }
            }
        }
    }


    /**
     * Check if this mail is registered for newsletters
     *
     * @param string $customer_email
     *
     * @return int    1 = registered in block
     *                2 = registered in customer
     *                3 = not a customer and not registered
     *                4 = customer not registered
     *
     */
    public function isNewsletterRegistered($customer_email)
    {
        if ($this->ps_version_16) {
            $blocknewsletter_installed = Module::isInstalled('blocknewsletter');
            if ($blocknewsletter_installed) {
                $sql = 'SELECT `email`
                        FROM '._DB_PREFIX_.'newsletter
                        WHERE `email` = \''.pSQL($customer_email).'\'
                        AND id_shop = '.(int)$this->context->shop->id;

                if (Db::getInstance()->getRow($sql)) {
                    return self::GUEST_REGISTERED;
                } // 1
            }
        } else {
            $ps_emailsubscription_installed = Module::isInstalled('ps_emailsubscription');
            if ($ps_emailsubscription_installed) {
                $sql = 'SELECT `email`
                        FROM '._DB_PREFIX_.'emailsubscription
                        WHERE `email` = \''.pSQL($customer_email).'\'
                        AND id_shop = '.(int)$this->context->shop->id.'
                        AND id_lang = '.(int)$this->context->language->id;

                if (Db::getInstance()->getRow($sql)) {
                    return self::GUEST_REGISTERED;
                } // 1
            }
        }

        $sql = 'SELECT `newsletter`
                FROM '._DB_PREFIX_.'customer
                WHERE `email` = \''.pSQL($customer_email).'\'
                AND id_shop = '.(int)$this->context->shop->id;

        if (!$registered = Db::getInstance()->getRow($sql)) {
            return self::GUEST_NOT_REGISTERED;
        } // 3

        if ($registered['newsletter'] == '1') {
            return self::CUSTOMER_REGISTERED;
        } // 2

        return self::CUSTOMER_NOT_REGISTERED; // 4
    }

    public function registerUser($email)
    {
        $sql = 'UPDATE '._DB_PREFIX_.'customer
                SET `newsletter` = 1, newsletter_date_add = NOW(), `ip_registration_newsletter` = \''.pSQL(Tools::getRemoteAddr()).'\'
                WHERE `email` = \''.pSQL($email).'\'
                AND id_shop = '.(int)$this->context->shop->id;

        $this->freshmailAddNewSubscriber($email);
        return Db::getInstance()->execute($sql);
    }


    public function registerGuest($email)
    {
        $active = true;
        if ($this->skip_validation == false) {
            $active = false;
        }

        if ($this->ps_version_16) {
            $blocknewsletter_installed = Module::isInstalled('blocknewsletter');
            if ($blocknewsletter_installed) {
                $sql = 'INSERT INTO '._DB_PREFIX_.'newsletter (id_shop, id_shop_group, email, newsletter_date_add, ip_registration_newsletter, http_referer, active)
                        VALUES
                        ('.(int)$this->context->shop->id.',
                        '.(int)$this->context->shop->id_shop_group.',
                        \''.pSQL($email).'\',
                        NOW(),
                        \''.pSQL(Tools::getRemoteAddr()).'\',
                        (
                            SELECT c.http_referer
                            FROM '._DB_PREFIX_.'connections c
                            WHERE c.id_guest = '.(int)$this->context->customer->id.'
                            ORDER BY c.date_add DESC LIMIT 1
                        ),
                        '.(int)$active.'
                        )';

                return Db::getInstance()->execute($sql);
            }
        } else {
            $ps_emailsubscription_installed = Module::isInstalled('ps_emailsubscription');
            if ($ps_emailsubscription_installed) {
                $sql = 'INSERT INTO '._DB_PREFIX_.'emailsubscription (id_shop, id_shop_group, email, newsletter_date_add, ip_registration_newsletter, http_referer, active, id_lang)
                        VALUES
                        ('.(int)$this->context->shop->id.',
                        '.(int)$this->context->shop->id_shop_group.',
                        \''.pSQL($email).'\',
                        NOW(),
                        \''.pSQL(Tools::getRemoteAddr()).'\',
                        (
                            SELECT c.http_referer
                            FROM '._DB_PREFIX_.'connections c
                            WHERE c.id_guest = '.(int)$this->context->customer->id.'
                            ORDER BY c.date_add DESC LIMIT 1
                        ),
                        '.(int)$active.',
                        '.(int)$this->context->language->id.'
                        )';

                return Db::getInstance()->execute($sql);
            }
        }
        return false;
    }


    public function hookActionCustomerAccountAdd($params)
    {
        if (isset($params['newCustomer'])) {
            $id_shop = $params['newCustomer']->id_shop;
            $email = $params['newCustomer']->email;
            if ($this->ps_version_16) {
                $blocknewsletter_installed = Module::isInstalled('blocknewsletter');
                if (Validate::isEmail($email) && $blocknewsletter_installed) {
                    return (bool)Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'newsletter WHERE id_shop='.(int)$id_shop.' AND email=\''.pSQL($email)."'");
                }
            } else {
                $ps_emailsubscription_installed = Module::isInstalled('ps_emailsubscription');
                if (Validate::isEmail($email) && $ps_emailsubscription_installed) {
                    return (bool)Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'emailsubscription WHERE id_shop='.(int)$id_shop.' AND email=\''.pSQL($email)."'");
                }
            }
            return true;
        }
    }

    public function sendConfirmationEmail($email)
    {
        if ($this->confirm_email) {
            return Mail::Send($this->context->language->id, 'pd_newsletter_conf', Mail::l('Newsletter confirmation', $this->context->language->id), array(), pSQL($email), null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
        } else {
            return true;
        }
    }

    public function sendVerificationEmail($email, $token, $id_pdpopupspro)
    {
        if ($this->skip_validation == false) {
            $verif_url = Context::getContext()->link->getModuleLink(
                'pdpopupspro',
                'verification',
                array(
                    'id' => (int)$id_pdpopupspro,
                    'token' => $token,
                )
            );

            return Mail::Send($this->context->language->id, 'pd_newsletter_verif', Mail::l('Email verification', $this->context->language->id), array('{verif_url}' => $verif_url), $email, null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
        } else {
            return true;
        }
    }


    /**
     *  Return a token associated to an user
     **/
    public function getToken($email, $register_status)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        if (in_array($register_status, array(self::GUEST_NOT_REGISTERED, self::GUEST_REGISTERED))) {
            if ($this->ps_version_16) {
                $blocknewsletter_installed = Module::isInstalled('blocknewsletter');
                if ($blocknewsletter_installed) {
                    $sql = 'SELECT MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL($this->salt).'\')) as token
                        FROM `'._DB_PREFIX_.'newsletter`
                        WHERE `active` = 0
                        AND `email` = \''.pSQL($email).'\'
                        AND `id_shop` = '.$id_shop;
                    return Db::getInstance()->getValue($sql);
                }
            } else {
                $ps_emailsubscription_installed = Module::isInstalled('ps_emailsubscription');
                if ($ps_emailsubscription_installed) {
                    $sql = 'SELECT MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL($this->salt).'\')) as token
                        FROM `'._DB_PREFIX_.'emailsubscription`
                        WHERE `active` = 0
                        AND `email` = \''.pSQL($email).'\'
                        AND `id_shop` = '.$id_shop.'
                        AND `id_lang` = '.$id_lang;
                    return Db::getInstance()->getValue($sql);
                }
            }
        } elseif ($register_status == self::CUSTOMER_NOT_REGISTERED) {
            $sql = 'SELECT MD5(CONCAT( `email` , `date_add`, \''.pSQL($this->salt).'\' )) as token
                FROM `'._DB_PREFIX_.'customer`
                WHERE `newsletter` = 0
                AND `email` = \''.pSQL($email).'\'
                AND id_shop = '.$id_shop;
            return Db::getInstance()->getValue($sql);
        }
        return false;
    }

    /**
     *  Activate guest email
     **/
    public function activateGuest($email)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;

        $this->freshmailAddNewSubscriber($email);
        if ($this->ps_version_16) {
            $blocknewsletter_installed = Module::isInstalled('blocknewsletter');
            if ($blocknewsletter_installed) {
                return Db::getInstance()->execute(
                    'UPDATE `'._DB_PREFIX_.'newsletter`
                    SET `active` = 1
                    WHERE `email` = \''.pSQL($email).'\'
                    AND `id_shop` = '.$id_shop
                );
            }
        } else {
            $ps_emailsubscription_installed = Module::isInstalled('ps_emailsubscription');
            if ($ps_emailsubscription_installed) {
                return Db::getInstance()->execute(
                    'UPDATE `'._DB_PREFIX_.'emailsubscription`
                    SET `active` = 1
                    WHERE `email` = \''.pSQL($email).'\'
                    AND `id_shop` = '.$id_shop.'
                    AND `id_lang` = '.$id_lang
                );
            }
        }
        return false;
    }

    /**
     *  Returns a guest email by token
     **/
    protected function getGuestEmailByToken($token)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;

        if ($this->ps_version_16) {
            $blocknewsletter_installed = Module::isInstalled('blocknewsletter');
            if ($blocknewsletter_installed) {
                return Db::getInstance()->getValue(
                    'SELECT `email`
                    FROM `'._DB_PREFIX_.'newsletter`
                    WHERE MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL($this->salt).'\')) = \''.pSQL($token).'\'
                    AND `active` = 0
                    AND `id_shop` = '.$id_shop
                );
            }
        } else {
            $ps_emailsubscription_installed = Module::isInstalled('ps_emailsubscription');
            if ($ps_emailsubscription_installed) {
                return Db::getInstance()->getValue(
                    'SELECT `email`
                    FROM `'._DB_PREFIX_.'emailsubscription`
                    WHERE MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL($this->salt).'\')) = \''.pSQL($token).'\'
                    AND `active` = 0
                    AND `id_shop` = '.$id_shop.'
                    AND `id_lang` = '.$id_lang
                );
            }
        }
        return false;
    }

    /**
     *  Returns a customer email by token
     **/
    protected function getUserEmailByToken($token)
    {
        $sql = 'SELECT `email`
                FROM `'._DB_PREFIX_.'customer`
                WHERE MD5(CONCAT( `email` , `date_add`, \''.pSQL($this->salt).'\')) = \''.pSQL($token).'\'
                AND `newsletter` = 0
                AND id_shop = '.(int)$this->context->shop->id;

        return Db::getInstance()->getValue($sql);
    }

    /**
     *  Ends the registration process to the newsletter
     **/
    public function confirmEmail($token, $id_pdpopupspro)
    {
        $activated = false;

        if ($email = $this->getGuestEmailByToken($token)) {
            $activated = $this->activateGuest($email);
            $popup = new PopUpModel((int)$id_pdpopupspro);
            if ($activated) {
                if ($popup->voucher_enabled) {
                    $this->sendVoucher($email, $id_pdpopupspro);
                }
            }
        } elseif ($email = $this->getUserEmailByToken($token)) {
            $activated = $this->registerUser($email);
            $popup = new PopUpModel((int)$id_pdpopupspro);
            if ($activated) {
                if ($popup->voucher_enabled) {
                    $this->sendVoucher($email, $id_pdpopupspro);
                }
            }
        }

        if (!$activated) {
            return $this->l('This email is already registered and/or invalid.');
        } else {
            $this->sendConfirmationEmail($email);
        }

        return $this->l('Thank you for subscribing to our newsletter.');
    }

    public function sendVoucher($email, $id_pdpopupspro)
    {
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $popup = new PopUpModel($id_pdpopupspro);

        $voucher = $this->createDiscount(
            (float)$popup->voucher_value,
            $popup->voucher_type,
            strftime('%Y-%m-%d %H:%M:%S', strtotime('+'.(int)$popup->voucher_date_validity.' day')),
            $popup->voucher_min_order,
            $this->l('Thank you for you subscription our newsletter.'),
            $popup->voucher_reduction_tax,
            $popup->voucher_reduction_currency,
            $popup->voucher_code_prefix,
            $popup->voucher_min_order_currency
        );

        if ($popup->voucher_type == 1) {
            $voucher_value = $popup->voucher_value.'%';
        } else {
            $voucher_value = $popup->voucher_value.''.$currency->sign;
        }

        if ($voucher) {
            return Mail::Send(
                $this->context->language->id,
                'pd_newsletter_voucher',
                Mail::l('Newsletter voucher', $this->context->language->id),
                array(
                    '{voucher_num}' => $voucher->code,
                    '{days}' => (int)$popup->voucher_date_validity,
                    '{value}' => $voucher_value
                ),
                $email,
                null,
                null,
                null,
                null,
                null,
                dirname(__FILE__).'/mails/',
                false,
                $this->context->shop->id
            );
        }
    }

    /**
     *  Create discount code returns (string) code
     **/
    public function createDiscount($value, $type, $date_validity, $min_order, $description, $reduction_tax, $reduction_currency, $prefix, $minimum_amount_currency)
    {
        $cart_rule = new CartRule();
        if ($type == 1) {
            $cart_rule->reduction_percent = (float)$value;
        } else {
            $cart_rule->reduction_amount = (float)$value;
            $cart_rule->reduction_tax = (int)$reduction_tax;
            $cart_rule->reduction_currency = (int)$reduction_currency;
        }
        $cart_rule->date_to = $date_validity;
        $cart_rule->date_from = date('Y-m-d H:i:s');
        $cart_rule->quantity = 1;
        $cart_rule->quantity_per_user = 1;
        $cart_rule->cart_rule_restriction = 1;
        $cart_rule->minimum_amount = $min_order;
        $cart_rule->minimum_amount_currency = $minimum_amount_currency;

        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            $cart_rule->name[(int)$language['id_lang']] = $description;
        }

        $code = $prefix.Tools::strtoupper(Tools::passwdGen(10));
        $cart_rule->code = $code;
        $cart_rule->active = 1;

        if (!$cart_rule->add()) {
            return false;
        }

        return $cart_rule;
    }

    public function freshmailAddNewSubscriber($email)
    {
        if ($this->freshmail_enabled) {
            $data = array(
                'email' => $email,
                'list'  => $this->freshmail_hash_list,
                'custom_fields' => array(),
                'state'   => $this->freshmail_status,
                'confirm' => $this->freshmail_confirm
            );

            try {
                $api = new FreshMailApi();
                $api->addSubscriber($data);
            } catch (\Exception $e) {
                if ($e->getCode() == 1304) {
                    $msg = 'FRESHMAIL_ALREADY_SUBSCRIBED_MESSAGE';
                } else {
                    $msg = 'FRESHMAIL_SUBMISSION_FAILURE_MESSAGE';
                }
            }
            return true;
        }
    }
}
