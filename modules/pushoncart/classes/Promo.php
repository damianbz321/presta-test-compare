<?php

	class Promo
	{
		public $id_pushoncart = 0;
		public $parameters = array();
		public $ranges = array();

		public function __construct($id_pushoncart = 0)
		{
			$query = 'SELECT * FROM '._DB_PREFIX_.'pushoncartproducts
			WHERE id_pushoncart = '.(int)$id_pushoncart;

			$this->id_pushoncart = $id_pushoncart;
			$this->parameters = Db::getInstance()->getRow($query);
			$this->ranges = $this->getRanges();
			$this->product_info = $this->getProductInfo();
		}

		public function getRanges()
		{
			$query_ranges = 'SELECT * FROM `'._DB_PREFIX_.'pushoncart_ranges`
					WHERE id_pushoncart = '.(int)$this->id_pushoncart.' ORDER BY amount1';
			$ranges = Db::getInstance()->ExecuteS($query_ranges);
			return $ranges;
		}

		public function getItemsCount()
		{
			$query_range_count = 'SELECT COUNT(id_range) FROM `'._DB_PREFIX_.'pushoncart_ranges`
								WHERE id_pushoncart = '.(int)$this->id_pushoncart;
			$range_count = Db::getInstance()->getValue($query_range_count);
			return $range_count;
		}

		public function getPromo()
		{
			return $this->parameters;
		}

		public function deletePromo()
		{
			/* Delete Cart Rules */
			$this->deleteCartRules();

			/* Delete from ranges table */
			$delete_range_query = 'DELETE FROM `'._DB_PREFIX_.'pushoncart_ranges`
			WHERE id_pushoncart = \''.(int)$this->id_pushoncart;
			Db::getInstance()->Execute($delete_range_query);

			/* Delete from push on cart products table*/
			$delete_promo_query = 'DELETE FROM `'._DB_PREFIX_.'pushoncartproducts`
			WHERE id_pushoncart = \''.(int)$this->id_pushoncart;
			Db::getInstance()->Execute($delete_promo_query);
		}

		public function archivePromo()
		{
			/* Delete Cart Rules */
			$this->deleteCartRules();

			$archive_pushoncarts = 'UPDATE '._DB_PREFIX_.'pushoncartproducts
				SET archive = 1, position = 0, active = 0
				WHERE id_pushoncart = '.(int)$this->id_pushoncart.' AND id_shop = '.(int)$this->parameters['id_shop'];
			return Db::getInstance()->Execute($archive_pushoncarts);
		}

		public function deleteCartRules()
		{
			/* Delete Cart Rules */
			$query = 'SELECT id_cart_rule FROM `'._DB_PREFIX_.'pushoncart_ranges`
					WHERE id_pushoncart = '.(int)$this->id_pushoncart;
			$rules = Db::getInstance()->ExecuteS($query);

			foreach ($rules as $rule)
			{
				$cr = new CartRule($rule['id_cart_rule']);
				$cr->delete();
			}
		}

		public function setPosition($position)
		{
			Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'pushoncartproducts
					SET position = '.(int)$position.' WHERE id_pushoncart = '.(int)$this->id_pushoncart.' AND archive = 0');
		}

		public function getPostition()
		{
			return $this->parameters['position'];
		}

		public function insertRange($amount1 = null, $amount2 = null, $discount = 0, $discount_type, $id_cart_rule, $code)
		{
			$query = 'INSERT INTO `'._DB_PREFIX_.'pushoncart_ranges` (id_pushoncart, discount, discount_type, amount1, amount2, code, id_cart_rule)
						VALUES ('.(int)$this->id_pushoncart.', '.(float)$discount.', '.(int)$discount_type.', '.(float)$amount1.', '
								.($amount2 == 0 ? 'NULL' : (float)$amount2).', "'.pSQL($code).'", '.(int)$id_cart_rule.')';

			return Db::getInstance()->Execute($query);
		}

		public function returnRange($id_cart)
		{
			$cart = new Cart((int)$id_cart);
			$cart_total = $cart->getOrderTotal();
			$valid_range = array();
			foreach ($this->ranges as $range)
			{
				if (($range['amount1'] <= $cart_total || $range['amount1'] == null) && ($range['amount2'] > $cart_total || $range['amount2'] == null))
					$valid_range = $range;
			}
			if (!empty($valid_range))
				return $valid_range;
			return 0;
		}

		public function viewCount() /* View counter */
		{
			$view_count = $this->parameters['view_count'];
			$count = $view_count + 1;
			$query = 'UPDATE '._DB_PREFIX_.'pushoncartproducts SET view_count = '.(int)$count.'
					WHERE id_pushoncart = '.(int)$this->id_pushoncart;
			Db::getInstance()->Execute($query);
		}

		public function multipleCodesAllowed($p_id) /* Checks the customer can benefit from the PushOnCart promotion with other cart rules */
		{
			$query = 'SELECT multiple_codes_allowed FROM '._DB_PREFIX_.'pushoncartproducts
						WHERE id_pushoncart = '.(int)$this->id_pushoncart;
			$result = (Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query));

			return (int)$result['multiple_codes_allowed'];
		}

		public function disableMultipleCodes()
		{
			Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'pushoncartproducts` SET `multiple_codes_allowed`=0
				WHERE archive = 0 AND id_pushoncart = '.(int)$this->id_pushoncart.' AND id_shop = '.(int)$this->parameters['id_shop']);
		}

		public function enableMultipleCodes()
		{
			Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'pushoncartproducts` SET `multiple_codes_allowed`=1
				WHERE archive = 0 AND id_pushoncart = '.(int)$this->id_pushoncart.' AND id_shop = '.(int)$this->parameters['id_shop']);
		}

		public function activate()
		{
			Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'pushoncartproducts` SET `active`=1
					WHERE archive = 0 AND id_pushoncart = '.(int)$this->id_pushoncart.' AND id_shop = '.(int)$this->parameters['id_shop']);
		}

		public function deactivate()
		{
			Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'pushoncartproducts` SET `active`=0
					WHERE archive = 0 AND id_pushoncart = '.(int)$this->id_pushoncart.' AND id_shop = '.(int)$this->parameters['id_shop']);
		}

		public function getProductInfo()
		{
			$info = array();
			$info['id_attribute_default'] = Product::getDefaultAttribute($this->parameters['id_product']);
			$info['product_attributes'] = $this->getProductAttributes();
			$info['all_the_atts'] = $this->getProductGroupAttributes();
			$info['default_att_color'] = $this->getDefaultAttColor($info['id_attribute_default']);
			$info['image_link'] = $this->getCoverImagePath();
			return $info;
		}

		public function getCoverImagePath($id_combination = null)
		{
			$product = new Product($this->parameters['id_product']);
			$cover_dir = Image::getImages((int)Context::getcontext()->language->id, $this->parameters['id_product'], $id_combination);

			$id_cover_image = null;
			foreach ($cover_dir as $img)
				if ($img['cover'] == 1)
					$id_cover_image = $img['id_image'];

			$link = new Link();
			return Tools::getCurrentUrlProtocolPrefix().$link->getImageLink($product->link_rewrite[(int)Context::getcontext()->language->id], $id_cover_image);
		}

		public function getDefaultAttColor($id_product_attribute)
		{
			$result = Db::getInstance()->ExecuteS('SELECT id_attribute FROM '._DB_PREFIX_.'product_attribute_combination
						WHERE id_product_attribute = '.(int)$id_product_attribute);
			foreach ($result as $row)
			{
				$attribute = new Attribute($row['id_attribute']);
				if ($attribute->isColorAttribute() == 1)
					return $row['id_attribute'];
			}
			return 0;
		}

		public function getProductGroupAttributes()
		{
			$product = new Product((int)$this->parameters['id_product']);
			$id_lang = Context::getContext()->language->id;
			if ($id_lang == '' || $id_lang == null)
				$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
			$pre_atts = $product->getAttributeCombinations($id_lang);
			$atts = array();
			foreach ($pre_atts as $att)
			{
				$att['stock_check'] = $this->checkStock($att['id_product_attribute']);
				array_push($atts, $att);
			}

			$atts_count = $product->hasAttributes();
			$product_attributes = array();
			$combos = array();
			$combos_names = array();
			$group_att_name = array();
			$default_atts = array();
			$check_stock = 1;

			if ($atts_count > 0)
			{
				$group_names = array_unique(array_map('self::names', $atts));
				$group_ids = array_unique(array_map('self::idAttributeGroup', $atts));

				$color_group_ids = array_unique(array_map('self::isColorGroup', $atts));

				$color_group_ids_js = Tools::jsonEncode($color_group_ids);
				$atts_names = array_unique(array_map('self::attributeName', $atts));
				$atts_ids = array_unique(array_map('self::idAttribute', $atts));

				$product_attributes = array('group_ids' => $group_ids,
											'color_group_ids' => $color_group_ids,
											'color_group_ids_js' => $color_group_ids_js,
											'atts_names' => $atts_names,
											'atts_ids' => $atts_ids);

				foreach ($group_ids as $key => $id_group_attribute)
				{
					$group_att_name[$id_group_attribute] = $group_names[$key];
					$query_attributes = Db::getInstance()->ExecuteS('SELECT id_attribute
						FROM '._DB_PREFIX_.'attribute
						WHERE id_attribute_group = '.(int)$id_group_attribute.' AND id_attribute IN ('.implode($atts_ids, ', ').') ORDER BY id_attribute');

					${$id_group_attribute} = array();
					foreach ($query_attributes as $att)
						array_push(${$id_group_attribute}, $att['id_attribute']);

					$combos[$id_group_attribute] = ${$id_group_attribute};

					$query_attributes_names = Db::getInstance()->ExecuteS('SELECT name
						FROM '._DB_PREFIX_.'attribute_lang
						WHERE id_lang = '.(int)$id_lang.' AND id_attribute IN ('.implode(${$id_group_attribute}, ', ').') ORDER BY id_attribute');

					${$id_group_attribute.'_names'} = array();
					foreach ($query_attributes_names as $name)
						array_push(${$id_group_attribute.'_names'}, $name['name']);

					$combos_names[$id_group_attribute] = ${$id_group_attribute.'_names'};

				}
				$default_prod_att = $product->getDefaultAttribute((int)$this->parameters['id_product']);
				$check_stock = $this->checkStock($default_prod_att);
				$default_atts = $this->getDefaultAtts((int)$default_prod_att);
			}
			$product_attributes['default_atts'] = $default_atts;
			$product_attributes['default_check_stock'] = $check_stock;
			$product_attributes['group_names'] = $group_att_name;
			$product_attributes['combinations'] = $combos;
			$product_attributes['names'] = $combos_names;
			$product_attributes['att_count'] = $atts_count;

			return $product_attributes;
		}

		public function getProductAttributes()
		{
			$id_lang = Context::getContext()->language->id;
			$product = new Product((int)$this->parameters['id_product']);
			if (!Validate::isLoadedObject($product))
				Tools::displayError('Error loading $product (function (getProductAttributes)).');
			$id_product_attribute = $product->getDefaultAttribute($this->parameters['id_product']);

			if ($id_product_attribute != null)
			{
				$attribute_ids = $this->getDefaultAtts($id_product_attribute);

				$list_for_query = implode(',', array_map('intval', $attribute_ids));

				$query_attributes = '
					SELECT DISTINCT ag.*, agl.*, a.`id_attribute`, al.`name`, agl.`name` AS `attribute_group`
					FROM `'._DB_PREFIX_.'attribute_group` ag
					LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
						ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'attribute` a
						ON a.`id_attribute_group` = ag.`id_attribute_group`
					LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
						ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
					'.Shop::addSqlAssociation('attribute_group', 'ag').'
					'.Shop::addSqlAssociation('attribute', 'a').'
					AND a.`id_attribute` IN ('.$list_for_query.')
					ORDER BY agl.`name` ASC, a.`position` ASC
				';
				$product_attributes = Db::getInstance()->ExecuteS($query_attributes);
				return $product_attributes;
			}
			return null;
		}

		public function getDefaultAtts($id_product_attribute)
		{
			$result = Db::getInstance()->ExecuteS('SELECT id_attribute FROM '._DB_PREFIX_.'product_attribute_combination
							WHERE id_product_attribute = '.(int)$id_product_attribute);
			$id_atts = array();
			foreach ($result as $row)
				array_push($id_atts, $row['id_attribute']);

			return $id_atts;
		}

		public function checkStock($id_product_attribute = null)
		{
			$enable_stock_management = (int)Configuration::get('PS_STOCK_MANAGEMENT');
			$id_shop = Context::getContext()->shop->id;
			$general_stock_management = (int)Configuration::get('PS_ORDER_OUT_OF_STOCK');
			/* General flag that checks if shop can sell out of stock products */
			$flag = StockAvailable::outOfStock((int)$this->parameters['id_product'], $id_shop);
			/* Flag on specific products that checks if shop can sell the product when out of stock */
			$quantity_available = StockAvailable::getQuantityAvailableByProduct((int)$this->parameters['id_product'], $id_product_attribute, $id_shop);
			/* Checks the quantity available of the current product */

			if ($enable_stock_management == 0)
				return 1;
			if (($general_stock_management == 1 && $flag == 2) || $flag == 1)	/* Checking if available stock */
				return 1;
			else {
				if ($quantity_available > 0)
					return 1;
				return 0;
			}
			return 0;
		}

		/* Small functions for array_maps of product attributes */
		public static function names($element)
		{
			return $element['group_name'];
		}

		public static function idAttributeGroup($element)
		{
			return $element['id_attribute_group'];
		}

		public static function isColorGroup($element)
		{
			if ((int)$element['is_color_group'] === 1)
				return (int)$element['id_attribute_group'];
			else
				return 0;
		}

		public static function idAttribute($element)
		{
			return $element['id_attribute'];
		}

		public static function attributeName($element)
		{
			return $element['attribute_name'];
		}
		/* End of small functions for array_maps of product attributes */
	}
