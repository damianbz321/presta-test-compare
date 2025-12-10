<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2020 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

require_once _PS_MODULE_DIR_ . 'fplist/fplist.php';

class AdminFplistAssociationController extends ModuleAdminController
{
    protected $position_identifier = 'id_fplist';

    public function __construct()
    {
        $this->table = 'fpl';
        $this->className = 'fpl';
        $this->lang = false;
        parent::__construct();
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            )
        );
        $this->bootstrap = true;
        $this->_orderBy = 'id_fpl';
        $this->_group = 'GROUP BY a.id_fpl';

        $this->fields_list = array(
            'id_fpl' => array(
                'title' => $this->l('ID'),
                'align' => 'left',
                'orderby' => true,
            ),
            'id_category' => array(
                'title' => $this->l('Category'),
                'align' => 'left',
                'type' => 'text',
                'width' => 200,
                'callback' => 'getCategory'
            ),
            'features' => array(
                'type' => 'bool',
                'title' => $this->l('Features'),
                'width' => 'auto',
                'callback' => 'getFeature'
            ),
            'active' => array(
                'type' => 'bool',
                'title' => $this->l('Active'),
                'width' => 'auto',
                'orderby' => true,
                'ajax' => true,
                'active' => 'active'
            )
        );
    }

    public function getFeature($row, $group)
    {
        $features = "-";
        if ($row != false && $row != "" && strlen($row) > 0) {
            $row = explode(',', $row);
            $features = "";
            foreach ($row AS $r) {
                $feature = new Feature($r, $this->context->language->id);
                $features .= $feature->name . "<br />";
            }
        }
        return $features;
    }

    public function getCategory($row, $group)
    {
        $cat = new Category($row, Context::getContext()->language->id);
        return $cat->name;
    }

    public function renderList()
    {
        $this->initToolbar();
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    public function showFeaturesForm($features)
    {
        $this->context->smarty->assign('selected_features', $features);
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'fplist/views/templates/fplist.tpl');
    }

    public function processUpdate()
    {
        $object = parent::processUpdate();
        $object->features = implode(',', Tools::getValue('selected_features'));
        $object->save();

        return true;
    }

    public function processAdd()
    {
        $_POST['features'] = implode(',', Tools::getValue('selected_features'));
        parent::processAdd();
    }

    public function initToolbar()
    {
        if (isset($this->toolbar_btn)) {
            unset($this->toolbar_btn);
        }
        $Link = new Link();
        $this->toolbar_btn['new'] = array(
            'desc' => $this->l('Add new'),
            'href' => $Link->getAdminLink('AdminFplistAssociation') . '&addfpl'
        );
    }


    public function ajaxProcess()
    {
        if (Tools::getValue('action') == 'activefpl' && Tools::isSubmit('ajax') && Tools::getValue('controller') == 'AdminFplistAssociation') {
            if (fpl::changeStatus(Tools::getValue('id_fpl'), str_replace('fpl', '', Tools::getValue('action')))) {
                echo json_encode(array('success' => true, 'text' => $this->l('Saved')));
            } else {
                echo json_encode(array('error' => true, 'text' => $this->l('Cant save changes')));
            }
        }
    }


    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }
        $cover = false;
        $obj = $this->loadObject(true);
        if (isset($obj->id)) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Add new category-features assosciation'),
            ),
            'input' => array(
                array(
                    'type' => 'categories',
                    'tree' => array('id' => 'fplist', 'use_search' => true, 'title' => $this->l('Categories'), 'selected_categories' => array((isset($obj->id_category) ? $obj->id_category : false))),
                    'label' => $this->l('Category'),
                    'name' => 'id_category',
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Features'),
                    'name' => 'features',
                    'html_content' => $this->showFeaturesForm((isset($obj->features) ? ($obj->features != "" && $obj->features != 0 ? explode(',', $obj->features) : false) : false)),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'required' => true,
                    'desc' => $this->l('If active - module will display selected features on lists of products for products from selected category'),
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
                'title' => $this->l('Save'),
            )
        );
        return parent::renderForm();
    }

    public function init()
    {
        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->_where = Shop::addSqlRestriction(false, 'a');
        }
        parent::init();
    }

}