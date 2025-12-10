<?php

use FreshMail\ApiV2\Client;
use FreshMail\Entity\Form;
use FreshMail\FreshmailApiV3;
use FreshMail\Service\FieldService;
use FreshMail\Service\FormService;

require_once __DIR__ . '/AdminFreshmailBase.php';

class AdminFreshmailFormConfigController extends AdminFreshmailBaseController
{
    public static $UPDATE_SUCCESS = "Success!";
    public static $ERROR = "Error!";

    private $fmForms = [];

    public function __construct()
    {

        $this->bootstrap = true;
        $this->tpl_folder = 'freshmail_controller';
        $this->lang = false;
        $this->explicitSelect = true;
        $this->context = Context::getContext();
        $this->show_cancel_button = false;


        $this->table = 'freshmail_form';
        $this->identifier = 'id_freshmail_form';
        $this->className = Form::class;

        parent::__construct();
    }

    public function init()
    {
        parent::init();
        if(Tools::getIsset('refresh')){
            $this->module->clearFormCache();
        }

        $this->fmForms = $this->freshmail->getForms($this->freshmailSettings->subscriber_list_hash);
    }

    public function initContent()
    {
        $this->initTabModuleList();
        $this->initToolbar();

        parent::initContent();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['refresh'] = array(
                'href' => self::$currentIndex . '&refresh=1&token=' . $this->token,
                'desc' => $this->module->l('Refresh cache'),
                'icon' => 'process-icon-refresh'
            );

            $this->page_header_toolbar_btn['new_product'] = array(
                'href' => self::$currentIndex . '&add' . $this->table . '=1&token=' . $this->token,
                'desc' => $this->module->l('Add new form'),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initToolbar()
    {
        switch ($this->display) {
            default: // list
                $this->toolbar_btn['new'] = array(
                    'href' => self::$currentIndex . '&add' . $this->table . '=1&token=' . $this->token,
                    'desc' => $this->module->l('Add New Form')
                );
        }

        unset($this->toolbar_btn['back']);
    }


    public function renderList()
    {
        if(empty($this->freshmailSettings->api_token)){
            $this->errors[] = $this->module->l('To use this feature You have to enable synchronization with FreshMail');
            return;
        }

        $this->fields_list = array(
            'id_freshmail_form' => array(
                'title' => 'ID',
                'align' => 'center',
                'width' => 25
            ),
            'form_hash' => array(
                'title' => $this->module->l('Name'),
                'width' => 'auto',
                'callback' => 'getName'
            ),
            'hook' => array(
                'title' => $this->module->l('Hook'),
                'width' => 'auto'
            ),
            'active' => array(
                'title' => $this->module->l('Active'),
                'width' => 'auto'
            ),
        );


        // Adds an Edit button for each result
        $this->addRowAction('edit');

        // Adds a Delete button for each result
        $this->addRowAction('delete');
        $this->specificConfirmDelete = $this->l('Delete selected items?', array(), 'Admin.Notifications.Warning');


        if (!($this->fields_list && is_array($this->fields_list))) {
            return false;
        }
        $this->getList($this->context->language->id);

        foreach ($this->_list as $k => $v) {
            if (isset($v['active'])) {
                if ((int)$this->_list[$k]['active'] == 0) {
                    $this->_list[$k]['active'] = $this->module->l('Disabled');
                } else {
                    $this->_list[$k]['active'] = $this->module->l('Enabled');
                }

                if (isset($v['position']) && isset(self::$POSITION[$this->_list[$k]['position']])) {
                    $this->_list[$k]['position'] = $this->module->l(self::$POSITION[$this->_list[$k]['position']]);
                }
            }
        }



        // If list has 'active' field, we automatically create bulk action
        if (isset($this->fields_list) && is_array($this->fields_list) && array_key_exists('active', $this->fields_list)
            && !empty($this->fields_list['active'])) {
            if (!is_array($this->bulk_actions)) {
                $this->bulk_actions = array();
            }
        }

        $helper = new HelperList();

        // Empty list is ok
        if (!is_array($this->_list)) {
            $this->displayWarning($this->module->l('Bad SQL query', 'Helper') . '<br />' . htmlspecialchars($this->_list_error));
            return false;
        }

        $this->setHelperDisplay($helper);
        $helper->simple_header = true;
        $helper->_default_pagination = $this->_default_pagination;
        $helper->_pagination = $this->_pagination;
        $helper->tpl_vars = $this->getTemplateListVars();

        // For compatibility reasons, we have to check standard actions in class attributes
        foreach ($this->actions_available as $action) {
            if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action) {
                $this->actions[] = $action;
            }
        }
        $helper->has_value = false;
        $helper->is_cms = $this->is_cms;
        $helper->sql = $this->_listsql;
        $list = $helper->generateList($this->_list, $this->fields_list);

        return $list . $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/forms.tpl');
    }

    private function valid($action = 'add')
    {
        $this->requiredFields = array('name', 'position');
        foreach ($this->requiredFields as $k => $v) {
            if (!empty($v)) {
                $value = Tools::getValue($v);
                if (empty($value)) {
                    $title = ucfirst($this->fields_list[$v]['title']);
                    $label = $title . $this->module->l(' is required!') . ' -> ' . $v;
                    $this->errors[] = Tools::displayError($label);
                }
            }
        }
        if (is_array($this->errors) && count($this->errors)) {
            $this->action = $action;
            return false;
        }
        return true;
    }




    // This method generates the Add/Edit form
    public function renderForm()
    {
        $hooks = [
            [
                'hook' => 'displayFooterProduct',
                'title' => $this->module->l('Product footer'),
                'description' => $this->module->l('This hook adds new blocks under the product\'s description')
            ],
            [
                'hook' => 'displayHeader',
                'title' => $this->module->l('Added in the header of every page'),
                'description' => $this->module->l('This hook adds new blocks in header')
            ],
            [
                'hook' => 'displayHome',
                'title' => $this->module->l('Homepage content'),
                'description' => $this->module->l('This hook displays new elements on the homepage')
            ],
            [
                'hook' => 'displayRightColumnProduct',
                'title' => $this->module->l('New elements on the product page (right column)'),
                'description' => $this->module->l('This hook displays new elements in the right-hand column of the product page')
            ],
            [
                'hook' => 'displayLeftColumnProduct',
                'title' => $this->module->l('New elements on the product page (left column)'),
                'description' => $this->module->l('This hook displays new elements in the left-hand column of the product page')
            ],
            [
                'hook' => 'displayTop',
                'title' => $this->module->l('Top of pages'),
                'description' => $this->module->l('This hook displays additional elements at the top of your pages')
            ],
            [
                'hook' => 'freshmailForm',
                'title' => $this->module->l('Custom hook'),
                'description' => $this->module->l('You can add this hook in every place You want')
            ]
        ];
        foreach ($hooks as &$hook){
            $hook['title'] = $hook['hook'] . ' | '.$hook['title'];
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Forms and additional fields'),
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->module->l('Hook'),
                    'name' => 'hook',
                    'required' => true,
                    'desc' => $this->module->l('Used hook'),
                    'class' => 'fixed-width-xxl',
                    'options' => [
                        'query' => $hooks,
                        'id' => 'hook',
                        'name' => 'title'
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->module->l('Form in Freshmail'),
                    'name' => 'form_hash',
                    'required' => true,
                    'desc' => $this->module->l(''),
                    'class' => 'fixed-width-xxl',
                    'options' => [
                        'query' => $this->fmForms,
                        'id' => 'id_hash',
                        'name' => 'name'
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->module->l('Enable form'),
                    'name' => 'active',
                    'required' => true,
                    'class' => 'switch prestashop-switch',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled')
                        ]
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->module->l('Save'),
                'class' => 'btn btn-default pull-right'
            ],
            'buttons' => [
                [
                    'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminFreshmailFormConfig'),
                    'title' => $this->module->l('Back to list'),
                    'icon' => 'process-icon-back'
                ]
            ]
        ];

        return parent::renderForm();
    }

    public function displayEditLink($token = null, $id, $name = null)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_edit.tpl');
        if (!array_key_exists('Edit', self::$cache_lang)) {
            self::$cache_lang['Edit'] = $this->module->l('Edit', 'Helper');
        }

        $href = self::$currentIndex . '&' . $this->identifier . '=' . $id . '&update' . $this->table . '&token=' . ($token != null ? $token : $this->token);

        if ($this->display == 'view') {
            $href = Context::getContext()->link->getAdminLink('AdminCustomers') . '&id_customer=' . (int)$id . '&updatecustomer';
        }

        $tpl->assign(array(
            'href' => $href,
            'action' => self::$cache_lang['Edit'],
            'id' => $id
        ));

        return $tpl->fetch();
    }

    public function checkAccess($disable = false)
    {
        return true;
    }

    public function viewAccess($disable = false)
    {
        return true;
    }

    public function displayAjax()
    {
        $return = array(
            'hasError' => true,
            'errors' => 'Error'
        );
        die(Tools::jsonEncode($return));
    }

    public function displayAjaxUpdateFieldsForm()
    {
        $this->ajax = true;
        $hash = Tools::getValue('hash');
        $fieldService = new FieldService(new \FreshMail\Repository\FieldRepository());

        $fieldsApiArray = $this->freshmail->getAllFieldsByIdHashList($hash);

        $idForm = Tools::getValue('id_form');
        $fieldsDbArray = !empty($idForm) ? $fieldService->getFieldsByIdForm($idForm) : [];


        $fields = [];
        foreach ($fieldsDbArray as $key => $value) {
            $fields[$value['hash']] = [
                'id' => $value['id'],
                'displayname' => $value['displayname'],
                'hash' => $value['hash'],
                'name' => $value['name'],
                'tag' => $value['tag'],
                'require_field' => $value['require_field'],
                'include_field' => $value['include_field']
            ];
        }

        foreach ($fieldsApiArray as $key => $value) {
            if (!isset($fields[$value['hash']])) {
                $fields[$value['hash']] = $fieldsApiArray[$key];
            }
        }

        if (!$this->checkExistField('email', $fields)) {
            $fields[] = array(
                'displayname' => "E-mail",
                'hash' => $hash,
                'name' => "E-mail",
                'tag' => "email",
                'require_field' => 1,
                'include_field' => 1
            );
        }

        $result = array(
            'status' => 'OK',
            'fields' => $fields
        );
        echo Tools::jsonEncode($result);
    }

    private function checkExistField($fieldName, $fields)
    {
        foreach ($fields as $key => $value) {
            if ($fieldName == $value['tag']) {
                return true;
            }
        }
        return false;
    }

    public function beforeAdd($form)
    {
        $form->id_shop = $this->context->shop->id;
    }

    public function processDelete()
    {
        $id = (int)Tools::getValue('id_freshmail_form');

        $form = new FreshMail\Entity\Form($id);
        $form->delete();
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminFreshmailFormConfig') . '&conf=1');
    }


    public function getName($value, $row){
        return isset($this->fmForms[$value]) ? $this->fmForms[$value]['name'] : '';
    }

}
