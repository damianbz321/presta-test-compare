<?php

if (!defined('_PS_VERSION_'))
	exit;

class LabPopupNewsletter extends Module
{
	private $_html = '';
	private $_postErrors = array();
	const GUEST_NOT_REGISTERED = -1;
	const CUSTOMER_NOT_REGISTERED = 0;
	const GUEST_REGISTERED = 1;
	const CUSTOMER_REGISTERED = 2;

    function __construct()
    {
		$this->name = 'labpopupnewsletter';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'laberthemes';

		$this->controllers = array('verification');
		
		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('LABER Popup Newsletter');
		$this->description = $this->l('Shows popup newsletter window with your message');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{		
		$this->installTab();
		$this->context->controller->getLanguages();
                $title = array();
                $content = array();
                foreach ($this->context->controller->_languages as $language){
                    $title[(int)$language['id_lang']] = 'SIGN UP FOR OUR NEWSLETTER & PROMOTIONS !';
                    $content[(int)$language['id_lang']] = '						<h2>GET 20% OFF</h2>						<p>ON YOUR NEXT PURCHASE</p>					';
                }
                
                //$this->_createTab();
		if (
			parent::install() == false
			|| $this->registerHook('header') == false
			|| $this->registerHook('displaypopupnewsletter') == false
			|| Configuration::updateValue('LAB_TEXT', $content) == false
			|| Configuration::updateValue('LAB_TITLE', $title) == false
			|| Configuration::updateValue('LAB_WIDTH', 600) == false
			|| Configuration::updateValue('LAB_HEIGHT', 700) == false
			|| Configuration::updateValue('LAB_NEWSLETTER', true) == false
			|| Configuration::updateValue('LAB_BG', true) == false
			|| Configuration::updateValue('LAB_BG_IMAGE',  _MODULE_DIR_.$this->name.'/img/background1.png') == false
                    )
			return false;
		return true;
	}
	
	public function uninstall()
	{		$this->uninstallTab();
		$this->context->controller->getLanguages();
		foreach ($this->context->controller->_languages as $language){
			Configuration::deleteByName('LAB_TEXT_'.(int)$language['id_lang']);
                        Configuration::deleteByName('LAB_TITLE_'.(int)$language['id_lang']);
                }
                //$this->_deleteTab();
		return 
			Configuration::deleteByName('LAB_WIDTH') &&
			Configuration::deleteByName('LAB_HEIGHT') &&
			Configuration::deleteByName('LAB_NEWSLETTER') &&
			Configuration::deleteByName('LAB_BG') &&
			Configuration::deleteByName('LAB_BG_IMAGE') &&
			parent::uninstall();
	}	public function installTab()    {        $response = true;        // First check for parent tab        $parentTabID = Tab::getIdFromClassName('AdminMenuFirst');        if ($parentTabID) {            $parentTab = new Tab($parentTabID);        }        else {            $parentTab = new Tab();            $parentTab->active = 1;            $parentTab->name = array();            $parentTab->class_name = "AdminMenuFirst";            foreach (Language::getLanguages() as $lang) {                $parentTab->name[$lang['id_lang']] = "FRAMEWORK";            }            $parentTab->id_parent = 0;            $parentTab->module ='';            $response &= $parentTab->add();        }			// Check for parent tab2			$parentTab_2ID = Tab::getIdFromClassName('AdminMenuSecond');			if ($parentTab_2ID) {				$parentTab_2 = new Tab($parentTab_2ID);			}			else {				$parentTab_2 = new Tab();				$parentTab_2->active = 1;				$parentTab_2->name = array();				$parentTab_2->class_name = "AdminMenuSecond";				foreach (Language::getLanguages() as $lang) {					$parentTab_2->name[$lang['id_lang']] = "Module Configure";				}				$parentTab_2->id_parent = $parentTab->id;				$parentTab_2->module = '';				$response &= $parentTab_2->add();			}		// Created tab        $tab = new Tab();        $tab->active = 1;        $tab->class_name = "Adminlabpopupnewsletter";        $tab->name = array();        foreach (Language::getLanguages() as $lang) {            $tab->name[$lang['id_lang']] = "Laber Popup Newsletter";        }        $tab->id_parent = $parentTab_2->id;        $tab->module = $this->name;        $response &= $tab->add();        return $response;    }    public function uninstallTab()    {        $id_tab = Tab::getIdFromClassName('Adminlabpopupnewsletter');		$parentTabID = Tab::getIdFromClassName('AdminMenuFirst');        $tab = new Tab($id_tab);        $tab->delete();		// Get the number of tabs inside our parent tab        // If there is no tabs, remove the parent		$parentTab_2ID = Tab::getIdFromClassName('AdminMenuSecond');		$tabCount_2 = Tab::getNbTabs($parentTab_2ID);        if ($tabCount_2 == 0) {            $parentTab_2 = new Tab($parentTab_2ID);            $parentTab_2->delete();        }        // Get the number of tabs inside our parent tab        // If there is no tabs, remove the parent        $tabCount = Tab::getNbTabs($parentTabID);        if ($tabCount == 0) {            $parentTab = new Tab($parentTabID);            $parentTab->delete();        }        return true;    }
	public function getContent()
	{

		$this->context->controller->getLanguages();
                $errors = array();
		if (Tools::isSubmit('lab_submit')) {
			Configuration::updateValue('LAB_WIDTH', (int)Tools::getValue('LAB_WIDTH'));
			Configuration::updateValue('LAB_HEIGHT', (int)Tools::getValue('LAB_HEIGHT'));
			Configuration::updateValue('LAB_NEWSLETTER', (bool)Tools::getValue('LAB_NEWSLETTER'));
                        Configuration::updateValue('LAB_BG', Tools::getValue('LAB_BG'));
                        if (Tools::isSubmit('LAB_BG_IMAGE')){
                            Configuration::updateValue('LAB_BG_IMAGE', Tools::getValue('LAB_BG_IMAGE'));
                        }
                        $message_trads = array();
                        $message_trads2 = array();
                        foreach ($_POST as $key => $value){
                                if (preg_match('/LAB_TITLE_/i', $key))
                                {
                                        $id_lang = preg_split('/LAB_TITLE_/i', $key);
                                        $message_trads2[(int)$id_lang[1]] = $value;
                                }
                                if (preg_match('/LAB_TEXT_/i', $key))
                                {
                                        $id_lang = preg_split('/LAB_TEXT_/i', $key);
                                        $message_trads[(int)$id_lang[1]] = $value;
                                }
                        }
                        Configuration::updateValue('LAB_TEXT', $message_trads, true);
                        Configuration::updateValue('LAB_TITLE', $message_trads2, true);
			$this->_clearCache($this->name.'.tpl');

		}
		return $output.$this->renderForm();
	}

	public function hookdisplaypopupnewsletter($params)
	{
		if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index') {
				$this->context->smarty->assign(array(
					'laber_popupnewlestter' => $this->getConfigFromDB(),
				));		
			return $this->display(__FILE__, $this->name.'.tpl');
		}
	}

	public function hookHeader($params)
	{
		if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index') {
			$this->context->controller->addJS(($this->_path).'js/init.js');
			$this->context->controller->addCSS(($this->_path).'css/styles.css', 'all');
		}
	}
        
         public function setMedia()
        {
                parent::setMedia();
                $this->addJqueryUI('ui.datepicker');
        }

	public function renderForm()
	{

		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Module Appearance'),
					'icon' => 'icon-cogs'
				),
				'input' => array(	
                                        array(
                                                'type' => 'text',
                                                'name' => 'LAB_TITLE',
                                                'label' => $this->l('Popup title'),
                                                'required' => false,												'class' => 'fixed-width-xxl',
                                                'lang' => true,
						'autoload_rte' => true
                                        ),
					array(
						'type' => 'text',
						'label' => $this->l('Width of popup window'),
						'name' => 'LAB_WIDTH',
						'class' => 'fixed-width-xxl'
					),	
					array(
						'type' => 'text',
						'label' => $this->l('Height of popup window'),
						'name' => 'LAB_HEIGHT',
						'class' => 'fixed-width-xxl'
					),
					array(
						'type' => 'textarea',
						'label' => $this->l('Popup content'),
						'name' => 'LAB_TEXT',
						'rows' => 10,
						'cols' => 40,
						'lang' => true,
						'autoload_rte' => true
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Show Newsletter form in popup'),
						'name' => 'LAB_NEWSLETTER',
						'is_bool' => true,
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->l('Yes')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->l('No')
									)
								),
						),
					array(
						'type' => 'switch',
						'label' => $this->l('Show background image'),
						'name' => 'LAB_BG',
						'is_bool' => true,
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => true,
										'label' => $this->l('Yes')
									),
									array(
										'id' => 'active_off',
										'value' => false,
										'label' => $this->l('No')
									)
								),
						),
                                        array(
						'type' => 'background_image',
						'label' => $this->l('Popup background image'),
						'name' => 'LAB_BG_IMAGE',
						'size' => 30,
					)
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);
		

		$languages = Language::getLanguages(false);
		foreach ($languages as $k => $language)
			$languages[$k]['is_default'] = (int)$language['id_lang'] == Configuration::get('PS_LANG_DEFAULT');

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->languages = $languages;
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		$helper->allow_employee_form_lang = true;
		$helper->toolbar_scroll = true;
		$helper->toolbar_btn = $this->initToolbar();
		$helper->title = $this->displayName;
		$helper->submit_action = 'lab_submit';
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
		);
		return $helper->generateForm(array($fields_form));
	}

	private function initToolbar()
	{
		$this->toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->l('Save')
		);

		return $this->toolbar_btn;
	}
	
	public function getConfigFieldsValues()
	{
		$values = array(
			'LAB_WIDTH' => Tools::getValue('LAB_WIDTH', Configuration::get('LAB_WIDTH')),
			'LAB_HEIGHT' => Tools::getValue('LAB_HEIGHT', Configuration::get('LAB_HEIGHT')),
			'LAB_NEWSLETTER' => Tools::getValue('LAB_NEWSLETTER', Configuration::get('LAB_NEWSLETTER')),
			'LAB_BG' => Tools::getValue('LAB_BG', Configuration::get('LAB_BG')),
			'LAB_BG_IMAGE' => Tools::getValue('LAB_BG_IMAGE', Configuration::get('LAB_BG_IMAGE')),
			'LAB_COUNTDOWN_POPUP' => Tools::getValue('LAB_COUNTDOWN_POPUP', Configuration::get('LAB_COUNTDOWN_POPUP')),
			'LAB_COUNTDOWN_POPUP_START' => Tools::getValue('LAB_COUNTDOWN_POPUP_START', Configuration::get('LAB_COUNTDOWN_POPUP_START')),
		);

                foreach (Language::getLanguages(false) as $lang){
                            $values['LAB_TITLE'][(int)$lang['id_lang']] = Configuration::get('LAB_TITLE', (int)$lang['id_lang']);
                            $values['LAB_TEXT'][(int)$lang['id_lang']] = Configuration::get('LAB_TEXT', (int)$lang['id_lang']);
                }
		return $values;
	}

	public function getConfigFromDB()
	{
                $now = date('Y-m-d H:i:00');
                $start_date = (Configuration::get('LAB_COUNTDOWN_POPUP_START') ? Configuration::get('LAB_COUNTDOWN_POPUP_START'): '0000-00-00 00:00:00');
                if (strtotime($start_date) > strtotime($now)){
                    $end_date = "0000-00-00 00:00:00";
                } else {
                    $end_date = (Configuration::get('LAB_COUNTDOWN_POPUP') ? Configuration::get('LAB_COUNTDOWN_POPUP'): '0000-00-00 00:00:00');
                }
		return array(
			'LAB_WIDTH' => (Configuration::get('LAB_WIDTH') ? Configuration::get('LAB_WIDTH'): "400"),
			'LAB_HEIGHT' => (Configuration::get('LAB_HEIGHT') ? Configuration::get('LAB_HEIGHT'): "400"),
			'LAB_NEWSLETTER' => (Configuration::get('LAB_NEWSLETTER') ? Configuration::get('LAB_NEWSLETTER'): false),
			'LAB_TEXT' => (Configuration::get('LAB_TEXT', $this->context->language->id) ? Configuration::get('LAB_TEXT', $this->context->language->id): false),
                        'LAB_TITLE' => (Configuration::get('LAB_TITLE', $this->context->language->id) ? Configuration::get('LAB_TITLE', $this->context->language->id): false),
			'LAB_BG' => (Configuration::get('LAB_BG') ? Configuration::get('LAB_BG'): 0),
			'LAB_BG_IMAGE' => (Configuration::get('LAB_BG_IMAGE') ? Configuration::get('LAB_BG_IMAGE'): 0),
			'LAB_PATH' => Tools::getShopProtocol().Context::getContext()->shop->domain.Context::getContext()->shop->physical_uri.'modules/labpopupnewsletter/ajax.php',
                        'LAB_COUNTDOWN_POPUP' => $end_date
		);
	}

	/**
	 * Check if this mail is registered for newsletters
	 *
	 * @param string $customer_email
	 *
	 * @return int -1 = not a customer and not registered
	 *                0 = customer not registered
	 *                1 = registered in block
	 *                2 = registered in customer
	 */
	private function isNewsletterRegistered($customer_email)
	{
		$sql = 'SELECT `email`
				FROM '._DB_PREFIX_.'emailsubscription
				WHERE `email` = \''.pSQL($customer_email).'\'
				AND id_shop = '.$this->context->shop->id;

		if (Db::getInstance()->getRow($sql))
			return self::GUEST_REGISTERED;

		$sql = 'SELECT `newsletter`
				FROM '._DB_PREFIX_.'customer
				WHERE `email` = \''.pSQL($customer_email).'\'
				AND id_shop = '.$this->context->shop->id;

		if (!$registered = Db::getInstance()->getRow($sql))
			return self::GUEST_NOT_REGISTERED;

		if ($registered['newsletter'] == '1')
			return self::CUSTOMER_REGISTERED;

		return self::CUSTOMER_NOT_REGISTERED;
	}

	/**
	 * Return true if the registered status correspond to a registered user
	 *
	 * @param int $register_status
	 *
	 * @return bool
	 */
	protected function isRegistered($register_status)
	{
		return in_array(
			$register_status,
			array(self::GUEST_REGISTERED, self::CUSTOMER_REGISTERED)
		);
	}


	public function activateGuest($email)
	{
		return Db::getInstance()->execute(
			'UPDATE `'._DB_PREFIX_.'emailsubscription`
						SET `active` = 1
						WHERE `email` = \''.pSQL($email).'\''
		);
	}

	/**
	 * Returns a guest email by token
	 *
	 * @param string $token
	 *
	 * @return string email
	 */
	protected function getGuestEmailByToken($token)
	{
		$sql = 'SELECT `email`
				FROM `'._DB_PREFIX_.'emailsubscription`
				WHERE MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) = \''.pSQL($token).'\'
				AND `active` = 0';

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Returns a customer email by token
	 *
	 * @param string $token
	 *
	 * @return string email
	 */
	protected function getUserEmailByToken($token)
	{
		$sql = 'SELECT `email`
				FROM `'._DB_PREFIX_.'customer`
				WHERE MD5(CONCAT( `email` , `date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) = \''.pSQL($token).'\'
				AND `newsletter` = 0';

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Subscribe a guest to the newsletter
	 *
	 * @param string $email
	 * @param bool   $active
	 *
	 * @return bool
	 */
	protected function registerGuest($email, $active = true)
	{
		$sql = 'INSERT INTO '._DB_PREFIX_.'emailsubscription (id_shop, id_shop_group, email, newsletter_date_add, ip_registration_newsletter, http_referer, active)
				VALUES
				('.$this->context->shop->id.',
				'.$this->context->shop->id_shop_group.',
				\''.pSQL($email).'\',
				NOW(),
				\''.pSQL(Tools::getRemoteAddr()).'\',
				(
					SELECT c.http_referer
					FROM '._DB_PREFIX_.'connections c
					WHERE c.id_guest = '.(int)$this->context->customer->id.'
					ORDER BY c.date_add DESC LIMIT 1
				),
				'.(int)$active.'
				)';

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Return a token associated to an user
	 *
	 * @param string $email
	 * @param string $register_status
	 */
	protected function getToken($email, $register_status)
	{
		if (in_array($register_status, array(self::GUEST_NOT_REGISTERED, self::GUEST_REGISTERED))){
			$sql = 'SELECT MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) as token
					FROM `'._DB_PREFIX_.'emailsubscription`
					WHERE `active` = 0
					AND `email` = \''.pSQL($email).'\'';
		}
		else if ($register_status == self::CUSTOMER_NOT_REGISTERED)
		{
			$sql = 'SELECT MD5(CONCAT( `email` , `date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\' )) as token
					FROM `'._DB_PREFIX_.'customer`
					WHERE `newsletter` = 0
					AND `email` = \''.pSQL($email).'\'';
		}

		return Db::getInstance()->getValue($sql);
	}
	
	/**
	 * Ends the registration process to the newsletter
	 *
	 * @param string $token
	 *
	 * @return string
	 */
	public function confirmEmail($token)
	{
		$activated = false;

		if ($email = $this->getGuestEmailByToken($token))
			$activated = $this->activateGuest($email);
		else if ($email = $this->getUserEmailByToken($token))
			$activated = $this->registerUser($email);

		if (!$activated)
			return $this->l('This email is already registered and/or invalid.');

		if ($discount = Configuration::get('NW_VOUCHER_CODE'))
			$this->sendVoucher($email, $discount);

		if (Configuration::get('NW_CONFIRMATION_EMAIL'))
			$this->sendConfirmationEmail($email);

		return $this->l('Thank you for subscribing to our newsletter.');
	}

	/**
	 * Send a verification email
	 *
	 * @param string $email
	 * @param string $token
	 *
	 * @return bool
	 */
	protected function sendVerificationEmail($email, $token)
	{
		$verif_url = Context::getContext()->link->getModuleLink(
			'labpopupnewsletter', 'verification', array(
				'token' => $token,
			)
		);

		return Mail::Send($this->context->language->id, 'fieldnewsletter_verif', Mail::l('Email verification', $this->context->language->id), array('{verif_url}' => $verif_url), $email, null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
	}

	/**
	 * Subscribe an email to the newsletter. It will create an entry in the newsletter table
	 * or update the customer table depending of the register status
	 *
	 * @param string $email
	 * @param int    $register_status
	 */
	protected function register($email, $register_status)
	{
		if ($register_status == self::GUEST_NOT_REGISTERED)
			return $this->registerGuest($email);

		if ($register_status == self::CUSTOMER_NOT_REGISTERED)
			return $this->registerUser($email);

		return false;
	}

	/**
	 * Subscribe a customer to the newsletter
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	protected function registerUser($email)
	{
		$sql = 'UPDATE '._DB_PREFIX_.'customer
				SET `newsletter` = 1, newsletter_date_add = NOW(), `ip_registration_newsletter` = \''.pSQL(Tools::getRemoteAddr()).'\'
				WHERE `email` = \''.pSQL($email).'\'
				AND id_shop = '.$this->context->shop->id;

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Send an email containing a voucher code
	 *
	 * @param $email
	 * @param $code
	 *
	 * @return bool|int
	 */
	protected function sendVoucher($email, $code)
	{
		return Mail::Send($this->context->language->id, 'fieldnewsletter_voucher', Mail::l('Newsletter voucher', $this->context->language->id), array('{discount}' => $code), $email, null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
	}

	/**
	 * Send a confirmation email
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	protected function sendConfirmationEmail($email)
	{
		return Mail::Send($this->context->language->id, 'fieldnewsletter_conf', Mail::l('Newsletter confirmation', $this->context->language->id), array(), pSQL($email), null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
	}


	/**
	 * Register in block newsletter
	 */
	public function newsletterRegistration($email)
	{
		if (empty($email) || !Validate::isEmail($email)) {
			echo $this->l('Invalid email address.');
			return;
		}

		$register_status = $this->isNewsletterRegistered($email);
		if ($register_status > 0) {
			echo $this->l('This email address is already registered.');
			return;
		}

		$email = pSQL($email);
		if (!$this->isRegistered($register_status))
		{
			if (Configuration::get('NW_VERIFICATION_EMAIL'))
			{
				// create an unactive entry in the newsletter database
				if ($register_status == self::GUEST_NOT_REGISTERED)
					$this->registerGuest($email, false);

				if (!$token = $this->getToken($email, $register_status)) {
					echo $this->l('An error occurred during the subscription process.');
					return;
				}

				$this->sendVerificationEmail($email, $token);

				echo $this->l('A verification email has been sent. Please check your inbox.');
				return;
			}
			else
			{
				if ($resp = $this->register($email, $register_status)) {
					if ($code = Configuration::get('NW_VOUCHER_CODE'))
						$resp = $this->sendVoucher($email, $code);

					if (Configuration::get('NW_CONFIRMATION_EMAIL'))
						$resp = $this->sendConfirmationEmail($email);

					if ($resp == true) 
						echo $this->l('You have successfully subscribed to this newsletter.');
					else 
						echo $resp;

					return;
				}
				else {
					echo $this->l('An error occurred during the subscription process.');
					return;
				}
			}
		}
	}

}