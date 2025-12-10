<?php

use FreshMail\Hooks;
use FreshMail\Installer\InstallerFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

require __DIR__ . '/lib/autoload.php';

class Freshmail extends Module
{
    const TMP_DIR = _PS_MODULE_DIR_ . 'freshmail/tmp/';

    const NAME_TAG = 'imie';

    const CACHE_FORM_LIFETIME = 10 * 60;

    use Hooks;
    use \FreshMail\HooksForms;

    public function __construct()
    {
        $this->name = "freshmail";
        $this->tab = "front_office_features";

        $this->version = "3.10.1";
        $this->author = "FreshMail";
        $this->need_instance = 1;
        $this->bootstrap = true;
        $this->displayName = $this->l('FreshMail for PrestaShop');
        $this->description = $this->l('Synchronizes your newsletter subscribers and shop items with Freshmail.com');

        /* $moduleManager = $this->get('prestashop.module.manager');

         if($moduleManager && $moduleManager->isInstalled($this->name)){
             $this->description .= ' ... <span>
                 <a class="module-read-more-list-btn url" href="/modules/freshmail/ajax.php" data-target="#module-modal-read-more-freshmail">Czytaj więcej</a>
             </span>';
         }*/

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?', 'freshmail');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        parent::__construct();
        $this->checkNativeSubscription();
    }

    public function install()
    {
        Configuration::updateValue('FRESHMAIL_SUBMISSION_SUCCESS_MESSAGE', $this->l('Your sign up request was successful! Please check your email inbox.'));
        Configuration::updateValue('FRESHMAIL_SUBMISSION_FAILURE_MESSAGE', $this->l('Oops. Something went wrong. Please try again later.'));
        Configuration::updateValue('FRESHMAIL_ALREADY_SUBSCRIBED_MESSAGE', $this->l('Given email address is already subscribed, thank you!'));
        Configuration::updateValue('FRESHMAIL_INVALID_EMAIL_ADDRESS_MESSAGE', $this->l('Please provide a valid email address'));
        Configuration::updateValue('FRESHMAIL_REQUIRED_FIELD_MISSING_MESSAGE', $this->l('Please fill all the required fields'));
        Configuration::updateValue('FRESHMAIL_FORM_SUBSCRIBE_BUTTON', $this->l('Sign me up!'));
        Configuration::updateValue('FRESHMAIL_SIGNUP_LABEL', $this->l('Sign me up for the newsletter'));
        Configuration::updateValue('FRESHMAIL_ACCEPT_EMAILS_PARTNERS_LABEL', $this->l('I agree to receive email communications from partners'));


        return parent::install()
            && InstallerFactory::getInstaller($this)->install();
    }

    public function uninstall()
    {
        return InstallerFactory::getInstaller($this)->uninstall()
            && parent::uninstall();
    }

    public function getContent()
    {
        $fs = (new \FreshMail\Repository\FreshmailSettings())->findForShop($this->context->shop->id);
        if(!empty($fs->wizard_completed) && !empty($fs->api_token)) {
            $link = $this->context->link->getAdminLink('AdminFreshmailConfig');
            Tools::redirectAdmin($link);
        }

        $controller = new AdminFreshmailWizardController();
        return $controller->run();
    }

    public function loadFreshmailApi()
    {
        require_once __DIR__ . '/lib/freshmail-api/vendor/autoload.php';
    }

    public static function getCronToken(){
        return substr(Tools::encrypt('freshmail/index'), 0, 10);
    }

    private function checkNativeSubscription(){
        if(!Module::isEnabled('ps_emailsubscription')){
            return;
        }
        if (!Tools::isSubmit('submitNewsletter')
            && !($this->context->controller instanceof Ps_EmailsubscriptionVerificationModuleFrontController)
        ) {
            return;
        }

        if(Tools::isSubmit('submitNewsletter') && Configuration::get('NW_VERIFICATION_EMAIL')){
            return;
        }

        $fs = (new \FreshMail\Repository\FreshmailSettings(Db::getInstance()))->findForShop($this->context->shop->id);

        $email = '';
        if($this->context->controller instanceof Ps_EmailsubscriptionVerificationModuleFrontController){
            $token = Tools::getValue('token');
            $email = $this->getGuestEmailByToken($token);
            if(empty($email)) {
                $email = $this->getUserEmailByToken($token);
            }
        }

        if(Tools::isSubmit('submitNewsletter') && !Configuration::get('NW_VERIFICATION_EMAIL')) {
            $email = Tools::getValue('email');
        }

        if(empty($email)){
            return;
        }

        $fm = new \FreshMail\Freshmail($fs->api_token);
        $result = $fm->addSubscriber([
            'email' => $email,
            'list' => $fs->subscriber_list_hash,
            'state' => (!$fs->send_confirmation ) ? 1 : 2,
            'confirm' => $fs->send_confirmation
        ]);
    }
}