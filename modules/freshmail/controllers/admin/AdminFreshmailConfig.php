<?php

use FreshMail\Repository\FreshmailSettings;

require_once __DIR__ . '/AdminFreshmailBase.php';

class AdminFreshmailConfigController extends AdminFreshmailBaseController
{
    const INTEGRATION_CREATED = 'FreshMail integration for Prestashop created successfully!';
    const SUCCESS_UPDATE_API_KEYS = 'API keys update successfully!';
    const FRESHMAIL_URL = 'http://freshmail.com';


    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->context = Context::getContext();
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();

        if (Tools::isSubmit('resetSettings')) {
            $this->resetSettings();
        }

        $freshmailSettings = (new FreshmailSettings())->findForShop($this->context->shop->id);

        if(empty($freshmailSettings->api_token)){
            $this->viewPreWizard();
            return;
        }

        $this->viewConfig();
    }

    public function createTemplate($tpl_name)
    {
        $path = __DIR__ . '/../../views/templates/admin/';
        return $this->context->smarty->createTemplate($path . $tpl_name, $this->context->smarty);
    }

    public function checkAccess($disable = false)
    {
        return true;
    }

    public function viewAccess($disable = false)
    {
        return true;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(
            array(
                _PS_MODULE_DIR_ . 'freshmail/views/js/submitNewsletter.js?'.$this->module->version
            )
        );
    }

    public function displayAjaxSubmitNewsletter()
    {
        $this->ajax = true;
        Configuration::updateValue('FRESHMAIL_NEWSLETTER_EMAIL', Tools::getValue('email'));

        $result = array(
            'link' => $this->context->link->getAdminLink('AdminFreshmailConfig') . '&conf=4',
            'status' => 'OK'
        );
        echo json_encode($result);
    }

    private function submitEnterApiKeys(Freshmail\Entity\FreshmailSetting $fs, \FreshMail\Freshmail $fm){
        $api_key = Tools::getValue('api_key');
        if (empty($api_key)) {
            $this->errors[] = $this->l('An error occurred while updating.');
            return;
        }
        $listHash = Tools::getValue('subscriber_list_hash');
        if(empty($listHash) || !in_array($listHash, $this->freshmail->getAllHashList())) {
            $this->errors[] = $this->l('Please choose a subscribers list');
            return;
        }

        $changeSMTP = !($fs->smtp == Tools::getValue('smtp'));
        $changedList = !($fs->subscriber_list_hash == Tools::getValue('subscriber_list_hash'));

        //$fs->api_token = $api_key;
        $fs->id_specific_price_rule = (int)Tools::getValue('id_specific_price_rule');
        $fs->smtp = (int)Tools::getValue('smtp');
        $fs->synchronize = (int)Tools::getValue('synchronize');
        $fs->send_confirmation = (int)Tools::getValue('send_confirmation');
        $fs->subscriber_list_hash = Tools::getValue('subscriber_list_hash');

        $fs->save();

        if($changeSMTP){
            \FreshMail\Tools::setShopSmtp($api_key);
        }
        if($changedList){
            // check list has tag imie
            if(!$fm->hasFieldWithTag($fs->subscriber_list_hash, Freshmail::NAME_TAG)){
                $name = \Context::getContext()->getTranslator()->trans('First name', [],'Admin.Global');
                $fm->addFieldToList($fs->subscriber_list_hash, Freshmail::NAME_TAG, $name) ;
            }
            \FreshMail\Tools::triggerSynchronization($fs->subscriber_list_hash);
        }

        $link = $this->context->link->getAdminLink('AdminFreshmailConfig') . '&conf=6';
        Tools::redirectAdmin($link);

    }

    private function submitConnectToApi(\Freshmail\Entity\FreshmailSetting $fs){
        $api = new \FreshMail\Freshmail($fs->api_token);
        $response = $api->check();

        if ($response && $response['status'] == 'OK') {
            return true;
        }
        else {
            $this->errors[] = $this->module->l('Error while connecting to FreshMail API');
        }
    }

    private function resetSettings()
    {
        \FreshMail\Tools::clearShopSettings($this->context->shop->id);
        $link = $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $this->module->name]);
        Tools::redirectAdmin($link);
    }

    private function viewPreWizard(){
        $links = [
            'reset' => $this->context->link->getAdminLink('AdminFreshmailConfig', true, [], ['resetSettings' => 1]),
            'base_url' => $this->context->link->getBaseLink(null, true),
        ];
        $this->context->smarty->assign([
            'links' => $links,
        ]);
        $this->setTemplate('wizard_preview.tpl');
    }

    private function viewConfig(){
        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        $this->addToolBarModulesListButton();

        if(!\FreshMail\Tools::checkDirPermission(FreshMail::TMP_DIR)){
            $this->errors[] = $this->l('Set temporary dir as writeable').' ('.FreshMail::TMP_DIR.')';
        }

        unset($this->toolbar_btn['save']);
        $back = $this->context->link->getAdminLink('AdminDashboard');
        $this->toolbar_btn['back'] = array(
            'href' => $back,
            'desc' => $this->l('Back to the dashboard'),
        );

        $freshmailSettings = (new FreshmailSettings())->findForShop($this->context->shop->id);
        $fm = new FreshMail\Freshmail($freshmailSettings->api_token);

        $helpArray = array(
            'url_post' => self::$currentIndex . '&token=' . $this->token,
            'module_templates' => _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/',
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
            'response' => null,
            'specific_price_rules' => \FreshMail\Tools::getSpecificPriceRules($this->context->shop->id),
            'subscribers_list' => $fm->getAllList(),
            'freshmail_settings' => $freshmailSettings
        );

        if (Tools::isSubmit('submitEnterApiKeys')) {
            $this->submitEnterApiKeys($freshmailSettings, $fm);
        } elseif (Tools::isSubmit('submitConnectToApi')) {
            if($this->submitConnectToApi($freshmailSettings)) {
                $helpArray['success'] = $this->module->l(self::INTEGRATION_CREATED);
            }
        }

        if (!empty($freshmailSettings->api_token)) {
            $helpArray['showCheck'] = true;
        } else {
            $helpArray['showCheck'] = false;
        }

        $helpArray['FRESHMAIL_NEWSLETTER_EMAIL'] = Configuration::get('FRESHMAIL_NEWSLETTER_EMAIL');
        $helpArray['FRESHMAIL_API_KEY'] = $freshmailSettings->api_token;

        $this->context->smarty->assign($helpArray);
        $this->setTemplate('api.tpl');
    }
}