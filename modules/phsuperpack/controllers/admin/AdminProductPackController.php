<?php

error_reporting(0);

if (!class_exists('ProductPack')) {
	require_once _PS_ROOT_DIR_ . '/modules/phsuperpack/classes/ProductPack.php';
}

define('PACK_DIR', _PS_MODULE_DIR_ . 'phsuperpack/');

class AdminProductPackController extends ModuleAdminController
{
    public $currId = 0;
    public function ajaxProcessDeletePack()
    {
        try {
			$id_superpack_product = (int)Tools::getValue('id_superpack_product');			
			if ($id_superpack_product > 0) {
				$pack = ProductPack::getPackById($id_superpack_product);
				if ($pack != null) {
					$pr = new Product((int)$pack['id_product_pack']);
					$pr->delete();
					Db::getInstance()->delete('phsuperpack_product', 'id_superpack_product = '.(int)$id_superpack_product);
				}
			}

            $this->ajaxProcessGetPackList();
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }
		
	public function ajaxProcessActivePack()
    {
        try {
            $id_superpack_product = (int)Tools::getValue('id_superpack_product');
			$active = (int)Tools::getValue('active');
			if ($id_superpack_product > 0) {
				$pack = ProductPack::getPackById($id_superpack_product);
				if ($pack != null) {
					echo Db::getInstance()->update('phsuperpack_product', array(
						'active' => $active,
					), 'id_superpack_product = '.(int)$id_superpack_product);
				}
			}
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }
		
	public function ajaxProcessPositionPack()
    {
		try {
            foreach (Tools::getValue('product_pack_positions') as $id_product_pack => $position) {                
				Db::getInstance()->update('phsuperpack_product', array(
					'position' => $position,
				), 'id_superpack_product = '.(int)$id_product_pack);
            }

            echo 1;
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }

    public function ajaxProcessGetPackList()
    {
        try {
			$id_product = (int)Tools::getValue('id_product');
			$list = ProductPack::getPack($id_product);
//            $list = array_unique($list);
			$max_pack = 0;
      
			$lang_id = (int)Configuration::get('PS_LANG_DEFAULT');
			$list2 = array();
//            var_dump($list);
			foreach ($list as $key => $pack) {
				$pr = new Product((int)$pack['id_product_pack']);
				
				if ($pr == null || !Validate::isLoadedObject($pr)) {
					Db::getInstance()->delete('phsuperpack_product', 'id_superpack_product = '.(int)$pack['id_superpack_product']);
					continue;
				}
				
				$q = 0;
                Pack::isPack($pack['id_product_pack']);
				$id_product_attribute = null;
				
				$price = $pr->getPrice(true, $id_product_attribute, 6);
				$price2 = $pr->getPrice(false, $id_product_attribute, 6);
				$regular_price = $pr->getPriceWithoutReduct(false, $id_product_attribute);

				$list[$key]['price_net'] = Tools::displayPrice($pr->price);
				$list[$key]['price'] = Tools::displayPrice($price);
				$list[$key]['price2'] = Tools::displayPrice($price2);
				$list[$key]['regular_price'] = Tools::displayPrice($regular_price);
				$no_pack = $pr->getNoPackPrice();
				$list[$key]['no_pack_price'] = Tools::displayPrice($no_pack);
								
				$list[$key]['has_discount'] = false;
				if ($regular_price > $price) {
					$list[$key]['has_discount'] = true;
					$list[$key]['discount'] = Tools::displayPrice($regular_price - $price);					
					$list[$key]['discount_proc'] = "-".Tools::ps_round(100 - ((100 * $price) / $regular_price), 2)."%";
				}
				
				$list[$key]['items'] = Pack::getItemTable((int)$pack['id_product_pack'], $lang_id, true);
		
				if ($q <= 0) {
					$packQuantity = 0; $index = -1; $qty = 1;

					foreach ($list[$key]['items'] as $line) {
						$item_id = (int)$line['id_product'];
						$item_id_attribute = (int)$line['id_product_attribute'];
						if ($item_id > 0) {
							$index++;
							//$itemQuantity = Product::getQuantity($item_id);
							if ($item_id_attribute > 0)
								$itemQuantity = Product::getQuantity($item_id, $item_id_attribute);
							else
								$itemQuantity = Product::getQuantity($item_id);
							
							$nbPackAvailableForItem = floor($itemQuantity / $qty);

							// Initialize packQuantity with the first product quantity
							// if pack decrement stock type is products only
							if ($index === 0
								//&& $packStockType == Pack::STOCK_TYPE_PRODUCTS_ONLY
							) {
								$packQuantity = $nbPackAvailableForItem;
								continue;
							}

							if ($nbPackAvailableForItem < $packQuantity) {
								$packQuantity = $nbPackAvailableForItem;
							}
						}
					}
					$q = $packQuantity;
				}
				
				$list[$key]['quantity'] = $q;
				
				if ($max_pack < count($list[$key]['items'])) {
					$max_pack = count($list[$key]['items']);
				}
				
				$list2[$key] = $list[$key];
			}

            $this->context->smarty->assign(array(
                'pack' => $list2,
                'product_pack_get_list' => $this->context->link->getAdminLink('AdminProductPack')
					. '&ajax=1&action=getpacklist',
                'product_pack_link_delete' => $this->context->link->getAdminLink('AdminProductPack')
                    . '&ajax=1&action=deletepack',
				'psVersion' => version_compare(_PS_VERSION_, 1.7, '<')
            ));			

            die($this->context->smarty->fetch(PACK_DIR . 'views/templates/admin/list.tpl'));
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }

    public function ajaxProcessGetPackForm()
    {
        try {
			$id_superpack_product = (int)Tools::getValue('id_superpack_product');
			
			if ($id_superpack_product > 0) {
				$pack = ProductPack::getPackById($id_superpack_product);
			}
			
			$this->context->smarty->assign(array(
                'base_admin' => $this->context->link->getBaseLink() . basename(_PS_ADMIN_DIR_) . '/',
				'id_superpack_product' => $id_superpack_product
            ));

            die($this->context->smarty->fetch(PACK_DIR . 'views/templates/admin/form.tpl'));
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }	
	
	public function ajaxProcessGetPackPrice()
    {
        try {			
			$id_product = (int)Tools::getValue('id_product');			
			$id_shop = (int)Context::getContext()->shop->id;
			$pack_products = Tools::getValue('pack_products');
            $pack_products_obj = Tools::getValue('pack_products_obj');
			$pack_products_tab = array();
			$pack_products_tab[] = $id_product;
			if (is_array($pack_products)) {
				foreach ($pack_products as $s) {
					$pack_products_tab[] = $s;
				}
			}
            $prx = new Product($id_product);
            $price_obj = 0.0;
            $pricesy = [];
            $price_obj_with_tax = 0.0;
            $price_obj += $prx->getPrice(false);
            $price_obj_with_tax += $prx->getPrice(true);
            $pricesy[] = [
                $prx->getPrice(false),
                $prx->getPrice(true)
            ];
            foreach ($pack_products as $product_id_pack){

                $qty_item_pack = (int)$pack_products_obj[$product_id_pack]["quantity"];

                $tab = explode(",", $product_id_pack);
                $item_id = (int)$tab[0];
                $item_id_attribute = null;
                if (count($tab) > 1) {
                    $item_id_attribute = (int)$tab[1];
                }

                if ($item_id > 0) {
                    $pr = new Product($item_id);

                    $price_obj += $pr->getPrice(false, ($item_id_attribute)?$item_id_attribute:null, 6)*$qty_item_pack;
                    $price_obj_with_tax += $pr->getPrice(true, ($item_id_attribute)?$item_id_attribute:null, 6)*$qty_item_pack;
                    $pricesy[] = [
                        $pr->getPrice(false, ($item_id_attribute)?$item_id_attribute:null, 6)*$qty_item_pack,
                        $pr->getPrice(true, ($item_id_attribute)?$item_id_attribute:null, 6)*$qty_item_pack
                    ];
                }


            }
			$price = 0; $priceGross = 0;
			$pack_products_tab = array_unique($pack_products_tab);
			if (count($pack_products_tab) <= 1) {
				//return $price;
			} else {
				$qty = 1;
                foreach ($pack_products_tab as $line) {
                    if (!empty($line)) {

                        $tab = explode(",", $line);
                        $item_id = (int)$tab[0];
                        $item_id_attribute = null;
                        if (count($tab) > 1) {
                            $item_id_attribute = (int)$tab[1];
                        }

                        if ($item_id > 0) {
                            $pr = new Product($item_id);
                            $price += $pr->price;
                            $priceGross += $pr->getPrice(true, $item_id_attribute, 6);
                        }
                        $price += $price * (int)$pack_products_obj[$item_id]["quantity"];
                    }
                }
			}

			echo Tools::jsonEncode([
                'success' => true,
                'line' => 'Suma: '. Tools::displayPrice($price_obj) . ' netto, ' . Tools::displayPrice($price_obj_with_tax) .' brutto',
                //'price' => $price
				'price' => Tools::ps_round($priceGross, 6)
            ]);
			exit();
		
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }
	
	public function ajaxProcessGetPackPriceDisc()
    {
        try {			
			$id_product = (int)Tools::getValue('id_product');			
			$id_shop = (int)Context::getContext()->shop->id;
			$pack_products = Tools::getValue('pack_products');
			$pack_products_obj = Tools::getValue('pack_products_obj');
			$pack_products_tab = array();
			$pack_products_tab[] = $id_product;
            $reduction_change = Tools::getValue('reduction_change');


			$prx = new Product($id_product, true);
            if ((int)$prx->tax_rate > 0) {
                $rate = ($prx->tax_rate / 100) + 1;
            } else {
                $taxes = $prx->getTaxesRate();
                $rate = ($taxes / 100) + 1;
            }
						
			if ($rate > 0)
				$pack_products_price = Tools::ps_round((float) (Tools::getValue('pack_products_price')) / $rate, 2);
			else
				$pack_products_price = (float) (Tools::getValue('pack_products_price'));			
			$pack_products_price = Tools::ps_round($pack_products_price, 2);

            $price_obj = 0.0;
            $pricesy = [];
            $price_obj_with_tax = 0.0;

            $pricesy[] = [
                $prx->getPrice(false),
                $prx->getPrice(true)
            ];
//            if(!$reduction_change)
                foreach ($pack_products as $product_id_pack){

                    $qty_item_pack = (int)$pack_products_obj[$product_id_pack]["quantity"];

                    $tab = explode(",", $product_id_pack);
                    $item_id = (int)$tab[0];
                    $item_id_attribute = null;
                    if (count($tab) > 1) {
                        $item_id_attribute = (int)$tab[1];
                    }

                    if ($item_id > 0) {
                        $pr = new Product($item_id);

                        $price_obj += $pr->getPrice(false, ($item_id_attribute)?$item_id_attribute:null, 6)*$qty_item_pack;
                        $price_obj_with_tax += $pr->getPrice(true, ($item_id_attribute)?$item_id_attribute:null, 6)*$qty_item_pack;


                        $pricesy[] = [
                            $pr->getPrice(false, ($item_id_attribute)?$item_id_attribute:null, 6)*$qty_item_pack,
                            $pr->getPrice(true, ($item_id_attribute)?$item_id_attribute:null, 6)*$qty_item_pack
                        ];
                    }


                }


			$reduction = (float) (Tools::getValue('pack_products_reduction'));
			$reduction_type = !$reduction ? 'amount' : Tools::getValue('pack_products_reduction_type');
			$reduction_type = $reduction_type == '-' ? 'amount' : $reduction_type;

			if (is_array($pack_products)) {
				foreach ($pack_products as $s) {
					$pack_products_tab[] = $s;
				}
			}

			$price = 0; $priceGross = 0;
			$pack_products_tab = array_unique($pack_products_tab);
			if (count($pack_products_tab) <= 1) {
				//return $price;
			} else {

//				if ($pack_products_price <= 0) {
//					$qty = 1;
//
//					foreach ($pack_products_tab as $line) {
//						if (!empty($line)) {
//
//							$tab = explode(",", $line);
//							$item_id = (int)$tab[0];
//							$item_id_attribute = null;
//							if (count($tab) > 1) {
//								$item_id_attribute = (int)$tab[1];
//							}
//
//							if ($item_id > 0) {
//								$pr = new Product($item_id);
//								$price += $pr->price;
//								$priceGross += $pr->getPrice(true, $item_id_attribute, 6);
//							}
//                            $price += $price * (int)$pack_products_obj[$item_id]["quantity"];
//						}
//					}
//				} else {
                if($reduction != 0){
					$priceP = $price_obj + $prx->getPrice(false);
					// $priceP = $pack_products_price;
					if ((float) $reduction != 0) {
						if ($reduction_type == 'percentage') {
							$priceP = $priceP - (($priceP * $reduction) / 100);
						} else {
							$priceP = $priceP - $reduction;

						}

					}
                    $price_obj = $priceP;

					if ($rate > 0)
                        $price_obj_with_tax = Tools::ps_round($priceP * $rate, 2);
					else
                        $price_obj_with_tax = $priceP;
				}
                else {
                    $price_obj += $prx->getPrice(false);
                    $price_obj_with_tax += $prx->getPrice(true);
                }
			}

			echo Tools::jsonEncode([
                'success' => true,
                'line' => 'Suma: '. Tools::displayPrice($price_obj) . ' netto, ' . Tools::displayPrice($price_obj_with_tax) .' brutto',
                //'price' => $price
				'price' => Tools::ps_round($price_obj_with_tax, 2)
            ]);
			exit();
		
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }
	
	public function ajaxProcessProductsList()
    {
        $query = Tools::getValue('q', false);
        if (empty($query)) {
            return;
        }

        if ($pos = strpos($query, ' (ref:')) {
            $query = substr($query, 0, $pos);
        }

        $excludeIds = Tools::getValue('excludeIds', false);
		if ($excludeIds == 'xexcludeidsx' || $excludeIds == '') {
			$excludeIds = Tools::getValue('id_product', 0);
		}		
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        // Excluding downloadable products from packs because download from pack is not supported
        $forceJson = Tools::getValue('forceJson', false);
        $disableCombination = Tools::getValue('disableCombination', false);
        $excludeVirtuals = (bool) Tools::getValue('excludeVirtuals', true);
        $exclude_packs = (bool) Tools::getValue('exclude_packs', true);

        $context = Context::getContext();

        $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image` id_image, il.`legend`, p.`cache_default_attribute`
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int) $context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $context->language->id . ')
                WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
                (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . $excludeIds . ') ' : ' ') .
                ($excludeVirtuals ? 'AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
                ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
                ' GROUP BY p.id_product';

        $items = Db::getInstance()->executeS($sql);

        if ($items && $disableCombination) {
            $results = [];
            foreach ($items as $item) {
                if (!$forceJson) {
					$pr = new Product($item['id_product']);
					$price = Tools::displayPrice($pr->getPrice(true, null, 6));
                    $item['name'] = str_replace('|', '&#124;', $item['name']);
                    $results[] = trim($item['name']) . (!empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : '') . ' - ' . $price . '|' . (int) ($item['id_product']);
                } else {
					$pr = new Product($item['id_product']);
					$price = Tools::displayPrice($pr->getPrice(true, null, 6));
                    // JP add quantity
                    $qty =  Product::getQuantity($item['id_product']);
                    $results[] = array(
                        'id' => $item['id_product'],
                        'name' => '1 '.$item['name'] . (!empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : '') . ' - ' . $price,
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], 'home_default')),
                    );
                }
            }

            if (!$forceJson) {
                echo Tools::jsonEncode(implode(PHP_EOL, $results));
				exit();
            }

            echo Tools::jsonEncode($results);
			exit();
        }
        if ($items) {
            // packs
            $results = array();
            foreach ($items as $item) {
                // check if product have combination
                if (Combination::isFeatureActive() && $item['cache_default_attribute']) {
                    $sql = 'SELECT pa.`id_product_attribute`, pa.`reference`, ag.`id_attribute_group`, pai.`id_image`, agl.`name` AS group_name, al.`name` AS attribute_name,
                                a.`id_attribute`
                            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
                            WHERE pa.`id_product` = ' . (int) $item['id_product'] . '
                            GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                            ORDER BY pa.`id_product_attribute`';

                    $combinations = Db::getInstance()->executeS($sql);
                    if (!empty($combinations)) {
                        $countCombination = count($combinations);
                        foreach ($combinations as $k => $combination) {
							
							$pr = new Product($item['id_product']);
							$price = Tools::displayPrice($pr->getPrice(true, $combination['id_product_attribute'], 6));
                            // JP add quantity
                            $qty =  Product::getQuantity($item['id_product'],$combination['id_product_attribute']);
                            $results[$combination['id_product_attribute']]['id'] = $item['id_product'];
                            $results[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
                            !empty($results[$combination['id_product_attribute']]['name']) ? $results[$combination['id_product_attribute']]['name'] .= ' ' . $combination['group_name'] . '-' . $combination['attribute_name']
                            : $results[$combination['id_product_attribute']]['name'] = $item['name'] . ' ' . $combination['group_name'] . '-' . $combination['attribute_name'];
                            // JP add quantity
//                            $results[$combination['id_product_attribute']]['name'] .=  '<input type="number" id="qty-product-'.$item['id_product'].'-pack" onchange="sendQty('.$item['id_product'].')" data-s="sd" name="pack_products_qty[data]">  / <b>("'.$qty.'")</b> ';
                            if (!empty($combination['reference'])) {
                                $results[$combination['id_product_attribute']]['ref'] = $combination['reference'];
                            } else {
                                $results[$combination['id_product_attribute']]['ref'] = !empty($item['reference']) ? $item['reference'] : '';
                            }
                            if (empty($results[$combination['id_product_attribute']]['image'])) {
                                $results[$combination['id_product_attribute']]['image'] = str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $combination['id_image'], 'home_default'));
                            }
							if ((int)$combination['id_image'] == 0) {
								$images = $pr->getImages((int)$context->language->id);
								foreach ($images as $k => $image) {
									if ($image['cover']) {										
										$cover = $image;
										$cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id.'-'.$image['id_image']) : $image['id_image']);
										$cover['id_image_only'] = (int)$image['id_image'];
									}
									$product_images[(int)$image['id_image']] = $image;
								}
								
								if (!isset($cover)) {
									if (isset($images[0])) {
										$cover = $images[0];
										$cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id.'-'.$images[0]['id_image']) : $images[0]['id_image']);
										$cover['id_image_only'] = (int)$images[0]['id_image'];
									} else {
										$cover = array(
											'id_image' => $this->context->language->iso_code.'-default'
										);
									}
								}
								$results[$combination['id_product_attribute']]['image'] = str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $cover['id_image_only'], 'home_default'));
							}							
							
							$results[$combination['id_product_attribute']]['name'] .= ' - ' . $price;

                            $results[$combination['id_product_attribute']]['name'] .=  '<input type="number" id="qty-product-'.$item['id_product'].'-pack" onchange="sendQty('.$item['id_product'].')" name="pack_products_qty[data]">  / <b>('.$qty.')</b> ';
                        }


                    } else {
						$pr = new Product($item['id_product']);
						$price = Tools::displayPrice($pr->getPrice(true, null, 6));
                        // JP add quantity
                        $qty =  Product::getQuantity($item['id_product']);
                        $results[] = array(
                            'id' => $item['id_product'],
                            'name' => $item['name'] . ' <input type="number" id="qty-product-'.$item['id_product'].'-pack" onchange="sendQty('.$item['id_product'].')" name="pack_products_qty[data]"> / <b>('.$qty.')</b> - ' . $price,
                            'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                            'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], 'home_default')),
                        );
                    }
                } else {
					$pr = new Product($item['id_product']);
					$price = Tools::displayPrice($pr->getPrice(true, null, 6));
                    // JP add quantity
                    $qty =  Product::getQuantity($item['id_product']);
                    $results[] = array(
                        'id' => $item['id_product'],
                        'name' => ''.$item['name'] . '<input type="number" id="qty-product-'.$item['id_product'].'-pack" onchange="sendQty('.$item['id_product'].')" name="pack_products_qty[data]"> / <b>('.$qty.')</b> - ' . $price,
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], 'home_default')),
                    );
                }
            }

            echo Tools::jsonEncode(array_values($results));
			exit();
        }

        echo Tools::jsonEncode([]);
		exit();
    }	
    public function getAttrFromProductId($product_id) {
        $context = Context::getContext();
        $sql = 'SELECT pa.`id_product_attribute` FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
                            WHERE pa.`id_product` = ' . (int) $product_id . '
                            GROUP BY pa.`id_product_attribute`
                            ORDER BY pa.`id_product_attribute`';

        return Db::getInstance()->executeS($sql);
    }
    public function ajaxProcessSavePack()
    {
        try {
			$shop = Context::getContext()->shop;
			if (!Validate::isLoadedObject($shop)) {
				$shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
			}
			//Important to setContext
			Shop::setContext($shop::CONTEXT_SHOP, $shop->id);
			$this->context->shop = $shop;
			$this->context->cookie->id_shop = $shop->id;
			$id_superpack_product = (int)Tools::getValue('id_superpack_product');			
			$id_product = (int)Tools::getValue('id_product');
			$list = ProductPack::getPack($id_product);
			$id_shop = (int)Context::getContext()->shop->id;
			$pack_products = Tools::getValue('pack_products');			
			$pack_products_obj = json_decode(Tools::getValue('pack_products_obj'),true);
			$pack_add_to_all = (int)Tools::getValue('pack_add_to_all');
			$pack_add_to_all_product = (int)Tools::getValue('pack_add_to_all_product');
			$context = Context::getContext();
			if ($pack_add_to_all==1) {
				if (Combination::isFeatureActive()) {
                    $sql = 'SELECT pa.`id_product_attribute` FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
                            WHERE pa.`id_product` = ' . (int) $id_product . '
                            GROUP BY pa.`id_product_attribute`
                            ORDER BY pa.`id_product_attribute`';

                    $combinations = Db::getInstance()->executeS($sql);

                    if (!empty($combinations)) {
//

                        foreach ($combinations as $k => $combination) {
							$pack_products_tab = array();
							$pack_products_tab[] = $id_product . ',' . $combination['id_product_attribute'];
							if (is_array($pack_products)) {
								foreach ($pack_products as $s) {						
									$pack_products_tab[] = $s;
								}
							}
							$pack_products_tab = array_unique($pack_products_tab);
							if (count($pack_products_tab) <= 1) {
								continue;
							}
                            $id_pack_product = $this->addProductPack($pack_products_tab, $id_superpack_product, $id_product, $combination['id_product_attribute'], $pack_add_to_all, $id_shop, $list, $pack_products_obj, ($id_pack_product != 0)?$id_pack_product:0);

						}
					}
					else{
                        // add to pack product combination date without main product

                        if($pack_add_to_all_product == 1) {
                            // sample data: array(2) { [6]=> string(1) "6" ["17,32"]=> string(5) "17,32" }
                            $pack_products_ids_to_combination = [];
                            foreach($pack_products as $product_form_pack) {

                                $pack_products_ids_to_combination[] = (strpos($product_form_pack, ",") === false) ? $product_form_pack : explode(",",$product_form_pack)[0];
                            }

                            foreach($pack_products_ids_to_combination as $prod_id) {

                                $combination_pack_prod = $this->getAttrFromProductId((int)$prod_id);

                                if (!empty($combination_pack_prod)) {
//

                                    foreach ($combination_pack_prod as $index_com => $combination_prod) {
                                        $pack_products_tab_from_pack = array();
                                        $pack_products_tab_from_pack[] = $prod_id . ',' . $combination_prod['id_product_attribute'];
                                        if (is_array($pack_products)) {
                                            foreach ($pack_products as $s) {
                                                $pack_products_tab_from_pack[] = $s;
                                            }
                                        }
                                        $pack_products_tab_from_pack = array_unique($pack_products_tab_from_pack);

//                                        if (count($pack_products_tab_from_pack) <= 1) {
//                                            continue;
//                                        }

                                        $id_pack_product = $this->addProductPack($pack_products_tab_from_pack, $id_superpack_product, $prod_id, $combination_prod['id_product_attribute'], $pack_add_to_all, $id_shop, $list, $pack_products_obj,($id_pack_product != 0)?$id_pack_product:0);

                                    }
                                }
                                else
                                    $pack_add_to_all = 0;

                            }

                        }
                        $pack_add_to_all = 0;
                    }


				}
				else
					$pack_add_to_all = 0;
			}
			
			if ($pack_add_to_all==0) {
				$pack_products_tab = array();
				$id_product_attribute = 0;
				if (Combination::isFeatureActive()) {
                    $sql = 'SELECT pa.`id_product_attribute` FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
                            WHERE pa.`id_product` = ' . (int) $id_product . ' and pa.`default_on` = 1
                            GROUP BY pa.`id_product_attribute`
                            ORDER BY pa.`id_product_attribute`';

                    $combinations = Db::getInstance()->executeS($sql);
                    if (!empty($combinations)) {
                        foreach ($combinations as $k => $combination) {
							$id_product_attribute = (int)$combination['id_product_attribute'];
							break;
						}
					}
				}
				if ($id_product_attribute > 0)					
					$pack_products_tab[] = $id_product . ',' . $id_product_attribute;
				else
					$pack_products_tab[] = $id_product;
				
				if (is_array($pack_products)) {
					foreach ($pack_products as $s) {						
						$pack_products_tab[] = $s;
					}
				}
				
				$pack_products_tab = array_unique($pack_products_tab);
				if (count($pack_products_tab) <= 1) {
					return;
				}
                $id_pack_product = $this->addProductPack($pack_products_tab, $id_superpack_product, $id_product, $id_product_attribute, $pack_add_to_all, $id_shop, $list,$pack_products_obj);
                // Show pack in all products

                if($pack_add_to_all_product != 0) {

                    foreach ($pack_products as $pack_product) {
                        $sql = 'SELECT pa.`id_product_attribute` FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
                            WHERE pa.`id_product` = ' . (int) $id_product . ' and pa.`default_on` = 1
                            GROUP BY pa.`id_product_attribute`
                            ORDER BY pa.`id_product_attribute`';

                        $combinations = Db::getInstance()->executeS($sql);
                        if (!empty($combinations)) {
                            foreach ($combinations as $k => $combination) {
                                $id_product_attribute = (int)$combination['id_product_attribute'];
                                break;
                            }
                        }
                        $id_pack_product = $this->addProductPack($pack_products_tab, $id_superpack_product, $pack_product, $id_product_attribute, $pack_add_to_all, $id_shop, $list,$pack_products_obj, ($id_pack_product != 0)?$id_pack_product:0);
                    }
                }

            }
            $this->ajaxProcessGetPackList();			
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }
//	public function addToAllProductFromPackWithCombination(pack_products_tab, $id_superpack_product, $pack_products, $id_product_attribute, $pack_add_to_all, $id_shop, $list) {
//        foreach ($pack_products as $pack_product) {
//            $this->addProductPack($pack_products_tab, $id_superpack_product, $pack_product, $id_product_attribute, $pack_add_to_all, $id_shop, $list);
//        }
//    }

/*
 *
 * @param $pack_products_tab
 *
 * */
	public function addProductPack($pack_products_tab, $id_superpack_product, $id_product, $id_product_attribute, $pack_add_to_all, $id_shop, $list,$pack_products_obj, $id_product_pack = 0)
	{
		if (version_compare(_PS_VERSION_, 1.7, '<')) {
			$packStockType = 0;
		} else {
			$packStockType = Pack::STOCK_TYPE_PACK_ONLY;
		}
		$context = Context::getContext();
		$id_lang = (int) $context->language->id;

		$phpack = DB::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'phsuperpack_product` WHERE `id_superpack_product` = '.(int)$id_superpack_product);

		if (empty($phpack) ) {
            if($this->currId  != 0){
                DB::getInstance()->insert('phsuperpack_product', array(
                    'id_product' => (int)$id_product,
                    'id_product_pack' => (int)$this->currId ,
                    'id_product_attribute' => (int)$id_product_attribute,
                    'id_shop' => $id_shop,
                    'active' => 1
                ));
//                if(empty($this->errors)) return $this->currId;
                $pr = new Product((int)$this->currId);
            }
			else
                if (Validate::isLoadedObject($pr = new Product($id_product))) {
				$id_product_old = $pr->id;
				if (empty($pr->price) && Shop::getContext() == Shop::CONTEXT_GROUP) {
					$shops = ShopGroup::getShopsFromGroup(Shop::getContextShopGroupID());
					foreach ($shops as $shop) {
						if ($pr->isAssociatedToShop($shop['id_shop'])) {
							//$pr_price = new Product($id_product_old, false, null, $shop['id_shop']);
							//$pr->price = $pr_price->price;
						}
					}
				}
				unset(
					$pr->id,
					$pr->id_product
				);

				$pr->indexed = 0;
				$pr->active = 1;
				$countPack = count($list)+1;
				foreach ($pr->name as $langKey => $oldName) {
					if(!empty(Tools::getValue('pack_name'))) {
						$pr->name[$langKey] = Tools::getValue('pack_name');
					}
					else {
						$pr->name[$langKey] = 'Zestaw ' . $countPack . ' - ' . $oldName;	
					}
				}
				$link_rewrite = Tools::link_rewrite($pr->name[$id_lang]);
				if (!(is_array($pr->link_rewrite) && count($pr->link_rewrite))) {
					$pr->link_rewrite = AdminImportController::createMultiLangField($link_rewrite);
				} else {
					$pr->link_rewrite[(int) $id_lang] = $link_rewrite;
				}

                    $pr->deleteCategories();
                    $pr->id_category_default = (int) Configuration::get('pack_category');
                    $pr->addToCategories([Configuration::get('pack_category')]);

				if ($pr->add()) {
					$this->addProductImage($pr->id, $id_product, 'product_image', false);
					DB::getInstance()->insert('phsuperpack_product', array(						
						'id_product' => (int)$id_product,
						'id_product_pack' => (int)($this->currId  != 0)?$this->currId :$pr->id,
						'id_product_attribute' => (int)$id_product_attribute,
						'id_shop' => $id_shop,
						'active' => 1
					));
                    $this->currId = ($this->currId  != 0)?$this->currId :$pr->id;

//                    if(Configuration::get('pack_category') == 0)
//                        $pr->addToCategories((int)Configuration::get('PH_CATEGORY_PACK_ID'));
//                    else
                        $pr->deleteCategories();
                        $pr->addToCategories([Configuration::get('pack_category')]);
					$pr->pack_stock_type = $packStockType;
					$pr->visibility = 'both';
					$pr->update();
				}
			}
		} else {

			$pr = new Product((int)$phpack['id_product_pack']);

		}
		
		$pack_products_price = (float) (Tools::getValue('pack_products_price'));
		
		$prx = new Product($id_product, true);
		$rate = ($prx->tax_rate / 100) + 1;
					
		if ($rate > 0)
			$pack_products_price = Tools::ps_round($pack_products_price / $rate, 2);
		
		if ($pack_add_to_all==1 && $id_product_attribute > 0) {
			$price2 = 0;
			foreach ($pack_products_tab as $line) {
				if (!empty($line)) {
					
					$tab = explode(",", $line);
					$item_id = (int)$tab[0];
					$item_id_attribute = null;
					if (count($tab) > 1) {
						$item_id_attribute = (int)$tab[1];
					}
					
					if ($item_id > 0) {
						$pr2 = new Product($item_id);
						//$price2 += $pr2->price;
						$price2 += $pr2->getPrice(false, $item_id_attribute, 6);
					}
				}
			}
			
			$pr->price = $price2;
			$pr->update();
		} else if ($pack_products_price > 0) {
			$pr->price = $pack_products_price;
			$pr->update();
		} else {
            $this->errors[] = Tools::displayError('No pack price');
        }
		
		$reduction = (float) (Tools::getValue('pack_products_reduction'));			
		if ((float) $reduction != 0) {
			$id_product_attribute = 0;// Tools::getValue('sp_id_product_attribute');				
			$id_currency = 0;// Tools::getValue('sp_id_currency');
			$id_country = 0;// Tools::getValue('sp_id_country');
			$id_group = 0;// Tools::getValue('sp_id_group');
			$id_customer = 0;// Tools::getValue('sp_id_customer');
			$price = '-1';// 0;// Tools::getValue('leave_bprice');
			$reduction = $reduction;//(float) (Tools::getValue('pack_products_reduction'));
			$reduction_tax = 0;//netto Tools::getValue('sp_reduction_tax');
			$reduction_type = !$reduction ? 'amount' : Tools::getValue('pack_products_reduction_type');
			$reduction_type = $reduction_type == '-' ? 'amount' : $reduction_type;
			$from_quantity = 1;
			
			$from = '0000-00-00 00:00:00';
			$to = '0000-00-00 00:00:00';

			if ($reduction_type == 'percentage' && ((float) $reduction <= 0 || (float) $reduction > 100)) {
				$this->errors[] = Tools::displayError('Submitted reduction value (0-100) is out-of-range');
			} elseif ($this->_validateSpecificPrice($pr->id, $id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $id_product_attribute)) {
				
				SpecificPrice::deleteByProductId((int) $pr->id);
				
				$specificPrice = new SpecificPrice();
				$specificPrice->id_product = (int) $pr->id;
				$specificPrice->id_product_attribute = (int) $id_product_attribute;
				$specificPrice->id_shop = (int) $id_shop;
				$specificPrice->id_currency = (int) ($id_currency);
				$specificPrice->id_country = (int) ($id_country);
				$specificPrice->id_group = (int) ($id_group);
				$specificPrice->id_customer = (int) $id_customer;
				$specificPrice->price = (float) ($price);
				$specificPrice->from_quantity = (int) ($from_quantity);
				$specificPrice->reduction = (float) ($reduction_type == 'percentage' ? $reduction / 100 : $reduction);
				$specificPrice->reduction_tax = $reduction_tax;
				$specificPrice->reduction_type = $reduction_type;
				$specificPrice->from = $from;
				$specificPrice->to = $to;					
				if (!$specificPrice->add()) {
					$this->errors[] = Tools::displayError('An error occurred while updating the specific price.');
				}
			}				
		}
		
		//echo $pr->id;
		Pack::deleteItems($pr->id);
		$pr->setDefaultAttribute(0);
		$packQuantity = 0; $index = -1;
		if (count($pack_products_tab)) {			
			foreach ($pack_products_tab as $line) {


				
				if (!empty($line)) {

					$tab = explode(",", $line);
					$item_id = (int)$tab[0];
					$item_id_attribute = 0;

                    if($pack_products_obj[$item_id] || $pack_products_obj[$tab[0].",".$tab[1]])
                        $qty = ($pack_products_obj[$item_id]) ? $pack_products_obj[$item_id]["quantity"]: $pack_products_obj[$tab[0].",".$tab[1]]["quantity"];
                    else
                        $qty = 1;


					if (count($tab) > 1)
					{
						$item_id_attribute = (int)$tab[1];
					}
					if ($item_id > 0) {
												
						$p = new Product((int)$item_id);
//                        if(Configuration::get('pack_category') != 0)
//                            $p->deleteCategories();
//                            $p->addToCategories([Configuration::get('pack_category')]);
						if (!empty($item_id_attribute)) {
							$minimal_quantity = (int) Attribute::getAttributeMinimalQty($item_id_attribute);
						} else {
							$minimal_quantity = (int) $p->minimal_quantity;
						}
						
						if ($qty < $minimal_quantity) {
							$qty = $minimal_quantity;
						}
						
						if (Pack::isPack((int) $item_id)) {
							$this->errors[] = Tools::displayError('You can\'t add product packs into a pack');
						} elseif (!Pack::addItem((int) $pr->id, (int) $item_id, (int) $qty, (int) $item_id_attribute)) {
							$this->errors[] = Tools::displayError('An error occurred while attempting to add products to the pack.');
						} else {
							$index++;
							if ($item_id_attribute > 0)
								$itemQuantity = Product::getQuantity($item_id, $item_id_attribute);
							else
								$itemQuantity = Product::getQuantity($item_id);
							$nbPackAvailableForItem = floor($itemQuantity / $qty);

							// Initialize packQuantity with the first product quantity
							// if pack decrement stock type is products only
							if ($index === 0
								//&& $packStockType == Pack::STOCK_TYPE_PRODUCTS_ONLY
							) {
								$packQuantity = $nbPackAvailableForItem;
								continue;
							}

							if ($nbPackAvailableForItem < $packQuantity) {
								$packQuantity = $nbPackAvailableForItem;
							}
						}
					}
				}
			}
			StockAvailable::setQuantity($pr->id, 0, (int)$packQuantity);
		}
        if(empty($this->errors)) return $idLastAddedPack;
	}
	
	public function addProductImage($idProduct, $id_product_main, $inputFileName = 'file', $die = true)
    {
        $product = new Product((int) $idProduct);
        $legends = Tools::getValue('legend');

        if (!is_array($legends)) {
            $legends = (array) $legends;
        }

        if (!Validate::isLoadedObject($product)) {
            $files = array();
            $files[0]['error'] = Tools::displayError('Cannot add image because product creation failed.');
        }

		$max_file_size = (int)(Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE') * 1000000);
        $max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');

		/*if (version_compare(_PS_VERSION_, 1.7, '<')) {
			
			$image_uploader = new HelperImageUploader($inputFileName);
			if (isset($_FILES) && count($_FILES) > 0)
			{		
				$image_uploader->setAcceptTypes(array('jpeg', 'gif', 'png', 'jpg'))->setMaxSize($max_image_size);
				$files = $image_uploader->process();

				foreach ($files as &$file) {
					$image = new Image();
					$image->id_product = (int)($product->id);
					$image->position = Image::getHighestPosition($product->id) + 1;

					foreach ($legends as $key => $legend) {
						if (!empty($legend)) {
							$image->legend[(int)$key] = $legend;
						}
					}
				}
			
			}
			
		}		
		else {*/
		
		$image_uploader = new HelperImageUploader($inputFileName);									
		if (isset($_FILES) && count($_FILES) > 0) {
			$image_uploader->setAcceptTypes(array('jpeg', 'gif', 'png', 'jpg'))->setMaxSize($max_image_size);
			$files = $image_uploader->process();
			foreach ($files as &$file) {
				$image = new Image();
				$image->id_product = (int) ($product->id);
				$image->position = Image::getHighestPosition($product->id) + 1;

				foreach ($legends as $key => $legend) {
					if (!empty($legend)) {
						$image->legend[(int) $key] = $legend;
					}
				}

				if (!Image::getCover($image->id_product)) {
					$image->cover = 1;
				} else {
					$image->cover = 0;
				}

				if (($validate = $image->validateFieldsLang(false, true)) !== true) {
					$file['error'] = $validate;
				}

				if (isset($file['error']) && (!is_numeric($file['error']) || $file['error'] != 0)) {
					continue;
				}

				if (!$image->add()) {
					$file['error'] = Tools::displayError('Error while creating additional image');
				} else {
					if (!$new_path = $image->getPathForCreation()) {
						$file['error'] = Tools::displayError('An error occurred while attempting to create a new folder.');

						continue;
					}

					$error = 0;

					if (!ImageManager::resize($file['save_path'], $new_path . '.' . $image->image_format, null, null, 'jpg', false, $error)) {
						switch ($error) {
							case ImageManager::ERROR_FILE_NOT_EXIST:
								$file['error'] = Tools::displayError('An error occurred while copying image, the file does not exist anymore.');

								break;

							case ImageManager::ERROR_FILE_WIDTH:
								$file['error'] = Tools::displayError('An error occurred while copying image, the file width is 0px.');

								break;

							case ImageManager::ERROR_MEMORY_LIMIT:
								$file['error'] = Tools::displayError('An error occurred while copying image, check your memory limit.');

								break;

							default:
								$file['error'] = Tools::displayError('An error occurred while copying the image.');

								break;
						}

						continue;
					} else {
						$imagesTypes = ImageType::getImagesTypes('products');
						$generate_hight_dpi_images = (bool) Configuration::get('PS_HIGHT_DPI');

						foreach ($imagesTypes as $imageType) {
							if (!ImageManager::resize($file['save_path'], $new_path . '-' . stripslashes($imageType['name']) . '.' . $image->image_format, $imageType['width'], $imageType['height'], $image->image_format)) {
								$file['error'] = Tools::displayError('An error occurred while copying this image:') . ' ' . stripslashes($imageType['name']);

								continue;
							}

							if ($generate_hight_dpi_images) {
								if (!ImageManager::resize($file['save_path'], $new_path . '-' . stripslashes($imageType['name']) . '2x.' . $image->image_format, (int) $imageType['width'] * 2, (int) $imageType['height'] * 2, $image->image_format)) {
									$file['error'] = Tools::displayError('An error occurred while copying this image:') . ' ' . stripslashes($imageType['name']);

									continue;
								}
							}
						}
					}

					unlink($file['save_path']);
					//Necesary to prevent hacking
					unset($file['save_path']);
					Hook::exec('actionWatermark', array('id_image' => $image->id, 'id_product' => $product->id));

					if (!$image->update()) {
						$file['error'] = Tools::displayError('Error while updating the status.');

						continue;
					}

					// Associate image to shop from context
					$shops = Shop::getContextListShopID();
					$image->associateTo($shops);
					$json_shops = array();

					foreach ($shops as $id_shop) {
						$json_shops[$id_shop] = true;
					}

					$file['status'] = 'ok';
					$file['id'] = $image->id;
					$file['position'] = $image->position;
					$file['cover'] = $image->cover;
					$file['legend'] = $image->legend;
					$file['path'] = $image->getExistingImgPath();
					$file['shops'] = $json_shops;

					@unlink(_PS_TMP_IMG_DIR_ . 'product_' . (int) $product->id . '.jpg');
					@unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $product->id . '_' . $this->context->shop->id . '.jpg');
				}
			}
		}
		else {
			$product_image_value = Configuration::get('PH_BANNER_IMG');
			$default_icon = _PS_MODULE_DIR_ .  'phsuperpack/img/'.$product_image_value;
			$file = array();
			if (!empty($product_image_value) && file_exists($default_icon)) {
				$file['save_path'] = $default_icon;
				$file['error'] = '';

				$image = new Image();
				$image->id_product = (int)($product->id);
				$image->position = Image::getHighestPosition($product->id) + 1;

				if (!Image::getCover($image->id_product)) {
					$image->cover = 1;
				} else {
					$image->cover = 0;
				}

				if (($validate = $image->validateFieldsLang(false, true)) !== true) {
					$file['error'] = $validate;
				}

				if (!$image->add()) {
					$file['error'] = Tools::displayError('Error while creating additional image');
				} else {
					if (!$new_path = $image->getPathForCreation()) {
						$file['error'] = Tools::displayError('An error occurred while attempting to create a new folder.');
					}

					$error = 0;
					if (!ImageManager::resize($file['save_path'], $new_path . '.' . $image->image_format, null, null, 'jpg', false, $error)) {
						switch ($error) {
							case ImageManager::ERROR_FILE_NOT_EXIST:
								$file['error'] = Tools::displayError('An error occurred while copying image, the file does not exist anymore.');
								break;
							case ImageManager::ERROR_FILE_WIDTH:
								$file['error'] = Tools::displayError('An error occurred while copying image, the file width is 0px.');
								break;
							case ImageManager::ERROR_MEMORY_LIMIT:
								$file['error'] = Tools::displayError('An error occurred while copying image, check your memory limit.');
								break;
							default:
								$file['error'] = Tools::displayError('An error occurred while copying the image.');
								break;
						}
					} else {
						$imagesTypes = ImageType::getImagesTypes('products');
						$generate_hight_dpi_images = (bool)Configuration::get('PS_HIGHT_DPI');

						foreach ($imagesTypes as $imageType) {
							if (!ImageManager::resize($file['save_path'], $new_path . '-' . stripslashes($imageType['name']) . '.' . $image->image_format, $imageType['width'], $imageType['height'], $image->image_format)) {
								$file['error'] = Tools::displayError('An error occurred while copying this image:') . ' ' . stripslashes($imageType['name']);
							}

							if ($generate_hight_dpi_images) {
								if (!ImageManager::resize($file['save_path'], $new_path . '-' . stripslashes($imageType['name']) . '2x.' . $image->image_format, (int)$imageType['width'] * 2, (int)$imageType['height'] * 2, $image->image_format)) {
									$file['error'] = Tools::displayError('An error occurred while copying this image:') . ' ' . stripslashes($imageType['name']);
								}
							}
						}
					}

					//unlink($file['save_path']);
					//Necesary to prevent hacking
					unset($file['save_path']);
					Hook::exec('actionWatermark', array('id_image' => $image->id, 'id_product' => $product->id));

					if (!$image->update()) {
						$file['error'] = Tools::displayError('Error while updating the status.');
					}

					// Associate image to shop from context
					$shops = Shop::getContextListShopID();
					$image->associateTo($shops);
					$json_shops = array();

					foreach ($shops as $id_shop) {
						$json_shops[$id_shop] = true;
					}

					$file['status'] = 'ok';
					$file['id'] = $image->id;
					$file['position'] = $image->position;
					$file['cover'] = $image->cover;
					$file['legend'] = $image->legend;
					$file['path'] = $image->getExistingImgPath();
					$file['shops'] = $json_shops;

					@unlink(_PS_TMP_IMG_DIR_ . 'product_' . (int)$product->id . '.jpg');
					@unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$product->id . '_' . $this->context->shop->id . '.jpg');

					$files = array();
					$files[] = $file;
				}
			}
			else {
				$id_image = Image::getCover($id_product_main)['id_image'];
				$image_url = 'https://'. Configuration::get('PS_SHOP_DOMAIN_SSL') . __PS_BASE_URI__ . $id_image . '-home_default/' . $product->link_rewrite[Context::getContext()->language->id] . '.jpg';
	
				$image = new Image();
				$image->id_product = $product->id;
				$image->cover = 1;
				$shops = Shop::getContextListShopID();
				$image->associateTo($shops);
				$image->position = Image::getHighestPosition($product->id) + 1;
				$image->add();
	
				$path = $this->getFullLink($image->id);
	
				$this->uploadImages($path, $image->id, $image_url);
			}
		}
		
		if ($die) {
			die(json_encode(array($image_uploader->getName() => $files)));
		} else {
			return $files;
		}
		/*
		}
		return false;        */
    }

	/**
	 * Get full link to created image
	 */
	private function getFullLink($image_id)
	{
		$link = _PS_PROD_IMG_DIR_;
		$explode = str_split((string)$image_id);

		for($i=0; $i<sizeof($explode); $i++) {
			$link .= $explode[$i].'/';
		}

		mkdir($link, 0755, true);

		return $link;
	}

	private function uploadImages($path, $image_id, $image_url)
	{
		$imageTypes = ImageType::getImagesTypes();
		foreach($imageTypes as $type) {
			if($type['products'] == 1) {
				$filename = $image_id.'-'.$type['name'].'.jpg';
				
				if (!file_put_contents($path.$filename, Tools::file_get_contents($image_url))) {
					$image->delete();
					return false;
				}
			}
		}

		if (!file_put_contents($path.$image_id.'.jpg', Tools::file_get_contents($image_url))) {
			$image->delete();
			return false;
		}

		return true;
	}
	
	protected function _validateSpecificPrice($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $id_combination = 0)
    {
        if (!Validate::isUnsignedId($id_shop) || !Validate::isUnsignedId($id_currency) || !Validate::isUnsignedId($id_country) || !Validate::isUnsignedId($id_group) || !Validate::isUnsignedId($id_customer)) {
            $this->errors[] = Tools::displayError('Wrong IDs');
        } elseif ((!isset($price) && !isset($reduction)) || (isset($price) && !Validate::isNegativePrice($price)) || (isset($reduction) && !Validate::isPrice($reduction))) {
            $this->errors[] = Tools::displayError('Invalid price/discount amount');
        } elseif (!Validate::isUnsignedInt($from_quantity)) {
            $this->errors[] = Tools::displayError('Invalid quantity');
        } elseif ($reduction && !Validate::isReductionType($reduction_type)) {
            $this->errors[] = Tools::displayError('Please select a discount type (amount or percentage).');
        } elseif ($from && $to && (!Validate::isDateFormat($from) || !Validate::isDateFormat($to))) {
            $this->errors[] = Tools::displayError('The from/to date is invalid.');
        }
//        elseif (SpecificPrice::exists($id_product, $id_combination, $id_shop, $id_group, $id_country, $id_currency, $id_customer, $from_quantity, $from, $to, false)) {
//            $this->errors[] = Tools::displayError('A specific price already exists for these parameters.');
//        }
        else {
            return true;
        }

        return false;
    }
}
