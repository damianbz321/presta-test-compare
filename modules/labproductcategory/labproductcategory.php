<?php
/**
* 2007-2015 PrestaShop
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
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
include_once(_PS_MODULE_DIR_.'labproductcategory/classes/LabGroupCategoryClass.php');
include_once(_PS_MODULE_DIR_.'labproductcategory/classes/LabCategoryClass.php');
include_once(_PS_MODULE_DIR_.'labproductcategory/sql/LabSampleDataProdCat.php');
class Labproductcategory extends Module implements WidgetInterface {
	protected $config_form = false;
	private $html = '';
	private $hook_into = array(
								'displayHome', 
								'displayPosition1',
								'displayPosition2',
								'displayPosition3',
								'displayPosition4',
								'displayPosition5',
								'displayPosition6',

								);
	private $type_display = array('accordion', 'accordion-column', 'accordion-banner', 'tab', 'column');
	
	public function __construct()
	{
		$this->name = 'labproductcategory';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'labersthemes';
		$this->need_instance = 1;
		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('LABER Product Category');
		$this->description = $this->l('Get product from category');

		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
	}

	public function install()
	{	
		$this->installTab();
		$res = true;
		$res &= parent::install() && $this->registerHook('header') 
								&& $this->registerHook('backOfficeHeader') 
								&& $this->registerHook('displayHome') 
								&& $this->registerHook('displayPosition1')
								&& $this->registerHook('displayPosition2')
								&& $this->registerHook('displayPosition3')
								&& $this->registerHook('displayPosition4')
								&& $this->registerHook('displayPosition5')
								&& $this->registerHook('displayPosition6')
								&& $this->registerHook('addproduct') 
								&& $this->registerHook('updateproduct') 
								&& $this->registerHook('deleteproduct') 
								&& $this->registerHook('categoryUpdate') 
								&& $this->registerHook('actionShopDataDuplication') 
								&& $this->registerHook('actionObjectLanguageAddAfter');
		include(dirname(__FILE__).'/sql/install.php');
		$sampleData = new LabSampleDataProdCat();
		$res &= $sampleData->initData();
		return $res;
	}

	public function uninstall()
	{
		$this->uninstallTab();	
		include(dirname(__FILE__).'/sql/uninstall.php');
			return parent::uninstall();
		return false;
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
        $tab->class_name = "Adminlabproductcategory";
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = "Laber Product Category";
        }
        $tab->id_parent = $parentTab_2->id;
        $tab->module = $this->name;
        $response &= $tab->add();

        return $response;
    }

    public function uninstallTab()
    {
        $id_tab = Tab::getIdFromClassName('Adminlabproductcategory');
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
		if (Tools::isSubmit('submitCatProd') || Tools::isSubmit('delete_id_group_cat') || Tools::isSubmit('changeStatus'))
		{
			$this->_postProcess();
			$this->html .= $this->renderList();
		}
		elseif (Tools::isSubmit('buildCat') && Tools::isSubmit('id_labgroupcategory'))
		{
			$this->_postProcess();
			$this->html .= $this->renderListCat();
		}
		elseif ((Tools::isSubmit('deleteicon') || Tools::isSubmit('deletebanner') || Tools::isSubmit('submitCatItem')) && Tools::isSubmit('id_labcategory') && Tools::isSubmit('id_labgroupcategory'))
		{

			$this->_postProcess();
			$this->html .= $this->renderAddCat();
		}
		elseif ((Tools::isSubmit('addcatitem') || Tools::isSubmit('id_labcategory')) && Tools::isSubmit('id_labgroupcategory'))
		{
		//		die('ádsad');
			$this->_postProcess();
			$this->html .= $this->renderAddCat();
		}
		elseif (Tools::isSubmit('delete_cat_item') || Tools::isSubmit('changeStatusCatItem'))
			$this->_postProcess();
		elseif (Tools::isSubmit('addCat') || (Tools::isSubmit('id_labgroupcategory') && $this->catExists(Tools::getValue('id_labgroupcategory'))))
			$this->html .= $this->renderAddForm();
		else
		{
			$this->_postProcess();
	
			$this->context->smarty->assign('module_dir', $this->_path);
			$this->html .= $this->renderList();
		}
		return $this->html;
	}

	public function renderList()
	{
		$info_category = $this->getCatInfo();
		foreach ($info_category as $key => $info_cat)
			$info_category[$key]['status'] = $this->displayStatus($info_cat['id_labgroupcategory'], $info_cat['active']);

		$this->context->smarty->assign(
			array(
				'link' => $this->context->link,
				'info_category' => $info_category
			)
		);
		return $this->display(__FILE__, 'views/templates/admin/list.tpl');
	}
	
	public function renderListCat()
	{
		//die('áđá');
		$id_lang = $this->context->language->id;
		$id_labgroupcategory = Tools::getValue('id_labgroupcategory');
		$info_cat_item = $this->getCatItemInfo($id_labgroupcategory,null);
		foreach ($info_cat_item as $key => $info_cat)
		{
			$info_cat_item[$key]['status'] = $this->displayStatusCatItem($id_labgroupcategory, $info_cat['id_labcategory'], $info_cat['active']);
			$category = new Category($info_cat_item[$key]['id_cat'], $id_lang);
			$info_cat_item[$key]['cat_name'] = $category->name;
		}

		$this->context->smarty->assign(
			array(
				'link' => $this->context->link,
				'info_cat_item' => $info_cat_item,
				'id_labgroupcategory' => $id_labgroupcategory
			)
		);
		return $this->display(__FILE__, 'views/templates/admin/list_catitem.tpl');
	}
	
	public function getCatInfo($active = null)
	{
		$this->context = Context::getContext();
		$id_shop = (int)$this->context->shop->id;
		$id_lang = (int)$this->context->language->id;
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT pc.*
			FROM '._DB_PREFIX_.'labgroupcategory_shop pc
			WHERE pc.id_shop = '.$id_shop.($active ? ' AND pc.`active` = 1' : ' ')
		);
	}
	
	public function getCatItemInfo($id_gr, $active = null)
	{
		$this->context = Context::getContext();
		$id_shop = (int)$this->context->shop->id;
		$id_grs = (int)$id_gr;
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT pc.*
			FROM '._DB_PREFIX_.'labcategory_shop pc
			WHERE pc.id_labgroupcategory = '.$id_grs.' AND pc.id_shop = '.$id_shop.($active ? ' AND pc.`active` = 1' : ' ')
		);
	}
	
	public function displayStatus($id_labgroupcategory, $active)
	{
		$title = ((int)$active == 0 ? $this->l('Disabled') : $this->l('Enabled'));
		$icon = ((int)$active == 0 ? 'icon-remove' : 'icon-check');
		$class = ((int)$active == 0 ? 'btn-danger' : 'btn-success');
		$html = '<a class="btn '.$class.'" href="'.AdminController::$currentIndex.
			'&configure='.$this->name.'
				&token='.Tools::getAdminTokenLite('AdminModules').'
				&changeStatus&id_labgroupcategory='.(int)$id_labgroupcategory.'" title="'.$title.'"><i class="'.$icon.'"></i> '.$title.'</a>';

		return $html;
	}
	
	public function displayStatusCatItem($id_labgroupcategory, $id_labcategory, $active)
	{
		$title = ((int)$active == 0 ? $this->l('Disabled') : $this->l('Enabled'));
		$icon = ((int)$active == 0 ? 'icon-remove' : 'icon-check');
		$class = ((int)$active == 0 ? 'btn-danger' : 'btn-success');
		$html = '<a class="btn '.$class.'" href="'.AdminController::$currentIndex.
			'&configure='.$this->name.'
				&token='.Tools::getAdminTokenLite('AdminModules').'
				&changeStatusCatItem&id_labgroupcategory='.(int)$id_labgroupcategory.'&id_labcategory='.(int)$id_labcategory.'" title="'.$title.'"><i class="'.$icon.'"></i> '.$title.'</a>';

		return $html;
	}

	protected function _postProcess()
	{
		$errors = array();
		if (Tools::isSubmit('submitCatProd'))
		{
			$this->clearCacheProdCat();
			if (Tools::getValue('id_labgroupcategory'))
			{
				$cat_group = new LabGroupCategoryClass((int)Tools::getValue('id_labgroupcategory'));
				if (!Validate::isLoadedObject($cat_group))
				{
					$this->html .= $this->displayError($this->l('Invalid id_labgroupcategory'));
					return false;
				}
			}
			else
			$cat_group = new LabGroupCategoryClass();
			$cat_group->active = (int)Tools::getValue('active_cat');
			$cat_group->id_hook = Tools::getValue('id_hook');
			$cat_group->type_display = Tools::getValue('type_display');
			$cat_group->num_show = Tools::getValue('num_show');
			$cat_group->use_slider = Tools::getValue('use_slider');
			$cat_group->show_sub = Tools::getValue('show_sub');
			$cat_group->group_cat = Tools::getValue('group_cat');
			
			/* Processes if no errors  */
			if (!$errors)
			{
				/* Adds */
				if (!Tools::getValue('id_labgroupcategory'))
				{
					
					if (!$cat_group->add())
						$errors[] = $this->displayError($this->l('The cat_group could not be added.'));
				}
				else
				{
					if (!$cat_group->update())
						$errors[] = $this->displayError($this->l('The cat_group could not be updated.'));
				}
			}
			return $errors;
		}
		elseif (Tools::isSubmit('submitCatItem'))
		{
			$this->clearCacheProdCat();
			if (Tools::getValue('id_labcategory'))
			{
				$cat_item = new LabCategoryClass(Tools::getValue('id_labcategory'));
				if (!Validate::isLoadedObject($cat_item))
				{
					$this->html .= $this->displayError($this->l('Invalid id_labgroupcategory'));
					return false;
				}	
			}
			else
				$cat_item = new LabCategoryClass();
			$cat_item->active = (int)Tools::getValue('active_cat_item');
			$cat_item->special_prod = (int)Tools::getValue('special_prod');
			$cat_item->show_img = (int)Tools::getValue('active_cat_img');
			$cat_item->position = 1;
			$cat_item->cat_color = Tools::getValue('cat_color');
			$cat_item->id_cat = Tools::getValue('id_cat');
			$cat_item->id_labgroupcategory = Tools::getValue('id_labgroupcategory');
			$manufacture_arr = Tools::getValue('manufacture');
			$cat_item->manufacture = Tools::jsonEncode($manufacture_arr);
			$icon_up = $cat_item->uploadImage1('cat_icon', 'labproductcategory/views/img/icons/');
			if (isset($icon_up) && $icon_up != '')
				$cat_item->cat_icon = $icon_up;
			
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				$temp_url = '{lab_cat_url}';
				$temp = Tools::getValue('cat_desc_'.$language['id_lang']);
				if (isset($temp))
				{
					$temp = str_replace(_PS_BASE_URL_.__PS_BASE_URI__, $temp_url, $temp);
					$cat_item->cat_desc[$language['id_lang']] = $temp;
				}
				$image_up = $cat_item->uploadImage('cat_banner_', $language['id_lang'], 'labproductcategory/views/img/banners/');
				
				if (isset($image_up) && $image_up != '')
					$cat_item->cat_banner[$language['id_lang']] = $image_up;
			}
			if (!$errors)
			{
				if (!Tools::getValue('id_labcategory'))
				{
					if (!$cat_item->add())
						$errors[] = $this->displayError($this->l('The cat_item could not be added.'));
				}
				else
				{
					if (!$cat_item->update())
						$errors[] = $this->displayError($this->l('The cat_item could not be updated.'));
				}
			}
			return $errors;
			
		}
		elseif (Tools::isSubmit('changeStatus') && Tools::getValue('id_labgroupcategory'))
		{
			$this->clearCacheProdCat();
			$group_cat = new LabGroupCategoryClass(Tools::getValue('id_labgroupcategory'));
			if ($group_cat->active == 0)
				$group_cat->active = 1;
			else
				$group_cat->active = 0;
			$res = $group_cat->update();
			$this->html .= ($res ? $this->displayConfirmation($this->l('Configuration updated')) : $this->displayError($this->l('The configuration could not be updated.')));
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		elseif (Tools::isSubmit('changeStatusCatItem') && Tools::getValue('id_labcategory'))
		{
			$this->clearCacheProdCat();
			$cat_item = new LabCategoryClass(Tools::getValue('id_labcategory'));
			if ($cat_item->active == 0)
				$cat_item->active = 1;
			else
				$cat_item->active = 0;
			$res = $cat_item->update();
			$this->html .= ($res ? $this->displayConfirmation($this->l('Configuration updated')) : $this->displayError($this->l('The configuration could not be updated.')));
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&id_labgroupcategory='.Tools::getValue('id_labgroupcategory').'&buildCat=1');
		}
		elseif (Tools::isSubmit('delete_cat_item'))
		{
			$this->clearCacheProdCat();
			$cat_item = new LabCategoryClass((int)Tools::getValue('delete_cat_item'));
			$res = $cat_item->delete();
			if (!$res)
				$this->html .= $this->displayError('Could not delete.');
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_labgroupcategory='.Tools::getValue('id_labgroupcategory').'&buildCat=1');
		}
		elseif (Tools::isSubmit('delete_id_group_cat'))
		{
			$this->clearCacheProdCat();
			$group_item = new LabGroupCategoryClass((int)Tools::getValue('delete_id_group_cat'));
			$res = $group_item->delete();
			if (!$res)
				$this->html .= $this->displayError('Could not delete.');
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		}
		elseif (Tools::isSubmit('deleteicon'))
		{
			$cat_item_n = new LabCategoryClass((int)Tools::getValue('id_labcategory'));
			$res = $cat_item_n->deleteIcon();
			if (!$res)
				$this->html .= $this->displayError('Could not delete.');
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_labgroupcategory='.Tools::getValue('id_labgroupcategory').'&id_labcategory='.Tools::getValue('id_labcategory'));
		}
		elseif (Tools::isSubmit('deletebanner'))
		{
			$cat_item_n = new LabCategoryClass((int)Tools::getValue('id_labcategory'));
			$res = $cat_item_n->deleteBanner();
			if (!$res)
				$this->html .= $this->displayError('Could not delete.');
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_labgroupcategory='.Tools::getValue('id_labgroupcategory').'&id_labcategory='.Tools::getValue('id_labcategory'));
		}
	}
	public function getHookList()
	{
		$hooks = array();
		
		foreach ($this->hook_into as $key => $hook)
		{
			$hooks[$key]['key'] = $hook;
			$hooks[$key]['name'] = $hook;
		}
		return $hooks;
	}
	
	public function getTypeList()
	{
		$hooks = array();
		
		foreach ($this->type_display as $key => $type)
		{
			$hooks[$key]['key'] = $type;
			$hooks[$key]['name'] = $type;
		}
		return $hooks;
	}
	
	public function renderAddForm()
	{
		$selected_categories = array();
		$hook_into = $this->getHookList();
		$type = $this->getTypeList();
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Category Group'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('group category'),
						'name' => 'group_cat',
					),
					array(
						'type' => 'select',
						'label' => $this->l('id_hook'),
						'name' => 'id_hook',
						'options' => array(
							'query' => $hook_into, 
							'id' => 'key',
							'name' => 'name'
						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Type display'),
						'desc' => $this->l(''),
						'name' => 'type_display',
						'options' => array(
							'query' => $type, 
							'id' => 'key',
							'name' => 'name'
						)
					),
					array(
						'type' => 'text',
						'label' => $this->l('number product'),
						'desc' => $this->l(''),
						'name' => 'num_show'
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Use Slider'),
						'name' => 'use_slider',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'useslider_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'useslider_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Show SubCategories'),
						'name' => 'show_sub',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'showsub_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'showsub_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Active'),
						'name' => 'active_cat',
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
				),
				'submit' => array(
					'title' => $this->l('Save'),
				),
				'buttons' => array(
					array(
					'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
					'title' => $this->l('Back to list'),
					'icon' => 'process-icon-back'
					),
					array(
					'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&id_labgroupcategory='.(int)Tools::getValue('id_labgroupcategory').'&buildCat=1',
					'title' => $this->l('Add Category'),
					'icon' => 'process-icon-new'
					)
				)
			),
		);
		if (Tools::isSubmit('id_labgroupcategory') && $this->catExists((int)Tools::getValue('id_labgroupcategory')))
		{
			$slide = new LabGroupCategoryClass((int)Tools::getValue('id_labgroupcategory'));
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_labgroupcategory');
		}

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitCatProd';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $this->getAddFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		$helper->override_folder = '/';

		return $helper->generateForm(array($fields_form));
	}
	
	public function getAddFieldsValues()
	{
		$fields = array();
		$languages = Language::getLanguages(false);
		if (Tools::isSubmit('id_labgroupcategory') && $this->catExists((int)Tools::getValue('id_labgroupcategory')))
		{
			$group_cat = new LabGroupCategoryClass((int)Tools::getValue('id_labgroupcategory'));
			
			$fields['group_cat'] = Tools::getValue('group_cat', $group_cat->group_cat);
			$fields['id_labgroupcategory'] = (int)Tools::getValue('id_labgroupcategory', $group_cat->id);
			$fields['active_cat'] = Tools::getValue('active_cat', $group_cat->active);
			$fields['id_hook'] = Tools::getValue('id_hook', $group_cat->id_hook);
			$fields['type_display'] = Tools::getValue('type_display', $group_cat->type_display);
			$fields['num_show'] = Tools::getValue('num_show', $group_cat->num_show);
			$fields['use_slider'] = Tools::getValue('use_slider', $group_cat->use_slider);
			$fields['show_sub'] = Tools::getValue('show_sub', $group_cat->show_sub);
		}
		else
		{
			$fields['group_cat'] = Tools::getValue('group_cat', 'Group category 1');
			$fields['active_cat'] = Tools::getValue('active_cat', 1);
			$fields['id_hook'] = Tools::getValue('id_hook', 1);
			$fields['type_display'] = Tools::getValue('type_display', 1);
			$fields['num_show'] = Tools::getValue('num_show', 8);
			$fields['use_slider'] = Tools::getValue('use_slider', 1);
			$fields['show_sub'] = Tools::getValue('show_sub', 1);
		}
		return $fields;
	}
	
	public function catExists($id)
	{
		$req = 'SELECT wt.`id_labgroupcategory` as id_labgroupcategory
				FROM `'._DB_PREFIX_.'labgroupcategory` wt
				WHERE wt.`id_labgroupcategory` = '.(int)$id;
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);

		return ($row);
	}
	
	public function renderAddCat()
	{
		$id_labgroupcategory = Tools::getValue('id_labgroupcategory');
		$manus = Manufacturer::getManufacturers();
		$root_category = $this->context->shop->getCategory();
		
		if (Tools::getValue('id_labcategory'))
		{
			
			$cat_info = new LabCategoryClass(Tools::getValue('id_labcategory'));
			$selected_categories = array($cat_info->id_cat);
		}
		else
			$selected_categories = array((int)$this->context->shop->getCategory());
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Category info'),
					'icon' => 'icon-plus'
				),
				'input' => array(
					array(
						'type' => 'select_link',
						'label' => $this->l('Cat ID'),
						'name' => 'id_cat'
					),
					array(
						'type' => 'text',
						'label' => $this->l('Description'),
						'desc' => $this->l(''),
						'name' => 'cat_desc',
						'lang' => true
					),
					array(
						'type' => 'file_lang',
						'label' => $this->l('Banner'),
						'desc' => $this->l(''),
						'name' => 'cat_banner',
						'lang' => true
					),
					array(
						'type' => 'file',
						'label' => $this->l('Icon'),
						'desc' => $this->l(''),
						'name' => 'cat_icon'
					),
					array(
						'type' => 'select',
						'label' => $this->l('Manufacture'),
						'multiple' =>true,
						'name' => 'manufacture[]',
						'id' => 'manufacture',
						'options' => array(
							'query' => $manus,
							'id' => 'id_manufacturer',
							'name' => 'name'
						)
					),
					 array(
						'type' => 'color',
						'label' => $this->l('Color'),
						'desc' => $this->l(''),
						'name' => 'cat_color'
					),
					/*array(
						'type' => 'text',
						'label' => $this->l('Special Product ID'),
						'name' => 'special_prod'
					), */
					array(
						'type' => 'switch',
						'label' => $this->l('Show Category Image'),
						'name' => 'active_cat_img',
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
						'label' => $this->l('Active'),
						'name' => 'active_cat_item',
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
				),
				'submit' => array(
					'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
					'title' => $this->l('Save'),
				),
				'buttons' => array(
					array(
					'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&id_labgroupcategory='.$id_labgroupcategory.'&buildCat=1',
					'title' => $this->l('Back to list'),
					'icon' => 'process-icon-back'
					)
				)
			),
		);

		if (Tools::isSubmit('id_labcategory'))
		{
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_labcategory');
			$cat_info = new LabCategoryClass((int)Tools::getValue('id_labcategory'));
			$fields_form['form']['cat_banner'] = $cat_info->cat_banner;
			$fields_form['form']['cat_icon'] = $cat_info->cat_icon;
			$id_labcategory = (int)Tools::getValue('id_labcategory');
			$cat_value = $cat_info->id_cat;
		}
		else
		{
			$id_labcategory = 1;
			$cat_value = 3;
		}
		$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_labgroupcategory');
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitCatItem';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_labgroupcategory='.$id_labgroupcategory.'&buildCat=1';
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $this->getAddFieldsValuesCat(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'image_baseurl' => $this->_path.'views/img/',
			'id_labcategory' => $id_labcategory,
			'id_labgroupcategory' => $id_labgroupcategory,
			'link' => $this->context->link,
			'cat_options' => $this->getAllCatLink(),
			'cat_value' => $cat_value
		);
		$helper->override_folder = '/';
		return $helper->generateForm(array($fields_form));
	}
	public function getAddFieldsValuesCat()
	{
		$fields = array();
		$languages = Language::getLanguages(false);
		if (Tools::isSubmit('id_labcategory'))
		{
			$cat_info = new LabCategoryClass((int)Tools::getValue('id_labcategory'));
			
			$fields['id_cat'] = Tools::getValue('id_cat', $cat_info->id_cat);
			$fields['id_labcategory'] = (int)Tools::getValue('id_labcategory', $cat_info->id);
			$fields['id_labgroupcategory'] = (int)Tools::getValue('id_labgroupcategory', $cat_info->id_labgroupcategory);
			$fields['cat_color'] = Tools::getValue('cat_color', $cat_info->cat_color);
			$fields['active_cat_item'] = Tools::getValue('active_cat_item', $cat_info->active);
			$fields['active_cat_img'] = Tools::getValue('active_cat_img', $cat_info->show_img);
			$fields['special_prod'] = Tools::getValue('special_prod', $cat_info->special_prod);
			
			$manufacture_arr = Tools::jsonDecode($cat_info->manufacture);
			$fields['manufacture[]'] = Tools::getValue('manufacture', $manufacture_arr);
			$fields['cat_icon'] = Tools::getValue('cat_icon', $cat_info->cat_icon);
			foreach ($languages as $lang)
			{
				$fields['cat_desc'][$lang['id_lang']] = Tools::getValue('cat_desc_'.(int)$lang['id_lang'], $cat_info->cat_desc[$lang['id_lang']]);
				$fields['cat_banner'][$lang['id_lang']] = Tools::getValue('cat_banner_'.(int)$lang['id_lang'], $cat_info->cat_banner[$lang['id_lang']]);
			}
		}
		else
		{
			$fields['id_cat'] = Tools::getValue('id_cat', 2);
			$fields['id_labgroupcategory'] = (int)Tools::getValue('id_labgroupcategory', 1);
			$fields['cat_color'] = Tools::getValue('cat_color', '');
			$fields['active_cat_item'] = Tools::getValue('active_cat_item', 1);
			$fields['active_cat_img'] = Tools::getValue('active_cat_img', 1);
			$fields['special_prod'] = Tools::getValue('special_prod', 0);
			$fields['manufacture[]'] = Tools::getValue('manufacture', '');
			$fields['cat_icon'] = Tools::getValue('cat_icon', '');
			foreach ($languages as $lang)
			{
				$fields['cat_desc'][$lang['id_lang']] = Tools::getValue('cat_desc_'.(int)$lang['id_lang'], '');
				$fields['cat_banner'][$lang['id_lang']] = Tools::getValue('cat_banner_'.(int)$lang['id_lang'], '');
			}
		}
		return $fields;
	}
	
	public function getAllCatLink($id_lang = null, $link = false)
	{
		if (is_null($id_lang)) $id_lang = (int)$this->context->language->id;
		$html = '<optgroup label="'.$this->l('Category').'">';
		$html .= $this->getCategoryOption(1, $id_lang, false, true, $link);
		$html .= '</optgroup>';
		return $html;
	}
	
	public function getCategoryOption($id_category = 1, $id_lang = false, $id_shop = false, $recursive = true, $link = false)
	{
		$html = '';
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
		$id_shop = $id_shop ? (int)$id_shop : (int)Context::getContext()->shop->id;
		$category = new Category((int)$id_category, (int)$id_lang, (int)$id_shop);
		if (is_null($category->id)) return;
		if ($recursive)
		{
			$children = Category::getChildren((int)$id_category, (int)$id_lang, true, (int)$id_shop);
			$spacer = str_repeat('&nbsp;', 3 * (int)$category->level_depth);
		}
		$shop = (object)Shop::getShop((int)$category->getShopID());
		if (!in_array($category->id, array(Configuration::get('PS_HOME_CATEGORY'), Configuration::get('PS_ROOT_CATEGORY'))))
		{
		if ($link)
			$html .= '<option value="'.$this->context->link->getCategoryLink($category->id).'">'.(isset($spacer) ? $spacer : '').str_repeat('&nbsp;', 3 * (int)$category->level_depth).$category->name.'</option>';
		else
			$html .= '<option value="'.(int)$category->id.'">'.str_repeat('&nbsp;', 3 * (int)$category->level_depth).$category->name.'</option>';
		}
		elseif ($category->id != Configuration::get('PS_ROOT_CATEGORY'))
			$html .= '<optgroup label="'.str_repeat('&nbsp;', 3 * (int)$category->level_depth).$category->name.'">';
		if (isset($children) && count($children))
			foreach ($children as $child)
			{
				$html .= $this->getCategoryOption((int)$child['id_category'], (int)$id_lang, (int)$child['id_shop'],
				$recursive, $link);
			}
		return $html;
	}
	
	public function hookHeader()
	{
		 $this->context->controller->addJS($this->_path.'/views/js/front.js');
		if ($this->context->controller->php_self == 'index')
			$this->context->controller->addCSS($this->_path.'/views/css/front.css');
	}


	public function prevHook($hook_name)
	{


		$result = array();
		$id_lang = $this->context->language->id;
		$group_cat_obj = new LabGroupCategoryClass();

		$group_cat_hook = $group_cat_obj->getGroupCatByHook($hook_name);
		
		foreach ($group_cat_hook as $group_cat) {
			$cat_prod = new LabCategoryClass();
			$cat_prod_group = $cat_prod->getCatByGroupId($group_cat['id_labgroupcategory']);
		//	echo "<pre>".print_r($cat_prod_group,1);die;
			
			foreach ($cat_prod_group as $cat_prod) {
				$id_cat = $cat_prod['id_cat'];
				$nProducts = $group_cat['num_show'];
				//
				$category = new Category($id_cat, $id_lang);
				
				$cat_prod['cat_name'] = $category->name;
				$cat_prod['id_image'] = $category->id_image;
				$cat_prod['link_rewrite'] = $category->link_rewrite;
				$cat_prod['sub_cat'] = $category->getSubCategories($id_lang);
				$cat_prod['link_rewrite'] = $category->link_rewrite;
				$cat_prod['sub_cat'] = $category->getSubCategories($id_lang);
				$cat_prod['product_list']		=$this->getProductbyCategory($id_cat, $nProducts);

				$manu_ids = Tools::jsonDecode($cat_prod['manufacture']);
				$manu_arr = array();
				if (is_array($manu_ids) && count($manu_ids) > 0)
				{
					foreach ($manu_ids as $manu_item)
						$manu_arr[] = new Manufacturer($manu_item, $id_lang);
				}
				$cat_prod['manufacture'] = $manu_arr;

				$id_prod = (int)$cat_prod['special_prod'];
				if (isset($id_prod) && $id_prod > 0)
				{
					$product = new Product($id_prod);
					$product->id_image = $product->getCoverWs();
					$cat_prod['special_prod_obj'] = $product;
				}

				$cat_prod_result[] = $cat_prod;
				
			}
			$group_cat_result['cat_info'] = $cat_prod_result;
		//	echo "<pre>".print_r($result,1);die;
			$result[] = $group_cat_result;
			$result[0]['id_labgroupcategory'] = $group_cat['id_labgroupcategory'];
			$result[0]['num_show'] = $group_cat['num_show'];
			$result[0]['type_display'] = $group_cat['type_display'];
			$result[0]['use_slider'] = $group_cat['use_slider'];
			$result[0]['show_sub'] = $group_cat['show_sub'];
			$result[0]['active'] = $group_cat['active'];
		//	echo "<pre>".print_r($result,1);die;

		}


		return $result;
	}

	public function getProductbyCategory ($id_cat, $nProducts){
		$category = new Category($id_cat);
		$searchProvider = new CategoryProductSearchProvider(
			$this->context->getTranslator(),
			$category
		);

		$context = new ProductSearchContext($this->context);

		$query = new ProductSearchQuery();
		
		$query
			->setResultsPerPage($nProducts)
			->setPage(1)
		;
		
		$query->setSortOrder(new SortOrder('product', 'position', 'DESC'));
		
		$result = $searchProvider->runQuery(
			$context,
			$query
		);

		$assembler = new ProductAssembler($this->context);

		$presenterFactory = new ProductPresenterFactory($this->context);
		$presentationSettings = $presenterFactory->getPresentationSettings();
		$presenter = new ProductListingPresenter(
			new ImageRetriever(
				$this->context->link
			),
			$this->context->link,
			new PriceFormatter(),
			new ProductColorsRetriever(),
			$this->context->getTranslator()
		);

		$products_for_template = [];

		foreach ($result->getProducts() as $rawProduct) {
			$products_for_template[] = $presenter->present(
				$presentationSettings,
				$assembler->assembleProduct($rawProduct),
				$this->context->language
			);
		}

		return $products_for_template;
	}


	public function hookdisplayHome()
	{
			$group_cat_result = $this->prevHook('displayHome');
			if (!isset($group_cat_result) || count($group_cat_result) <= 0)
				return false;
			$this->context->smarty->assign(array(
				'group_cat_result' => $group_cat_result,
				'banner_path' => $this->_path.'views/img/banners/',
				'icon_path' => $this->_path.'views/img/icons/',
			));
		return $this->display(__FILE__, 'labproductcategory_home.tpl');
	}
	

	public function hookdisplayPosition1()
	{
			$group_cat_result = $this->prevHook('displayPosition1');
			if (!isset($group_cat_result) || count($group_cat_result) <= 0)
				return false;
			$this->context->smarty->assign(array(
				'group_cat_result' => $group_cat_result,
				'banner_path' => $this->_path.'views/img/banners/',
				'icon_path' => $this->_path.'views/img/icons/',
			));
		return $this->display(__FILE__, 'labproductcategory_home.tpl');
	}

	public function hookdisplayPosition2()
	{
			$group_cat_result = $this->prevHook('displayPosition2');
			if (!isset($group_cat_result) || count($group_cat_result) <= 0)
				return false;
			$this->context->smarty->assign(array(
				'group_cat_result' => $group_cat_result,
				'banner_path' => $this->_path.'views/img/banners/',
				'icon_path' => $this->_path.'views/img/icons/',
			));
		return $this->display(__FILE__, 'labproductcategory_home.tpl');
	}
	public function hookdisplayPosition3()
	{
			$group_cat_result = $this->prevHook('displayPosition3');
			if (!isset($group_cat_result) || count($group_cat_result) <= 0)
				return false;
			$this->context->smarty->assign(array(
				'group_cat_result' => $group_cat_result,
				'banner_path' => $this->_path.'views/img/banners/',
				'icon_path' => $this->_path.'views/img/icons/',
			));
		return $this->display(__FILE__, 'labproductcategory_home.tpl');
	}
	public function hookdisplayPosition4()
	{
			$group_cat_result = $this->prevHook('displayPosition4');
			if (!isset($group_cat_result) || count($group_cat_result) <= 0)
				return false;
			$this->context->smarty->assign(array(
				'group_cat_result' => $group_cat_result,
				'banner_path' => $this->_path.'views/img/banners/',
				'icon_path' => $this->_path.'views/img/icons/',
			));
		return $this->display(__FILE__, 'labproductcategory_home.tpl');
	}
	public function hookdisplayPosition5()
	{
			$group_cat_result = $this->prevHook('displayPosition5');
			if (!isset($group_cat_result) || count($group_cat_result) <= 0)
				return false;
			$this->context->smarty->assign(array(
				'group_cat_result' => $group_cat_result,
				'banner_path' => $this->_path.'views/img/banners/',
				'icon_path' => $this->_path.'views/img/icons/',
			));
		return $this->display(__FILE__, 'labproductcategory_home.tpl');
	}
	public function hookdisplayPosition6()
	{
			$group_cat_result = $this->prevHook('displayPosition6');
			if (!isset($group_cat_result) || count($group_cat_result) <= 0)
				return false;
			$this->context->smarty->assign(array(
				'group_cat_result' => $group_cat_result,
				'banner_path' => $this->_path.'views/img/banners/',
				'icon_path' => $this->_path.'views/img/icons/',
			));
		return $this->display(__FILE__, 'labproductcategory_home.tpl');
	}
	public function hookAddProduct()
	{
		$this->clearCacheProdCat();
	}
	public function hookUpdateProduct()
	{
		$this->clearCacheProdCat();
	}
	public function hookDeleteProduct()
	{
		$this->clearCacheProdCat();
	}
	public function hookCategoryUpdate()
	{
		$this->clearCacheProdCat();
	}
	public function clearCacheProdCat()
	{
		$this->_clearCache('labproductcategory_home.tpl');
	}
	
	public function hookActionShopDataDuplication($params)
	{
		Db::getInstance()->execute('
			INSERT IGNORE INTO '._DB_PREFIX_.'labgroupcategory_shop (`id_labgroupcategory`, `group_cat`, `id_shop`, `id_hook`, `type_display`, `num_show`, `use_slider`, `show_sub`, `active`)
			SELECT `id_labgroupcategory`, `group_cat`, '.(int)$params['new_id_shop'].', `id_hook`, `type_display`, `num_show`, `use_slider`, `show_sub`, `active`
			FROM '._DB_PREFIX_.'labgroupcategory_shop
			WHERE id_shop = '.(int)$params['old_id_shop']
		);
		
		Db::getInstance()->execute('
			INSERT IGNORE INTO '._DB_PREFIX_.'labcategory_shop (`id_labcategory`, `id_labgroupcategory`, `id_shop`, `id_cat`, `cat_icon`, `cat_color`, `manufacture`, `position`, `show_img`, `special_prod`, `active`)
			SELECT `id_labcategory`, `id_labgroupcategory`, '.(int)$params['new_id_shop'].', `id_cat`, `cat_icon`, `cat_color`, `manufacture`, `position`, `show_img`, `special_prod`, `active`
			FROM '._DB_PREFIX_.'labcategory_shop
			WHERE id_shop = '.(int)$params['old_id_shop']
		);
		
		Db::getInstance()->execute('
			INSERT IGNORE INTO '._DB_PREFIX_.'labcategory_lang (`id_labcategory`, `id_shop`, `id_lang`, `cat_desc`, `cat_banner`)
			SELECT id_labcategory, '.(int)$params['new_id_shop'].', `id_lang`, `cat_desc`, `cat_banner`
			FROM '._DB_PREFIX_.'labcategory_lang
			WHERE id_shop = '.(int)$params['old_id_shop']
		);
	}
	public function hookActionObjectLanguageAddAfter($params)
	{
		Db::getInstance()->execute('
			INSERT IGNORE INTO '._DB_PREFIX_.'labcategory_lang (`id_labcategory`, `id_shop`, `id_lang`, `cat_desc`, `cat_banner`)
			SELECT `id_labcategory`, `id_shop`, '.(int)$params['object']->id.', `cat_desc`, `cat_banner`
			FROM '._DB_PREFIX_.'labcategory_lang
			WHERE id_lang = '.(int)Configuration::get('PS_LANG_DEFAULT')
		);
	}
	public function renderWidget($hookName, array $configuration)
	{
		// TODO: Implement renderWidget() method.
	}

	public function getWidgetVariables($hookName, array $configuration)
	{
		// TODO: Implement getWidgetVariables() method.
	}
}