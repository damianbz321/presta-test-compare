<?php
/**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class LabCategoryFeature extends Module implements WidgetInterface
{

	private $html;
	private $config;
	private $settings_default;
	private $selected_category;
	protected static $cache_filter_categories;
	private $lab_cat_feature_config;
	public function __construct()
	{
		$this->name = 'labcategoryfeature';
		$this->tab = 'front_office_features';
		$this->version = '1.1.0';
		$this->author = 'laberthemes';
		$this->need_instance = 0;
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->getTranslator()->trans('LABER Category Feature', array(), 'Modules.LABCategoryFeature.Admin');
		$this->description = $this->getTranslator()->trans('Show category feature.', array(), 'Modules.LABCategoryFeature.Admin');
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
		
		$this->lab_cat_feature_config = 'LAB_CONFIG_CATEGORY_FEATURE';
		$this->settings_default = array(
			'used_slider' => 1,
			'showimg' => 1,
			'showsub' => 0,
			'numbersub' => 4,
			'category' => array(3,4,5,6)
		);
		$this->getInitSettings();
	}
	public function getInitSettings()
	{
		$this->config = (array)Tools::jsonDecode(Configuration::get($this->lab_cat_feature_config));
		$this->config = (object)array_merge((array)$this->settings_default, $this->config);
		$this->selected_category = $this->config->category;
	}
	public function install()
	{	
		$this->installTab();
		$this->_clearCache('labcategoryfeature.tpl');
		if (!parent::install() || !$this->registerHook('displayHeader') || !$this->registerHook('categoryUpdate') || !$this->registerHook('displayPosition1'))
			return false;
		if (!Configuration::hasKey($this->lab_cat_feature_config))
			Configuration::updateValue($this->lab_cat_feature_config, '');
		return true;
	}
	public function uninstall()
	{
		$this->uninstallTab();		
		$this->_clearCache('labcategoryfeature.tpl');
		if (parent::uninstall() == false || !Configuration::deleteByName($this->lab_cat_feature_config))
			return false;
		return true;
	}
	public function installTab()
    {
        $response = true;

        // First check for parent tab
        $parentTabID = Tab::getIdFromClassName('AdminMenuFirst');

        if ($parentTabID) {
            $parentTab = new Tab($parentTabID);
        }
        else {
            $parentTab = new Tab();
            $parentTab->active = 1;
            $parentTab->name = array();
            $parentTab->class_name = "AdminMenuFirst";
            foreach (Language::getLanguages() as $lang) {
                $parentTab->name[$lang['id_lang']] = "FRAMEWORK";
            }
            $parentTab->id_parent = 0;
            $parentTab->module ='';
            $response &= $parentTab->add();
        }

			// Check for parent tab2
			$parentTab_2ID = Tab::getIdFromClassName('AdminMenuSecond');
			if ($parentTab_2ID) {
				$parentTab_2 = new Tab($parentTab_2ID);
			}
			else {
				$parentTab_2 = new Tab();
				$parentTab_2->active = 1;
				$parentTab_2->name = array();
				$parentTab_2->class_name = "AdminMenuSecond";
				foreach (Language::getLanguages() as $lang) {
					$parentTab_2->name[$lang['id_lang']] = "Module Configure";
				}
				$parentTab_2->id_parent = $parentTab->id;
				$parentTab_2->module = '';
				$response &= $parentTab_2->add();
			}
		// Created tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = "Adminlabcategoryfeature";
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = "Laber Category Feature";
        }
        $tab->id_parent = $parentTab_2->id;
        $tab->module = $this->name;
        $response &= $tab->add();

        return $response;
    }

    public function uninstallTab()
    {
        $id_tab = Tab::getIdFromClassName('Adminlabcategoryfeature');
		$parentTabID = Tab::getIdFromClassName('AdminMenuFirst');
        $tab = new Tab($id_tab);
        $tab->delete();

		// Get the number of tabs inside our parent tab
        // If there is no tabs, remove the parent
		$parentTab_2ID = Tab::getIdFromClassName('AdminMenuSecond');
		$tabCount_2 = Tab::getNbTabs($parentTab_2ID);
        if ($tabCount_2 == 0) {
            $parentTab_2 = new Tab($parentTab_2ID);
            $parentTab_2->delete();
        }
        // Get the number of tabs inside our parent tab
        // If there is no tabs, remove the parent
        $tabCount = Tab::getNbTabs($parentTabID);
        if ($tabCount == 0) {
            $parentTab = new Tab($parentTabID);
            $parentTab->delete();
        }

        return true;
    }
	public function getContent()
	{
		$this->postProcess();
		$this->initForm();
		return $this->html;
	}
	public function checkValidate()
	{
		$configs = Tools::getValue('config');
		$errors = array();
		foreach ($configs as $key_option => $value_option)
		{
			$pos = strpos($key_option, 'number_');
			if ($pos !== false)
				if (isset($value_option) && (!$value_option || $value_option <= 0 || !Validate::isInt($value_option)))
					$errors[] = $this->l('An invalid '.$key_option.' has been specified.');
		}
		return $errors;
	}
	public function postProcess()
	{
		if (Tools::isSubmit('saveConfig'))
		{
			$errors = $this->checkValidate();
			if (isset($errors) && count($errors))
				$this->html .= $this->displayError(implode('<br />', $errors));
			else
			{
				$config = Tools::jsonEncode(Tools::getValue('config'));
				if ($config)
				{
					Configuration::updateValue($this->lab_cat_feature_config, $config);
					$this->_clearCache('labcategoryfeature.tpl');
					Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&successConfirmation');
				}
			}
		}
		else if (Tools::isSubmit('successConfirmation'))
			$this->html .= $this->displayConfirmation($this->l('Your settings have been updated.'));
	}
	public function initForm()
	{
		$fields_form = array();
		include(dirname(__FILE__).'/class/settings.php');
		$this->fields_form[0]['form'] = $fields_form;
		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		foreach (Language::getLanguages(false) as $lang)
			$helper->languages[] = array(
				'id_lang' => $lang['id_lang'],
				'iso_code' => $lang['iso_code'],
				'name' => $lang['name'],
				'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
			);
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name;
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		$helper->toolbar_scroll = true;
		$helper->title = $this->displayName;
		$helper->submit_action = 'saveConfig';
		if (Tools::getIsset('config'))
			$this->config = (object)array_merge(Tools::getValue('config'), (array)$this->config);
		foreach ($this->fields_form[0]['form']['input'] as $field)
		{
			$option = str_replace('config[', '', $field['name']);
			$option = str_replace(']', '', $option);
			$helper->fields_value[''.$field['name'].''] = (isset($this->config->$option) ? $this->config->$option : '');
		}
		$this->html .= $helper->generateForm($this->fields_form);
	}
	public function callGetCategoryList($id_lang)
	{
		if (!isset($this->config->category) || empty($this->config->category))
			return;
		$categories = array();
		foreach ($this->config->category as $key => $id_cat)
		{
			$category = new Category($id_cat, $id_lang);
			$categories[$key]['category'] = $category;
			$categories[$key]['sub_cat'] = $category->getSubCategories($id_lang);
			if (file_exists(_PS_CAT_IMG_DIR_.$category->id_category.'_thumb.jpg'))
				$categories[$key]['cat_thumb'] = 1;
			else
				$categories[$key]['cat_thumb'] = 0;
		}
		return $categories;
	}
	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS($this->_path.'/views/css/labcategoryfeature.css');
		
	}
	
public function getWidgetVariables($hookName = null, array $configuration = [])
{
		$id_lang = (int)$this->context->language->id;
			$iso_code = $this->context->language->iso_code;
			$categories = $this->callGetCategoryList($id_lang);
			
		
			return [
           'lab_categories' => $categories,
				'labconfig' => $this->config,
				'iso_code' => $iso_code,
				'path_ssl' => $this->context->link->getBaseLink(),
			];
		
}

public function renderWidget($hookName = null, array $configuration = [])
{

		$this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        return $this->fetch('module:'.$this->name.'/views/templates/hook/'.$this->name.'.tpl', $this->getCacheId());
        
}
}