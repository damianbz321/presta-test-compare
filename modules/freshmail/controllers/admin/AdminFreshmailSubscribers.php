<?php

use FreshMail\Repository\FreshmailSettings;

require_once __DIR__ . '/AdminFreshmailBase.php';

class AdminFreshmailSubscribersController extends AdminFreshmailBaseController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'freshmail_list_email';
        $this->className = 'FreshMail\Entity\Email';
        $this->lang = false;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->imageType = 'gif';
        $this->fieldImageSettings = [
            'name' => 'icon',
            'dir' => 'os',
        ];

        $this->display = 'list';
        parent::__construct();

        $this->fields_list = [
            'email' => [
                'title' => $this->module->l('Email'),
                'maxlength' => 30,
                'remove_onclick' => true
            ],
            'add_date' => [
                'title' => $this->module->l('Date add'),
            ],
            'status' => [
                'title' => $this->module->l('Subscriber status'),
                'remove_onclick' => true
            ]
        ];

        $hash = (new FreshmailSettings)->findForShop($this->context->shop->id)->subscriber_list_hash;
        $this->_where = ' AND hash_list = "' . pSQL($hash) . '"';


    }

    public function init()
    {
        parent::init();

        $freshmailSettings = (new FreshmailSettings())->findForShop($this->context->shop->id);

        if(empty($freshmailSettings->api_token)){
            $this->errors[] = $this->module->l('To use this feature You have to enable synchronization with FreshMail');
            return;
        }

        if(!in_array($freshmailSettings->subscriber_list_hash, $this->freshmail->getAllHashList() )){
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminFreshmailConfig')
            );
        }

        $ajr = new \FreshMail\Repository\AsyncJobs(Db::getInstance());
        if(!empty($ajr->getRunningJobs($freshmailSettings->api_token))){
            $this->warnings[] = $this->module->l('Synchronization is already pending');
        }

        if(Tools::getIsset('trigger_sync')){
            if(empty($ajr->getRunningJobs($freshmailSettings->api_token))){
                \FreshMail\Tools::triggerSynchronization($freshmailSettings->subscriber_list_hash);
                $this->confirmations[] = $this->module->l('Synchronization has started');
            }
        }
    }

    public function renderList()
    {
        $freshmailSettings = (new FreshmailSettings())->findForShop($this->context->shop->id);
        $this->context->smarty->assign([
            'synchronization_url' => $this->context->link->getBaseLink(null, true).'modules/freshmail/cron/synchronize.php?hash='.$freshmailSettings->subscriber_list_hash . '&token='.Freshmail::getCronToken(),
            'synchronization_cron' => _PS_MODULE_DIR_.'freshmail/cron/synchronize.php'
        ]);

        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name. '/views/templates/admin/manage.tpl')
            . parent::renderList()
            . $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name. '/views/templates/admin/subscriber_list.tpl')
        ;
    }

    public function initPageHeaderToolbar()
    {
        if (!empty($this->display) && 'list' == $this->display) {
            $this->page_header_toolbar_btn['trigger_sync'] = array(
                'href' => self::$currentIndex . '&trigger_sync&token=' . $this->token,
                'desc' => $this->module->l('Start synchronization'),
                'icon' => 'process-icon-refresh',
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

}