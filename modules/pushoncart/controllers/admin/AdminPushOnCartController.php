<?php
/**
* 2007-2018 PrestaShop
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2018 PrestaShop SA
* @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
* International Registered Trademark & Property of PrestaShop SA
*/
require_once(_PS_MODULE_DIR_.'/pushoncart/classes/Promo.php');

class AdminPushOnCartController extends ModuleAdminController
{
	public function ajaxProcessErasePromo()
	{
		$p_id = (int)Tools::getValue('id_pushoncart');
		$promo = new Promo($p_id);
		$promo->deletePromo();
	}

	public function ajaxProcessSelectProduct()
	{
		$currency = Currency::getDefaultCurrency();
		$lang = (int)Tools::getValue('lang');
		$symbol = $currency->sign;
		$id_product = (int)Tools::getValue('id_product');
		$product = new Product($id_product);
		if (Validate::isLoadedObject($product))
		{
			$price = number_format($product->price, 2);

			$presta15 = 0;
			if ((bool)version_compare(_PS_VERSION_, '1.5.4', '<') == 1)
				$presta15 = 1;
			if ($presta15 == 0)
			{
				$image = $this->module->getCoverImagePath($id_product);

				$html = '<div id="productBanner" style="width:100%;" class="well">
							<i style="float:right;color:red;cursor:pointer;" onclick="clearProductBanner();" class="icon-remove"></i>
							<p><b>'.$this->module->l(' Product ID :  ').'</b>'.$id_product.'</p>
							<center><p><b>'.$this->module->l(' Pre-Tax Retail Price :  ').'</b>'.$symbol.$price.'<br />
							<img style="height:auto; width:auto; max-width:200px; max-height:200px;" src="'.$image.'"><br />';
			}
			else
			{
				$html = '<div id="productBanner" style="width:100%;" class="well">
							<i style="float:right;color:red;cursor:pointer;" onclick="clearProductBanner();" class="icon-remove"></i>
							<p><b>'.$this->module->l(' Product ID :  ').'</b>'.$id_product.'</p>
							<center><p><b>'.$this->module->l(' Pre-Tax Retail Price :  ').'</b>'.$symbol.$price.'<br />';
			}
			$shop_count = $this->module->shopCount();

			$shop_ids = $this->module->getShopIDs($id_product);

			$shop_checkbox_html = '';
			for ($i = 0; $i < $shop_count; $i++)
				$shop_checkbox_html .= '<div class="checkbox">
											<label>
											&nbsp;&nbsp;<input type="checkbox" name="shop_id['.$i.']" value="'.$shop_ids[$i]['id_shop'].'">'.
											$shop_ids[$i]['name']
											.'</label>
										</div>';
			$info_html = '<div><p>'
							.$this->module->l('If you do not choose any shops in the options above, the default shop will be chosen.')
						.'</p></div>';

			if ($shop_count != 1)
				$html = $html.$shop_checkbox_html.$info_html;
			echo $html;
			exit;
		}
		else
			Tools::displayError('Error loading Product.');
	}

	public function ajaxProcessRefreshTable()
	{
		$table = $this->module->createProductTable();
		echo $table;
	}

	public function ajaxProcessSetVal()
	{
		$value = pSQL(Tools::getValue('value'));
		$val = pSQL(Tools::getValue('val'));
		Configuration::updateValue($value, $val);
	}

	public function ajaxProcessSetPromoCount()
	{
		$val = pSQL(Tools::getValue('val'));
		Configuration::updateValue('PUSHONCART_PROMO_COUNT', $val);
	}

	public function ajaxProcessShowDiscountInfo()
	{
		$id_pushoncart = (int)Tools::getValue('id_pushoncart');
		$promo = new Promo($id_pushoncart);
		$html = '';
		foreach ($promo->ranges as $range)
		{
			if ($range['discount_type'] == 0)
				$symbol = '%';
			else
			{
				$currency = new Currency($range['discount_type']);
				$symbol = $currency->getSign();
			}
			$amount1 = (empty($range['amount1'])) ? 0 : round($range['amount1'], 2);
			$amount2 = (empty($range['amount2'])) ? '&infin;' : round($range['amount2'], 2);
			$html .= '<tr>
						<td></td>
						<td>'.$promo->parameters['id_product'].'</td>
						<td>'.$promo->parameters['discount_name'].'</td>
						<td>'.$amount1.'</td>
						<td>'.$amount2.'</td>
						<td>'.$range['discount'].'</td>
						<td>'.$symbol.'</td>
						<td>'.$range['code'].'</td>
						<td>'.$range['id_cart_rule'].'</td>
					</tr>
					';
		}
		echo $html;
	}

	public function ajaxProcessEnableMultipleCodes()
	{
		$id_pushoncart = (int)Tools::getValue('id_pushoncart');
		$promo = new Promo($id_pushoncart);

		if ($promo->parameters['multiple_codes_allowed'])
			$promo->disableMultipleCodes();
		else
			$promo->enableMultipleCodes();
	}

	public function ajaxProcessEnable()
	{
		$id_pushoncart = (int)Tools::getValue('id_pushoncart');
		$promo = new Promo($id_pushoncart);
		if ($promo->parameters['active'])
			$promo->deactivate();
		else
			$promo->activate();
	}

	public function ajaxProcessMove()
	{
		$direction = pSQL(trim(Tools::getValue('direction')));
		$id_product = (int)Tools::getValue('id_product');
		$this->module->move($direction, $id_product);
	}

	public function ajaxProcessDeleteProduct()
	{
		$id_pushoncart = (int)Tools::getValue('id_pushoncart');
		$promo = new Promo($id_pushoncart);
		$promo->deleteCartRules();
		$promo->archivePromo();
		$this->module->afterDelete($promo->parameters['id_product']);
	}

	public function ajaxProcessShowStatsOrders()
	{
		$id_pushoncart = (int)Tools::getValue('id_pushoncart');

		$html = '';
		$query = 'SELECT DISTINCT ocr.id_order,
								 pso.date_add, pso.id_shop, pso.id_customer,
								 cu.email
							FROM '._DB_PREFIX_.'pushoncartproducts pp
							JOIN '._DB_PREFIX_.'pushoncart_ranges pr ON pp.id_pushoncart = pr.id_pushoncart
							JOIN '._DB_PREFIX_.'order_cart_rule ocr ON pr.id_cart_rule = ocr.id_cart_rule
							JOIN '._DB_PREFIX_.'order_detail od ON ocr.id_order = od.id_order
							JOIN '._DB_PREFIX_.'orders pso ON od.id_order = pso.id_order
							JOIN '._DB_PREFIX_.'customer cu ON pso.id_customer = cu.id_customer
								WHERE pp.id_pushoncart = '.(int)$id_pushoncart;

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
		$count_result = count($result);
		for ($i = 0; $i < $count_result; $i++)
		{
			$date = Tools::substr($result[$i]['date_add'], 0, 10);
			$html .= '<tr>
				<td></td>
				<td>'.$id_pushoncart.'</td>
				<td>'.$result[$i]['id_shop'].'</td>
				<td>'.$date.'</td>
				<td>'.$result[$i]['id_order'].'</td>
				<td>'.$result[$i]['id_customer'].'</td>
				<td>'.$result[$i]['email'].'</td>
				</tr>
				';
		}
		echo $html;
	}

	public function ajaxProcessSearch()
	{
		$value = pSQL(trim(Tools::getValue('value')));
		$lang = (int)Tools::getValue('lang');
		$result = $this->module->getProductInfo($lang, $value);

		exit(Tools::jsonEncode($result));
	}
}
