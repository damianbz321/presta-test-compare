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


require_once(dirname(__FILE__) . '../../../models/cvov.php');

class AdminComVouCommentsController extends ModuleAdminController
{
    protected $position_identifier = 'id_cvov';

    public function __construct()
    {
        $this->table = 'cvov';
        $this->className = 'cvov';
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
        $this->_orderBy = 'id_cvov';
        $this->fields_list = array(
            'id_cvov' => array(
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
            'id_product' => array(
                'title' => $this->l('Product'),
                'width' => 'auto',
                'orderby' => true,
                'callback' => 'getProductName',
            ),
            'deliverydate' => array(
                'title' => $this->l('Delivery date'),
                'width' => 'auto',
                'orderby' => true,
            ),
            'voucher' => array(
                'title' => $this->l('Voucher code'),
                'align' => 'left',
                'orderby' => true,
                'width' => 120
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

    public function getProductName($group, $row)
    {
        $product = new Product($group, false, $this->context->language->id);
        return $product->name;
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
            'href' => $Link->getAdminLink('AdminComVouComments') . '&addcvov'
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

        if (Tools::isSubmit('commentFilter'))
        {
            if (Module::isInstalled('revws') && Module::isEnabled('revws'))
            {
                // DATA KICK MODULE SUPPORT
                $search_query = trim(Tools::getValue('q'));
                $id_customer = trim(Tools::getValue('id_customer'));
                $customers = Db::getInstance()->executeS('SELECT pl.`name`, (SELECT AVG(rr.`grade`) FROM `' . _DB_PREFIX_ . 'revws_review_grade` rr WHERE rr.id_review = pc.id_review ) as `grade` , pc.`id_product`, pc.`id_review` AS `id_product_comment`, pc.`title`, pc.`content`, cv.`id_cvov`
                FROM `' . _DB_PREFIX_ . 'revws_review` pc
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pc.`id_product` = pl.`id_product`)
                LEFT JOIN `' . _DB_PREFIX_ . 'cvov` cv ON (pc.`id_review` = cv.`id_product_comment`)
                WHERE pc.`id_customer` = "' . pSQL($id_customer) . '" AND pl.`id_lang` = "' . $this->context->language->id . '" AND pl.`id_shop` ="' . $this->context->shop->id . '" AND pl.`name` like "%' . pSQL($search_query) . '%"
                GROUP BY pc.`id_review`
                ORDER BY pc.`date_add` DESC
                LIMIT 50');
                die(Tools::jsonEncode($customers));
            }

            if (Module::isInstalled('iqitreviews') && Module::isEnabled('iqitreviews'))
            {
                // IQIT MODULE SUPPORT
                $search_query = trim(Tools::getValue('q'));
                $id_customer = trim(Tools::getValue('id_customer'));

                $customers = Db::getInstance()->executeS('SELECT pl.`name`, pc.`rating` as `grade` , pc.`id_product`, pc.`id_review` AS `id_product_comment`, pc.`title`, pc.`comment` as `content`, cv.`id_cvov`
                FROM `' . _DB_PREFIX_ . 'iqitreviews_products` pc
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pc.`id_product` = pl.`id_product`)
                LEFT JOIN `' . _DB_PREFIX_ . 'cvov` cv ON (pc.`id_review` = cv.`id_product_comment`)
                WHERE pc.`id_customer` = "' . pSQL($id_customer) . '" AND pl.`id_lang` = "' . $this->context->language->id . '" AND pl.`id_shop` ="' . $this->context->shop->id . '" AND pl.`name` like "%' . pSQL($search_query) . '%"
                GROUP BY pc.`id_review`
                ORDER BY pc.`date_add` DESC
                LIMIT 50');
                die(Tools::jsonEncode($customers));
            }

            $inner_join = '';
            $where_join = '';
            if (Configuration::get('cvo_filters'))
            {
                $inner_join = "INNER JOIN `" . _DB_PREFIX_ . "product` p ON (p.`id_product` = pc.`id_product`) ";
                $where_join = "AND p.`id_manufacturer` IN (".(Configuration::get('cvo_fil_manuf') != false && trim(Configuration::get('cvo_fil_manuf')) != '' ? Configuration::get('cvo_fil_manuf'):0).") ";
            }

            $search_query = trim(Tools::getValue('q'));
            $id_customer = trim(Tools::getValue('id_customer'));
            $customers = Db::getInstance()->executeS('SELECT pl.`name`, pc.`grade`, pc.`id_product`, pc.`id_product_comment`, pc.`title`, pc.`content`, cv.`id_cvov`
                FROM `' . _DB_PREFIX_ . 'product_comment` pc
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pc.`id_product` = pl.`id_product`)
                LEFT JOIN `' . _DB_PREFIX_ . 'cvov` cv ON (pc.`id_product_comment` = cv.`id_product_comment`)
                '.$inner_join.'
                WHERE pc.`id_customer` = "' . pSQL($id_customer) . '" '.$where_join.' AND pl.`id_lang` = "' . $this->context->language->id . '" AND pl.`id_shop` ="' . $this->context->shop->id . '" AND pl.`name` like "%' . pSQL($search_query) . '%"
                GROUP BY pc.`id_product_comment`
                ORDER BY pc.`date_add` DESC
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
                'title' => $this->l('Send new voucher code'),
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
                    'type' => 'commentFilter',
                    'label' => $this->l('Comment'),
                    'name' => 'commentFilter',
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
        if (file_exists(_PS_MODULE_DIR_ . 'comvou/lib/voucherengine/engine.php'))
        {
            require_once _PS_MODULE_DIR_ . 'comvou/lib/voucherengine/engine.php';
        }
        if (Tools::isSubmit('submitAddcvov'))
        {
            if (Tools::getValue('email', 'false') == 'false' || Tools::getValue('email') == '')
            {
                $this->errors[] = Tools::displayError('You must search and select customer');
            }
            if (Tools::getValue('id_product', 'false') == 'false' || Tools::getValue('id_product') == '' || Tools::getValue('id_product_comment', 'false') == 'false' || Tools::getValue('id_product_comment') == '')
            {
                $this->errors[] = Tools::displayError('You must search and select customer\'s comment');
            }

            if (empty($this->errors))
            {
                $comvou = new comvou();
                $comment = array();
                $comment['id_customer'] = Tools::getValue('id_customer', 0);
                if ($comvou->checkPrivilegesVoucher($comment))
                {
                    $voucher = new comvouVoucherEngine("cvv");
                    $voucher_added = $voucher->addVoucherCode('cvv', Tools::getValue('id_customer'));
                    $_POST['deliverydate'] = date("Y-m-d H:i:s");
                    $_POST['voucher'] = $voucher_added->code;
                    $_POST['id_voucher'] = $voucher_added->id;
                    $comment = comvou::getAllCommentsWithoutVoucherById(Tools::getValue('id_product_comment'), $this->context->language->id);
                    $comvou->sendVoucher($comment, $voucher_added);
                }
            }
        }
        return parent::postProcess();
    }
}