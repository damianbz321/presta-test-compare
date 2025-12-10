<?php
class PushOnCartProductOffersModuleFrontController extends ModuleFrontController
{
	public function displayAjaxReloadImageAndPrice()
	{
		$context = Context::getContext();
		$pushoncart = new PushOnCart();
		$id_pushoncart = (int)Tools::getValue('id_pushoncart');
		$promo = new Promo($id_pushoncart);
		$range = $promo->returnRange($context->cart->id);

		$id_product = (int)Tools::getValue('id_product');
		$product = new Product($id_product);

		$id_att_group = (int)Tools::getValue('id_att_group');
		$color_group_ids = Tools::getValue('color_group_ids');

		$id_attributes = Tools::getValue('id_attributes');
		$id_attribute = (int)Tools::getValue('id_attribute');

		$id_product_attributes = array();
		$result = array();
		foreach ($id_attributes as $id_att)
		{
			$query = 'SELECT pac.id_product_attribute
							FROM '._DB_PREFIX_.'product_attribute_combination pac
							LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON pac.id_product_attribute = pa.id_product_attribute
							WHERE pac.id_attribute = '.(int)$id_att.' AND pa.id_product = '.(int)$id_product;
			if (!empty($id_product_attributes) && $id_product_attributes)
				$query .= ' AND pac.id_product_attribute IN ('.implode($id_product_attributes, ', ').')';

			$result = Db::getInstance()->executeS($query);

			$id_product_attributes = array();
			foreach ($result as $id_product_attribute)
				array_push($id_product_attributes, $id_product_attribute['id_product_attribute']);
		}

		$stock_check = $pushoncart->checkStock($id_product, $context->shop->id, $id_product_attributes[0]);
		$price = $product->getPrice(true, $id_product_attributes[0]);
		$discount_amount = $pushoncart->getDiscountAmount($range, $price);
		$sale_price = $price - $discount_amount;

		$id_image = Db::getInstance()->getValue('SELECT id_image
							FROM '._DB_PREFIX_.'product_attribute_image
							WHERE id_product_attribute = '.(int)$id_product_attributes[0]);

		$link = new Link();
		$link_rewrite = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT link_rewrite FROM '._DB_PREFIX_.'product_lang
			  WHERE id_product = '.(int)$id_product.' AND id_lang = '.(int)$context->language->id);

		$image_type = ImageType::getImagesTypes();

		if (Configuration::get('PS_LEGACY_IMAGES'))
			$image_link = '/img/p/'.(int)$id_product.'-'.$id_image.'-large.jpg';
		else
		{
			//$image_link = $link->getImageLink($link_rewrite[0]['link_rewrite'], $id_image, $image_type[3]['name']);
			$img_dir = implode('/', str_split($id_image));
			if ($pushoncart->ps17)
				$image_link = _PS_IMG_.'p/'.$img_dir.'/'.$id_image.'-'.ImageType::getFormattedName('home').'.jpg';
			else
				$image_link = _PS_IMG_.'p/'.$img_dir.'/'.$id_image.'-'.ImageType::getFormatedName('home').'.jpg';
		}
		$info['image_link'] = $image_link;
		$info['stock_check'] = $stock_check;
		$info['price'] = number_format($price, 2);
		$info['sale_price'] = number_format($sale_price, 2);

		exit(Tools::jsonEncode($info));
	}
}
