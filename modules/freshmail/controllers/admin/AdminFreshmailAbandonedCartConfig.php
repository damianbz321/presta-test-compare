<?php

require_once __DIR__.'/AdminFreshmailBase.php';

class AdminFreshmailAbandonedCartConfigController extends AdminFreshmailBaseController
{
    const TPL_DIR = 'PrestaShop';

    const TPL_CATEGORY = 3;

    private $cart_config;

    public function __construct()
    {
        $this->bootstrap = true;

        parent::__construct();

        $this->template = 'cart_config.tpl';
        $this->override_folder = '/';

        $this->cart_config = (new \FreshMail\Repository\FreshmailAbandonCartSettings(Db::getInstance()))->findForShop($this->context->shop->id);
    }

    public function run()
    {
        if(Tools::getIsset('ajax')){
            return $this->ajax();
        }
        return parent::run();
    }

    private function ajax(){
        $action = Tools::getValue('action');
        $availableActions = ['set', 'getTpl', 'test'];

        $result = [
            'success' => false,
            'message' => sprintf('Action %s nof found', $action)
        ];

        if(in_array($action, $availableActions) && method_exists($this, $action)) {
            $result = $this->$action();
        }

        die(json_encode($result));
    }

    public function getTpl()
    {
        parent::init();

        $tplList = [];
        if(!empty($this->freshmail) && $this->freshmail->check()){
            $tplList = $this->freshmail->getEmailsTemplates(self::TPL_DIR);
            \FreshMail\Tools::filterTplByCategory($tplList, self::TPL_CATEGORY);
        }

        die(
            json_encode([
                'template' => json_decode($this->cart_config->template),
                'tpl_list' => $tplList
            ])
        );

    }

    public function init(){
        parent::init();

        $idShop = $this->context->shop->id;
        if (Configuration::get('PS_LOGO_MAIL') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL', null, null, $idShop))) {
            $logo = _PS_IMG_.Configuration::get('PS_LOGO_MAIL', null, null, $idShop);

        } else {
            if (file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $idShop))) {
                $logo = _PS_IMG_.Configuration::get('PS_LOGO', null, null, $idShop);
            } else {
                $logo = '';
            }
        }
        $logo = $this->context->shop->getBaseURL(true).ltrim($logo, '/');

        if(!Validate::isLoadedObject($this->cart_config)){
            $idLang = Configuration::get('PS_LANG_DEFAULT');
            $this->cart_config->email_preheader[$idLang] = $this->module->l('🛒 Complete your purchases!');
            $this->cart_config->email_subject[$idLang] = $this->module->l('You have unfinished purchases in your cart.');
        }

        $this->context->smarty->assign([
            'cart_config' => $this->cart_config,
            'links' => $this->getLinks(),
            'logo' => $logo,
            'is_logged' => !empty($this->freshmail) && $this->freshmail->check(),
            'id_lang' => Configuration::get('PS_LANG_DEFAULT')
        ]);

    }

    private function getLinks()
    {
        return [
            'base_url' => $this->context->link->getBaseLink(null, true),
            'cron_url' => $this->context->link->getBaseLink(null, true).'modules/freshmail/cron/abandoned_cart.php?token='.$this->module->getCronToken(),
            'cron_cli' => _PS_MODULE_DIR_ . 'freshmail/cron/abandoned_cart.php'
        ];
    }

    private function set(){
        $config = json_decode(Tools::getValue('config'));
        $this->cart_config->enabled = $config->emails == 'true' ? 1 : 0;
        $this->cart_config->id_shop = $this->context->shop->id;
        $this->cart_config->template = Tools::getValue('template');
        $this->cart_config->discount_percent = (int)$config->discount_percent_value;
        $this->cart_config->discount_code = pSQL($config->discount_custom_value);
        $this->cart_config->discount_type = pSQL($config->discount);
        $this->cart_config->template_id_hash = pSQL($config->template_id_hash);
        $this->cart_config->send_after = (int)$config->send_after;
        $this->cart_config->discount_lifetime = (int)$config->discount_percent_livetime;
        $this->cart_config->email_subject = pSQL($config->email_subject);
        $this->cart_config->email_preheader = pSQL($config->email_preheader);
        $this->cart_config->save();

        try {
            foreach (Language::getIsoIds() as $lang) {
                \FreshMail\Tools::writeEmailTpl(rawurldecode(Tools::getValue('html')), $lang['iso_code']);
            }
        } catch (Exception $e){
            return [
                'success' => false,
                'message' => $this->module->l('Unable to write file: '.$e->getMessage())
            ];
        }

        return [
            'success' => true,
            'message' => $this->module->l('Configuration saved')
        ];
    }

    private function test(){
        $testEmail = Tools::getValue('email');
        if(!Validate::isEmail($testEmail)){
            return [
                'success' => false,
                'message' => sprintf('Email %s is not valid', $testEmail)
            ];
        }

        $abandonRepository = new \FreshMail\Repository\FreshmailAbandonCartSettings(Db::getInstance());
        $activeAbandon = $abandonRepository->getActive();
        $cartsRepository = new \FreshMail\Repository\Carts(Db::getInstance());
        $settingsRepository = new \FreshMail\Repository\FreshmailSettings(Db::getInstance());
        $cartService = new \FreshMail\Service\AbandonCartService($cartsRepository, $settingsRepository, $abandonRepository);

        foreach ($activeAbandon as $abandon) {
            $settings = new \Freshmail\Entity\AbandonedCartSettings($abandon['id_freshmail_cart_setting']);
            $cart = new Cart();
            $cart->id_shop = $this->context->shop->id;
            $cart->id_lang = $this->context->language->id;
            $cart->id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
            $fmCart = new \FreshMail\Entity\Cart();
            $email = new \FreshMail\Sender\Email($testEmail);
            $cartService->sendNotifications($cart, $fmCart, $email, new \FreshMail\Sender\Service\MockCartData());
        }
        return [
            'success' => true,
            'message' => $this->module->l('Test e-mail was sent')
        ];
    }
}
