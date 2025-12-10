<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA Miłosz Myszczuk VATEU: PL9730945634
 * @copyright 2010-2022 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class Link extends LinkCore
{
    public function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp = $explode = explode(".", $version);
        if ($part == 1) {
            return $exp[1];
        }
        if ($part == 2) {
            return $exp[2];
        }
        if ($part == 3) {
            return $exp[3];
        }
    }

    public function getParentsCategories($idLang = null, $id)
    {
        $context = Context::getContext()->cloneContext();
        $context->shop = clone($context->shop);

        if (is_null($idLang)) {
            $idLang = $context->language->id;
        }

        $categories = null;
        $idCurrent = $id;
        if (count(Category::getCategoriesWithoutParent()) > 1 && Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && count(Shop::getShops(true, null, true)) != 1) {
            $context->shop->id_category = (int)Configuration::get('PS_ROOT_CATEGORY');
        } elseif (!$context->shop->id) {
            $context->shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
        }
        $idShop = $context->shop->id;
        while (true) {
            $sql = '
			SELECT c.*, cl.*
			FROM `' . _DB_PREFIX_ . 'category` c
			LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
				ON (c.`id_category` = cl.`id_category`
				AND `id_lang` = ' . (int)$idLang . Shop::addSqlRestrictionOnLang('cl') . ')';
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (c.`id_category` = cs.`id_category` AND cs.`id_shop` = ' . (int)$idShop . ')';
            }
            $sql .= ' WHERE c.`id_category` = ' . (int)$idCurrent;
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sql .= ' AND cs.`id_shop` = ' . (int)$context->shop->id;
            }
            $rootCategory = Category::getRootCategory();
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP
                && (!Tools::isSubmit('id_category') || (int)Tools::getValue('id_category') == (int)$rootCategory->id || (int)$rootCategory->id == (int)$context->shop->id_category)) {
                $sql .= ' AND c.`id_parent` != 0';
            }

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

            if ($result) {
                $categories[] = $result;
            } elseif (!$categories) {
                $categories = array();
            }
            if (!$result || ($result['id_category'] == $context->shop->id_category)) {
                return $categories;
            }
            $idCurrent = $result['id_parent'];
        }
    }

    public function getCategoryLink($category, $alias = null, $id_lang = null, $selected_filters = null, $id_shop = null, $relative_protocol = false)
    {
        if (!$id_lang) {
            $idLang = Context::getContext()->language->id;
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        if (Configuration::get('purls_categories') != 1) {
            return parent::getCategoryLink($category, $alias, $id_lang, $selected_filters, $id_shop, $relative_protocol);
        }

        if (Configuration::get('purls_categories') == 1) {
            $dispatcher = Dispatcher::getInstance();
            if (!$id_lang) {
                $id_lang = Context::getContext()->language->id;
            }
            $url = $this->getBaseLink($id_shop, null, $relative_protocol) . $this->getLangLink($id_lang, null, $id_shop);
            if (!is_object($category)) {
                $category = new Category($category, $id_lang);
            }
            $params = array();
            $params['id'] = $category->id;
            $params['rewrite'] = (!$alias) ? $category->link_rewrite : $alias;
            if ($this->psversion() == 6 || $this->psversion() == 7) {
                $params['meta_keywords'] = Tools::str2url($category->getFieldByLang('meta_keywords'));
                $params['meta_title'] = Tools::str2url($category->getFieldByLang('meta_title'));
            } else {
                $params['meta_keywords'] = Tools::str2url($category->meta_keywords);
                $params['meta_title'] = Tools::str2url($category->meta_title);
            }
            $selected_filters = is_null($selected_filters) ? '' : $selected_filters;
            if (empty($selected_filters)) {
                $rule = 'category_rule';
            } else {
                $rule = 'layered_rule';
                $params['selected_filters'] = $selected_filters;
            }
            if ($dispatcher->hasKeyword('category_rule', $id_lang, 'parent_categories')) {
                $cats = array();
                foreach ($this->getParentsCategories($id_lang, $category->id) as $cat) {
                    if (!in_array($cat['id_category'], array(
                        1,
                        2,
                        $category->id
                    ))
                    ) {
                        $cats[] = $cat['link_rewrite'];
                    }
                }
                $params['parent_categories'] = implode('/', array_reverse($cats));
            }
            return $url . Dispatcher::getInstance()->createUrl($rule, $id_lang, $params, $this->allow);
        } else {
            if (!$id_lang) {
                $id_lang = Context::getContext()->language->id;
            }
            $url = $this->getBaseLink($id_shop, null, $relative_protocol) . $this->getLangLink($id_lang, null, $id_shop);
            if (!is_object($category)) {
                $category = new Category($category, $id_lang);
            }
            $params = array();
            $params['id'] = $category->id;
            $params['rewrite'] = (!$alias) ? $category->link_rewrite : $alias;
            $params['meta_keywords'] = Tools::str2url($category->getFieldByLang('meta_keywords'));
            $params['meta_title'] = Tools::str2url($category->getFieldByLang('meta_title'));
            $selected_filters = is_null($selected_filters) ? '' : $selected_filters;
            if (empty($selected_filters)) {
                $rule = 'category_rule';
            } else {
                $rule = 'layered_rule';
                $params['selected_filters'] = $selected_filters;
            }
            return $url . Dispatcher::getInstance()->createUrl($rule, $id_lang, $params, $this->allow, '', $id_shop);
        }
    }

    public function getProductLink($product, $alias = null, $category = null, $ean13 = null, $idLang = null, $idShop = null, $ipa = NULL, $force_routes = false, $relativeProtocol = false, $withIdInAnchor = false, $extraParams = array(), $addAnchor = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        if (!in_array(Configuration::get('purls_products'), array(1,2,3))) {
            return parent::getProductLink($product, $alias, $category, $ean13, $idLang, $idShop, $ipa, $force_routes, $relativeProtocol, $addAnchor, $extraParams);
        }

        $dispatcher = Dispatcher::getInstance();

        $url = $this->getBaseLink($idShop, null, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop);
        $params = array();
        if (!is_object($product)) {
            if (is_array($product) && isset($product['id_product'])) {
                $params['id'] = $product['id_product'];
            } elseif ((int)$product) {
                $params['id'] = $product;
            } else {
                throw new PrestaShopException('Invalid product vars');
            }
        } else {
            $params['id'] = $product->id;
        }
        $params['id_product_attribute'] = $ipa;
        if (!$alias) {
            $product = $this->getProductObject($product, $idLang, $idShop);
        }
        $params['rewrite'] = (!$alias) ? $product->getFieldByLang('link_rewrite') : $alias;
        if (!$ean13) {
            $product = $this->getProductObject($product, $idLang, $idShop);
        }
        $params['ean13'] = (!$ean13) ? $product->ean13 : $ean13;
        if ($dispatcher->hasKeyword('product_rule', $idLang, 'meta_keywords', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['meta_keywords'] = Tools::str2url($product->getFieldByLang('meta_keywords'));
        }
        if ($dispatcher->hasKeyword('product_rule', $idLang, 'meta_title', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['meta_title'] = Tools::str2url($product->getFieldByLang('meta_title'));
        }
        if ($dispatcher->hasKeyword('product_rule', $idLang, 'manufacturer', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['manufacturer'] = Tools::str2url($product->isFullyLoaded ? $product->manufacturer_name : Manufacturer::getNameById($product->id_manufacturer));
        }
        if ($dispatcher->hasKeyword('product_rule', $idLang, 'supplier', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['supplier'] = Tools::str2url($product->isFullyLoaded ? $product->supplier_name : Supplier::getNameById($product->id_supplier));
        }
        if ($dispatcher->hasKeyword('product_rule', $idLang, 'price', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['price'] = $product->isFullyLoaded ? $product->price : Product::getPriceStatic($product->id, false, null, 6, null, false, true, 1, false, null, null, null, $product->specificPrice);
        }
        if ($dispatcher->hasKeyword('product_rule', $idLang, 'tags', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['tags'] = Tools::str2url($product->getTags($idLang));
        }
        if ($dispatcher->hasKeyword('product_rule', $idLang, 'category', $idShop)) {
            if (!$category) {
                $product = $this->getProductObject($product, $idLang, $idShop);
            }
            $params['category'] = (!$category) ? $product->category : $category;
        }
        if ($dispatcher->hasKeyword('product_rule', $idLang, 'reference', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['reference'] = Tools::str2url($product->reference);
        }
        if ($dispatcher->hasKeyword('product_rule', $idLang, 'categories', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['category'] = (!$category) ? $product->category : $category;
            $cats = array();
            foreach ($product->getParentCategories($idLang) as $cat) {
                if (!in_array($cat['id_category'], Link::$category_disable_rewrite)) {
                    $cats[] = $cat['link_rewrite'];
                }
            }
            $params['categories'] = implode('/', $cats);
        }
        if ($ipa) {
            $product = $this->getProductObject($product, $idLang, $idShop);
        }
        $anchor = $ipa ? $product->getAnchor((int)$ipa, false) : '';
        return strtok($url . $dispatcher->createUrl('product_rule', $idLang, array_merge($params, $extraParams), $force_routes, $anchor, $idShop), (Configuration::get('purls_product_hash') == 0 && (Configuration::get('purls_products') == 1 || Configuration::get('purls_products') == 2) ? '':'#'));
    }

    public function getSupplierLink($supplier, $alias = null, $idLang = null, $idShop = null, $relativeProtocol = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        if (Configuration::get('purls_suppliers') != 1) {
            return parent::getSupplierLink($supplier, $alias, $idLang, $idShop, $relativeProtocol);
        }

        $url = $this->getBaseLink($idShop, null, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop);

        $dispatcher = Dispatcher::getInstance();
        if (!is_object($supplier)) {
            if ($alias !== null &&
                !$dispatcher->hasKeyword('supplier_rule_' . $idLang, $idLang, 'meta_keywords', $idShop) &&
                !$dispatcher->hasKeyword('supplier_rule_' . $idLang, $idLang, 'meta_title', $idShop)
            ) {
                return $url . $dispatcher->createUrl('supplier_rule_' . $idLang, $idLang, ['id' => (int)$supplier, 'rewrite' => (string)$alias], $this->allow, '', $idShop);
            }
            $supplier = new Supplier($supplier, $idLang);
        }

        // Set available keywords
        $params = [];
        $params['id'] = $supplier->id;
        $params['rewrite'] = (!$alias) ? $supplier->link_rewrite : $alias;
        $params['meta_keywords'] = Tools::str2url($supplier->meta_keywords);
        $params['meta_title'] = Tools::str2url($supplier->meta_title);

        return $url . $dispatcher->createUrl('supplier_rule_' . $idLang, $idLang, $params, $this->allow, '', $idShop);
    }

    public function getCMSLink($cms, $alias = null, $ssl = null, $idLang = null, $idShop = null, $relativeProtocol = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        if (Configuration::get('purls_cms') != 1) {
            return parent::getCmsLink($cms, $alias, $ssl, $idLang, $idShop, $relativeProtocol);
        }

        $url = $this->getBaseLink($idShop, $ssl, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop);

        $dispatcher = Dispatcher::getInstance();
        if (!is_object($cms)) {
            if ($alias !== null && !$dispatcher->hasKeyword('cms_rule_' . $idLang, $idLang, 'meta_keywords', $idShop) && !$dispatcher->hasKeyword('cms_rule_' . $idLang, $idLang, 'meta_title', $idShop)) {
                return $url . $dispatcher->createUrl('cms_rule_' . $idLang, $idLang, ['id' => (int)$cms, 'rewrite' => (string)$alias], $this->allow, '', $idShop);
            }
            $cms = new CMS($cms, $idLang);
        }

        $cmsCat = new CMSCategoryCore($cms->id_cms_category, $idLang);


        // Set available keywords
        $params = [];
        $params['id'] = $cms->id;
        $params['rewrite'] = (!$alias) ? (is_array($cms->link_rewrite) ? $cms->link_rewrite[(int)$idLang] : $cms->link_rewrite) : $alias;

        $params['meta_keywords'] = '';
        if (isset($cms->meta_keywords) && !empty($cms->meta_keywords)) {
            $params['meta_keywords'] = is_array($cms->meta_keywords) ? Tools::str2url($cms->meta_keywords[(int)$idLang]) : Tools::str2url($cms->meta_keywords);
        }

        $params['meta_title'] = '';
        if (isset($cms->meta_title) && !empty($cms->meta_title)) {
            $params['meta_title'] = is_array($cms->meta_title) ? Tools::str2url($cms->meta_title[(int)$idLang]) : Tools::str2url($cms->meta_title);
        }

        if ($dispatcher->hasKeyword('cms_rule_' . $idLang, $idLang, 'categories')) {
            $params['categories'] = $cmsCat->link_rewrite;
        }

        return $url . $dispatcher->createUrl('cms_rule_' . $idLang, $idLang, $params, $this->allow, '', $idShop);
    }

    public function getCMSCategoryLink($cmsCategory, $alias = null, $idLang = null, $idShop = null, $relativeProtocol = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        if (Configuration::get('purls_cms') != 1) {
            return parent::getCMSCategoryLink($cmsCategory, $alias, $idLang, $idShop, $relativeProtocol);
        }

        $url = $this->getBaseLink($idShop, null, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop);

        $dispatcher = Dispatcher::getInstance();
        if (!is_object($cmsCategory)) {
            if ($alias !== null && !$dispatcher->hasKeyword('cms_category_rule_' . $idLang, $idLang, 'meta_keywords', $idShop) && !$dispatcher->hasKeyword('cms_category_rule', $idLang, 'meta_title', $idShop)) {
                return $url . $dispatcher->createUrl('cms_category_rule_' . $idLang, $idLang, ['id' => (int)$cmsCategory, 'rewrite' => (string)$alias], $this->allow, '', $idShop);
            }
            $cmsCategory = new CMSCategory($cmsCategory, $idLang);
        }
        if (is_array($cmsCategory->link_rewrite) && isset($cmsCategory->link_rewrite[(int)$idLang])) {
            $cmsCategory->link_rewrite = $cmsCategory->link_rewrite[(int)$idLang];
        }
        if (is_array($cmsCategory->meta_keywords) && isset($cmsCategory->meta_keywords[(int)$idLang])) {
            $cmsCategory->meta_keywords = $cmsCategory->meta_keywords[(int)$idLang];
        }
        if (is_array($cmsCategory->meta_title) && isset($cmsCategory->meta_title[(int)$idLang])) {
            $cmsCategory->meta_title = $cmsCategory->meta_title[(int)$idLang];
        }

        // Set available keywords
        $params = [];
        $params['id'] = $cmsCategory->id;
        $params['rewrite'] = (!$alias) ? $cmsCategory->link_rewrite : $alias;
        $params['meta_keywords'] = Tools::str2url($cmsCategory->meta_keywords);
        $params['meta_title'] = Tools::str2url($cmsCategory->meta_title);

        return $url . $dispatcher->createUrl('cms_category_rule_' . $idLang, $idLang, $params, $this->allow, '', $idShop);
    }

    public function getManufacturerLink($manufacturer, $alias = null, $idLang = null, $idShop = null, $relativeProtocol = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        if (Configuration::get('purls_manufacturers') != 1) {
            return parent::getManufacturerLink($manufacturer, $alias, $idLang, $idShop, $relativeProtocol);
        }

        $url = $this->getBaseLink($idShop, null, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop);

        $dispatcher = Dispatcher::getInstance();
        if (!is_object($manufacturer)) {
            if ($alias !== null && !$dispatcher->hasKeyword('manufacturer_rule_' . $idLang, $idLang, 'meta_keywords', $idShop) && !$dispatcher->hasKeyword('manufacturer_rule', $idLang, 'meta_title', $idShop)) {
                return $url . $dispatcher->createUrl('manufacturer_rule_' . $idLang, $idLang, ['id' => (int)$manufacturer, 'rewrite' => (string)$alias], $this->allow, '', $idShop);
            }
            $manufacturer = new Manufacturer($manufacturer, $idLang);
        }

        // Set available keywords
        $params = [];
        $params['id'] = $manufacturer->id;
        $params['rewrite'] = (!$alias) ? $manufacturer->link_rewrite : $alias;
        $params['meta_keywords'] = Tools::str2url($manufacturer->meta_keywords);
        $params['meta_title'] = Tools::str2url($manufacturer->meta_title);

        return $url . $dispatcher->createUrl('manufacturer_rule_' . $idLang, $idLang, $params, $this->allow, '', $idShop);
    }

    public function getPaginationLink($type, $id_object, $nb = false, $sort = false, $pagination = false, $array = false)
    {
        if (!$type && !$id_object) {
            $method_name = 'get' . Dispatcher::getInstance()->getController() . 'Link';
            if (method_exists($this, $method_name) && isset($_GET['id_' . Dispatcher::getInstance()->getController()])) {
                $type = Dispatcher::getInstance()->getController();
                $id_object = $_GET['id_' . $type];
            }
        }
        if ($type && $id_object) {
            $url = $this->{'get' . $type . 'Link'}($id_object, null);
        } else {
            if (isset(Context::getContext()->controller->php_self)) {
                $name = Context::getContext()->controller->php_self;
            } else {
                $name = Dispatcher::getInstance()->getController();
            }
            $url = $this->getPageLink($name);
        }
        $vars = array();
        $vars_nb = array(
            'n',
            'search_query'
        );
        $vars_sort = array(
            'orderby',
            'orderway'
        );
        $vars_pagination = array('p');
        foreach ($_GET as $k => $value) {
            if ($k != 'id_' . $type && $k != $type . '_rewrite' && $k != 'controller') {
                if (Configuration::get('PS_REWRITING_SETTINGS') && ($k == 'isolang' || $k == 'id_lang')) {
                    continue;
                }
                $if_nb = (!$nb || ($nb && !in_array($k, $vars_nb)));
                $if_sort = (!$sort || ($sort && !in_array($k, $vars_sort)));
                $if_pagination = (!$pagination || ($pagination && !in_array($k, $vars_pagination)));
                if ($if_nb && $if_sort && $if_pagination) {
                    if (!is_array($value)) {
                        $vars[urlencode($k)] = $value;
                    } else {
                        foreach (explode('&', http_build_query(array($k => $value), '', '&')) as $key => $val) {
                            $data = explode('=', $val);
                            $vars[urldecode($data[0])] = $data[1];
                        }
                    }
                }
            }
        }
        if (!$array) {
            if (count($vars)) {
                return $url . (($this->allow == 1 || $url == $this->url) ? '?' : '&') . http_build_query($vars, '', '&');
            } else {
                return $url;
            }
        }
        $vars['requestUrl'] = $url;
        if ($type && $id_object) {
            $vars['id_' . $type] = (is_object($id_object) ? (int)$id_object->id : (int)$id_object);
        }
        if (!$this->allow == 1) {
            $vars['controller'] = Dispatcher::getInstance()->getController();
        }
        return $vars;
    }
}