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

class LABProductNextPrev extends Module
{
	public $html = '';
	public $success = '';
public function __construct()
{
		$this->name = 'labproductnextprev';
		$this->tab = 'front_office_features';
		$this->version = '1.7.x';
		$this->author = 'laberthemes';
		$this->need_instance = 0;
		$this->bootstrap = true;
		parent::__construct();
		
		$this->displayName = $this->getTranslator()->trans('LABER Next Prev Product Page', array(), 'Modules.LABProductNextPrev.Admin');
		$this->description = $this->getTranslator()->trans('Display the next or prev of current product on product page.', array(), 'Modules.LABProductNextPrev.Admin');
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
}

public function install()
{
		Configuration::updateValue('LAB_BKG_COLOR', '#0166c3');
		Configuration::updateValue('LAB_BORDER_COLOR', '#0166c3');
		Configuration::updateValue('LAB_BUTTON_COLOR', '#ffffff');
		
		if (!parent::install()
		|| !$this->registerHook('Header')
		|| !$this->registerHook('displayProductNextPrev'))
			return false;
		return true;
}
public function uninstall()
{
		return Configuration::deleteByName('LAB_BKG_COLOR') && Configuration::deleteByName('LAB_BORDER_COLOR') && Configuration::deleteByName('LAB_BUTTON_COLOR') && parent::uninstall();
}

/* public function getContent()
{
		 $this->html .= $this->_postProcess();
			$this->html .= $this->displayFormOption();
		return $this->html;
} */

private function _postProcess()
	{
		$errors = array();
		if (Tools::isSubmit('submitSaveOption'))
		{
			Configuration::updateValue('LAB_BKG_COLOR', Tools::getValue('lab_bkg_color'));
			Configuration::updateValue('LAB_BORDER_COLOR', Tools::getValue('lab_border_color'));
			Configuration::updateValue('LAB_BUTTON_COLOR', Tools::getValue('lab_button_color'));
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&saveOptionConfirmation');
		}
		elseif (Tools::isSubmit('saveOptionConfirmation'))
			$this->html = $this->displayConfirmation($this->l('The option has been saved successfully'));
}

	
public function displayFormOption()
{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Option'),
					'icon' => 'icon-cogs'
				),
				'input' => array
				(
					array(
						'type' => 'color',
						'label' => $this->l('Background button next/prev'),
						'name' => 'lab_bkg_color',
						'class' => 'color mColorPickerInput',
					),
					array(
						'type' => 'color',
						'label' => $this->l('Border color button next/prev'),
						'name' => 'lab_border_color',
						'class' => 'color mColorPickerInput',
					),
					array(
						'type' => 'color',
						'label' => $this->l('Color button icon'),
						'name' => 'lab_button_color',
						'class' => 'color mColorPickerInput',
					),					
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$this->fields_form = array();
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitSaveOption';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'
		&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValuesOption()
		);
	//	$this->html .= '<legend><img src="'.$this->_path.'views/img/setting.png" alt="" title="" /> '.$this->l('Options').'</legend>';
		$this->html .= $helper->generateForm(array($fields_form));
		
}

public function getConfigFieldsValuesOption()
{
		return array(
			'lab_bkg_color' => Tools::getValue('lab_bkg_color', Configuration::get('LAB_BKG_COLOR')),
			'lab_border_color' => Tools::getValue('lab_bkg_color', Configuration::get('LAB_BORDER_COLOR')),
			'lab_button_color' => Tools::getValue('lab_button_color', Configuration::get('LAB_BUTTON_COLOR'))
		);
}

public function getMaxID()
{
	$id_shop = $this->context->shop->id;
	$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT MAX(ps.`id_product`) as max_id 
	FROM `'._DB_PREFIX_.'product` p 
	LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.`id_product` = ps.`id_product`) 
	WHERE ps.`id_shop` = '.(int)$id_shop.'');
	foreach ($results as $row)
	{
		return (int)$row['max_id'];
	}
	
}
public function getMinID()
{
	$id_shop = $this->context->shop->id;
	
	$sql = 'SELECT MIN(ps.`id_product`) as min_id 
	FROM `'._DB_PREFIX_.'product` p 
	LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.`id_product` = ps.`id_product`) 
	WHERE ps.`id_shop` = '.(int)$id_shop.'';
	
	$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);
	foreach ($results as $row)
	{
		return (int)$row['min_id'];
	}
}

public function lookForProduct($id)
{
	$id_shop = $this->context->shop->id;
	$sql = 'SELECT ps.`id_product`
	FROM `'._DB_PREFIX_.'product` p 
	LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.`id_product` = ps.`id_product`) 
	WHERE ps.`id_product` = '.(int)$id.' AND ps.`id_shop` = '.(int)$id_shop.'';
	
	$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

	if (count($results) > 0)
	return true;
	else
		return false;
}

public function hookdisplayHeader()
	{
		$this->context->controller->registerStylesheet('modules-labproductnextprev', 'modules/'.$this->name.'/views/css/labproductnextprev.css', ['media' => 'all']);
	}
	

public function hookDisplayProductNextPrev($params)
{
	if ($this->context->controller->php_self == 'product')
	{
		$id_product = (int)Tools::getValue('id_product');
	$id_max = $this->getMaxID();
	$id_min = $this->getMinID();
	
	$id_next = 0;
	$id_prev = 0;
	
	$tam1 = 0;
	for ($i = $id_product; $i < $id_max; $i++)
	{
		$tam1 = $i+1;
		if($this->lookForProduct($tam1))
		{
			$id_next = $tam1;
			break;
		}
	}
	$tam2 = 0;
	for ($j = $id_product; $j > $id_min; $j--)
	{
		$tam2 = $j-1;
		if($this->lookForProduct($tam2))
		{
			$id_prev = $tam2;
			break;
		}
	}
	
	if($id_next !=0)
	{
		$product_next = new Product($id_next, true, (int)Context::getContext()->language->id);
		$id_images_next = $product_next->getCoverWs();
	}
	else
	{
		$product_next = null;
		$id_images_next = null;
	}
	

	if($id_prev !=0)
	{
		$product_prev = new Product($id_prev, true, (int)Context::getContext()->language->id);
		$id_images_prev = $product_prev->getCoverWs();
	}
	else
	{
		$product_prev = null;
		$id_images_prev = null;
	}
	
	$this->context->smarty->assign(array(
				'product_next' => $product_next,
				'id_images_next' => $id_images_next,
				'product_prev' => $product_prev,
				'id_images_prev' => $id_images_prev,
				'lab_bkg_color' => Configuration::get('LAB_BKG_COLOR'),
				'lab_border_color' => Configuration::get('LAB_BORDER_COLOR'),
				'lab_button_color' => Configuration::get('LAB_BUTON_COLOR')
		));
	return $this->display(__FILE__, '/views/templates/hook/labproductnextprev.tpl');
	}
	
}

}
?>