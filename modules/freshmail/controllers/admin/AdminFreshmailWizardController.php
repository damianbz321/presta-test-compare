<?php

use FreshMail\ApiV2\UnauthorizedException;
use FreshMail\FreshmailApiV3;
use FreshMail\Repository\FreshmailSettings;

class AdminFreshmailWizardController extends ModuleAdminController
{
    private $settingRepository;

    const NEW_LIST_KEY = 'create_new';

    public function __construct()
    {
        $this->bootstrap = true;

        parent::__construct();

        $this->templateFile = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/wizard.tpl';
        $this->settingRepository = new FreshmailSettings();
    }

    public function run()
    {
        if ($step = Tools::getValue('step')) {
            if (method_exists($this, $step)) {
                echo json_encode($this->$step());
            }
        }

        $this->context->smarty->assign([
            'is_wizard_available' => $this->isWizardAvailable(),
            'links' => $this->getLinks(),
            'module_version' => $this->module->version,
            'specific_price_rules' => \FreshMail\Tools::getSpecificPriceRules($this->context->shop->id)
        ]);
        return $this->context->smarty->fetch($this->templateFile);
    }

    private function getLinks()
    {
        return [
            'settings' => $this->context->link->getAdminLink('AdminFreshmailWizard', true, [], ['step' => 'settings']),
            'connect' => $this->context->link->getAdminLink('AdminFreshmailWizard', true, [], ['step' => 'connect']),
            'save' => $this->context->link->getAdminLink('AdminFreshmailWizard', true, [], ['step' => 'save']),
            'lists' => $this->context->link->getAdminLink('AdminFreshmailWizard', true, [], ['step' => 'getLists']),
            'register' => 'https://app.freshmail.com/pl/auth/login',
            'help' => 'https://freshmail.pl/podrecznik-uzytkownika/',
            'success_redirect' => $this->context->link->getAdminLink('AdminFreshmailConfig', true, [], ['step' => 'getLists']),
            'abandon_carts_redirect' => $this->context->link->getAdminLink('AdminFreshmailAbandonedCartConfig', true, []),
            'synchronize_link' => $this->context->link->getBaseLink(null, true).'modules/'.$this->module->name.'/cron/synchronize.php?token='.$this->module->getCronToken().'&hash=',
            'activate_package' => $this->context->link->getAdminLink('AdminFreshmailWizard', true, [], ['step' => 'success']),
            'base_url' => $this->context->link->getBaseLink(null, true),
        ];
    }

    private function isWizardAvailable()
    {
        return true;

    }

    private function settings()
    {
        $settings = $this->settingRepository->findForShop($this->context->shop->id);

        $this->module->loadFreshmailApi();
        $freshailApi = new \FreshMail\ApiV2\Client($settings->api_token);

        $isLogged = false;
        try {
            $status = $freshailApi->doRequest('ping');
            $isLogged = true;
        } catch (UnauthorizedException $unauthorizedException) {
        }


        return [
            'logged_in' => $isLogged,
            'api_token' => $settings->api_token,
            'smtp' => (bool)$settings->smtp,
            'synchronize' => (bool)$settings->synchronize,
            'id_specific_price_rule' => ((int)$settings->id_specific_price_rule > 0) ? (int)$settings->id_specific_price_rule : '',
            'synchronize_list' => 'create_new'
        ];
    }

    private function connect()
    {
        $this->module->loadFreshmailApi();

        $apiV2 = new \FreshMail\ApiV2\Client(Tools::getValue('api_token'));

        try {
            $apiV2->doRequest('ping');
        } catch (UnauthorizedException $unauthorizedException) {
            header("HTTP/1.1 401 Unauthorized");

            return [
                'success' => false,
                'message' => $this->module->l('Please provide a valid token')
            ];
        }

        $fs = $this->settingRepository->findForShop($this->context->shop->id);
        $fs->api_token = Tools::getValue('api_token');
        $fs->id_shop = $this->context->shop->id;
        $fs->save();

        return [
            'success' => true,
            'message' => $this->module->l('Correctly connected to the freshmail account')
        ];
    }

    private function getLists()
    {
        $fs = $this->settingRepository->findForShop($this->context->shop->id);
        $freshmail = new \FreshMail\Freshmail($fs->api_token);
        try {
            $list = array_merge(
                [[
                    'subscriberListHash' => self::NEW_LIST_KEY,
                    'name' => 'Prestashop - ' . $this->context->shop->name
                ]],
                $freshmail->getLists()
            );
            return [
                'success' => true,
                'data' => $list
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $this->module->l('Please provide a valid token')
            ];
        }


        // /rest/

    }

    private function save()
    {
        $fs = $this->settingRepository->findForShop($this->context->shop->id);
        if(empty($fs->api_token)){
            \FreshMail\Installer\Tabs::install($this->module);
            return $this->saveWithoutToken($fs);
        }
        \FreshMail\Installer\Tabs::install($this->module, 'extended');
        return $this->saveWithToken($fs);

    }

    private function saveWithoutToken(\Freshmail\Entity\FreshmailSetting $fs)
    {
        $fs->wizard_completed = 1;
        $fs->id_shop = $this->context->shop->id;

        $fs->save();

        return [
            'success' => true,
            'message' => $this->module->l('Configuration saved'),
            'synchronize' => 0,
        ];

    }

    private function saveWithToken(\Freshmail\Entity\FreshmailSetting $fs)
    {
        $fs->smtp = (Tools::getValue('smtp') == 'true') ? 1 : 0;

        if (1 == $fs->smtp) {
            \FreshMail\Tools::setShopSmtp($fs->api_token);
        }

        $fs->synchronize = (Tools::getValue('synchronize') == 'true') ? 1 : 0;

        $listHash = Tools::getValue('synchronize_list');

        $fm = new \FreshMail\Freshmail($fs->api_token);
        if (self::NEW_LIST_KEY == Tools::getValue('synchronize_list', '')) {
            $name = 'Prestashop - ' . $this->context->shop->name;
            $description = \Context::getContext()->getTranslator()->trans('List from ', [], 'Modules.freshmail').$this->context->shop->name;
            $listHash = $fm->addList($name, $description);
        }

        if(!$fm->hasFieldWithTag($listHash, Freshmail::NAME_TAG)) {
            $fm->addFieldToList($listHash, Freshmail::NAME_TAG, $this->trans('First name', [],'Admin.Global'));
        }

        if (1 == $fs->synchronize) {

        }

        $fs->subscriber_list_hash = $listHash;
        $fs->wizard_completed = 1;
        $fs->id_specific_price_rule = (int)Tools::getValue('id_specific_price_rule');
        $fs->save();

        $apiV3 = new FreshmailApiV3($fs->api_token);
        try {
            $apiV3->sendIntegrationInfo();

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        // \FreshMail\Tools::sendWizardSuccessMail();

        return [
            'success' => true,
            'message' => $this->module->l('Configuration saved'),
            'synchronize' => (int)$fs->synchronize,
        ];
    }

}