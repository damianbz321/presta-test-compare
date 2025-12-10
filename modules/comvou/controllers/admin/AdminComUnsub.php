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

require_once(dirname(__FILE__) . '../../../models/cunsub.php');
require_once(dirname(__FILE__) . '../../../comvou.php');

class AdminComUnsubController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'cunsub';
        $this->className = 'cunsub';
        $this->lang = false;
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        parent::__construct();
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            )
        );
        $this->bootstrap = true;
        $this->_orderBy = 'id_cunsub';
        $this->_group = 'GROUP BY a.id_cunsub';
        $this->fields_list = array(
            'id_cunsub' => array(
                'title' => $this->l('ID'),
                'align' => 'left',
                'orderby' => true,
                'width' => 30
            ),
            'email' => array(
                'title' => $this->l('Email'),
                'align' => 'left',
                'hint' => $this->l('Email of customer that unsubscribed from reminders'),
                'orderby' => true,
                'width' => 250
            ),
            'ip' => array(
                'title' => $this->l('Ip'),
                'align' => 'left',
                'orderby' => true,
                'width' => 70
            ),
            'unsub_date' => array(
                'title' => $this->l('Unsubscription date'),
                'align' => 'left',
                'type' => 'datetime',
                'orderby' => true,
            ),
        );
    }

    public function renderForm()
    {

        $obj = $this->loadObject(true);
        if (isset($obj->id)) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Unsubscription from reminders'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Email of customer'),
                    'name' => 'email',
                    'required' => true,
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('IP'),
                    'name' => 'ip',
                    'required' => true,
                    'lang' => false,
                ),
                array(
                    'type' => 'datetime',
                    'label' => $this->l('Date'),
                    'name' => 'unsub_date',
                    'required' => true,
                ),
            ),

            'submit' => array(
                'title' => $this->l('Save')
            )
        );
        return parent::renderForm();
    }
}