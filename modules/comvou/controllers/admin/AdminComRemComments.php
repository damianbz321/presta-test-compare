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


require_once(dirname(__FILE__) . '../../../models/cvor.php');

class AdminComRemCommentsController extends ModuleAdminController
{
    protected $position_identifier = 'id_cvor';

    public function __construct()
    {
        $this->table = 'cvor';
        $this->className = 'cvor';
        $this->list_no_link = true;
        $this->lang = false;
        //$this->addRowAction('edit');
        $this->addRowAction('delete');
        parent::__construct();
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            )
        );
        $this->bootstrap = true;
        $this->_orderBy = 'id_cvor';
        $this->fields_list = array(
            'id_cvor' => array(
                'title' => $this->l('ID'),
                'align' => 'left',
                'orderby' => true,
                'width' => 30
            ),
            'email' => array(
                'title' => $this->l('Customer'),
                'width' => 'auto',
                'orderby' => true,
                'callback' => 'getCustomerByEmail',
            ),
            'id_order' => array(
                'title' => $this->l('Order'),
                'width' => 'auto',
                'orderby' => true,
                'callback' => 'getOrderDetails',
            ),
            'deliverydate' => array(
                'title' => $this->l('Delivery date'),
                'width' => 'auto',
                'orderby' => true,
            ),
        );
    }


    public function setMedia($return = false)
    {
        parent::setMedia();
        $this->addJqueryPlugin(array(
            'typewatch',
            'fancybox',
            'autocomplete'
        ));
    }

    public function renderList()
    {
        $this->initToolbar();
        return parent::renderList();
    }

    public function getCustomerName($group, $row)
    {
        $customer = new Customer($group);
        return $customer->firstname . ' ' . $customer->lastname;
    }

    public function getCustomerByEmail($group, $row)
    {
        $customer = Customer::getCustomersByEmail($group);
        $customer = $customer[0];
        return $customer['firstname'] . " " . $customer['lastname'] . " (" . $group . ")";
    }

    public function getOrderDetails($group, $row)
    {
        $order = new Order($group);
        return '(#'.$order->id.') '.$order->reference;
    }

    public function init()
    {
        parent::init();
    }

    public function initToolbar()
    {
        unset($this->toolbar_btn);
        $Link = new Link();
        $this->toolbar_btn['new'] = array(
            'desc' => $this->l('Add new'),
            'href' => $Link->getAdminLink('AdminComRemComments') . '&addcvor'
        );
    }

    public function ajaxProcess()
    {
        if (Tools::isSubmit('customerFilter'))
        {
            $query_multishop = Shop::isFeatureActive() ? 's.`name` AS `from_shop_name`,' : '';
            $search_query = trim(Tools::getValue('q'));
            $customers = Db::getInstance()->executeS('SELECT c.`id_customer`, c.`email`, ' . $query_multishop . ' CONCAT(c.`firstname`, \' \', c.`lastname`) as cname
                FROM `' . _DB_PREFIX_ . 'customer` c
                LEFT JOIN `' . _DB_PREFIX_ . 'shop` s ON (c.`id_shop` = s.`id_shop`)
                WHERE c.`deleted` = 0 AND c.`is_guest` = 0 AND c.`active` = 1
                AND (
                    c.`id_customer` = ' . (int)$search_query . '
                    OR c.`email` LIKE "%' . pSQL($search_query) . '%"
                    OR c.`firstname` LIKE "%' . pSQL($search_query) . '%"
                    OR c.`lastname` LIKE "%' . pSQL($search_query) . '%"
                )
                ORDER BY c.`firstname`, c.`lastname` ASC
                LIMIT 50');
            die(Tools::jsonEncode($customers));
        }

        if (Tools::isSubmit('orderFilter'))
        {
            $search_query = trim(Tools::getValue('q'));
            $id_customer = trim(Tools::getValue('id_customer'));
            $customers = Db::getInstance()->executeS('SELECT  o.`payment`, o.`id_order`, o.`reference`, o.`date_add`
                FROM `' . _DB_PREFIX_ . 'orders` o
                WHERE o.`id_customer` = "' . pSQL($id_customer) . '" AND o.`id_shop` ="' . $this->context->shop->id . '"
                ORDER BY o.`date_add` DESC
                LIMIT 50');
            die(Tools::jsonEncode($customers));
        }
    }

    public function renderForm()
    {

        $this->context->smarty->assign(array(
            'currentToken' => $this->token
        ));

        $module = new comvou();
        if (!$this->loadObject(true))
        {
            return;
        }
        $cover = false;
        $obj = $this->loadObject(true);
        if (isset($obj->id))
        {
            $this->display = 'edit';
        }
        else
        {
            $this->display = 'add';
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Send new reminder'),
            ),
            'input' => array(
                array(
                    'type' => 'customerFilter',
                    'label' => $this->l('Customer'),
                    'name' => 'customerFilter',
                    'required' => true,
                    'lang' => false,
                ),
                array(
                    'type' => 'orderFilter',
                    'label' => $this->l('Order'),
                    'name' => 'orderFilter',
                    'required' => true,
                    'lang' => false,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save')
            )
        );
        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAddcvor'))
        {
            if (Tools::getValue('email', 'false') == 'false' || Tools::getValue('email') == '')
            {
                $this->errors[] = Tools::displayError('You must search and select customer');
            }
            if (Tools::getValue('orderFilter', 'false') == 'false' || Tools::getValue('orderFilter') == '' || Tools::getValue('orderFilter') == '0')
            {
                $this->errors[] = Tools::displayError('You must search and select customer\'s order first');
            }

            if (empty($this->errors))
            {
                $comvou = new comvou();
                $customer = Customer::getCustomersByEmail(Tools::getValue('email'));
                $thisOrder = new Order(Tools::getValue('orderFilter'), $customer[0]['id_lang']);
                $comvou->sendReminder($thisOrder, false);
                $_POST['id_order'] = Tools::getValue('orderFilter');
                $_POST['deliverydate'] = date('Y-m-d H:i:s');
            }
        }
        return parent::postProcess();
    }
}