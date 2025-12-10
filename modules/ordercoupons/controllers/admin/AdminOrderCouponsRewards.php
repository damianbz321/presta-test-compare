<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 * @version   of the vouchers engine: 4.3
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

class AdminOrderCouponsRewardsController extends ModuleAdminController
{
    protected $position_identifier = 'id_reward';

    public function __construct()
    {
        $this->table = 'orewards_list';
        $this->className = 'orewardslist';
        $this->lang = false;
        $this->identifier = 'id_reward';
        $this->addRowAction('delete');
        $this->bulk_actions = array();
        $this->bootstrap = true;
        parent::__construct();
        $this->_orderBy = 'id_reward';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'customer` b ON (b.`id_customer` = a.`id_customer`) LEFT JOIN `' . _DB_PREFIX_ . 'orders` c ON (c.`id_order` = a.`id_order`)';
        $this->fields_list = array(
            'id_reward' => array(
                'title' => $this->l('ID'),
                'align' => 'left',
                'orderby' => true,
                'width' => 30
            ),
            'id_order' => array(
                'title' => $this->l('Order ID'),
                'width' => 'auto',
                'orderby' => true,
            ),
            'id_customer' => array(
                'title' => $this->l('Customer'),
                'width' => 'auto',
                'orderby' => true,
                //'desc' => $this->l('read').'<a href="">'.$this->l('how to get Product ID').'</a>',
                'callback' => 'getCustomerName',
                'filter_key' => 'b!firstname',
            ),
            'date_del' => array(
                'title' => $this->l('Email'),
                'width' => 'auto',
                'orderby' => true,
                //'desc' => $this->l('read').'<a href="">'.$this->l('how to get Product ID').'</a>',
                'callback' => 'getCustomerEmail',
                'filter_key' => 'b!email',
            ),
            'date_upd' => array(
                'title' => $this->l('Order reference'),
                'width' => 'auto',
                'orderby' => true,
                //'desc' => $this->l('read').'<a href="">'.$this->l('how to get Product ID').'</a>',
                'callback' => 'getOrderReference',
                'filter_key' => 'c!reference',
            ),
            'reward' => array(
                'title' => $this->l('Reward code'),
                'align' => 'left',
                'orderby' => true,
                'width' => 120
            ),
            'date_add' => array(
                'title' => $this->l('Generation date'),
                'align' => 'left',
                'orderby' => true,
                'width' => 120
            ),
        );
    }

    public function initToolbar()
    {
        $this->toolbar_btn = array();
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

    public function getCustomerEmail($group, $row)
    {
        $customer = new Customer($row['id_customer']);
        return $customer->email;
    }

    public function getOrderReference($group, $row)
    {
        $order = new Order($row['id_order']);
        return $order->reference;
    }

    public function init()
    {
        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->_where = 'AND b.id_shop=' . Context::getContext()->shop->id;
        }

        if (Tools::getValue('updateorewards_list', 'false') != 'false') {
            $this->errors[] = $this->l('You cant alter generated reward');
        }
        parent::init();
    }
}