<?php
/*
* 2017 AxonVIP
*
* NOTICE OF LICENSE
*
*  @author AxonVIP <axonvip@gmail.com>
*  @copyright  2017 axonvip.com
*   
*/

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

if (!defined('_PS_VERSION_')) {
    exit;
}

class laberCompare extends Module implements WidgetInterface
{
    public $prefix;

    public function __construct()
    {
		$this->name = 'labercompare';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'laberthemes';
		$this->need_instance = 0;

		$this->bootstrap = true;
        $this->controllers = array('comparator');
		parent::__construct();

		$this->displayName = $this->l('LABER Product Compare');
		$this->description = $this->l('LABER Product Compare');
		$this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
		
		$this->prefix = 'NRT_';
        $this->cf_defaults = array(
            'COMPARATOR_MAX_ITEM' => 3,
        );
    }

    public function install()
    {
		$success = (parent::install()
            && $this->setCfDefaults()
            && $this->registerHook('header')
            && $this->registerHook('buttoncompare')
		);
		return $success;
    }

    public function uninstall()
    {
		$success = (parent::uninstall()
			&& $this->deleteCfDefaults()
		);
		return $success;
    }

    public function setCfDefaults()
    {
        foreach ($this->cf_defaults as $cf_default => $value) {
            Configuration::updateValue($this->prefix . $cf_default, $value);
        }
        return true;
    }
	
    public function deleteCfDefaults()
    {
        foreach ($this->cf_defaults as $cf_default => $value) {
            Configuration::deleteByName($this->prefix . $cf_default);
        }
        return true;
    }

	public function getContent()
	{
		$output = '';
		$errors = array();
		if (Tools::isSubmit('submit' . $this->name))
		{
			$max_compare_item = Tools::getValue($this->prefix . 'COMPARATOR_MAX_ITEM');
			if (!strlen($max_compare_item))
					$errors[] = $this->l('Please complete the "Displayed tags" field.');
			elseif (!Validate::isInt($max_compare_item) || (int)($max_compare_item) <= 0)
					$errors[] = $this->l('Invalid number.');
			if (count($errors))
					$output = $this->displayError(implode('<br />', $errors));
			else
			{
				Configuration::updateValue($this->prefix . 'COMPARATOR_MAX_ITEM', (int)$max_compare_item);
				$output = $this->displayConfirmation($this->l('Settings updated'));
			}
		}
		return $output.$this->renderForm();
	}
	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type'	=> 'text',
						'label' => $this->l('Product comparison'),
						'name' 	=> $this->prefix . 'COMPARATOR_MAX_ITEM',
						'class' => 'fixed-width-xxl',
						'desc'	=> $this->l('Set the maximum number of products that can be selected for comparison.')
                        )
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submit' . $this->name;
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			$this->prefix . 'COMPARATOR_MAX_ITEM' => Tools::getValue($this->prefix . 'COMPARATOR_MAX_ITEM', (int)Configuration::get($this->prefix . 'COMPARATOR_MAX_ITEM'))
		);
	}
    public function hookHeader()
    {
		$this->context->controller->addCSS($this->_path.'views/css/front.css');
		
		$this->context->controller->addJS($this->_path.'views/js/front.js');

		$list_ids_product = array();
		$productsIds = $this->context->cookie->laberCompare;
        if ($productsIds) {
            $productsIds = json_decode($productsIds, true);
            foreach ($productsIds as $idProduct) {
				$list_ids_product[] = $idProduct;
            }
		}
		$productsIds = $list_ids_product;
		$this->smarty->assignGlobal('count_compare',count($productsIds));
		$this->smarty->assignGlobal('link_compare',true);
        Media::addJsDef(array(
			'compareProductsIds' =>  $productsIds,
			'compareNotifications' => ['addCompare' => 'Dodaj do porównania',
									   'removeCompare' => 'Usuń, aby porównać']
        ));
    }
	
    public function hookButtoncompare($params)
	{ 
		$this->smarty->assign('product', $params['product']);
			
        return $this->fetch('module:' . $this->name . '/views/templates/hook/product-button.tpl');
    }
	

    public function renderWidget($hookName = null, array $configuration = [])
    {
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        return $this->fetch('module:' . $this->name . '/views/templates/hook/display-nb.tpl');
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        return true;
    }
}
