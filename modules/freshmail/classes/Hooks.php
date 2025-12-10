<?php

namespace FreshMail;

use Configuration;
use FreshMail\Entity\Cart;
use FreshMail\Repository\AsyncJobs;
use FreshMail\Repository\FormRepository;
use FreshMail\Repository\FreshmailAbandonCartSettings;
use FreshMail\Repository\FreshmailSettings;
use FreshMail\Service\FormService;
use Validate;

trait Hooks
{

    public function getHooks()
    {
        return [
            'displayBackOfficeHeader',
            'actionCustomerAccountAdd',
            'actionDeleteGDPRCustomer',
            'actionObjectCustomerDeleteAfter',
            'actionObjectCartAddAfter',
        ];
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        // $this->context->controller->addCSS($this->_path . 'views/css/freshmail-core.css', 'all');

        $ets = new \FreshMail\Repository\EmailToSynchronize(\Db::getInstance());
        $list = $ets->getListToSync();
        if(!empty($list[0])){
            $this->context->smarty->assign([
                'pendingSend' => true,
                'sendUrl' => $this->context->link->getBaseLink(null,true).'modules/'.$this->name.'/cron/send_subscribers.php?hash='.$list[0]['hash_list'].'&token='.$this->getCronToken()
            ]);
        }

        $this->context->smarty->assign([
            'base_url' => $this->context->link->getBaseLink(null,true),
        ]);

        $aj = new AsyncJobs(\Db::getInstance());
        $jobs = $aj->getRunningJobs();
        if(!empty($jobs)){
            Tools::asyncJobPing();
        }

        return $this->display(_PS_MODULE_DIR_ .'freshmail', 'views/templates/admin/header.tpl');
    }
     public function hookActionCustomerAccountAdd($params){
        $customer = $params['newCustomer'];

        if($customer->newsletter){
            $freshmailSettings = (new FreshmailSettings())->findForShop($this->context->shop->id);
            $fmList = $this->getFreshmailList();
            if(empty($fmList)){
                return;
            }

            $subscriber = new Subscriber($customer->email);
            $subscriber->custom_fields[\Freshmail::NAME_TAG] = $customer->firstname ;
            $fmList->addSubscriber($subscriber);
        }
    }

    public function hookActionDeleteGDPRCustomer($customer)
    {
        if (!empty($customer['email']) && Validate::isEmail($customer['email'])) {
            $fmList = $this->getFreshmailList();
            if(empty($fmList)){
                return;
            }
            $fmList->deleteSubscriber(new Subscriber($customer['email']));
        }
    }

    public function hookActionObjectCustomerDeleteAfter($params)
    {
        $email = $params['object']->email;
        $fmList = $this->getFreshmailList();

        if(empty($fmList)){
            return;
        }
        $fmList->deleteSubscriber(new Subscriber($email));
    }


    public function getFreshmailList() : FreshmailList
    {
        $freshmailSettings = (new FreshmailSettings())->findForShop($this->context->shop->id);
        $fm = new FreshmailList($freshmailSettings);
        if(empty($freshmailSettings->subscriber_list_hash) || !$fm->check()){
            return false;
        }

        return $fm;
    }

    public function hookActionObjectCartAddAfter($params)
    {
        $cart = $params['object'];
        if(
            !(new FreshmailAbandonCartSettings(\Db::getInstance()))->findForShop($cart->id_shop)->enabled
        ){
            return;
        }

        $fmCart = new Cart();
        $fmCart->id_cart = $cart->id;
        $fmCart->cart_token = sha1(time()).md5(time());
        $fmCart->save();
    }
}