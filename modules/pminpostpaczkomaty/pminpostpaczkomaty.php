<?php
/**
* 2014-2020 Presta-Mod.pl Rafał Zontek
*
* NOTICE OF LICENSE
*
* Poniższy kod jest kodem płatnym, rozpowszechanie bez pisemnej zgody autora zabronione
* Moduł można zakupić na stronie Presta-Mod.pl. Modyfikacja kodu jest zabroniona,
* wszelkie modyfikacje powodują utratę gwarancji
*
* http://presta-mod.pl
*
* DISCLAIMER
*
*
*  @author    Presta-Mod.pl Rafał Zontek <biuro@presta-mod.pl>
*  @copyright 2014-2020 Presta-Mod.pl
*  @license   Licecnja na jedną domenę
*  Presta-Mod.pl Rafał Zontek
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(dirname(__FILE__) . '/lib/api.php');
include_once(dirname(__FILE__) . '/lib/shipxapi.php');
include_once(dirname(__FILE__) . '/classes/PaczkomatyList.php');
include_once(dirname(__FILE__) . '/classes/BaselinkerOrder.php');

class PmInpostPaczkomaty extends Module
{
    protected $config_form = false;
    private $_prefix = 'PMINPOSTPACZKOMATY';

    public function __construct()
    {
        $this->name = 'pminpostpaczkomaty';
        $this->tab = 'shipping_logistics';
        $this->version = '2.7.6';
        $this->author = 'Presta-Mod.pl';
        $this->need_instance = 0;
        $this->move_footer = true;
        $this->bootstrap = true;
        $this->warnings = array();
        $this->errors = array();
        $this->confirmations = array();
        $this->sandbox = false;

        parent::__construct();

        $this->displayName = $this->l('Inpost Paczkomaty Poland');
        $this->description = $this->l('Inpost Paczkomaty Poland with new fast map');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<=')) {
            $this->version_ps = '1.6';
        } else {
            $this->version_ps = '1.7';
        }
        $this->switchSandbox();
        $this->carrierAutoEnableDisable();
    }

    public function install()
    {
        if (extension_loaded('curl') == false) {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }

        return parent::install() &&
        PaczkomatyList::installSql() &&
        PmBaselinkerOrder::installSql() &&
        $this->registerHook('header') &&
        $this->registerHook('backOfficeHeader') &&
        $this->registerHook('displayBeforeCarrier') &&
        $this->registerHook('updateCarrier') &&
        $this->registerHook('actionCarrierUpdate') &&
        $this->registerHook('actionValidateOrder') &&
        $this->registerHook('footer') &&
        $this->registerAdminHook() &&
        $this->createAjaxController();
    }

    public function uninstall()
    {
        $config = $this->getConfigurationFormValues();
        foreach (array_keys($config) as $key) {
            Configuration::deleteByName($key);
        }
        parent::uninstall();
        PaczkomatyList::uninstallSql();
        PmBaselinkerOrder::uninstallSql();
        $this->unregisterHook('header');
        $this->unregisterHook('backOfficeHeader');
        $this->unregisterHook('actionValidateOrder');
        $this->unregisterHook('displayBeforeCarrier');
        $this->unregisterHook('updateCarrier');
        $this->unregisterHook('actionCarrierUpdate');
        $this->unregisterHook('footer');
        $this->unregisterAdminHook();
        $this->removeAjaxController();
        return true;
    }

    public function registerAdminHook()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.6.0.9', '>=')) {
            return  $this->registerHook('displayAdminOrderTabLink') &&
            $this->registerHook('displayAdminOrderTabContent') &&
            $this->registerHook('displayAdminOrderTabShip') &&
            $this->registerHook('displayAdminOrderContentShip');
        } else {
            return $this->registerHook('adminOrder');
        }
    }

    public function hookDisplayAdminOrderTabLink($hook_params)
    {
        $params = array();
        $params['order'] = new Order($hook_params['id_order']);
        return $this->hookDisplayAdminOrderTabShip($params);
    }
    public function hookDisplayAdminOrderTabContent($hook_params)
    {
        $params = array();
        $params['order'] = new Order($hook_params['id_order']);
        return $this->hookDisplayAdminOrderContentShip($params);
    }

    public function unregisterAdminHook()
    {
        $this->unregisterHook('displayAdminOrderTabShip');
        $this->unregisterHook('displayAdminOrderContentShip');
        $this->unregisterHook('adminOrder');
    }

    public function createAjaxController()
    {
        $tab = new Tab();
        $tab->active = 1;
        $languages = Language::getLanguages(false);
        if (is_array($languages)) {
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = $this->name;
            }
        }
        $tab->class_name = 'PmInpostPaczkomatyOrder';
        $tab->module = $this->name;
        $tab->id_parent = - 1;
        $tab->add();

        $tab = new Tab();
        $tab->active = 1;
        $languages = Language::getLanguages(false);
        if (is_array($languages)) {
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = 'Inpost';
            }
        }
        $tab->class_name = 'PmInpostPaczkomatyList';
        $tab->module = $this->name;
        $id = Tab::getIdFromClassName('AdminParentShipping');
        $tab->id_parent = $id;
        $tab->add();

        $languages = Language::getLanguages(false);
        if (is_array($languages)) {
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = 'Inpost paczkomaty zlecenia';
            }
        }
        $tab->class_name = 'PmInpostPaczkomatyZlecenia';
        $tab->module = $this->name;
        $id = Tab::getIdFromClassName('AdminParentShipping');
        $tab->id_parent = $id;
        $tab->add();
        return true;
    }

    public function removeAjaxController()
    {
        $id = Tab::getIdFromClassName('PmInpostPaczkomatyOrder');
        $tab = new Tab($id);
        $tab->delete();

        $id = Tab::getIdFromClassName('PmInpostPaczkomatyList');
        $tab = new Tab($id);
        $tab->delete();

        $id = Tab::getIdFromClassName('PmInpostPaczkomatyZlecenia');
        $tab = new Tab($id);
        $tab->delete();

        return true;
    }

    public function installOverrides()
    {
        if (Module::isInstalled('x13opc') || Module::isInstalled('onepagecheckout')) {
            if (!is_dir($this->getLocalPath().'override')) {
                return true;
            }

            $result = true;
            foreach (Tools::scandir($this->getLocalPath().'override', 'php', '', true) as $file) {
                if (strpos($file, 'OrderOpcController.php')) {
                    continue ;
                }
                $class = basename($file, '.php');
                if (PrestaShopAutoload::getInstance()->getClassPath($class.'Core') ||
                Module::getModuleIdByName($class)) {
                    $result &= $this->addOverride($class);
                }
            }

            return $result;
        } else {
            return parent::installOverrides();
        }
    }

    public function uninstallOverrides()
    {
        if (Module::isInstalled('x13opc')) {
            if (!is_dir($this->getLocalPath().'override')) {
                return true;
            }

            $result = true;
            foreach (Tools::scandir($this->getLocalPath().'override', 'php', '', true) as $file) {
                if (strpos($file, 'OrderOpcController.php') !== false) {
                    continue ;
                }
                $class = basename($file, '.php');
                if (PrestaShopAutoload::getInstance()->getClassPath($class.'Core') ||
                Module::getModuleIdByName($class)) {
                    $result &= $this->removeOverride($class);
                }
            }

            return $result;
        } else {
            return parent::uninstallOverrides();
        }
    }

    private function addMediaConfig()
    {
        Media::addJsDef(
            array(
            'pm_inpostpaczkomaty_mapkey' => Configuration::get('PMINPOSTPACZKOMATY_MAP_KEY'),
            'pm_inpostpaczkomaty_gcup' => $this->context->link->getModuleLink(
                $this->name,
                'ajax'
            ),
            'prestakey' => md5(_COOKIE_KEY_),
            'wpzl' => (int)Configuration::get('PMINPOSTPACZKOMATY_WPL'),
            )
        );
        if (Tools::version_compare(_PS_VERSION_, '1.6.1.7', '<=')) {
            $this->context->smarty->assign('js_def', Media::getJsDef());
        }
        if (Tools::version_compare(_PS_VERSION_, '1.6.1.7', '<=')) {
            $js = '';
            $js_def = Media::getJsDef();
            $this->context->smarty->assign('js_def', $js_def);
            foreach ($js_def as $key => $item) {
                $js .= 'var '.$key.' = \''.$item.'\''.PHP_EOL;
            }
            file_put_contents(dirname(__FILE__).'/views/js/jsdef.js', $js);
        }
    }

    private function addSmartyVariablesConfig()
    {
        $output = $this->getOutput();
        $this->context->smarty->assign(
            array(
                'selected_menu' => Tools::getValue('selected_menu', 0),
                'module_dir' => $this->_path,
                'config_form' => $this->renderForm(),
                'output' => $output,
                'host' => $_SERVER['HTTP_HOST'],
                'module_key' => '',
            )
        );
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPminpostpaczkomatyModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigurationFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm($this->getConfigurationForm());
    }

    protected function renderFormAdmin($params)
    {
        if (isset($params['order']) && $params['order'] instanceof Order) {
            $o = $params['order'];
        } else {
            $o = new Order($params['id_order']);
        }

        $id_cart = $o->id_cart;
        $paczkomatyList = PaczkomatyList::getByIdCart($id_cart);
        $this->addDeliveryAllegro($paczkomatyList);
        if (!$paczkomatyList->id) {
            $btn = 1;
        } elseif ($paczkomatyList->nr_listu == '') {
            $btn = 1;
        } elseif ($paczkomatyList->nr_listu != '' && $paczkomatyList->status != 'Opłacona') {
            $btn = 2;
        } elseif ($paczkomatyList->nr_listu != '' && $paczkomatyList->status == 'Opłacona') {
            $btn = 3;
        }

        Context::getContext()->smarty->assign('btn', $btn);

        $helper = new HelperForm();
        $helper->base_folder = _PS_ADMIN_DIR_.'/themes/default/template/helpers/form/';
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = $this->name.'submit';
        $helper->currentIndex = AdminController::$currentIndex.'&id_order='.Tools::getValue('id_order').'&vieworder';
        $helper->token = Tools::getAdminTokenLite('AdminOrders');
        $values = $this->getConfigFormValuesAdmin($params);

        $helper->tpl_vars = array(
            'fields_value' => $values,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $form = $this->getDeliverListForm($btn);
        if (isset($values['warnings'])) {
            $form[0]['form'] = array_merge(array('warning' => $values['warnings']), $form[0]['form']);
        }

        return $helper->generateForm($form);
    }

    protected function getConfigurationForm()
    {
        $s = 'switch';
        $form = array();
        $form[] = array(
        'form' => array(
            'legend' => array(
                'title' => $this->l('Access'),
                'required' => true,
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'col' => 5,
                    'type' => 'hidden',
                    'name' => 'selected_menu',
                    'label' => $this->l('Selected menu'),
                ),
                array(
                    'col' => 5,
                    'type' => 'hidden',
                    'name' => 'PMINPOSTPACZKOMATY_GUIDE',
                    'label' => $this->l('Selected Guide'),
                ),
                array(
                    'type' => $s,
                    'label' => $this->l('Use shipx API'),
                    'name' => 'PMINPOSTPACZKOMATY_SHIPX',
                    'is_default' => 0,
                    'class' => 'inpostshipx',
                    'hint' => $this->l('If you want to use the new API, you must have TOKEN'),
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
                    )
                ),
                array(
                    'col' => 3,
                    'type' => 'text',
                    'required' => true,
                    'name' => 'PMINPOSTPACZKOMATY_LOGIN',
                    'label' => $this->l('Login'),
                    'hint' => $this->l('Login used in https://manager.paczkomaty.pl'),
                ),
                array(
                    'col' => 3,
                    'type' => 'text',
                    'required' => true,
                    'name' => 'PMINPOSTPACZKOMATY_PASSWORD',
                    'label' => $this->l('Password'),
                    'hint' => $this->l('The password used in https://manager.paczkomaty.pl'),
                ),
                array(
                    'col' => 9,
                    'type' => 'textarea',
                    'required' => true,
                    'name' => 'PMINPOSTPACZKOMATY_TOKEN',
                    'label' => $this->l('Token'),
                    'hint' => $this->l('The token can be obtained by contacting InPost opieka@grupainteger.pl'),
                ),
                array(
                    'col' => 3,
                    'type' => 'text',
                    'required' => true,
                    'name' => 'PMINPOSTPACZKOMATY_ID',
                    'label' => $this->l('Id organization'),
                    'hint' => $this->l('The organization\'s id can be obtained by contacting InPost'),
                ),
                array(
                    'type' => $s,
                    'label' => $this->l('Sandbox'),
                    'name' => 'PMINPOSTPACZKOMATY_SANDBOX',
                    'is_default' => 0,
                    'class' => 'inpostshipx',
                    'hint' => $this->l('If you want to use sandox '),
                    'desc' => $this->l('Option for advanced users - Default should be select No'),
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
                    )
                ),
                array(
                    'type' => $s,
                    'label' => $this->l('Log'),
                    'name' => 'PMINPOSTPACZKOMATY_LOG',
                    'is_default' => 0,
                    'hint' => $this->l('Generate log file from all request '),
                    'desc' => $this->l('Option for advanced users - Default should be select No'),
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
                    )
                ),
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Save and test connection'),
                    'name' => 'saveAndTest',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        )
        );

        $form[] = array(
        'form' => array(
            'legend' => array(
                'title' => $this->l('Sender settings'),
                'required' => true,
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'name' => 'PMINPOSTPACZKOMATY_COMPANY_NAME',
                    'label' => $this->l('Company name'),
                    'hint' => array(
                        $this->l('Company name'),
                        $this->l('- required field if you want to communicate with api')
                    )
                ),
                array(
                    'type' => 'text',
                    'name' => 'PMINPOSTPACZKOMATY_FIRST_NAME',
                    'label' => $this->l('Firstname'),
                    'hint' => array(
                        $this->l('Firstname'),
                        $this->l('- required field if you want to communicate with api')
                    )
                ),
                array(
                    'type' => 'text',
                    'name' => 'PMINPOSTPACZKOMATY_LAST_NAME',
                    'label' => $this->l('Lastname'),
                    'hint' => array(
                        $this->l('Lastname'),
                        $this->l('- required field if you want to communicate with api')
                    )
                ),
                array(
                    'type' => 'text',
                    'name' => 'PMINPOSTPACZKOMATY_EMAIL',
                    'label' => $this->l('Sender e-mail'),
                    'prefix' => '<i class="icon icon-envelope"></i>',
                    'hint' => array(
                        $this->l('Sender e-mail'),
                        $this->l('- required field if you want to communicate with api')
                    )
                ),
                array(
                    'type' => 'text',
                    'name' => 'PMINPOSTPACZKOMATY_PHONE',
                    'label' => $this->l('Phone'),
                    'prefix' => '<i class="icon icon-phone"></i>',
                    'desc' => $this->l('Use mobile phone without prefix only 9 numbers (for example 123456789)'),
                    'hint' => array(
                        $this->l('Phone'),
                        $this->l('- required field if you want to communicate with api')
                    )
                ),
                array(
                    'type' => 'text',
                    'name' => 'PMINPOSTPACZKOMATY_STREET',
                    'label' => $this->l('Street'),
                    'hint' => array(
                        $this->l('Street'),
                        $this->l('- required field if you want to communicate with api')
                    )
                ),
                array(
                    'type' => 'text',
                    'name' => 'PMINPOSTPACZKOMATY_BUILDING_NUMBER',
                    'label' => $this->l('Bulding number'),
                    'hint' => array(
                        $this->l('Bulding number'),
                        $this->l('- required field if you want to communicate with api')
                    )
                ),
                array(
                    'type' => 'text',
                    'name' => 'PMINPOSTPACZKOMATY_POST_CODE',
                    'label' => $this->l('Post code'),
                    'hint' => array(
                        $this->l('Post code'),
                        $this->l('- required field if you want to communicate with api')
                    )
                ),
                array(
                    'type' => 'text',
                    'name' => 'PMINPOSTPACZKOMATY_CITY',
                    'label' => $this->l('City'),
                    'hint' => array(
                        $this->l('City'),
                        $this->l('- required field if you want to communicate with api')
                    )
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        ),
        );

        $form[] = array(
        'form' => array(
            'legend' => array(
                'title' => $this->l('Package Settings Prepayment'),
                'icon' => 'icon-cogs',
                'required' => true,
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Default Size'),
                    'name' => 'PMINPOSTPACZKOMATY_SIZE_BW',
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('The default size of the parcel paid in advance.'),
                        $this->l('You can change the size when generating the label.')
                    ),
                    'options' => array(
                        'query' => $this->getSizes(),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Delivery method'),
                    'name' => 'PMINPOSTPACZKOMATY_DELIVERY_BW',
                    'multiple' => true,
                    'desc' => $this->l('To select more than one use CTRL'),
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('Delivery method for prepaid shipment.'),
                        $this->l('If you do not see the delivery method, create it.'),
                        $this->l('The module does not automatically create a carrier')
                    ),
                    'options' => array(
                        'query' => $this->getCarriers(),
                        'id' => 'id_carrier',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Use button to create new delivery method'),
                    'name' => 'CREATE_DELIVERY_METHOD_BW',
                    'hint' => array(
                        $this->l('Click on this button to create delivery method.')
                    ),
                    'html_content' =>
                    '<button name="CREATE_DELIVERY_METHOD" value="prepayment" class="btn btn-default">'.
                        $this->l('Create delivery method')
                    .'</button>'
                ),
                array(
                    'type' => $s,
                    'label' => $this->l('Issurance'),
                    'name' => 'PMINPOSTPACZKOMATY_ISSURANCE_BW',
                    'is_default' => 0,
                    'class' => 't',
                    'hint' => $this->l('Delivery issurance'),
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
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        )
        );

        $form[] = array(
        'form' => array(
            'legend' => array(
                'title' => $this->l('Package Settings Cash on delivery'),
                'icon' => 'icon-cogs',
                'required' => true,
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Default Size'),
                    'hint' => array(
                        $this->l('The default size of the parcel paid in advance.'),
                        $this->l('You can change the size when generating the label.'),
                    ),
                    'name' => 'PMINPOSTPACZKOMATY_SIZE_COD',
                    'is_default' => 0,
                    'options' => array(
                        'query' => $this->getSizes(),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Delivery method'),
                    'name' => 'PMINPOSTPACZKOMATY_DELIVERY_COD',
                    'is_default' => 0,
                    'multiple' => true,
                    'desc' => $this->l('To select more than one use CTRL'),
                    'hint' =>
                        array(
                            $this->l('Delivery method for prepaid shipment.'),
                            $this->l('If you do not see the delivery method, create it.'),
                            $this->l('The module does not automatically create a carrier')
                        ),
                    'options' => array(
                        'query' => $this->getCarriers(),
                        'id' => 'id_carrier',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Use button to create new delivery method'),
                    'name' => 'CREATE_DELIVERY_METHOD_COD',
                    'hint' => array(
                        $this->l('Click on this button to create delivery method.')
                    ),
                    'html_content' => '<button name="CREATE_DELIVERY_METHOD" value="cod" class="btn btn-default">'.
                        $this->l('Create delivery method')
                    .'</button>'
                ),
                array(
                    'type' => $s,
                    'label' => $this->l('Issurance'),
                    'name' => 'PMINPOSTPACZKOMATY_ISSURANCE_COD',
                    'is_default' => 0,
                    'class' => 't',
                    'hint' => $this->l('Shipment insurance'),
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
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                ),
            )
        );

         $form[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Parcel settings'),
                    'icon' => 'icon-cogs',
                    'required' => true,
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Method of sending'),
                        'name' => 'PMINPOSTPACZKOMATY_SHIPPING_METHOD',
                        'is_default' => 0,
                        'options' => array(
                            'query' => $this->getShippingMethod(),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'hint' => array(
                            $this->l('If you want to give packages in a parcel locker - check:'),
                            $this->l('Posting in a parcel locker,'),
                            $this->l('if you want shipments to be picked up by InPost courier,'),
                            $this->l('select the Pick Up Courier method. The option can be changed during generation'),
                            $this->l('labels, in the order view'),
                            $this->l('Giving in to POP works only with ShipX')
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Machine'),
                        'name' => 'PMINPOSTPACZKOMATY_MACHINE',
                        'is_default' => 0,
                        'hint' => array(
                            $this->l('Parcel machine to send parcels, if you want send your parcels'),
                            $this->l('by the courier - choose the delivery method - Receive by courier'),
                        )
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'PMINPOSTPACZKOMATY_REFERENCE',
                        'label' => $this->l('Reference'),
                        'desc' => $this->l('Reference code on label max 40 chars'),
                        'desc' =>
                                array(
                                     $this->l('{id_order} - order number, {reference} - reference code'),
                                     $this->l('{firstname} - firstname, {lastname} - lastname'),
                                     $this->l('{productsreference} - order products reference'),
                                )
                    ),
                    array(
                        'type' => $s,
                        'label' => $this->l('Add COD value to reference'),
                        'name' => 'PMINPOSTPACZKOMATY_ADD_COD',
                        'is_default' => 0,
                        'class' => 't',
                        'hint' => $this->l('Shipment insurance'),
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
                        )
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'PMINPOSTPACZKOMATY_MPK',
                        'label' => $this->l('MPK'),
                        'desc' => array(
                            $this->l('Place of costs - optional, the place of costs must be').
                            $this->l('be added to the organization first so that it can be assigned to the shipment.'),
                            $this->l('Such an operation must be carried out by an InPost employee.')
                        )
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'PMINPOSTPACZKOMATY_INSC',
                        'label' => $this->l('Insurance value for all packages'),
                        'desc' => array(
                            $this->l('If you want, you can enter a fixed amount of insurance'),
                            $this->l('If the amount is to be charged automatically from the order, leave it blank')
                        )
                    ),

                    array(
                        'type' => 'select',
                        'label' => $this->l('Label format'),
                        'name' => 'PMINPOSTPACZKOMATY_LABEL_FORMAT',
                        'is_default' => 0,
                        'options' => array(
                            'query' => $this->getLabelFormat(),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'hint' => $this->l('Label file format'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Label type'),
                        'name' => 'PMINPOSTPACZKOMATY_LABEL_SIZE',
                        'is_default' => 0,
                        'hint' => $this->l('The size of the downloaded file'),
                        'options' => array(
                            'query' => $this->getLabelSize(),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => $s,
                        'label' => $this->l('Download PDF with multiple deliveries'),
                        'name' => 'PMINPOSTPACZKOMATY_DF',
                        'is_default' => 0,
                        'class' => 't',
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
                        )
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            )
         );

         $form[] = array(
                'form' => array(
                'legend' => array(
                'title' => $this->l('Maps settings'),
                'required' => true,
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Map select'),
                        'name' => 'PMINPOSTPACZKOMATY_GGMAP',
                        'is_default' => 0,
                        'hint' => array(
                            $this->l('You can use open map or google map.'),
                        ),
                        'options' => array(
                            'query' => $this->getMaps(),
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Google map key'),
                        'name' => 'PMINPOSTPACZKOMATY_MAP_KEY',
                        'hint' => $this->l('Key is required to display the map'),
                        'desc' => $this->l('The public key that is generated for the www address'),
                        'is_default' => 0,

                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Google map key (for search engine)'),
                        'name' => 'PMINPOSTPACZKOMATY_MAP_KEY2',
                        'hint' =>
                            array(
                                $this->l('The key is required to use the Select parcel locker from the list field')
                            ),
                        'desc' => array(
                            $this->l('Private key for the server\'s IP address'),
                            $this->l('Required if you want to give the customer the opportunity to use ').
                            $this->l('from the option Select parcel locker from the list')
                        ),
                        'is_default' => 0,

                    ),
                    array(
                        'type' => $s,
                        'label' => $this->l('Show Select a parcel locker from the list'),
                        'name' => 'PMINPOSTPACZKOMATY_WPL',
                        'is_default' => 0,
                        'hint' =>
                            array(
                                $this->l('The option works in the back-office and in the front-office'),
                            ),
                        'class' => 't',
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
                        )
                    ),
                    array(
                        'type' => $s,
                        'label' => $this->l('Disable geolocalization for openmaps'),
                        'name' => 'PMINPOSTPACZKOMATY_GEO',
                        'is_default' => 0,
                        'class' => 't',
                        'hint' => $this->l('Geolocalization wokrs only with SSL connections'),
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
                        )
                    ),
                     array(
                        'type' => $s,
                        'label' => $this->l('Restore the last selected parcel locker'),
                        'name' => 'PMINPOSTPACZKOMATY_LL',
                        'is_default' => 0,
                        'class' => 't',
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
                        )
                    ),
                ),
                'submit' => array(
                'title' => $this->l('Save'),
                ),
            )
         );

         $form[] = array(
            'form' => array(
            'legend' => array(
                'title' => $this->l('Inpost Paczkomaty Display Settings'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Display place'),
                    'name' => 'PMINPOSTPACZKOMATY_DISPLAY_PLACE',
                    'hint' => array(
                        $this->l('If you use a different template than the basic one,'),
                        $this->l('try where the parcel lockers look best'),
                    ),
                    'is_default' => 0,
                    'options' => array(
                        'query' => $this->getDisplayPlace(),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            )
         );
         
         $params = array(
            'token' => substr(md5(_COOKIE_KEY_), 0, 12)
         );
         
         $cron_url = $this->context->link->getModuleLink($this->name, 'cron', $params);

         $form[] = array(
            'form' => array(
            'legend' => array(
                'title' => $this->l('Order status'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => $s,
                    'label' => $this->l('Change order status'),
                    'name' => 'PMINPOSTPACZKOMATY_OS',
                    'hint' => array(
                        $this->l('Automatically change the status of the order after generating the letter,'),
                        $this->l('attention, you will see the change only when you refresh the page'),
                    ),
                    'is_default' => 0,
                    'class' => 't',
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
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Order status afer send'),
                    'name' => 'PMINPOSTPACZKOMATY_STATUS',
                    'hint' => $this->l('Choose the order status'),
                    'is_default' => 0,
                    'options' => array(
                        'query' => $this->getOrderStatus(),
                        'id' => 'id_order_state',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Select statuses with available to change'),
                    'name' => 'PMINPOSTPACZKOMATY_STATUS_AV',
                    'hint' => $this->l('Choose the order status'),
                    'is_default' => 0,
                    'multiple' => true,
                    'options' => array(
                        'query' => $this->getOrderStatus(),
                        'id' => 'id_order_state',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Order status pickup'),
                    'name' => 'PMINPOSTPACZKOMATY_STATUS_PIC',
                    'hint' => $this->l('Choose the order status'),
                    'is_default' => 0,
                    'options' => array(
                        'query' => $this->getOrderStatus(),
                        'id' => 'id_order_state',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Order status afer delivered'),
                    'name' => 'PMINPOSTPACZKOMATY_STATUS_DEL',
                    'hint' => $this->l('Choose the order status'),
                    'is_default' => 0,
                    'options' => array(
                        'query' => $this->getOrderStatus(),
                        'id' => 'id_order_state',
                        'name' => 'name'
                    ),
                    'desc' => array(
                        $this->l('This function works only when you run ajax,'),
                        $this->l('or when you click Change order state for delivered packages (Inpost Controller)'),
                    )
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Link to cron'),
                    'name' => 'CREATE_DELIVERY_CRON_URL',
                    'hint' => array(
                        $this->l('Copy this link and add to cron task'),
                    ),
                        'html_content' => '<div class="col-lg-12">
                            <input readonly="readonly" type="text" value="'.$cron_url.'" />
                        </div>'
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            )
         );

         $form[] = array(
            'form' => array(
            'legend' => array(
                'title' => $this->l('Customer message settings'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                 array(
                    'type' => $s,
                    'label' => $this->l('Additional order message'),
                    'name' => 'PMINPOSTPACZKOMATY_CM_EN',
                    'hint' => array(
                        $this->l('If you want use customer message set to Yes,'),
                    ),
                    'is_default' => 0,
                    'class' => 't',
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
                    )
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Customer message'),
                    'name' => 'PMINPOSTPACZKOMATY_CM',
                    'desc' => array(
                        $this->l('{message_title} - tytuł wiadomości - jest zdefiniowany w tłumaczeniach sklep'),
                        $this->l('{customer} - klient'),
                        $this->l('{order_id} - id zamówienia'),
                        $this->l('{order_reference} - kod referencyjny zamówienia'),
                        $this->l('{name} - numer paczkomatu'),
                        $this->l('{address} - adres'),
                    ),
                    'size' => 20,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            )
         );

         $form[] = array(
            'form' => array(
            'legend' => array(
                'title' => $this->l('Order message settings'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                 array(
                    'type' => $s,
                    'label' => $this->l('Additional customer message'),
                    'name' => 'PMINPOSTPACZKOMATY_OM_EN',
                    'hint' => array(
                        $this->l('If you want use order message set to Yes,'),
                        $this->l('This feature works with easy uploader.'),
                        $this->l('Your customer will get message in order confirmations.'),
                    ),
                    'is_default' => 0,
                    'class' => 't',
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
                    )
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Order message'),
                    'name' => 'PMINPOSTPACZKOMATY_OM',
                    'desc' => array(
                        $this->l('{name} - numer paczkomatu'),
                        $this->l('{address} - adres'),
                    ),
                    'size' => 20,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            )
         );

         $form[] = array(
            'form' => array(
            'legend' => array(
                'title' => $this->l('Package Settings Prepayment').' - '.$this->l('End of week'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Default Size'),
                    'name' => 'PMINPOSTPACZKOMATY_SIZE_EOF',
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('The default size of the parcel paid in advance.'),
                        $this->l('You can change the size when generating the label.')
                    ),
                    'options' => array(
                        'query' => $this->getSizes(),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Delivery method'),
                    'name' => 'PMINPOSTPACZKOMATY_DELIVERY_EOF',
                    'multiple' => true,
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('Delivery method for prepaid shipment.'),
                        $this->l('If you do not see the delivery method, create it.'),
                        $this->l('The module does not automatically create a carrier')
                    ),
                    'options' => array(
                        'query' => $this->getCarriers(),
                        'id' => 'id_carrier',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => $s,
                    'label' => $this->l('Issurance'),
                    'name' => 'PMINPOSTPACZKOMATY_ISSURANCE_EOF',
                    'is_default' => 0,
                    'class' => 't',
                    'hint' => $this->l('Delivery issurance'),
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
                    )
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Use button to create new delivery method'),
                    'name' => 'CREATE_DELIVERY_METHOD_EOF',
                    'hint' => array(
                        $this->l('Click on this button to create delivery method.')
                    ),
                    'html_content' =>
                    '<button name="CREATE_DELIVERY_METHOD" value="prepayment_eof" class="btn btn-default">'.
                        $this->l('Create delivery method')
                    .'</button>'
                ),
                array(
                    'type' => $s,
                    'label' => $this->l('Auto enable'),
                    'name' => 'PMINPOSTPACZKOMATY_EOF_AE',
                    'is_default' => 0,
                    'class' => 't',
                    'hint' => $this->l('Auto enable delivery option'),
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
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Available from day'),
                    'name' => 'PMINPOSTPACZKOMATY_EOF_D',
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('If you want to use this function, remember about the InPost regulations'),
                        $this->l('Select the days of the week and the hours'),
                        $this->l('when the customer will be able to choose the delivery option'),
                        $this->l('The delivery method will be enabled only in this range'),
                    ),
                    'options' => array(
                        'query' => $this->getDays(),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Available from hour'),
                    'name' => 'PMINPOSTPACZKOMATY_EOF_H',
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('If you want to use this function, remember about the InPost regulations'),
                        $this->l('Select the days of the week and the hours'),
                        $this->l('when the customer will be able to choose the delivery option'),
                        $this->l('The delivery method will be enabled only in this range'),
                    ),
                    'options' => array(
                        'query' => $this->getHours(),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Available to day'),
                    'name' => 'PMINPOSTPACZKOMATY_EOF_DT',
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('If you want to use this function, remember about the InPost regulations'),
                        $this->l('Select the days of the week and the hours'),
                        $this->l('when the customer will be able to choose the delivery option'),
                        $this->l('The delivery method will be enabled only in this range'),
                    ),
                    'options' => array(
                        'query' => $this->getDays(),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Available from hour'),
                    'name' => 'PMINPOSTPACZKOMATY_EOF_HT',
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('If you want to use this function, remember about the InPost regulations'),
                        $this->l('Select the days of the week and the hours'),
                        $this->l('when the customer will be able to choose the delivery option'),
                        $this->l('The delivery method will be enabled only in this range'),
                    ),
                    'options' => array(
                        'query' => $this->getHours(),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            )
         );

         $form[] = array(
            'form' => array(
            'legend' => array(
                'title' => $this->l('Package Settings Cash on delivery').' - '.$this->l('End of week'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Default Size'),
                    'name' => 'PMINPOSTPACZKOMATY_SIZE_EOF_COD',
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('The default size of the parcel paid in advance.'),
                        $this->l('You can change the size when generating the label.')
                    ),
                    'options' => array(
                        'query' => $this->getSizes(),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Delivery method'),
                    'name' => 'PMINPOSTPACZKOMATY_DELIVERY_EOF_COD',
                    'multiple' => true,
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('Delivery method for prepaid shipment.'),
                        $this->l('If you do not see the delivery method, create it.'),
                        $this->l('The module does not automatically create a carrier')
                    ),
                    'options' => array(
                        'query' => $this->getCarriers(),
                        'id' => 'id_carrier',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Use button to create new delivery method'),
                    'name' => 'CREATE_DELIVERY_METHOD_EOF_COD',
                    'hint' => array(
                        $this->l('Click on this button to create delivery method.')
                    ),
                    'html_content' =>
                    '<button name="CREATE_DELIVERY_METHOD" value="cod_eof" class="btn btn-default">'.
                        $this->l('Create delivery method')
                    .'</button>'
                ),
                array(
                    'type' => $s,
                    'label' => $this->l('Issurance'),
                    'name' => 'PMINPOSTPACZKOMATY_ISSURANCE_EOF_COD',
                    'is_default' => 0,
                    'class' => 't',
                    'hint' => $this->l('Delivery issurance'),
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
                    )
                ),
                array(
                    'type' => $s,
                    'label' => $this->l('Auto enable'),
                    'name' => 'PMINPOSTPACZKOMATY_EOF_COD_AE',
                    'is_default' => 0,
                    'class' => 't',
                    'hint' => $this->l('Auto enable delivery option'),
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
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Available from day'),
                    'name' => 'PMINPOSTPACZKOMATY_EOF_COD_D',
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('If you want to use this function, remember about the InPost regulations'),
                        $this->l('Select the days of the week and the hours'),
                        $this->l('when the customer will be able to choose the delivery option'),
                        $this->l('The delivery method will be enabled only in this range'),
                    ),
                    'options' => array(
                        'query' => $this->getDays(),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Available from hour'),
                    'name' => 'PMINPOSTPACZKOMATY_EOF_COD_H',
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('If you want to use this function, remember about the InPost regulations'),
                        $this->l('Select the days of the week and the hours'),
                        $this->l('when the customer will be able to choose the delivery option'),
                        $this->l('The delivery method will be enabled only in this range'),
                    ),
                    'options' => array(
                        'query' => $this->getHours(),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Available to day'),
                    'name' => 'PMINPOSTPACZKOMATY_EOF_COD_DT',
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('If you want to use this function, remember about the InPost regulations'),
                        $this->l('Select the days of the week and the hours'),
                        $this->l('when the customer will be able to choose the delivery option'),
                        $this->l('The delivery method will be enabled only in this range'),
                    ),
                    'options' => array(
                        'query' => $this->getDays(),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Available to hour'),
                    'name' => 'PMINPOSTPACZKOMATY_EOF_COD_HT',
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('If you want to use this function, remember about the InPost regulations'),
                        $this->l('Select the days of the week and the hours'),
                        $this->l('when the customer will be able to choose the delivery option'),
                        $this->l('The delivery method will be enabled only in this range'),
                    ),
                    'options' => array(
                        'query' => $this->getHours(),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            )
         );
         
         $params = array(
            'token' => substr(md5(_COOKIE_KEY_), 0, 12)
         );

         $cron_url_bl = $this->context->link->getModuleLink($this->name, 'baselinker', $params);

         $form[] = array(
            'form' => array(
            'legend' => array(
                'title' => $this->l('Baselinker send Parcelocker with api'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Id order start'),
                    'name' => 'PMINPOSTPACZKOMATY_BL_ORDER',
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('If you are use Baselinker connected by API.'),
                        $this->l('To send pracel locker to baselinker. Set first order')
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Baselinker token'),
                    'name' => 'PMINPOSTPACZKOMATY_BL_TOKEN',
                    'is_default' => 0,
                    'hint' => array(
                        $this->l('If you are use Baselinker connected by API.'),
                        $this->l('To send pracel locker to baselinker. Set baselinker token')
                    ),
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Send orders to baselinker'),
                    'name' => 'CREATE_DELIVERY_CRON_URL',
                    'is_default' => 0,
                    'html_content' => '
                        <div class="col-lg-12">
                            <a target="_blank" class="btn btn-default" href="'.$cron_url_bl.'">
                                '.$this->l('Send orders to baselinker').'
                            </a>
                        </div>
                    '
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Link to cron'),
                    'name' => 'CREATE_DELIVERY_CRON_URL',
                    'is_default' => 0,
                    'html_content' => '<div class="col-lg-12">
                            <input readonly="readonly" type="text" value="'.$cron_url_bl.'" />
                        </div>'
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            )
         );
         $form_titles = array();
         foreach ($form as $form_item) {
             $form_titles[] =  array(
                'name' => $form_item['form']['legend']['title'],
                'required' => isset($form_item['form']['legend']['required']) ?
                $form_item['form']['legend']['required'] : false
             );
         }
         $this->context->smarty->assign('form_titles', $form_titles);
         return $form;
    }

    protected function getConfigurationFormValues()
    {
        $return = array(
            'PMINPOSTPACZKOMATY_ADD_COD' => Configuration::get('PMINPOSTPACZKOMATY_ADD_COD'),
            'PMINPOSTPACZKOMATY_BL_CRON' => Configuration::get('PMINPOSTPACZKOMATY_BL_CRON'),
            'PMINPOSTPACZKOMATY_BL_ORDER' => Configuration::get('PMINPOSTPACZKOMATY_BL_ORDER'),
            'PMINPOSTPACZKOMATY_BL_TOKEN' => Configuration::get('PMINPOSTPACZKOMATY_BL_TOKEN'),
            'PMINPOSTPACZKOMATY_BUILDING_NUMBER' => Configuration::get('PMINPOSTPACZKOMATY_BUILDING_NUMBER'),
            'PMINPOSTPACZKOMATY_CITY' => Configuration::get('PMINPOSTPACZKOMATY_CITY'),
            'PMINPOSTPACZKOMATY_CM' => Configuration::get('PMINPOSTPACZKOMATY_CM'),
            'PMINPOSTPACZKOMATY_CM_EN' => Configuration::get('PMINPOSTPACZKOMATY_CM_EN'),
            'PMINPOSTPACZKOMATY_COMPANY_NAME' => Configuration::get('PMINPOSTPACZKOMATY_COMPANY_NAME'),
            'PMINPOSTPACZKOMATY_DELIVERY_BW[]' => $this->getCarriersBw(),
            'PMINPOSTPACZKOMATY_DELIVERY_COD[]' => $this->getCarriersCod(),
            'PMINPOSTPACZKOMATY_DELIVERY_EOF[]' => $this->getCarriersBwEof(),
            'PMINPOSTPACZKOMATY_DELIVERY_EOF_COD[]' => $this->getCarriersCodEof(),
            'PMINPOSTPACZKOMATY_DELIVERY_BW' => $this->getCarriersBw(),
            'PMINPOSTPACZKOMATY_DELIVERY_COD' => $this->getCarriersCod(),
            'PMINPOSTPACZKOMATY_DELIVERY_EOF' => $this->getCarriersBwEof(),
            'PMINPOSTPACZKOMATY_DELIVERY_EOF_COD' => $this->getCarriersCodEof(),
            'PMINPOSTPACZKOMATY_DISPLAY_PLACE' => Configuration::get('PMINPOSTPACZKOMATY_DISPLAY_PLACE'),
            'PMINPOSTPACZKOMATY_DISPLAY_TYPE' => Configuration::get('PMINPOSTPACZKOMATY_DISPLAY_TYPE'),
            'PMINPOSTPACZKOMATY_DF' => Configuration::get('PMINPOSTPACZKOMATY_DF'),
            'PMINPOSTPACZKOMATY_EMAIL' => Configuration::get('PMINPOSTPACZKOMATY_EMAIL'),
            'PMINPOSTPACZKOMATY_EOF_AE' => Configuration::get('PMINPOSTPACZKOMATY_EOF_AE'),
            'PMINPOSTPACZKOMATY_EOF_COD_AE' => Configuration::get('PMINPOSTPACZKOMATY_EOF_COD_AE'),
            'PMINPOSTPACZKOMATY_EOF_COD_D' => Configuration::get('PMINPOSTPACZKOMATY_EOF_COD_D'),
            'PMINPOSTPACZKOMATY_EOF_COD_DT' => Configuration::get('PMINPOSTPACZKOMATY_EOF_COD_DT'),
            'PMINPOSTPACZKOMATY_EOF_COD_H' => Configuration::get('PMINPOSTPACZKOMATY_EOF_COD_H'),
            'PMINPOSTPACZKOMATY_EOF_COD_HT' => Configuration::get('PMINPOSTPACZKOMATY_EOF_COD_HT'),
            'PMINPOSTPACZKOMATY_EOF_D' => Configuration::get('PMINPOSTPACZKOMATY_EOF_D'),
            'PMINPOSTPACZKOMATY_EOF_DT' => Configuration::get('PMINPOSTPACZKOMATY_EOF_DT'),
            'PMINPOSTPACZKOMATY_EOF_H' => Configuration::get('PMINPOSTPACZKOMATY_EOF_H'),
            'PMINPOSTPACZKOMATY_EOF_HT' => Configuration::get('PMINPOSTPACZKOMATY_EOF_HT'),
            'PMINPOSTPACZKOMATY_FIRST_NAME' => Configuration::get('PMINPOSTPACZKOMATY_FIRST_NAME'),
            'PMINPOSTPACZKOMATY_GEO' => Configuration::get('PMINPOSTPACZKOMATY_GEO'),
            'PMINPOSTPACZKOMATY_LL' => Configuration::get('PMINPOSTPACZKOMATY_LL'),
            'PMINPOSTPACZKOMATY_GGMAP' => Configuration::get('PMINPOSTPACZKOMATY_GGMAP'),
            'PMINPOSTPACZKOMATY_GUIDE' => Configuration::get('PMINPOSTPACZKOMATY_GUIDE'),
            'PMINPOSTPACZKOMATY_ID' => Configuration::get('PMINPOSTPACZKOMATY_ID'),
            'PMINPOSTPACZKOMATY_ISSURANCE_BW' => Configuration::get('PMINPOSTPACZKOMATY_ISSURANCE_BW'),
            'PMINPOSTPACZKOMATY_ISSURANCE_COD' => Configuration::get('PMINPOSTPACZKOMATY_ISSURANCE_COD'),
            'PMINPOSTPACZKOMATY_ISSURANCE_EOF' => Configuration::get('PMINPOSTPACZKOMATY_ISSURANCE_EOF'),
            'PMINPOSTPACZKOMATY_ISSURANCE_EOF_COD' => Configuration::get('PMINPOSTPACZKOMATY_ISSURANCE_EOF_COD'),
            'PMINPOSTPACZKOMATY_LABEL_FORMAT' => Configuration::get('PMINPOSTPACZKOMATY_LABEL_FORMAT'),
            'PMINPOSTPACZKOMATY_LABEL_SIZE' => Configuration::get('PMINPOSTPACZKOMATY_LABEL_SIZE'),
            'PMINPOSTPACZKOMATY_LAST_NAME' => Configuration::get('PMINPOSTPACZKOMATY_LAST_NAME'),
            'PMINPOSTPACZKOMATY_LOG' => Configuration::get('PMINPOSTPACZKOMATY_LOG'),
            'PMINPOSTPACZKOMATY_LOGIN' => Configuration::get('PMINPOSTPACZKOMATY_LOGIN'),
            'PMINPOSTPACZKOMATY_MACHINE' => Configuration::get('PMINPOSTPACZKOMATY_MACHINE'),
            'PMINPOSTPACZKOMATY_MAP_KEY' => Configuration::get('PMINPOSTPACZKOMATY_MAP_KEY'),
            'PMINPOSTPACZKOMATY_MAP_KEY2' => Configuration::get('PMINPOSTPACZKOMATY_MAP_KEY2'),
            'PMINPOSTPACZKOMATY_MPK' => Configuration::get('PMINPOSTPACZKOMATY_MPK'),
            'PMINPOSTPACZKOMATY_INSC' => Configuration::get('PMINPOSTPACZKOMATY_INSC'),
            'PMINPOSTPACZKOMATY_NAME' => Configuration::get('PMINPOSTPACZKOMATY_NAME'),
            'PMINPOSTPACZKOMATY_OM' => Configuration::get('PMINPOSTPACZKOMATY_OM'),
            'PMINPOSTPACZKOMATY_OM_EN' => Configuration::get('PMINPOSTPACZKOMATY_OM_EN'),
            'PMINPOSTPACZKOMATY_OS' => Configuration::get('PMINPOSTPACZKOMATY_OS'),
            'PMINPOSTPACZKOMATY_PASSWORD' => Configuration::get('PMINPOSTPACZKOMATY_PASSWORD'),
            'PMINPOSTPACZKOMATY_PHONE' => Configuration::get('PMINPOSTPACZKOMATY_PHONE'),
            'PMINPOSTPACZKOMATY_POST_CODE' => Configuration::get('PMINPOSTPACZKOMATY_POST_CODE'),
            'PMINPOSTPACZKOMATY_REFERENCE' => Configuration::get('PMINPOSTPACZKOMATY_REFERENCE'),
            'PMINPOSTPACZKOMATY_SANDBOX' => Configuration::get('PMINPOSTPACZKOMATY_SANDBOX'),
            'PMINPOSTPACZKOMATY_SHIPPING_METHOD' => Configuration::get('PMINPOSTPACZKOMATY_SHIPPING_METHOD'),
            'PMINPOSTPACZKOMATY_SHIPX' => Configuration::get('PMINPOSTPACZKOMATY_SHIPX'),
            'PMINPOSTPACZKOMATY_SIZE_BW' => Configuration::get('PMINPOSTPACZKOMATY_SIZE_BW'),
            'PMINPOSTPACZKOMATY_SIZE_COD' => Configuration::get('PMINPOSTPACZKOMATY_SIZE_COD'),
            'PMINPOSTPACZKOMATY_SIZE_EOF' => Configuration::get('PMINPOSTPACZKOMATY_SIZE_EOF'),
            'PMINPOSTPACZKOMATY_SIZE_EOF_COD' => Configuration::get('PMINPOSTPACZKOMATY_SIZE_EOF_COD'),
            'PMINPOSTPACZKOMATY_STATUS' => Configuration::get('PMINPOSTPACZKOMATY_STATUS'),
            'PMINPOSTPACZKOMATY_STATUS_AV[]' => explode(',', Configuration::get('PMINPOSTPACZKOMATY_STATUS_AV')),
            'PMINPOSTPACZKOMATY_STATUS_AV' => explode(',', Configuration::get('PMINPOSTPACZKOMATY_STATUS_AV')),
            'PMINPOSTPACZKOMATY_STATUS_DEL' => Configuration::get('PMINPOSTPACZKOMATY_STATUS_DEL'),
            'PMINPOSTPACZKOMATY_STATUS_PIC' => Configuration::get('PMINPOSTPACZKOMATY_STATUS_PIC'),
            'PMINPOSTPACZKOMATY_STREET' => Configuration::get('PMINPOSTPACZKOMATY_STREET'),
            'PMINPOSTPACZKOMATY_TOKEN' => Configuration::get('PMINPOSTPACZKOMATY_TOKEN'),
            'PMINPOSTPACZKOMATY_WPL' => Configuration::get('PMINPOSTPACZKOMATY_WPL'),
            'selected_menu' => Tools::getValue('selected_menu'),
        );
        
        return $return;
    }

    private function switchSandbox()
    {
        if (Configuration::get('PMINPOSTPACZKOMATY_SANDBOX')) {
            $this->sandbox = true;
            InpostApiShipx::$shipxUrl = 'https://sandbox-api-shipx-pl.easypack24.net';
            InpostApi::$wsdl = 'https://sandbox-api.paczkomaty.pl/';
        } else {
            $this->sandbox = false;
        }
    }


    public function validatePostFields()
    {
        $bw = Tools::getValue('PMINPOSTPACZKOMATY_DELIVERY_BW');
        if (!is_array($bw)) {
            $this->warnings[] = $this->l('Please select delivery method tab:').' '.$this->l('Delivery method');
        }

        if (trim(Tools::getValue('PMINPOSTPACZKOMATY_COMPANY_NAME')) == '' ||
        trim(Tools::getValue('PMINPOSTPACZKOMATY_FIRST_NAME')) == '' ||
        trim(Tools::getValue('PMINPOSTPACZKOMATY_LAST_NAME')) == '' ||
        trim(Tools::getValue('PMINPOSTPACZKOMATY_EMAIL')) == '' ||
        trim(Tools::getValue('PMINPOSTPACZKOMATY_PHONE')) == '' ||
        trim(Tools::getValue('PMINPOSTPACZKOMATY_STREET')) == '' ||
        trim(Tools::getValue('PMINPOSTPACZKOMATY_BUILDING_NUMBER')) == '' ||
        trim(Tools::getValue('PMINPOSTPACZKOMATY_POST_CODE')) == '' ||
        trim(Tools::getValue('PMINPOSTPACZKOMATY_CITY')) == ''
        ) {
            $this->warnings[] = $this->l('Please enter company details tab:').' '.$this->l('Sender settings');
        }
        if (trim(Tools::getValue('PMINPOSTPACZKOMATY_ID')) != '') {
            $id = Tools::getValue('PMINPOSTPACZKOMATY_ID');
            if (!is_numeric($id)) {
                $this->warnings[] = $this->l('Id organization').' '.$this->l('Should be numeric');
            }
        } elseif (Tools::getValue('PMINPOSTPACZKOMATY_SHIPX') && trim(Tools::getValue('PMINPOSTPACZKOMATY_ID')) == '') {
            $this->errors[] = $this->l('Please enter id organization');
        }
    }

    public function removeOldValues($config)
    {
        foreach (array_keys($config) as $key) {
            Configuration::deleteByName($key);
        }
    }

    protected function postProcess()
    {
        $this->confirmations[] = $this->l('Settings updated.');
        $this->validatePostFields();
        $form_values = $this->getConfigurationFormValues();
        $this->removeOldValues($form_values);
        foreach (array_keys($form_values) as $key) {
            if ($key == 'PMINPOSTPACZKOMATY_PHONE') {
                $value = Tools::getValue($key);
                $value = str_replace(array('-',' ','+48'), array('', '',''), $value);
                if (strlen($value) != 9) {
                    $this->warnings[] = $this->l('Please enter vaild phone number');
                }
                Configuration::updateValue($key, $value);
            } elseif ($key == 'PMINPOSTPACZKOMATY_DELIVERY_BW[]' || $key == 'PMINPOSTPACZKOMATY_DELIVERY_BW') {
                $value = Tools::getValue('PMINPOSTPACZKOMATY_DELIVERY_BW');
                $this->updateSelectedCarriersBw($value);
            } elseif ($key == 'PMINPOSTPACZKOMATY_DELIVERY_COD[]' || $key == 'PMINPOSTPACZKOMATY_DELIVERY_COD') {
                $value = Tools::getValue('PMINPOSTPACZKOMATY_DELIVERY_COD');
                $this->updateSelectedCarriersCod($value);
            } elseif ($key == 'PMINPOSTPACZKOMATY_DELIVERY_EOF[]' || $key == 'PMINPOSTPACZKOMATY_DELIVERY_EOF') {
                $value = Tools::getValue('PMINPOSTPACZKOMATY_DELIVERY_EOF');
                $this->updateSelectedCarriersBwEof($value);
            } elseif ($key == 'PMINPOSTPACZKOMATY_DELIVERY_EOF_COD[]' || $key == 'PMINPOSTPACZKOMATY_DELIVERY_EOF_COD') {
                $value = Tools::getValue('PMINPOSTPACZKOMATY_DELIVERY_EOF_COD');
                $this->updateSelectedCarriersCodEof($value);
            } elseif ($key == 'PMINPOSTPACZKOMATY_STATUS_AV[]' || $key == 'PMINPOSTPACZKOMATY_STATUS_AV') {
                $value = Tools::getValue('PMINPOSTPACZKOMATY_STATUS_AV');
                $this->updateSelectedStatuses($value);
            } else {
                $value = Tools::getValue($key);
                if ($key == 'PMINPOSTPACZKOMATY_TOKEN') {
                    $value = trim($value);
                }
                Configuration::updateValue($key, $value);
            }
        }
        if (Configuration::get('PMINPOSTPACZKOMATY_LABEL_FORMAT') == 'Zpl' &&
        !Configuration::get('PMINPOSTPACZKOMATY_SHIPX')
        ) {
            Configuration::updateValue('PMINPOSTPACZKOMATY_LABEL_FORMAT', 'Pdf');
            $this->warnings[] = $this->l('Zpl format is not supported with old API, Defalt Value: Pdf');
        }
        if (Configuration::get('PMINPOSTPACZKOMATY_SHIPX')) {
            $id = Tab::getIdFromClassName('PmInpostPaczkomatyZlecenia');
            if ($id) {
                $tab = new Tab($id);
                $tab->active = true;
                $tab->update();
            }
        } else {
            $id = Tab::getIdFromClassName('PmInpostPaczkomatyZlecenia');
            if ($id) {
                $tab = new Tab($id);
                $tab->active = false;
                $tab->update();
            }
        }

        if (Tools::getValue('PMINPOSTPACZKOMATY_EOF_AE')) {
            $config = Configuration::getMultiple(
                array(
                'PMINPOSTPACZKOMATY_EOF_D',
                'PMINPOSTPACZKOMATY_EOF_H',
                'PMINPOSTPACZKOMATY_EOF_DT',
                'PMINPOSTPACZKOMATY_EOF_HT',
                )
            );
            $start = $config['PMINPOSTPACZKOMATY_EOF_D'].str_replace(':', '', $config['PMINPOSTPACZKOMATY_EOF_H']);
            $end = $config['PMINPOSTPACZKOMATY_EOF_DT'].str_replace(':', '', $config['PMINPOSTPACZKOMATY_EOF_HT']);
            if ($start > $end) {
                $this->warnings[] = $this->l('End of week').': '.$this->l('Please check start date and hour').' '.
                $this->l('Option is disable');
                Configuration::updateValue('PMINPOSTPACZKOMATY_EOF_AE', false);
            }
        }

        if (Tools::getValue('PMINPOSTPACZKOMATY_EOF_COD_AE')) {
            $config = Configuration::getMultiple(
                array(
                    'PMINPOSTPACZKOMATY_EOF_COD_D',
                    'PMINPOSTPACZKOMATY_EOF_COD_H',
                    'PMINPOSTPACZKOMATY_EOF_COD_DT',
                    'PMINPOSTPACZKOMATY_EOF_COD_HT',
                    'PMINPOSTPACZKOMATY_EOF_COD_LAST'
                )
            );
            $start = $config['PMINPOSTPACZKOMATY_EOF_COD_D'].str_replace(':', '', $config['PMINPOSTPACZKOMATY_EOF_COD_H']);
            $end = $config['PMINPOSTPACZKOMATY_EOF_COD_DT'].str_replace(':', '', $config['PMINPOSTPACZKOMATY_EOF_COD_HT']);
            if ($start > $end) {
                $this->warnings[] = $this->l('End of week').' '.$this->l('Cash on delivery').': '.
                $this->l('Please check start date and hour').' '.
                $this->l('Option is disable');
                Configuration::updateValue('PMINPOSTPACZKOMATY_EOF_COD_AE', false);
            }
        }

        if (Tools::isSubmit('PMINPOSTPACZKOMATY_LOG') && Tools::getValue('PMINPOSTPACZKOMATY_LOG') == 0) {
            if (file_exists(dirname(__FILE__).'/log/log.txt')) {
                unlink(dirname(__FILE__).'/log/log.txt');
            }
        }
        if (Tools::isSubmit('CREATE_DELIVERY_METHOD')) {
            $this->createDeliveryMethod();
        }
        $this->switchSandbox();

        if (sizeof($this->getCarriersBw()) == 0) {
            $this->warnings[] = $this->l('Please chose Parcel locker delivery method');
        }

        if (sizeof($this->getCarriersCod()) == 0) {
            $this->warnings[] = $this->l('Please chose Parcel locker delivery method for COD');
        }

        if ((int)Configuration::get('PMINPOSTPACZKOMATY_GGMAP') &&
        Configuration::get('PMINPOSTPACZKOMATY_MAP_KEY') == '') {
            $this->warnings[] = $this->l('Please enter: Google map key');
        }

        if ((int)Configuration::get('PMINPOSTPACZKOMATY_GGMAP') &&
        Configuration::get('PMINPOSTPACZKOMATY_WPL') && Configuration::get('PMINPOSTPACZKOMATY_MAP_KEY2') == '') {
            $this->warnings[] = $this->l('Please enter: Google map key (for search)');
        }

        if (Tools::isSubmit('saveAndTest') && Tools::getValue('PMINPOSTPACZKOMATY_SHIPX') == 1) {
            $api = new InpostApiShipx();
            $connection = $api->checkConnections();
            if ($connection === false) {
                $this->errors[] = $this->l('Can not connect to API');
            } elseif (isset($connection['status']) && $connection['status'] == 401) {
                $this->errors[] = $this->l('There is some error: ').$this->parseErrors($connection['message']);
            } elseif (isset($connection['count'])) {
                $this->confirmations[] = $this->l('Connect successfully');
                if ($connection['count'] == 1) {
                    $this->context->smarty->assign('company_informations', $connection['items'][0]);
                    if (Configuration::get('PMINPOSTPACZKOMATY_COMPANY_NAME') == '') {
                        if (isset($connection['items'][0]['name'])) {
                            Configuration::updateValue(
                                'PMINPOSTPACZKOMATY_COMPANY_NAME',
                                $connection['items'][0]['name']
                            );
                        }
                    }
                    if (Configuration::get('PMINPOSTPACZKOMATY_STREET') == '') {
                        if (isset($connection['items'][0]['address']['street'])) {
                            Configuration::updateValue(
                                'PMINPOSTPACZKOMATY_STREET',
                                $connection['items'][0]['address']['street']
                            );
                        }
                    }
                    if (Configuration::get('PMINPOSTPACZKOMATY_BUILDING_NUMBER') == '') {
                        if (isset($connection['items'][0]['address']['building_number'])) {
                            Configuration::updateValue(
                                'PMINPOSTPACZKOMATY_BUILDING_NUMBER',
                                $connection['items'][0]['address']['building_number']
                            );
                        }
                    }
                    if (Configuration::get('PMINPOSTPACZKOMATY_POST_CODE') == '') {
                        if (isset($connection['items'][0]['address']['post_code'])) {
                            Configuration::updateValue(
                                'PMINPOSTPACZKOMATY_POST_CODE',
                                $connection['items'][0]['address']['post_code']
                            );
                        }
                    }
                    if (Configuration::get('PMINPOSTPACZKOMATY_CITY') == '') {
                        if (isset($connection['items'][0]['address']['city'])) {
                            Configuration::updateValue(
                                'PMINPOSTPACZKOMATY_CITY',
                                $connection['items'][0]['address']['city']
                            );
                        }
                    }
                }
            }
        } elseif (Tools::isSubmit('saveAndTest') && Tools::getValue('PMINPOSTPACZKOMATY_SHIPX') == 0) {
            if (!ini_get('allow_url_fopen')) {
                $this->errors[] =
                $this->l('Test connection faild').' '.
                $this->l('On the server enable allow_url_fopen, ask your provider how to enable the option.').' '.
                $this->l('You can try using ShipX');
            } else {
                $api = new InpostApi();
                $result = $api->checkConnections();
                if ($result) {
                    $this->confirmations[] = $this->l('Connect successfully');
                } else {
                    $this->errors[] = $this->l('There is some error: ').$this->l('Can not connect to API');
                }
            }
        }
        Configuration::clearConfigurationCacheForTesting();
    }

    public function mFil($array, $array2)
    {
        $return = array_merge($array, $array2);
        if (function_exists('array_filter')) {
            $return = array_filter($return, 'strlen');
        }
        return $return;
    }
    
    private function addMediaCart()
    {
        $opc_module = '';
        if ($this->version_ps == '1.7') {
            if (Module::isInstalled('supercheckout') && Module::isEnabled('supercheckout')) {
                $settings = unserialize(Configuration::get('VELOCITY_SUPERCHECKOUT'));
                if (isset($settings['enable']) && $settings['enable']) {
                    $opc_module = 'supercheckout_17';
                }
            }
            if (Module::isInstalled('steasycheckout') && Module::isEnabled('steasycheckout')) {
                $opc_module = 'steasycheckout_17';
            }

            if (Module::isInstalled('spstepcheckout') && Module::isEnabled('spstepcheckout')) {
                $opc_module = 'spstepcheckout_17';
            }
        }
        $this->getCustomerLastInpostMachineAndSetCurrentCart();
        $def = array(
            'prestakey' => md5(_COOKIE_KEY_),
            'pm_inpostpaczkomaty_sandbox' => $this->sandbox,
            'pm_inpostpaczkomaty_opc' => $opc_module,
            'pm_inpostpaczkomaty_gcup' => $this->context->link->getModuleLink($this->name, 'ajax'),
            'pm_inpostpaczkomaty_carrier' => $this->mFil($this->getCarriersBw(), $this->getCarriersBwEof()),
            'pm_inpostpaczkomaty_carrier_cod' => $this->mFil($this->getCarriersCod(), $this->getCarriersCodEof()),
            'pm_inpostpaczkomaty_carrier_eof' => $this->mFil($this->getCarriersCodEof(), $this->getCarriersBwEof()),
            'pm_inpostpaczkomaty_selected' => $this->getSelectedMachine(),
            'pm_inpostpaczkomaty_type' => Configuration::get('PMINPOSTPACZKOMATY_DISPLAY_TYPE'),
            'pm_inpostpaczkomaty_display_place' => Configuration::get('PMINPOSTPACZKOMATY_DISPLAY_PLACE'),
            'pm_inpostpaczkomaty_typewpzl' => Configuration::get('PMINPOSTPACZKOMATY_WPL'),
            'pm_inpostpaczkomaty_token' => Tools::encrypt(Context::getContext()->cart->id),
            'pm_inpostpaczkomaty_ps' => $this->version_ps,
            'pm_inpostpaczkomaty_geo' => (int)Configuration::get('PMINPOSTPACZKOMATY_GEO'),
            'pm_inpost_placeholder2' => $this->l('Use button to select machine'),
            'paczkomatySelectLabel' => $this->l('Please select machine to your order'),
            'pm_inpostpaczkomaty_label_weekend' =>
                $this->l('Please select machine to your order').' '.
                $this->l(' - only parcel lockers for weekend packages'),
            'pm_inpostpaczkomaty_label' => $this->l('Select machine'),
            'pm_inpostpaczkomaty_mapkey' => Configuration::get('PMINPOSTPACZKOMATY_MAP_KEY'),
            'pm_inpost_placeholder' => $this->l('Enter city, street, or machine name'),
            'current_cart' => Context::getContext()->cart->id,
            'modulepath' => $this->context->link->getModuleLink($this->name, 'ajax'),
            'id_pudo_carrier' => '0',
            'pm_inpostpaczkomaty_paczkomatLabel' => $this->l('Machine: '),
        );
        
        Media::addJsDef($def);
    }

    public function isCarrierEnabled()
    {
        $s = 0;
        $s += sizeof($this->getCarriersBw());
        $s += sizeof($this->getCarriersCod());
        $s += sizeof($this->getCarriersBwEof());
        $s += sizeof($this->getCarriersCodEof());
        if ($s) {
            return true;
        } else {
            return false;
        }
    }

    private function getSizes()
    {
        $sizes = array();
        $sizes[] = array(
        'id_option' => 'A',
        'name' => $this->l('A')
        );
        $sizes[] = array(
        'id_option' => 'B',
        'name' => $this->l('B')
        );
        $sizes[] = array(
        'id_option' => 'C',
        'name' => $this->l('C')
        );
        return $sizes;
    }

    private function getDays()
    {
        $days = array();

        $days[] = array(
        'id_option' => '1',
        'name' => $this->l('Monday')
        );

        $days[] = array(
        'id_option' => '2',
        'name' => $this->l('Tuesday')
        );

        $days[] = array(
        'id_option' => '3',
        'name' => $this->l('Wednesday')
        );

        $days[] = array(
        'id_option' => '4',
        'name' => $this->l('Thursday')
        );

        $days[] = array(
        'id_option' => '5',
        'name' => $this->l('Friday')
        );

        $days[] = array(
        'id_option' => '6',
        'name' => $this->l('Saturday')
        );

        $days[] = array(
        'id_option' => '7',
        'name' => $this->l('Sunday')
        );

        return $days;
    }

    private function getHours()
    {
        $hours = array();

        for ($i = 0; $i <= 23; $i++) {
            $j = $i;
            if ($j < 10) {
                $j = '0'.$j;
            }
            $hours[] = array('id_option' => $j.':00', 'name' => $i.':00');
            $hours[] = array('id_option' => $j.':30', 'name' => $i.':30');
        }

        return $hours;
    }

    private function getLabelFormat()
    {
        $sizes = array();
        $sizes[] = array(
        'id_option' => 'Pdf',
        'name' => $this->l('Pdf')
        );
        if (Configuration::get('PMINPOSTPACZKOMATY_SHIPX')) {
            $sizes[] = array(
            'id_option' => 'Zpl',
            'name' => $this->l('Zpl')
            );
        }
        $sizes[] = array(
        'id_option' => 'Epl',
        'name' => $this->l('Epl')
        );
        return $sizes;
    }

    private function getLabelSize()
    {
        $sizes = array();
        $sizes[] = array(
        'id_option' => 'A4',
        'name' => $this->l('A4')
        );
        $sizes[] = array(
            'id_option' => 'A6P',
            'name' => $this->l('A6P')
        );
        return $sizes;
    }

    private function getCarriers()
    {
        $carriers_list = Carrier::getCarriers(Context::getContext()->language->id);
        return $carriers_list;
    }

    private function getMaps()
    {
        $map_list = array(
        array(
            'id' => 0,
            'name' => $this->l('Open maps')
        ),
        array(
            'id' => 1,
            'name' => $this->l('Google maps maps')
        ),
        );

        return $map_list;
    }

    private function getShippingMethod()
    {
        $shipping_method = array();
        $shipping_method[] = array(
        'id_option' => '1',
        'name' => $this->l('Giving in to the machine')
        );
        $shipping_method[] = array(
        'id_option' => '0',
        'name' => $this->l('I will create a pickup order (courier) service extra paid')
        );

        $shipping_method[] = array(
        'id_option' => '2',
        'name' => $this->l('Giving in to the POP')
        );
        return $shipping_method;
    }

    private function getDisplayTypes()
    {
        $display_type = array();
        $display_type[] = array(
        'id_option' => '2',
        'name' => $this->l('Display button with map')
        );
        $display_type[] = array(
        'id_option' => '1',
        'name' => $this->l('Display list')
        );
        return $display_type;
    }

    private function getDisplayPlace()
    {
        $display_place = array();
        $display_place[] = array(
        'id_option' => '1',
        'name' => $this->l('Display after table')
        );
        $display_place[] = array(
        'id_option' => '2',
        'name' => $this->l('Display before table')
        );
        $display_place[] = array(
        'id_option' => '3',
        'name' => $this->l('Display after carrier description')
        );
        $display_place[] = array(
        'id_option' => '4',
        'name' => $this->l('Display before carrier description')
        );
        if (version_compare(_PS_VERSION_, '1.7.0', '>=') === false) {
            $display_place[] = array(
            'id_option' => '5',
            'name' => $this->l('Display in HOOK BEFORE CARRIER')
            );
        }
        if (version_compare(_PS_VERSION_, '1.7.0', '>=') === false) {
            $display_place[] = array(
            'id_option' => '6',
            'name' => $this->l('Display in HOOK EXTRA CARRIER')
            );
        }
        return $display_place;
    }

    private function findInArray($search_in, $term)
    {
        foreach ($search_in as $search_in_item) {
            if (strpos($search_in_item, mb_strtolower($term)) !== false) {
                return 1;
            }
        }
        return 0;
    }

    private function sortResult($items, $terms)
    {
        $search = array();
        $find = array();
        $terms = str_replace('-', ' ', $terms);
        $terms = explode(' ', $terms);
        foreach ($items as $items_item) {
            $search = array();
            $search[] = $items_item->name;
            $search[] = $items_item->address->line1;
            $search[] = $items_item->address->line2;
            $search = array_merge($search, (array)$items_item->address_details);
            $search = array_map('mb_strtolower', $search);
            $math = 0;
            foreach ($terms as $terms_item) {
                $math += $this->findInArray($search, $terms_item);
            }

            $find[] = array('math' => $math, 'item' =>$items_item);
        }


        usort($find, function ($a, $b) {
            return $b['math'] - $a['math'];
        });
        $objects = array();
        foreach ($find as $find_item) {
            $objects[] = $find_item['item'];
        }
        return  $objects;
    }

    private function getPoint($point_name)
    {
        if ($this->sandbox) {
            $result = @file_get_contents('https://sandbox-api-pl-points.easypack24.net/v1/points/'.$point_name);
            $paczkomat = @json_decode($result);
            if (is_object($paczkomat)) {
                return array(
                    '{name}' => $point_name,
                    '{address}' => $paczkomat->address->line1.', '.$paczkomat->address->line2
                );
            }
        } elseif (!empty($point_name)) {
            $result = @file_get_contents('https://api-pl-points.easypack24.net/v1/points/'.$point_name);
            $paczkomat = @json_decode($result);
            if (is_object($paczkomat)) {
                return array(
                    '{name}' => $point_name,
                    '{address}' => $paczkomat->address->line1.', '.$paczkomat->address->line2
                );
            }
        }
        return false;
    }

    function capitalize($str, $encoding = 'UTF-8')
    {
        return mb_convert_case($str, MB_CASE_TITLE);
    }

    private function getPoints()
    {
        $get = $_GET;
        $terms = Tools::getValue('term');
        $get['city'] = trim(mb_strtolower($get['term']));
        $get['city'] = $this->capitalize($get['city']);
        unset($get['term'], $get['prequest'], $get['module'], $get['controller'], $get['fc']);
        $get2 = $get;
        unset($get2['city']);
        $params = http_build_query($get);
        $params = str_replace('%5B0%5D=', '%5B%5D=', $params);
        $params2 = http_build_query($get2);
        $params2 = str_replace('%5B0%5D=', '%5B%5D=', $params2);
        if ($this->sandbox) {
            $map_result = file_get_contents('https://sandbox-api-pl-points.easypack24.net/v1/points/?'.$params);
        } else {
            $map_result = file_get_contents('https://api-pl-points.easypack24.net/v1/points/?'.$params);
        }

        $result = json_decode($map_result);
        if (sizeof($result->items) == 0) {
            if ($this->sandbox) {
                $map_result = file_get_contents('https://sandbox-api-pl-points.easypack24.net/v1/points/?'.$params2);
            } else {
                $map_result = file_get_contents('https://api-pl-points.easypack24.net/v1/points/?'.$params2);
            }
            $result = json_decode($map_result);
        }
        foreach ($result->items as $key => $item) {
            if (strpos($item->name, 'POP') !== false) {
                unset($result->items[$key]);
            }
        }
        $result->items = $this->sortResult($result->items, $terms);

        ob_clean();
        die(json_encode($result));
    }

    private function selectMachine()
    {
        $id_cart = (int)Tools::getValue('current_cart');
        if (Tools::getValue('token') == Tools::encrypt($id_cart)) {
            $paczkomatyList = PaczkomatyList::getByIdCart($id_cart);
            $paczkomatyList->machine = pSql(Tools::getValue('selected'));
            $paczkomatyList->save();
        } else {
            die(Tools::jsonEncode(array('Bad token')));
        }
    }

    private function getLocation()
    {
        $id_cart = Context::getContext()->cart->id;
        if (Tools::getValue('token') == Tools::encrypt($id_cart) || Tools::getValue('token') == md5(_COOKIE_KEY_)) {
            foreach (array_keys($_GET) as $key) {
                if (!in_array($key, array('address','timestamp','ajaxSearch','key'))) {
                    unset($_GET[$key]);
                }
            }
            ob_clean();

            $address = urlencode(Tools::getValue('address'));
            $md5trimaddress = md5($address);
            if (file_exists(dirname(__FILE__).'/cache/mapcache/'.$md5trimaddress)) {
                die(Tools::file_get_contents(dirname(__FILE__).'/cache/mapcache/'.$md5trimaddress));
            }
            if ((int)Configuration::get('PMINPOSTPACZKOMATY_GGMAP') == 0) {
                $user_agent = $_SERVER['HTTP_USER_AGENT'];
                $opts = array('http' => array('header'=>"User-Agent: $user_agent\r\n"));
                $context = stream_context_create($opts);
                $map_result = file_get_contents(
                    'https://osm.inpost.pl/nominatim/search?q='.$address.'&format=jsonv2',
                    false,
                    $context
                );
                $result = array();
                $r = Tools::jsonDecode($map_result);

                if (isset($r[0])) {
                    $lat = $r[0]->lat;
                    $lon = $r[0]->lon;
                    if (isset($r[0])) {
                        $result['status'] = 'OK';
                        $result['results'][0]['geometry']['location']['lat'] = $lat;
                        $result['results'][0]['geometry']['location']['lng'] = $lon;
                    }
                    $jsoncontent = Tools::jsonEncode($result);
                    echo($jsoncontent);
                    file_put_contents(dirname(__FILE__).'/cache/mapcache/'.$md5trimaddress, $jsoncontent);
                    die();
                } else {
                    $result['status'] = 'Bad location';
                    $jsoncontent = Tools::jsonEncode($result);
                    echo($jsoncontent);
                    die();
                }
            } else {
                $_GET['key'] = Configuration::get('PMINPOSTPACZKOMATY_MAP_KEY2');
                $result = Tools::jsonDecode(
                    file_get_contents(
                        'https://maps.googleapis.com/maps/api/geocode/json?'.http_build_query($_GET)
                    )
                );
                $jsoncontent = Tools::jsonEncode($result);
                echo($jsoncontent);
                file_put_contents(dirname(__FILE__).'/cache/mapcache/'.$md5trimaddress, $jsoncontent);
                die();
            }
        } else {
            $result = array();
            $result['status'] = 'NO';
            $result['status'] = 'Bad token';
            $result['error_message'] = 'Bad token';
            echo Tools::jsonEncode($result);
            die();
        }
    }

    public function ajaxCall()
    {
        if (Tools::isSubmit('prequest')) {
            $this->getPoints();
        } elseif (Tools::isSubmit('selmachine')) {
            $this->selectMachine();
        } elseif (Tools::isSubmit('address') && Tools::isSubmit('timestamp') &&
        Tools::isSubmit('ajaxSearch') && Tools::isSubmit('key')) {
            $this->getLocation();
        }
    }

    private function getSelectedMachine()
    {
        $cart = Context::getContext()->cart;
        return PaczkomatyList::getSelectedMachine($cart->id);
    }

    public function getSelectedMachineForPayment()
    {
        $cart = Context::getContext()->cart;
        if ($this->isSelectedCarrierBw($cart->id_carrier) ||
            $this->isSelectedCarrierBwEof($cart->id_carrier) ||
            $this->isSelectedCarrierCod($cart->id_carrier) ||
            $this->isSelectedCarrierCodEof($cart->id_carrier)
        ) {
            return $this->l('Selected machine: ').PaczkomatyList::getSelectedMachine($cart->id);
        }
    }

    public function getDeliverListForm($button = 1)
    {
        $s = 'switch';
        $form = array();
        $buttons = array(
        'send' => array(
            'title' => $this->l('Send Parcel'),
            'icon' => 'icon-save',
            'class' => 'btn btn-primary pminpostbutton-ajax send',
            'href' => Context::getContext()->link->getAdminLink(
                'PmInpostPaczkomatyOrder'
            ).'&send=1&id_order='.(int)Tools::getValue('id_order')
        ),
        'printlabel' => array(
            'title' => $this->l('Print label'),
            'icon' => 'icon-print',
            'class' => 'btn btn-primary pminpostbutton-ajax printlabel',
            'href' => Context::getContext()->link->getAdminLink(
                'PmInpostPaczkomatyOrder'
            ).'&printlabel=1&id_order='.(int)Tools::getValue('id_order')
        ),
        'createnew' => array(
            'title' => $this->l('Create addition'),
            'icon' => 'icon-file',
            'class' => 'btn btn-primary pminpostbutton-ajax createnew',
            'href' => Context::getContext()->link->getAdminLink(
                'PmInpostPaczkomatyOrder'
            ).'&createnew=1&id_order='.(int)Tools::getValue('id_order')
        ),
        'sendandprint' => array(
            'title' => $this->l('Send and Print Label'),
            'icon' => 'icon-print',
            'class' => 'btn btn-primary pminpostbutton-ajax sendandprint',
            'href' => Context::getContext()->link->getAdminLink('PmInpostPaczkomatyOrder').
            '&sendandprint=1&id_order='.(int)Tools::getValue('id_order')
            ),
        );

        if ($button == 1) {
            $buttons['printlabel']['class'] = $buttons['printlabel']['class'] .= ' hidden';
            $buttons['createnew']['class'] = $buttons['createnew']['class'] .= ' hidden';
        }

        if ($button == 2) {
            $buttons['send']['class'] = $buttons['send']['class'] .= ' hidden';
            $buttons['sendandprint']['class'] = $buttons['sendandprint']['class'] .= ' hidden';
        }

        if ($button == 3) {
            $buttons['send']['class'] = $buttons['send']['class'] .= ' hidden';
            $buttons['sendandprint']['class'] = $buttons['sendandprint']['class'] .= ' hidden';
        }
        $o = new Order(Tools::getValue('id_order'));
        $this->context->smarty->assign(
            array(
            'packages' => PaczkomatyList::getPackages($o->id_cart),
            'id_order' => Tools::getValue('id_order')
            )
        );
        $others = $this->context->smarty->fetch(dirname(__FILE__).'/views/templates/admin/others.tpl');
        $form[] = array(
        'form' => array(
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Recipient`s e-mail address'),
                    'name' => 'pminpostorder_email',
                    'class' => 'form-control',
                    'required' => true,
                    'size' => 20,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Mobile phone'),
                    'name' => 'pminpostorder_phone',
                    'required' => true,
                    'size' => 20,
                    'class' => 'paczkomaty_input form-control',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('To machine InPost'),
                    'name' => 'pminpostorder_selected',
                    'is_default' => 0,
                    'class' => 'widget form-control',
                    'required' => true,
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Size'),
                    'name' => 'pminpostorder_size',
                    'is_default' => 0,
                    'br' => false,
                    'class' => 'btnpaczkomaty-size',
                    'required' => true,
                    'values' => array(
                        array(
                            'id' => 'A',
                            'value' => 'A',
                            'label' => $this->l(' A')
                        ),
                        array(
                            'id' => 'B',
                            'value' => 'B',
                            'label' => $this->l(' B')
                        ),
                        array(
                            'id' => 'C',
                            'value' => 'C',
                            'label' => $this->l(' C')
                        )
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Reference number'),
                    'name' => 'pminpostorder_reference',
                    'size' => 20,
                    'class' => 'paczkomaty_input form-control',
                ),
                array(
                    'type' => $s,
                    'label' => $this->l('Giving to the machine'),
                    'name' => 'pminpostorder_machine',
                    'is_default' => 0,
                    'class' => 'paczkomaty_input',
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
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('From machine InPost'),
                    'name' => 'pminpostorder_selected_from',
                    'is_default' => 0,
                    'class' => 'widget form-control',
                    'required' => true,
                ),
                array(
                    'type' => $s,
                    'label' => $this->l('COD'),
                    'name' => 'pminpostorder_pobranie',
                    'is_default' => 0,
                    'class' => 'paczkomaty_input t machine',
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
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('COD Value'),
                    'name' => 'pminpostorder_pobranie_value',
                    'is_default' => 0,
                    'class' => 'paczkomaty_input t machine form-control',
                ),
                array(
                    'type' => $s,
                    'label' => $this->l('Issurance'),
                    'name' => 'pminpostorder_ubezpieczenie',
                    'is_default' => 0,
                    'class' => 'paczkomaty_input t machine',
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
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Isurance Value'),
                    'name' => 'pminpostorder_ubezpieczenie_value',
                    'is_default' => 0,
                    'class' => 'paczkomaty_input t machine form-control',
                ),
                array(
                    'type' => $s,
                    'label' => $this->l('End of week'),
                    'name' => 'pminpostorder_eof',
                    'is_default' => 0,
                    'class' => 'paczkomaty_input t machine',
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
                    )
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Tracking numers'),
                    'name' => 'OTHER_PACKAGES',
                    'html_content' => $others
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Label format'),
                    'name' => 'pminpostorder_format',
                    'class' => 'custom-select',
                    'is_default' => 0,
                    'options' => array(
                        'query' => $this->getLabelFormat(),
                        'id' => 'id_option',
                        'name' => 'name'
                    ),
                    'hint' => $this->l('Label file format'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Label type'),
                    'name' => 'pminpostorder_size_l',
                    'class' => 'custom-select',
                    'is_default' => 0,
                    'options' => array(
                        'query' => $this->getLabelSize(),
                        'id' => 'id_option',
                        'name' => 'name'
                    ),
                    'hint' => $this->l('The size of the downloaded file'),
                ),
            ),
            'buttons' => $buttons
            )
        );
        return $form;
    }

    public function isSelectedCarrierBw($id_carrier)
    {
        return in_array($id_carrier, $this->getCarriersBw()) ||$this->isSelectedCarrierBwEof($id_carrier);
    }

    public function isSelectedCarrierBwEof($id_carrier)
    {
        return in_array($id_carrier, $this->getCarriersBwEof());
    }

    private function filterResult(&$result)
    {
        $results = array();
        if (Module::isInstalled('onepagecheckout')) {
            foreach ($result as &$result_item) {
                if ($result_item) {
                    $results[] = Cart::intifier($result_item.',');
                    $results[] = $result_item;
                }
            }

            $result = array_filter($results, 'strlen');
        }
    }

    public function getCarriersBw()
    {
        $result = explode(',', Configuration::get('PMINPOSTPACZKOMATY_DELIVERY_BW'));
        $this->filterResult($result);
        return $result;
    }

    public function getCarriersBwEof()
    {
        $result = explode(',', Configuration::get('PMINPOSTPACZKOMATY_DELIVERY_EOF'));
        $this->filterResult($result);
        return $result;
    }

    public function isSelectedCarrierCod($id_carrier)
    {
        return in_array($id_carrier, $this->getCarriersCod()) || $this->isSelectedCarrierCodEof($id_carrier);
    }

    public function isSelectedCarrierCodEof($id_carrier)
    {
        return in_array($id_carrier, $this->getCarriersCodEof());
    }

    public function getCarriersCod()
    {
        $result = explode(',', Configuration::get('PMINPOSTPACZKOMATY_DELIVERY_COD'));
        $this->filterResult($result);
        return $result;
    }

    public function getCarriersCodEof()
    {
        $result = explode(',', Configuration::get('PMINPOSTPACZKOMATY_DELIVERY_EOF_COD'));
        $this->filterResult($result);
        return $result;
    }

    public function getConfigFormValuesAdmin($params)
    {
        $config = array();

        if (isset($params['order']) && $params['order'] instanceof Order) {
            $order = $params['order'];
        } else {
            $order = new Order($params['id_order']);
        }
        $id_cart = $order->id_cart;
        $customer = new Customer($order->id_customer);
        $address = new Address($order->id_address_delivery);
        $config['pminpostorder_email'] = $customer->email;
        $config['pminpostorder_phone'] = $address->phone_mobile;

        if ($config['pminpostorder_phone'] == '') {
            $config['pminpostorder_phone'] = $address->phone;
        }
        
        $config['pminpostorder_phone'] = str_replace('+48', '', $config['pminpostorder_phone']);
        $config['pminpostorder_phone'] = preg_replace('/\s+/', '', $config['pminpostorder_phone']);

        $config['pminpostorder_phone'] = $this->trimPhone($config['pminpostorder_phone']);

        $config['pminpostorder_firstname'] = $address->firstname;

        $config['pminpostorder_pobranie_value'] = (float)$order->total_paid_tax_incl;
        $config['pminpostorder_ubezpieczenie_value'] = (float)$order->total_paid_tax_incl;

        if ($this->isSelectedCarrierCod($order->id_carrier)) {
            $config['pminpostorder_size'] = Configuration::get('PMINPOSTPACZKOMATY_SIZE_COD');
            $config['pminpostorder_pobranie'] = 1;
            $config['pminpostorder_ubezpieczenie'] = Configuration::get('PMINPOSTPACZKOMATY_ISSURANCE_COD');
            $config['pminpostorder_eof'] = 0;
        } elseif ($this->isSelectedCarrierBw($order->id_carrier)) {
            $config['pminpostorder_pobranie'] = 0;
            $config['pminpostorder_size'] = Configuration::get('PMINPOSTPACZKOMATY_SIZE_BW');
            $config['pminpostorder_ubezpieczenie'] = Configuration::get('PMINPOSTPACZKOMATY_ISSURANCE_BW');
            $config['pminpostorder_eof'] = 0;
        } elseif ($this->isSelectedCarrierBwEof($order->id_carrier)) {
            $config['pminpostorder_eof'] = 1;
            $config['pminpostorder_pobranie'] = 0;
            $config['pminpostorder_size'] = Configuration::get('PMINPOSTPACZKOMATY_SIZE_EOF');
            $config['pminpostorder_ubezpieczenie'] = Configuration::get('PMINPOSTPACZKOMATY_ISSURANCE_EOF');
        } elseif ($this->isSelectedCarrierCodEof($order->id_carrier)) {
            $config['pminpostorder_pobranie'] = 1;
            $config['pminpostorder_size'] = Configuration::get('PMINPOSTPACZKOMATY_SIZE_EOF_CODEOF');
            $config['pminpostorder_ubezpieczenie'] = Configuration::get('PMINPOSTPACZKOMATY_ISSURANCE_EOF_COD');
            $config['pminpostorder_eof'] = 1;
        } else {
            $config['pminpostorder_pobranie'] = 0;
            $config['pminpostorder_size'] = Configuration::get('PMINPOSTPACZKOMATY_SIZE_BW');
            $config['pminpostorder_ubezpieczenie'] = Configuration::get('PMINPOSTPACZKOMATY_ISSURANCE_BW');
            $config['pminpostorder_eof'] = 0;
            $config['warnings'] =
            $this->l('Warning: delivery method was not foud please check Cash on delivery value AND end of week');
        }
        if ($order->module == 'pm_cashondelivery') {
            $config['pminpostorder_pobranie'] = 1;
            $config['pminpostorder_ubezpieczenie'] = Configuration::get('PMINPOSTPACZKOMATY_ISSURANCE_COD');
        }
        $config['pminpostorder_selected_from'] = Configuration::get('PMINPOSTPACZKOMATY_MACHINE');
        $config['pminpostorder_machine'] = Configuration::get('PMINPOSTPACZKOMATY_SHIPPING_METHOD');
        if ($config['pminpostorder_machine'] == 2) {
            $config['pminpostorder_machine'] = false;
        }

        $config['pminpostorder_format'] = Configuration::get('PMINPOSTPACZKOMATY_LABEL_FORMAT');
        $config['pminpostorder_size_l'] = Configuration::get('PMINPOSTPACZKOMATY_LABEL_SIZE');

        $config['pminpostorder_reference'] = $this->genereateReference();
        $sql = 'SELECT machine FROM `'._DB_PREFIX_.$this->name.'list` WHERE id_cart = '.(int)$id_cart;
        $r = Db::getInstance()->getValue($sql);
        if ($r) {
            $config['pminpostorder_selected'] = $r;
        } else {
            $config['pminpostorder_selected'] = '';
        }

        $post_info_tmp = PaczkomatyList::getPostInfoByIdCart($id_cart);
        $post_info = @stripslashes($post_info_tmp);
        if (empty($post_info)) {
            $post_info = @Tools::stripslashes($post_info_tmp);
        }
        if ($post_info) {
            $post_info = unserialize($post_info);
            $config = array_merge($config, $post_info);
        }
        return $config;
    }

    public function prepareAdminHook($params, $html = 'all')
    {
        if ($this->_errors) {
            $messages = $this->_errors;
        } else {
            $messages = false;
        }
        
        $this->context->smarty->assign(
            array(
                'form' => $this->renderFormAdmin($params),
                'selectedpm' => $this->isSelected($params),
                'message' => $messages,
                'machine' => '',
            )
        );
        $this->context->smarty->assign('pminpostview', $html);
        $this->context->smarty->assign('module_dir', $this->_path);
        return $this->display(__FILE__, 'order.tpl');
    }

    public function genereateReference()
    {
        $order = new Order(Tools::getValue('id_order'));
        $referenceGen = Configuration::get('PMINPOSTPACZKOMATY_REFERENCE');
        $id_order = $order->id;
        $address = new Address($order->id_address_delivery);
        $firstname = $address->firstname;
        $lastname = $address->lastname;
        $order_reference = $order->reference;
        $products = $order->getProducts();
        $productsreference = array();
        foreach ($products as $product) {
            if (trim($product['product_reference']) != '') {
                $productsreference[] = $product['product_reference'];
            }
        }
        $productsreference = implode(' ', $productsreference);
        $reference = str_replace(
            array(
            '{id_order}',
            '{reference}',
            '{productsreference}',
            '{firstname}',
            '{lastname}'
            ),
            array(
            $id_order,
            $order_reference,
            $productsreference,
              $firstname,
            $lastname
            ),
            $referenceGen
        );

        $reference = trim(Tools::strtoupper($reference));
        if (Tools::strlen($reference)>100) {
            $reference = mb_substr($reference, 0, 100).'@';
        }
        return $reference;
    }

    private function isSelected($params)
    {
        if (isset($params['order']) && $params['order'] instanceof Order) {
            $o = $params['order'];
        } else {
            $o = new Order($params['id_order']);
        }
        $id_cart = $o->id_cart;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.$this->name.'list` WHERE id_cart = '.(int)$id_cart;
        return Db::getInstance()->getRow($sql);
    }

    public function getOrderStatus($addempty = true)
    {
        $statuses = OrderState::getOrderStates(Context::getContext()->language->id);
        $status = array(
        array(
            'id_order_state' => -1,
            'name' => '--'
        )
        );
        if (!$addempty) {
            return $statuses;
        } else {
            return array_merge($status, $statuses);
        }
    }

    public function trimPhone($num)
    {
        $phone = preg_replace("/[^0-9+]/", "", $num);
        $phone = str_replace('+48', '', $phone);
        if (isset($phone[0], $phone[1])) {
            if ($phone[0].$phone[1] == '48') {
                $phone = Tools::substr($phone, 2);
            }
        }
        return $phone;
    }

    private function getOutput()
    {
        $output = '';
        if (!$this->sandbox) {
            try {
                $file = Tools::file_get_contents(
                    'https://presta-mod.pl/auto-update/?module=pminpostpaczkomaty&domain='.
                    $_SERVER['HTTP_HOST'].'&version='.$this->version,
                    false,
                    null,
                    1
                );
                if ($file) {
                    $xml = simplexml_load_string($file);
                    Context::getContext()->smarty->assign('xml', $xml);
                    Context::getContext()->smarty->assign('version', $this->version);
                    if (version_compare($this->version, $xml->version, '<') === true) {
                        Context::getContext()->smarty->assign('version', true);
                    } else {
                        Context::getContext()->smarty->assign('version', false);
                    }
                    $output = Context::getContext()->smarty->fetch(
                        dirname(__FILE__).'/views/templates/admin/admin.tpl'
                    );
                }
            } catch (Exception $exp) {
                $output = '';
            }
        }
        if (sizeof($this->confirmations)) {
            $output .= $this->displayConfirmation(implode('<br/>', $this->confirmations));
        }
        if (sizeof($this->errors)) {
            $output .= $this->displayError(implode('<br/>', $this->errors));
        }
        if (sizeof($this->warnings)) {
            $output .= $this->displayWarning(implode('<br/>', $this->warnings));
        }
        return $output;
    }

    private function parseErrors($error)
    {
        $errors = array();
        $errors['Token is missing or invalid.'] = $this->l('Token is missing or invalid.');
        if (isset($errors[$error])) {
            return $errors[$error];
        } else {
            return $error;
        }
    }

    public function getSelectedMachineForOrder($id_order)
    {
        $o = new Order($id_order);
        $id_cart = $o->id_cart;
        if ($this->isSelectedCarrierBw($o->id_carrier) ||
        $this->isSelectedCarrierCod($o->id_carrier)) {
            return PaczkomatyList::getSelectedMachine($id_cart);
        } else {
            return false;
        }
    }

    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('controller') == 'AdminOrders'
        || Tools::isSubmit('id_order')
        || Tools::getValue('module_name') == $this->name
        || Tools::getValue('configure') == $this->name
        || Tools::getValue('controller') == 'PmInpostPaczkomatyList'
         ) {
            $create_text = array();
            $create_text[] = $this->l('After create the delivery method you will be redirected to editing.');
            $create_text[] = $this->l('Remember to set appropriate zones and price for the delivery method');
            $create_text[] = '';
            $create_text[] = $this->l('Next return to module configuration');
            $create_text = implode('<br>', $create_text);

            Media::addJsDef(
                array(
                    'pm_inpostpaczkomaty_mapkey' => Configuration::get('PMINPOSTPACZKOMATY_MAP_KEY'),
                    'pm_inpostpaczkomaty_gcup' => $this->context->link->getModuleLink($this->name, 'ajax'),
                    'pm_inpostpaczkomaty_cancel' => $this->l('Cancel'),
                    'pm_inpostpaczkomaty_create' => $this->l('Create delivery method'),
                    'pm_inpostpaczkomaty_create_text' => $create_text,
                    'prestakey' => md5(_COOKIE_KEY_),
                    'wpzl' => Configuration::get('PMINPOSTPACZKOMATY_WPL'),
                    'psv' => $this->version_ps,
                )
            );
            $this->addMediaConfig();
            $this->context->smarty->assign('js_def', Media::getJsDef());
            $this->context->controller->addJquery();
            $css_array = array(
                $this->_path.'/views/css/paczkomator-'.$this->version.'.css',
                $this->_path.'/views/css/admin-'.$this->version.'.css',
                $this->_path.'/views/css/jquery-confirm.css'
            );
            if (Tools::version_compare(_PS_VERSION_, '1.6.1.7', '<=')) {
                $scripts_array = array(
                    $this->_path.'/views/js/jsdef.js',
                    $this->_path.'/views/js/admin-order-'.$this->version.'.js',
                    $this->_path.'/views/js/paczkomator-'.$this->version.'.js',
                    $this->_path.'/views/js/jquery-confirm.js'
                );
            } else {
                $scripts_array = array(
                    $this->_path.'/views/js/admin-order-'.$this->version.'.js',
                    $this->_path.'/views/js/paczkomator-'.$this->version.'.js',
                    $this->_path.'/views/js/jquery-confirm.js'
                );
            }
            if (Tools::getValue('module_name') == $this->name || Tools::getValue('configure') == $this->name) {
                $css_array[] = $this->_path.'/views/css/enjoyhint.css';
                $scripts_array[] = $this->_path.'/views/js/kinetic.js';
                $scripts_array[] = $this->_path.'/views/js/enjoyhint.js';
                $scripts_array[] = $this->_path.'/views/js/guide.js';
            }
            $this->context->controller->addCSS(
                $css_array
            );
            $this->context->controller->addJS(
                $scripts_array
            );
        }
    }

    public function hookFooter($params)
    {
        if (!$this->isCarrierEnabled()) {
            return '';
        }
        if (!in_array($this->context->controller->php_self, array('order','order-opc',null))) {
            return '';
        }
        if (!Module::isInstalled('onepagecheckoutps') &&
            !Module::isEnabled('onepagecheckoutps') &&
            !Module::isInstalled('supercheckout') &&
            !Module::isEnabled('supercheckout') &&
            !Module::isInstalled('thecheckout') &&
            !Module::isEnabled('thecheckout') &&
            !$this->move_footer
        ) {
            return '';
        }
        if ((int)Context::getContext()->cart->id) {
            $this->addMediaCart();
            return '';
        } else {
            return '';
        }
    }

    public function hookDisplayAdminOrderTabShip($params)
    {
        Media::addJsDef(
            array(
                'pm_inpostpaczkomaty_mapkey' => Configuration::get('PMINPOSTPACZKOMATY_MAP_KEY'),
                'pm_inpostpaczkomaty_gcup' => $this->context->link->getModuleLink($this->name, 'ajax'),
                'prestakey' => md5(_COOKIE_KEY_),
                'wpzl' => Configuration::get('PMINPOSTPACZKOMATY_WPL'),
                'psv' => $this->version_ps,
            )
        );

        return $this->prepareAdminHook($params, 'tab');
    }

    public function hookDisplayAdminOrderContentShip($params)
    {
        return $this->prepareAdminHook($params, 'content');
    }

    public function hookHeader()
    {
        if (!in_array($this->context->controller->php_self, array('order','order-opc', null)) &&
        Tools::getValue('controller') != 'supercheckout') {
            return '';
        }
        if (!$this->isCarrierEnabled()) {
            return '';
        }

        if (version_compare(_PS_VERSION_, '1.7.0', '>=') === true) {
            $this->addMediaCart();
            $this->context->controller->registerStylesheet(
                'modules-pmpaczkomaty',
                'modules/'.$this->name.'/views/css/front17-'.$this->version.'.css'
            );

            $this->context->controller->registerStylesheet(
                'modules-pmpaczkomaty-con',
                'modules/'.$this->name.'/views/css/css/jquery-confirm.css'
            );

            $this->context->controller->registerJavascript(
                'jquery-confim',
                'modules/'.$this->name.'/views/js/jquery-confirm.js'
            );
            if (!Configuration::get('PMINPOSTPACZKOMATY_GGMAP')) {
                $this->context->controller->registerJavascript(
                    'remote-widget',
                    'modules/'.$this->name.'/views/js/openmaps-'.$this->version.'.js'
                );
                $this->context->controller->registerStylesheet(
                    'modules-pmpaczkomaty-ep',
                    'modules/'.$this->name.'/views/css/easypack-'.$this->version.'-openmaps.css'
                );
            } else {
                $this->context->controller->registerStylesheet(
                    'modules-pmpaczkomaty-ep',
                    'modules/'.$this->name.'/views/css/easypack-'.$this->version.'-google.css'
                );
                $this->context->controller->registerJavascript(
                    'remote-widget',
                    'modules/'.$this->name.'/views/js/maps-'.$this->version.'.js'
                );
            }

            $this->context->controller->registerJavascript(
                'pminpostpaczkomaty-script',
                'modules/'.$this->name.'/views/js/front-'.$this->version.'.js'
            );

            $this->context->controller->registerJavascript(
                'pminpostpaczkomaty-paczkomator',
                'modules/'.$this->name.'/views/js/paczkomator-'.$this->version.'.js'
            );
        } else {
            if (!Configuration::get('PMINPOSTPACZKOMATY_GGMAP')) {
                $this->context->controller->addJS($this->_path.'/views/js/openmaps-'.$this->version.'.js');
                $this->context->controller->addCSS($this->_path.'/views/css/easypack-'.$this->version.'-openmaps.css');
            } else {
                $this->context->controller->addJS($this->_path.'/views/js/maps-'.$this->version.'.js');
                $this->context->controller->addCSS($this->_path.'/views/css/easypack-'.$this->version.'-google.css');
            }
            $this->context->controller->addJS($this->_path.'/views/js/paczkomator-'.$this->version.'.js');
            $this->context->controller->addJS($this->_path.'/views/js/front-'.$this->version.'.js');
            $this->context->controller->addJS($this->_path.'/views/js/jquery-confirm.js');
            $this->context->controller->addCSS($this->_path.'/views/css/front-'.$this->version.'.css');
            $this->context->controller->addCSS($this->_path.'/views/css/jquery-confirm.css');
        }
        return $this->hookFooter(array());
    }

    private function updateSelectedCarriersBw($bw)
    {
        if (empty($bw)) {
            $bw = array();
        }
        if (!is_array($bw)) {
            $bw = array($bw);
            $bw = array_merge($bw, $this->getCarriersBw());
        }
        $bw = array_filter($bw, 'strlen');
        $bw = implode(',', $bw);
        Configuration::updateValue('PMINPOSTPACZKOMATY_DELIVERY_BW', $bw);
    }

    private function updateSelectedCarriersBwEof($bw)
    {
        if (empty($bw)) {
            $bw = array();
        }
        if (!is_array($bw)) {
            $bw = array($bw);
            $bw = array_merge($bw, $this->getCarriersBwEof());
        }
        $bw = array_filter($bw, 'strlen');
        $bw = implode(',', $bw);
        Configuration::updateValue('PMINPOSTPACZKOMATY_DELIVERY_EOF', $bw);
    }

    private function updateSelectedCarriersCod($cod)
    {
        if (empty($cod)) {
            $cod = array();
        }
        if (!is_array($cod)) {
            $cod = array($cod);
            $cod = array_merge($cod, $this->getCarriersCod());
        }
        $cod = array_filter($cod, 'strlen');
        $cod = implode(',', $cod);
        Configuration::updateValue('PMINPOSTPACZKOMATY_DELIVERY_COD', $cod);
    }

    private function updateSelectedCarriersCodEof($cod)
    {
        if (empty($cod)) {
            $cod = array();
        }
        if (!is_array($cod)) {
            $cod = array($cod);
            $cod = array_merge($cod, $this->getCarriersCodEof());
        }
        $cod = array_filter($cod, 'strlen');
        $cod = implode(',', $cod);
        Configuration::updateValue('PMINPOSTPACZKOMATY_DELIVERY_EOF_COD', $cod);
    }

    private function updateSelectedStatuses($statuses)
    {
        if (empty($statuses)) {
            $statuses = array();
        }
        if (!is_array($statuses)) {
            $statuses = array($statuses);
            $statuses = array_merge($statuses, $this->getCarriersCodEof());
        }
        $statuses = array_filter($statuses, 'strlen');
        $statuses = implode(',', $statuses);
        Configuration::updateValue('PMINPOSTPACZKOMATY_STATUS_AV', $statuses);
    }

    public function hookActionCarrierUpdate($carrier)
    {
        $old = (int)$carrier['id_carrier'];
        $bw = $this->getCarriersBw();
        $cod = $this->getCarriersCod();

        $bweof = $this->getCarriersBwEof();
        $codeof = $this->getCarriersCodEof();

        if ($this->isSelectedCarrierBw($old)) {
            foreach ($bw as $key => $value) {
                if ($value == $old) {
                    unset($bw[$key]);
                }
            }
            $bw[] = (int)$carrier['carrier']->id;
            $this->updateSelectedCarriersBw($bw);
        }

        if ($this->isSelectedCarrierBwEof($old)) {
            foreach ($bweof as $key => $value) {
                if ($value == $old) {
                    unset($bweof[$key]);
                }
            }
            $bweof[] = (int)$carrier['carrier']->id;
            $this->updateSelectedCarriersBwEof($bweof);
        }

        if ($this->isSelectedCarrierCod($old)) {
            foreach ($cod as $key => $value) {
                if ($value == $old) {
                    unset($cod[$key]);
                }
            }
            $cod[] = (int)$carrier['carrier']->id;
            $this->updateSelectedCarriersCod($cod);
        }
        if ($this->isSelectedCarrierCodEof($old)) {
            foreach ($codeof as $key => $value) {
                if ($value == $old) {
                    unset($codeof[$key]);
                }
            }
            $codeof[] = (int)$carrier['carrier']->id;
            $this->updateSelectedCarriersCodEof($codeof);
        }
    }

    public function hookDisplayBeforeCarrier($params)
    {
        if (!$this->isCarrierEnabled()) {
            return '';
        }

        if (Module::isInstalled('steasycheckout') && Module::isEnabled('steasycheckout')) {
            $this->move_footer = false;
        }
        if ($this->move_footer) {
            return '';
        }
        if (!in_array($this->context->controller->php_self, array('order','order-opc',null))) {
            return '';
        }
        if (Module::isInstalled('onepagecheckoutps') &&
        Module::isEnabled('onepagecheckoutps') ||
        Module::isInstalled('supercheckout') &&
        Module::isEnabled('supercheckout')
        ) {
            return '';
        }

        if ((int)Context::getContext()->cart->id) {
            $this->addMediaCart();
            if (Module::isInstalled('steasycheckout') && Module::isEnabled('steasycheckout')) {
                return $this->context->smarty->fetch(dirname(__FILE__).'/views/templates/front/carrier-1.7.tpl');
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public function hookAdminOrder($params)
    {
        Media::addJsDef(
            array(
                'pm_inpostpaczkomaty_mapkey' => Configuration::get('PMINPOSTPACZKOMATY_MAP_KEY'),
                'pm_inpostpaczkomaty_gcup' => $this->context->link->getModuleLink($this->name, 'ajax'),
                'prestakey' => md5(_COOKIE_KEY_),
                'wpzl' => Configuration::get('PMINPOSTPACZKOMATY_WPL'),
                'psv' => $this->version_ps,
            )
        );
        if (Tools::version_compare(_PS_VERSION_, '1.6.1.7', '<=')) {
            $this->context->smarty->assign('js_def', Media::getJsDef());
        }
        return $this->prepareAdminHook($params);
    }

    public function getContent()
    {
        PaczkomatyList::update268();
        PaczkomatyList::update256();
        PaczkomatyList::update262();
        $api = InpostApiShipx::getInstance();
        $api->getShipments();

        if ((int)Tools::isSubmit('submitPminpostpaczkomatyModule')) {
            $this->postProcess();
        }
        $this->addMediaConfig();
        $this->addSmartyVariablesConfig();

        return $this->context->smarty->fetch(dirname(__FILE__).'/views/templates/admin/config.tpl');
    }

    public function displayWarning($warning)
    {
        $output = '
        <div class="bootstrap">
        <div class="module_warning alert alert-warning" >
            <button type="button" class="close" data-dismiss="alert">&times;</button>';

        if (is_array($warning)) {
            $output .= '<ul>';
            foreach ($warning as $msg) {
                $output .= '<li>'.$msg.'</li>';
            }
            $output .= '</ul>';
        } else {
            $output .= $warning;
        }

        $output .= '</div></div>';

        return $output;
    }

    public function displayConfirmation($string)
    {
        $output = '
        <div class="bootstrap">
        <div class="module_confirmation conf confirm alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            '.$string.'
        </div>
        </div>';
        return $output;
    }

    public function displayError($error)
    {
        $output = '
        <div class="bootstrap">
        <div class="module_error alert alert-danger" >
            <button type="button" class="close" data-dismiss="alert">&times;</button>';

        if (is_array($error)) {
            $output .= '<ul>';
            foreach ($error as $msg) {
                $output .= '<li>'.$msg.'</li>';
            }
            $output .= '</ul>';
        } else {
            $output .= $error;
        }

        $output .= '</div></div>';

        $this->error = true;
        return $output;
    }

    private function additionalOrderMessage($order, $point_replace)
    {
        $id_cart = $order->id_cart;
        $msg = new Message();
        $message = Configuration::get('PMINPOSTPACZKOMATY_OM');
        if (Validate::isCleanHtml($message)) {
            $message = str_replace(array_keys($point_replace), array_values($point_replace), $message);
            $msg->message = nl2br($message);
            $msg->id_cart = (int)$id_cart;
            $msg->id_customer = (int)($order->id_customer);
            $msg->id_order = (int)$order->id;
            $msg->private = 0;
            $msg->add();

            $sql = '
                SELECT      id_customer_thread
                FROM        `'._DB_PREFIX_.'customer_thread`
                WHERE       id_order = '.(int)$order->id;
            $id = (int)Db::getInstance()->getValue($sql);
            if (!$id) {
                $customer = new Customer($order->id_customer);
                $customer_thread = new CustomerThread();
                $customer_thread->id_customer = (int)($order->id_customer);
                $customer_thread->id_lang = Context::getContext()->language->id;
                $customer_thread->id_shop = Context::getContext()->shop->id;
                $customer_thread->id_contact = 0;
                $customer_thread->id_order = $order->id;
                $customer_thread->id_product = 0;
                $customer_thread->status = 'open';
                $customer_thread->email = $customer->email;
                $customer_thread->token = Tools::getToken();
                $customer_thread->add();
                $id = $customer_thread->id;
            }

            $cm = new CustomerMessage();
            $cm->id_customer_thread = $id;
            $cm->id_employee = 0;
            $cm->message = $message;
            $cm->private = 0;
            $cm->read = 0;
            $cm->add();
        }
    }

    private function additionalCustomerMessage($order, $point_replace)
    {
        $customer = new Customer($order->id_customer);
        $customer_email = $customer->email;
        if ($this->version_ps == '1.6') {
            $template = 'paczkomat';
        } else {
            $template = 'paczkomat17';
        }
        $subject = $this->l('You have chosen the parcel machine to your order');
        $dir_mail = dirname(__FILE__).'/mails/';
        $template_vars = array();
        $template_vars['{message_title}'] = $subject;
        $message = Configuration::get('PMINPOSTPACZKOMATY_CM');
        $point_replace['{customer}'] = trim($customer->firstname.' '.$customer->lastname.' '.$customer->company);
        $point_replace['{order_id}'] = $order->id;
        $point_replace['{order_reference}'] = $order->reference;
        $point_replace['{message_title}'] = $subject;

        $message = str_replace(array_keys($point_replace), array_values($point_replace), $message);

        $template_vars['{message_lang}'] = nl2br($message);
        $template_vars['{order_reference}'] = $order->reference;
        Mail::Send(
            $order->id_lang,
            $template,
            $subject,
            $template_vars,
            $customer_email,
            null,
            null,
            Configuration::get('PS_SHOP_NAME'),
            null,
            null,
            $dir_mail
        );
    }

    public function hookActionValidateOrder($params)
    {
        if (isset($params['order'])) {
            $order = $params['order'];
        } else {
            return ;
        }
        $machine_selected = $this->getSelectedMachineForOrder($order->id);
        if ($machine_selected === false) {
            return ;
        }
        $point_replace = $this->getPoint($machine_selected);
        if ($point_replace && Configuration::get('PMINPOSTPACZKOMATY_OM_EN')) { // Admin message
            $this->additionalOrderMessage($order, $point_replace);
        }

        if ($point_replace && Configuration::get('PMINPOSTPACZKOMATY_CM_EN')) { // Customer message
            $this->additionalCustomerMessage($order, $point_replace);
        }
    }

    protected function addGroups($carrier)
    {
        $groups_ids = array();
        $groups = Group::getGroups(Context::getContext()->language->id);
        foreach ($groups as $group) {
            $groups_ids[] = $group['id_group'];
        }

        $carrier->setGroups($groups_ids);
    }

    protected function addRanges($carrier)
    {
        $range_weight = new RangeWeight();
        $range_weight->id_carrier = $carrier->id;
        $range_weight->delimiter1 = '0';
        $range_weight->delimiter2 = '25';
        $range_weight->add();
        return $range_weight->id;
    }

    protected function addZones($carrier)
    {
        $zones = Zone::getZones();

        foreach ($zones as $zone) {
            $carrier->addZone($zone['id_zone']);
        }
        return $zones;
    }


    private function createDeliveryMethod()
    {
        $name = $this->l('Paczkomaty InPost');
        $price = 15;
        if (Tools::getValue('CREATE_DELIVERY_METHOD') == 'cod') {
            $name = $this->l('Paczkomaty InPost - ').$this->l(' Cash On Delivery');
            $price = 20;
        } elseif (Tools::getValue('CREATE_DELIVERY_METHOD') == 'cod_eof') {
            $name = $this->l('Paczkomaty InPost - ').$this->l(' Cash On Delivery').': '.$this->l('End of week');
            $price = 20;
        } elseif (Tools::getValue('CREATE_DELIVERY_METHOD') == 'prepayment_eof') {
            $name = trim($this->l('Paczkomaty InPost ')).': '.$this->l('End of week');
            $price = 15;
        }
        $carrier = new Carrier();
        $carrier->name = $name;
        $carrier->max_width = 38;
        $carrier->max_height = 41;
        $carrier->max_depth = 64;
        $carrier->max_weight = 25;
        $carrier->url = 'https://inpost.pl/sledzenie-przesylek?number=@';

        foreach (Language::getLanguages() as $lang) {
            $carrier->delay[$lang['id_lang']] = $this->l('48h - 72h');
        }
        $carrier->add();
        $zones = $this->addZones($carrier);
        $this->addGroups($carrier);
        $range_id = $this->addRanges($carrier);

        $price_list = array();
        foreach ($zones as $zone_item) {
            $price_list[] = array(
                'id_range_weight' => (int)$range_id,
                'id_carrier' => (int)$carrier->id,
                'id_zone' => (int)$zone_item['id_zone'],
                'price' => $price,
            );
        }
        if (Tools::getValue('CREATE_DELIVERY_METHOD')== 'cod') {
            $this->updateSelectedCarriersCod($carrier->id);
        } elseif (Tools::getValue('CREATE_DELIVERY_METHOD') == 'prepayment') {
            $this->updateSelectedCarriersBw($carrier->id);
        } elseif (Tools::getValue('CREATE_DELIVERY_METHOD') == 'prepayment_eof') {
            $this->updateSelectedCarriersBwEof($carrier->id);
        } elseif (Tools::getValue('CREATE_DELIVERY_METHOD') == 'cod_eof') {
            $this->updateSelectedCarriersCodEof($carrier->id);
        }

        Db::getInstance()->insert('delivery', $price_list);
        copy(dirname(__FILE__).'/views/img/inpost-logo-normal.jpg', _PS_SHIP_IMG_DIR_.$carrier->id.'.jpg');
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminCarrierWizard').'&id_carrier='.$carrier->id);
    }

    public function changeStates()
    {
        if (Tools::getValue('token') != substr(md5(_COOKIE_KEY_), 0, 12)) {
            die('Bad token');
        }
        $avstates = explode(',', Configuration::get('PMINPOSTPACZKOMATY_STATUS_AV'));
        $configshipx = Configuration::get('PMINPOSTPACZKOMATY_SHIPX');
        $state = (int)Configuration::get('PMINPOSTPACZKOMATY_STATUS_DEL');
        $stored = (int)Configuration::get('PMINPOSTPACZKOMATY_STATUS_PIC');
        $prepared = (int)Configuration::get('PMINPOSTPACZKOMATY_STATUS');
        $configchangestate = Configuration::get('PMINPOSTPACZKOMATY_OS');
        if (Configuration::get('PMINPOSTPACZKOMATY_SHIPX')) {
            $api = InpostApiShipx::getInstance();
        } else {
            $api = InpostApi::getInstance();
        }
        if (sizeof($avstates) == 0) {
            die('Go to module config and select statuses available to change');
        }
        if ($configchangestate) {
            $sql = '
                SELECT      current_state,
                            nr_listu,
                            id_order,
                            pack_status,
                            o.id_cart
                FROM        `'._DB_PREFIX_.'orders` o
                JOIN        `'._DB_PREFIX_.$this->name.'list` list
                ON          list.id_cart = o.id_cart
                AND         current_state IN ('.implode(',', $avstates).')
                AND         nr_listu <> ""
                WHERE o.date_add >= NOW() - INTERVAL 1 MONTH
                AND         pack_status NOT LIKE "%Dostarczona"
                LIMIT       200';
            $result = Db::getInstance()->executeS($sql);

            foreach ($result as $result_item) {
                $api = $api;
                $shipping_number = $result_item['nr_listu'];
                $pack_status = $api->getPackStatus($shipping_number);
                if ($pack_status === false) {
                    echo 'Pominięte: '.$result_item['id_order'].'<br/>';
                    continue;
                }
                if (!$configshipx) {
                    $data = strtotime((string)$pack_status->statusDate);
                    $data = Date('Y-m-d H:i:s', $data);
                    $status = (string)$pack_status->status;
                } else {
                    $pack_status = (Tools::jsonDecode($pack_status));
                    if (!isset($pack_status->tracking_details)) {
                        echo 'Pominięte: '.$result_item['id_order'].'<br/>';
                        continue;
                    }
                    $data = strtotime((string)$pack_status->tracking_details[0]->datetime);
                    $data = Date('Y-m-d H:i:s', $data);
                    $status = $pack_status->status;
                }

                if (isset($pack_status->status) && $pack_status->status == '404') {
                    echo 'Pominięte: '.$result_item['id_order'].'<br/>';
                    continue;
                }
                $paczkomatyList = PaczkomatyList::getObjectByShippingNumber($shipping_number);

                if ($configchangestate && strtolower($status) == 'delivered') {
                    $id_cart = $result_item['id_cart'];
                    if ($state && $id_cart) {
                        $order = new Order(Order::getOrderByCartId($id_cart));
                        if ($state != $order->getCurrentState()) {
                            $order->setCurrentState($state);
                        }
                    }
                } elseif ($configchangestate && strtolower($status) == 'prepared') {
                    $id_cart = $result_item['id_cart'];
                    if ($state && $id_cart) {
                        $order = new Order(Order::getOrderByCartId($id_cart));
                        if ($state != $order->getCurrentState() && $prepared != $order->getCurrentState()) {
                            $order->setCurrentState($prepared);
                        }
                    }
                } elseif ($configchangestate &&
                    (strtolower($status) == 'stored' || strtolower($status) == 'intransit') ||
                    $status == 'sent_from_source_branch' || $status == 'collected_from_sender' ||
                    $status == 'adopted_at_sorting_center') {
                    $id_cart = $result_item['id_cart'];
                    if ($state && $id_cart) {
                        $order = new Order(Order::getOrderByCartId($id_cart));
                        if ($state != $order->getCurrentState() && $stored != $order->getCurrentState()) {
                            $order->setCurrentState($stored);
                        }
                    }
                }
                $status = $data.' '.$this->translateText($status);
                
                echo $status.' '.$result_item['id_order'].'<br/>';
                if ($paczkomatyList) {
                    $paczkomatyList->pack_status = $status;
                    $paczkomatyList->save();
                }
            }
        }
    }

    private function carrierAutoEnableDisable()
    {
        $day_of_week = date('w');
        if ($day_of_week == 0) {
            $day_of_week = 7;
        }
        
        $hour = Date('G');
        if (strlen($hour) == 1) {
            $hour = '0'.$hour;
        }
        $min = Date('i');
        $current = $day_of_week.$hour.$min;
        $carriers_to_update = $this->getCarriersBwEof();
        if (Configuration::get('PMINPOSTPACZKOMATY_EOF_AE') && sizeof($carriers_to_update)) {
            $config = Configuration::getMultiple(
                array(
                    'PMINPOSTPACZKOMATY_EOF_D',
                    'PMINPOSTPACZKOMATY_EOF_H',
                    'PMINPOSTPACZKOMATY_EOF_DT',
                    'PMINPOSTPACZKOMATY_EOF_HT',
                    'PMINPOSTPACZKOMATY_EOF_LAST'
                )
            );

            $start = $config['PMINPOSTPACZKOMATY_EOF_D'].str_replace(':', '', $config['PMINPOSTPACZKOMATY_EOF_H']);
            $end = $config['PMINPOSTPACZKOMATY_EOF_DT'].str_replace(':', '', $config['PMINPOSTPACZKOMATY_EOF_HT']);

            if ($start < $current && $current <= $end && $config['PMINPOSTPACZKOMATY_EOF_LAST'] == false) {
                Configuration::updateValue('PMINPOSTPACZKOMATY_EOF_LAST', true);
                $sql = '
                    UPDATE      `'._DB_PREFIX_.'carrier`
                    SET         active = 1
                    WHERE       id_carrier IN ('.implode(',', $carriers_to_update).')
                ';
                Db::getInstance()->execute($sql);
            } elseif (!($start < $current && $current <= $end) && $config['PMINPOSTPACZKOMATY_EOF_LAST'] == true) {
                Configuration::updateValue('PMINPOSTPACZKOMATY_EOF_LAST', false);
                $sql = '
                    UPDATE      `'._DB_PREFIX_.'carrier`
                    SET         active = 0
                    WHERE       id_carrier IN ('.implode(',', $carriers_to_update).')
                ';
                Db::getInstance()->execute($sql);
            }
        }
        $carriers_to_update = $this->getCarriersCodEof();
        if (Configuration::get('PMINPOSTPACZKOMATY_EOF_COD_AE') && sizeof($carriers_to_update)) {
            $config = Configuration::getMultiple(
                array(
                    'PMINPOSTPACZKOMATY_EOF_COD_D',
                    'PMINPOSTPACZKOMATY_EOF_COD_H',
                    'PMINPOSTPACZKOMATY_EOF_COD_DT',
                    'PMINPOSTPACZKOMATY_EOF_COD_HT',
                    'PMINPOSTPACZKOMATY_EOF_COD_LAST'
                )
            );

            $start = $config['PMINPOSTPACZKOMATY_EOF_COD_D'].str_replace(
                ':',
                '',
                $config['PMINPOSTPACZKOMATY_EOF_COD_H']
            );
            $end = $config['PMINPOSTPACZKOMATY_EOF_COD_DT'].str_replace(
                ':',
                '',
                $config['PMINPOSTPACZKOMATY_EOF_COD_HT']
            );
            if ($start < $current && $current <= $end && $config['PMINPOSTPACZKOMATY_EOF_COD_LAST'] == false) {
                Configuration::updateValue('PMINPOSTPACZKOMATY_EOF_COD_LAST', true);
                $sql = '
                    UPDATE      `'._DB_PREFIX_.'carrier`
                    SET         active = 1
                    WHERE       id_carrier IN ('.implode(',', $carriers_to_update).')
                ';
                Db::getInstance()->execute($sql);
            } elseif (!($start < $current && $current <= $end) && $config['PMINPOSTPACZKOMATY_EOF_COD_LAST'] == true) {
                Configuration::updateValue('PMINPOSTPACZKOMATY_EOF_COD_LAST', false);
                $sql = '
                    UPDATE      `'._DB_PREFIX_.'carrier`
                    SET         active = 0
                    WHERE       id_carrier IN ('.implode(',', $carriers_to_update).')
                ';
                Db::getInstance()->execute($sql);
            }
        }
    }


    public function addDeliveryAllegro(&$paczkomatyList)
    {
        if ($paczkomatyList->machine) {
            return;
        }
        $allegro = Module::getModuleIdByName('x13allegro');
        if ($allegro) {
            $sql = '
            SELECT      * 
            FROM        `'._DB_PREFIX_.'xallegro_order` 
            WHERE       id_order = '.(int)Tools::getValue('id_order');
            $row = Db::getInstance()->getRow($sql);
            if (!$row) {
                return ;
            }
            $checkout = @Tools::jsonDecode($row['checkout_form_content']);
            if ($checkout &&
                isset($checkout->delivery, $checkout->delivery->method) &&
                isset($checkout->delivery->pickupPoint, $checkout->delivery->pickupPoint->id)
            ) {
                $method = $checkout->delivery->method->name;
                $pickupPoint = $checkout->delivery->pickupPoint->id;
                if (strpos($method, 'Paczkomaty') !== false) {
                    $paczkomatyList->machine = (string)$pickupPoint;
                    if (isset($checkout->payment, $checkout->payment->type) && $checkout->payment->type == 'CASH_ON_DELIVERY') {
                        $paczkomatyList->post_info = serialize(array('pminpostorder_pobranie' => true));
                    } else {
                        $paczkomatyList->post_info = serialize(array('pminpostorder_pobranie' => false));
                    }
                    $paczkomatyList->save();
                }
            }
        }
    }

    private function translateText(&$error)
    {
        $transin = array();
        $transout = array();
        $transin[] = 'Prepared';
        $transout[] = $this->l('Przesyłka utworzona.');

        $transin[] = 'InTransit';
        $transout[] ='W drodze do odbiorcy';
        $transin[] = 'Sent';
        $transout[] ='Przyjęta w oddziale';
        $transin[] = 'InTransit';
        $transout[] ='W drodze do odbiorcy';
        $transin[] = 'Stored';
        $transout[] ='W Paczkomacie, w POP lub punkcie sieci Partnerskiej';
        $transin[] = 'Avizo';
        $transout[] ='Ponowne awizo';
        $transin[] = 'CustomerDelivering';
        $transout[] ='Do nadania w Paczkomacie, POP lub punkcie sieci Partnerskiej';
        $transin[] = 'CustomerStored';
        $transout[] ='Umieszczona w Paczkomacie, POP lub punkcie sieci Partnerskiej';
        $transin[] = 'LabelExpired';
        $transout[] ='Etykieta przeterminowana';
        $transin[] = 'Expired';
        $transout[] ='Nie odebrana';
        $transin[] = 'Delivered';
        $transout[] ='Dostarczona';
        $transin[] = 'RetunedToAgency';
        $transout[] ='liveredToAgency - Przekazana do oddziału';
        $transin[] = 'Cancelled';
        $transout[] ='Anulowana';
        $transin[] = 'Claimed';
        $transout[] ='Przyjęto zgłoszenie reklamacyjne';
        $transin[] = 'ClaimProcessed';
        $transout[] ='Rozpatrzono zgłoszenie reklamacyjne';
        $transin[] = 'CustomerSent';
        $transout[] ='Wyjęta przez kuriera z Paczkomatu, odebrana przez kuriera z POP lub z punktu sieci Partnerskiej';
        $transin[] = 'ReturnedToSortingCenter';
        $transout[] ='DeliveredToSortingCenter - W drodze do nadawcy';
        $transin[] = 'ReturnedToSender';
        $transout[] ='Zwrócono nadawcy';
        $transin[] = 'LabelDestroyed';
        $transout[] ='Etykieta nieczytelna lub jej brak';
        $transin[] = 'Missing';
        $transout[] ='Zagubiono';
        $transin[] = 'NotDelivered';
        $transout[] ='Nie dostarczono';
        $transin[] = 'offers_prepared';
        $transout[] = $this->l('Przygotowano ofertę.');
        $transin[] = 'offer_selected';
        $transout[] = $this->l('Oferta wybrana.');
        $transin[] = 'confirmed';
        $transout[] = $this->l('Przygotowana przez Nadawcę.');
        $transin[] = 'dispatched_by_sender';
        $transout[] = $this->l('Paczka nadana w paczkomacie.');
        $transin[] = 'collected_from_sender';
        $transout[] = $this->l('Odebrana od klienta.');
        $transin[] = 'taken_by_courier';
        $transout[] = $this->l('Odebrana od Nadawcy.');
        $transin[] = 'adopted_at_source_branch';
        $transout[] = $this->l('Przyjęta w oddziale InPost.');
        $transin[] = 'sent_from_source_branch';
        $transout[] = $this->l('W trasie.');
        $transin[] = 'ready_to_pickup_from_pok';
        $transout[] = $this->l('Czeka na odbiór w Punkcie Obsługi Paczek.');
        $transin[] = 'ready_to_pickup_from_pok_registered';
        $transout[] = $this->l('Czeka na odbiór w Punkcie Obsługi Klienta.');
        $transin[] = 'oversized';
        $transout[] = $this->l('Przesyłka ponadgabarytowa.');
        $transin[] = 'adopted_at_sorting_center';
        $transout[] = $this->l('Przyjęta w Sortowni.');
        $transin[] = 'sent_from_sorting_center';
        $transout[] = $this->l('Wysłana z Sortowni.');
        $transin[] = 'adopted_at_target_branch';
        $transout[] = $this->l('Przyjęta w Oddziale Docelowym.');
        $transin[] = 'out_for_delivery';
        $transout[] = $this->l('Przekazano do doręczenia.');
        $transin[] = 'ready_to_pickup';
        $transout[] = $this->l('Umieszczona w Paczkomacie (odbiorczym).');
        $transin[] = 'pickup_reminder_sent';
        $transout[] = $this->l('Przypomnienie o czekającej paczce.');
        $transin[] = 'delivered';
        $transout[] = $this->l('Dostarczona.');
        $transin[] = 'pickup_time_expired';
        $transout[] = $this->l('Upłynął termin odbioru.');
        $transin[] = 'avizo';
        $transout[] = $this->l('Powrót do oddziału.');
        $transin[] = 'claimed';
        $transout[] = $this->l('Zareklamowana w Paczkomacie.');
        $transin[] = 'returned_to_sender';
        $transout[] = $this->l('Zwrot do nadawcy.');
        $transin[] = 'canceled';
        $transout[] = $this->l('Anulowano etykietę.');
        $transin[] = 'other';
        $transout[] = $this->l('Inny status.');
        $transin[] = 'dispatched_by_sender_to_pok';
        $transout[] = $this->l('Nadana w Punkcie Obsługi Klienta.');
        $transin[] = 'out_for_delivery_to_address';
        $transout[] = $this->l('W doręczeniu.');
        $transin[] = 'pickup_reminder_sent_address';
        $transout[] = $this->l('W doręczeniu.');
        $transin[] = 'rejected_by_receiver';
        $transout[] = $this->l('Odmowa przyjęcia.');
        $transin[] = 'undelivered_wrong_address';
        $transout[] = $this->l('Brak możliwości doręczenia.');
        $transin[] = 'undelivered_incomplete_address';
        $transout[] = $this->l('Brak możliwości doręczenia.');
        $transin[] = 'undelivered_unknown_receiver';
        $transout[] = $this->l('Brak możliwości doręczenia.');
        $transin[] = 'undelivered_cod_cash_receiver';
        $transout[] = $this->l('Brak możliwości doręczenia.');
        $transin[] = 'taken_by_courier_from_pok';
        $transout[] = $this->l('W drodze do oddziału nadawczego InPost.');
        $transin[] = 'undelivered';
        $transout[] = $this->l('Przekazanie do magazynu przesyłek niedoręczalnych.');
        $transin[] = 'return_pickup_confirmation_to_sender';
        $transout[] = $this->l('Przygotowano dokumenty zwrotne.');
        $transin[] = 'ready_to_pickup_from_branch';
        $transout[] = $this->l('Paczka nieodebrana – czeka w Oddziale');
        $transin[] = 'delay_in_delivery';
        $transout[] = $this->l('Możliwe opóźnienie doręczenia.');
        $transin[] = 'redirect_to_box';
        $transout[] = $this->l('Przekierowano do Paczkomatu.');
        $transin[] = 'canceled_redirect_to_box';
        $transout[] = $this->l('Anulowano przekierowanie.');
        $transin[] = 'readdressed';
        $transout[] = $this->l('Przekierowano na inny adres.');
        $transin[] = 'undelivered_no_mailbox';
        $transout[] = $this->l('Brak możliwości doręczenia.');
        $transin[] = 'undelivered_not_live_address';
        $transout[] = $this->l('Brak możliwości doręczenia.');
        $transin[] = 'undelivered_lack_of_access_letterbox';
        $transout[] = $this->l('Brak możliwości doręczenia.');
        $transin[] = 'missing';
        $transout[] = $this->l('translation missing: pl_PL.statuses.missing.title');
        $transin[] = 'stack_in_customer_service_point';
        $transout[] = $this->l('Paczka magazynowana w POP.');
        $transin[] = 'stack_parcel_pickup_time_expired';
        $transout[] = $this->l('Upłynął termin odbioru paczki magazynowanej.');
        $transin[] = 'unstack_from_customer_service_point';
        $transout[] = $this->l('W drodze do wybranego paczkomatu.');
        $transin[] = 'courier_avizo_in_customer_service_point';
        $transout[] = $this->l('Oczekuje na odbiór.');
        $transin[] = 'taken_by_courier_from_customer_service_point';
        $transout[] = $this->l('Zwrócona do nadawcy.');

        $error = str_replace($transin, $transout, $error);
        return $error;
    }

    public function cronCallBaselinker()
    {
        if (Tools::getValue('token') != substr(md5(_COOKIE_KEY_), 0, 12)) {
            die('Bad token');
        }
        PmBaselinkerOrder::getLastOrders(15);
    }

    public function getPrefix()
    {
        return $this->_prefix;
    }

    public function getCustomerLastInpostMachineAndSetCurrentCart()
    {
        if (!Configuration::get('PMINPOSTPACZKOMATY_LL')) {
            return ;
        }
        $context = Context::getContext();
        $id_cart = (int)$context->cart->id;
        $id_customer = (int)$context->customer->id;

        $paczkomatyList = PaczkomatyList::getByIdCart($id_cart);
        if ((int)$paczkomatyList->id) {
            return ;
        }
        

        $sql ='
            SELECT      il.`machine`
            FROM        `'._DB_PREFIX_.'pminpostpaczkomatylist` il 
            LEFT JOIN   `'._DB_PREFIX_.'cart` c 
                ON (c.`id_cart` = il.`id_cart` AND c.`id_customer` = '.(int)$id_customer.')
            LEFT JOIN   `'._DB_PREFIX_.'orders` o 
                ON (o.`id_cart` = c.`id_cart`)
            WHERE       o.`id_customer` = '.(int)$id_customer.'
            ORDER BY    o.date_add DESC
        ';
        $row  = Db::getInstance()->getRow($sql);

        if ($row && $id_cart) {
            $paczkomatyList->id_cart = $id_cart;
            $paczkomatyList->machine = $row['machine'];
            $paczkomatyList->save();
        }
    }
}
