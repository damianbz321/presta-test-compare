<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA Mi³osz Myszczuk VATEU: PL9730945634
 * @copyright 2010-2022 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Dispatcher extends DispatcherCore
{
    private function buildRequestUri($requestUri, $isMultiLanguageActivated, Shop $shop = null)
    {
        // Decode raw request URI
        $requestUri = rawurldecode($requestUri);

        // Remove the shop base URI part from the request URI
        if (null !== $shop) {
            $requestUri = preg_replace(
                '#^' . preg_quote($shop->getBaseURI(), '#') . '#i',
                '/',
                $requestUri
            );
        }

        // If there are several languages, set $_GET['isolang'] and remove the language part from the request URI
        if (
            $this->use_routes &&
            $isMultiLanguageActivated &&
            preg_match('#^/([a-z]{2})(?:/.*)?$#', $requestUri, $matches)
        ) {
            $_GET['isolang'] = $matches[1];
            $requestUri = substr($requestUri, 3);
        }

        return $requestUri;
    }

    private function getRequest()
    {
        return $this->request;
    }

    private function setRequest(SymfonyRequest $request = null)
    {
        if (null === $request) {
            $request = SymfonyRequest::createFromGlobals();
        }

        $this->request = $request;
    }

    protected function setRequestUri()
    {
        $shop = Context::getContext()->shop;
        if (!Validate::isLoadedObject($shop)) {
            $shop = null;
        }

        $this->request_uri = $this->buildRequestUri(
            $this->getRequest()->getRequestUri(),
            Language::isMultiLanguageActivated(),
            $shop
        );
    }

    public static function psversion($part = 1)
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

    protected function __construct(SymfonyRequest $request = null)
    {
        $this->setRequest($request);

        $this->original_dispatcher_routes = $this->default_routes;
        $this->use_routes = (bool)Configuration::get('PS_REWRITING_SETTINGS');
        if (defined('_PS_ADMIN_DIR_')) {
            $this->front_controller = self::FC_ADMIN;
            $this->controller_not_found = 'adminnotfound';
        } elseif (Tools::getValue('fc') == 'module') {
            $this->front_controller = self::FC_MODULE;
            $this->controller_not_found = 'pagenotfound';
        } else {
            $this->front_controller = self::FC_FRONT;
            $this->controller_not_found = 'pagenotfound';
        }
        $this->setRequestUri();
        if (in_array($this->front_controller, array(
            self::FC_FRONT,
            self::FC_MODULE
        ))) {
            Tools::switchLanguage();
        }
        if (Language::isMultiLanguageActivated()) {
            $this->multilang_activated = true;
        }
        if (Configuration::get('purls_suppliers') == 1) {
            $this->default_routes['supplier_rule'] = array(
                'controller' => 'supplier',
                'rule' => 'supplier/{rewrite}/',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+'),
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'supplier_rewrite'
                    ),
                    'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
            );
            foreach (Language::getLanguages(false) AS $lang) {
                $this->default_routes['supplier_rule_'.$lang['id_lang']] = array(
                    'controller' => 'supplier',
                    'rule' => Configuration::get('PURLS_ROUTE_supplier_rule', $lang['id_lang']),
                    'keywords' => array(
                        'id' => array('regexp' => '[0-9]+'),
                        'rewrite' => array(
                            'regexp' => '[_a-zA-Z0-9-\pL]*',
                            'param' => 'supplier_rewrite'
                        ),
                        'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    ),
                );
            }
        }
        if (Configuration::get('purls_manufacturers') == 1) {
            $this->default_routes['manufacturer_rule'] = array(
                'controller' => 'manufacturer',
                'rule' => 'manufacturer/{rewrite}/',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+'),
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'manufacturer_rewrite'
                    ),
                    'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
            );
            foreach (Language::getLanguages(false) AS $lang) {
                $this->default_routes['manufacturer_rule_'.$lang['id_lang']] = array(
                    'controller' => 'manufacturer',
                    'rule' => Configuration::get('PURLS_ROUTE_manufacturer_rule', $lang['id_lang']),
                    'keywords' => array(
                        'id' => array('regexp' => '[0-9]+'),
                        'rewrite' => array(
                            'regexp' => '[_a-zA-Z0-9-\pL]*',
                            'param' => 'manufacturer_rewrite'
                        ),
                        'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    ),
                );
            }
        }
        if (Configuration::get('purls_cms') == 1) {
            $this->default_routes['cms_rule'] = array(
                'controller' => 'cms',
                'rule' => 'info/{rewrite}.html',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+'),
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'cms_rewrite'
                    ),
                    'categories' => array('regexp' => '[/_a-zA-Z0-9-\pL]*'),
                    'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
            );
            $this->default_routes['cms_category_rule'] = array(
                'controller' => 'cms',
                'rule' => 'info/{rewrite}',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+'),
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'cms_category_rewrite'
                    ),
                    'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
            );
            foreach (Language::getLanguages(false) AS $lang) {
                $this->default_routes['cms_rule_'.$lang['id_lang']] = array(
                    'controller' => 'cms',
                    'rule' => Configuration::get('PURLS_ROUTE_cms_rule', $lang['id_lang']),
                    'keywords' => array(
                        'id' => array('regexp' => '[0-9]+'),
                        'rewrite' => array(
                            'regexp' => '[_a-zA-Z0-9-\pL]*',
                            'param' => 'cms_rewrite'
                        ),
                        'categories' => array('regexp' => '[/_a-zA-Z0-9-\pL]*'),
                        'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    ),
                );
                $this->default_routes['cms_category_rule_'.$lang['id_lang']] = array(
                    'controller' => 'cms',
                    'rule' =>  Configuration::get('PURLS_ROUTE_cms_category_rule', $lang['id_lang']),
                    'keywords' => array(
                        'id' => array('regexp' => '[0-9]+'),
                        'rewrite' => array(
                            'regexp' => '[_a-zA-Z0-9-\pL]*',
                            'param' => 'cms_category_rewrite'
                        ),
                        'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    ),
                );
            }
        }

        if (Configuration::get('purls_products') == 1) {
            $this->default_routes['product_rule'] = array(
                'controller' => 'product',
                'rule' => '{category:/}{rewrite}.html',
                'keywords' => array(

                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9\pL\pS-]*',
                        'param' => 'product_rewrite'
                    ),
                    'id_product_attribute' => array('regexp' => '[0-9]+'),
                    'id' => array('regexp' => '[0-9]+'),
                    'ean13' => array('regexp' => '[0-9\pL]*'),
                    'category' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'rewrite' => array('regexp' => '[/_a-zA-Z0-9-\pL]*'),
                    'categories' => array('regexp' => '[/_a-zA-Z0-9-\pL]*'),
                    'reference' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'manufacturer' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'supplier' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'price' => array('regexp' => '[0-9\.,]*'),
                    'tags' => array('regexp' => '[a-zA-Z0-9-\pL]*'),
                ),
            );
        }

        if (Configuration::get('purls_products') == 2) {
            $this->default_routes['product_rule'] = array(
                'controller' => 'product',
                'rule' => '{category:/}{id}-{rewrite}.html',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+', 'param' => 'id_product'),
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9\pL\pS-]*',
                        'param' => 'product_rewrite'
                    ),
                    'ean13' => array('regexp' => '[0-9\pL]*'),
                    'id_product_attribute' => array('regexp' => '[0-9]+'),
                    'category' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'categories' => array('regexp' => '[/_a-zA-Z0-9-\pL]*'),
                    'rewrite' => array('regexp' => '[/_a-zA-Z0-9-\pL]*'),
                    'reference' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'manufacturer' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'supplier' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'price' => array('regexp' => '[0-9\.,]*'),
                    'tags' => array('regexp' => '[a-zA-Z0-9-\pL]*'),
                ),
            );
        }

        if (Configuration::get('purls_products') == 3) {
            $this->default_routes['product_rule'] = array(
                'controller' => 'product',
                'rule' => '{category:/}{id_product_attribute:-}{rewrite}.html',
                'keywords' => array(
                    'id_product_attribute' => array('regexp' => '[0-9]+', 'param' => 'id_product_attribute'),
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9\pL\pS-]*',
                        'param' => 'product_rewrite'
                    ),
                    'id' => array('regexp' => '[0-9]+'),
                    'ean13' => array('regexp' => '[0-9\pL]*'),
                    'category' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'categories' => array('regexp' => '[/_a-zA-Z0-9-\pL]*'),
                    'rewrite' => array('regexp' => '[/_a-zA-Z0-9-\pL]*'),
                    'reference' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'manufacturer' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'supplier' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'price' => array('regexp' => '[0-9\.,]*'),
                    'tags' => array('regexp' => '[a-zA-Z0-9-\pL]*'),
                ),
            );
        }

        if (Configuration::get('purls_categories') == 1) {
            $this->default_routes['category_rule'] = array(
                'controller' => 'category',
                'rule' => '{parent_categories:/}{rewrite}/',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+'),
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'category_rewrite'
                    ),
                    'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'parent_categories' => array('regexp' => '[/_a-zA-Z0-9-\pL]*'),
                ),
            );
        }
        $this->loadRoutes();
    }

    public static function isProductLinkAttribute($idpa)
    {
        if (Configuration::get('purls_products') == 1 || Configuration::get('purls_products') == 3) {

            $sql = 'SELECT pas.`id_product`
        			      FROM `' . _DB_PREFIX_ . 'product_attribute_shop` pas
                    WHERE pas.`id_product_attribute` = '.$idpa. ' AND pas.`id_shop` = '.Context::getContext()->shop->id;


            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sql .= ' AND pl.`id_shop` = ' . (int)Shop::getContextShopID();
            }

            $id_product = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);


            if ($id_product > 0) {
                $_GET['id_product'] = $id_product;
                $_GET['id_product_attriubte'] = $idpa;
            }

            return ($id_product > 0) ? true : false;
        } else {
            if (Tools::getValue('id_product', 'false') != 'false' && Tools::getValue('controller') == "product") {
                return Tools::getValue('id_product');
            }
        }
    }

    public static function isProductLink($short_link, $route=false)
    {
        if (strpos($route['rule'], '.html') !== false)
        {
          if(strpos($short_link, '.html') == false) {
            return false;
          }
        }

        if (Configuration::get('purls_products') == 1 || Configuration::get('purls_products') == 3) {
            $explode_product_link = explode("/", $short_link);
            $count = count($explode_product_link);
            if ($count > 1) {
                $sql = 'SELECT p.`id_product`
        			      FROM `' . _DB_PREFIX_ . 'product` p
                    INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = p.`id_product`)
                    INNER JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (cl.`link_rewrite` = \'' . psql($explode_product_link[$count - 2]) . '\')
        			WHERE pl.`link_rewrite` = \'' . str_replace('.html', '', psql($explode_product_link[$count - 1])) . '\'
                      AND pl.`id_lang` = ' . Context::getContext()->language->id . '
                      AND cl.`link_rewrite` = \'' . psql($explode_product_link[$count - 2]) . '\'
                      AND p.`id_category_default` = cl.`id_category`';
            } else {
                $sql = 'SELECT `id_product`
        			FROM `' . _DB_PREFIX_ . 'product_lang` pl
        			WHERE `link_rewrite` = \'' . str_replace('.html', '', psql($explode_product_link[$count - 1])) . '\' AND `id_lang` = ' . Context::getContext()->language->id;
            }
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sql .= ' AND pl.`id_shop` = ' . (int)Shop::getContextShopID();
            }
            if ($explode_product_link[$count - 1] == "") {
                return false;
            }
            $id_product = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            if ($id_product > 0) {
                $_GET['id_product'] = $id_product;
            } else {
                $sql = 'SELECT `id_product`
        			FROM `' . _DB_PREFIX_ . 'product_lang` pl
        			WHERE `link_rewrite` = \'' . str_replace('.html', '', psql($explode_product_link[$count - 1])) . '\' AND `id_lang` = ' . Context::getContext()->language->id;
                $id_product = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                if ($id_product > 0) {
                    $_GET['id_product'] = $id_product;
                }
            }
            return ($id_product > 0) ? true : false;
        } else {
            if (Tools::getValue('id_product', 'false') != 'false' && Tools::getValue('controller') == "product") {
                return Tools::getValue('id_product');
            }
        }
    }

    public static function isCategoryLink($short_link)
    {
        if (Configuration::get('purls_categories') == 1) {
            $short_link = rtrim($short_link, "/");
            $categories = explode("/", $short_link);
            $categories_count = count($categories);
            $where_clause = '';
            $shop_association = 'AND cs.id_shop=' . Context::getContext()->shop->id;
            if ($categories_count > 2) {
                $where_clause = ' AND c.`id_parent` IN
                        (SELECT cll.`id_category` FROM `' . _DB_PREFIX_ . 'category_lang` cll
                        INNER JOIN `' . _DB_PREFIX_ . 'category` cc ON (cc.id_category = cll.id_category)
                        INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (cc.id_category = cs.id_category)
                        WHERE 1 ' . $shop_association . ' AND cll.`link_rewrite` = \'' . str_replace('.html', '', psql($categories[$categories_count - 2])) . '\' AND cll.`id_lang` = ' . Context::getContext()->language->id . ' AND cc.`id_parent` IN
                            (SELECT clll.`id_category` FROM `' . _DB_PREFIX_ . 'category_lang` clll
                            INNER JOIN `' . _DB_PREFIX_ . 'category` ccc ON (ccc.id_category = clll.id_category)
                            INNER JOIN `' . _DB_PREFIX_ . 'category_shop` css ON (ccc.id_category = css.id_category)
                            WHERE 1 ' . $shop_association . ' AND clll.`link_rewrite` = \'' . str_replace('.html', '', psql($categories[$categories_count - 3])) . '\' AND clll.`id_lang` = ' . Context::getContext()->language->id . ')
                        )';
            } else {
                if ($categories_count > 1) {
                    $where_clause = ' AND c.`id_parent` IN
                        (SELECT cll.`id_category` FROM `' . _DB_PREFIX_ . 'category_lang` cll
                        INNER JOIN `' . _DB_PREFIX_ . 'category` cc ON (cc.id_category = cll.id_category)
                        INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (cc.id_category = cs.id_category)
                        WHERE 1 ' . $shop_association . ' AND cll.`link_rewrite` = \'' . str_replace('.html', '', psql($categories[$categories_count - 2])) . '\' AND cll.`id_lang` = ' . Context::getContext()->language->id . ')';
                }
            }
            $sql = 'SELECT c.`id_category` FROM `' . _DB_PREFIX_ . 'category_lang` cl
                    INNER JOIN `' . _DB_PREFIX_ . 'category` c ON (c.id_category = cl.id_category)
    				WHERE cl.`link_rewrite` = \'' . str_replace('.html', '', psql($categories[$categories_count - 1])) . '\' AND cl.`id_lang` = ' . Context::getContext()->language->id . ' ' . $where_clause;
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sql .= ' AND cl.`id_shop` = ' . (int)Shop::getContextShopID();
            }
            $id_category = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

            if ($id_category > 0) {
                //$_POST['id_category'] = $id_category;
                $_GET['id_category'] = $id_category;
            }  else {
                $sql = 'SELECT c.`id_category` FROM `' . _DB_PREFIX_ . 'category_lang` cl
                    INNER JOIN `' . _DB_PREFIX_ . 'category` c ON (c.id_category = cl.id_category)
                    INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (c.id_category = cs.id_category)
    				WHERE 1 ' . $shop_association . ' AND cl.`link_rewrite` = \'' . str_replace('.html', '', psql($categories[$categories_count - 1])) . '\' AND cl.`id_lang` = ' . Context::getContext()->language->id;
                $id_category = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                if ($id_category > 0) {
                    $_GET['id_category'] = $id_category;
                }
            }

            return ($id_category > 0) ? true : false;
        } else {
            if (Tools::getValue('id_cat', 'false') != 'false' && Tools::getValue('controller') == "category") {
                return Tools::getValue('id_cat');
            }
        }
    }

    public static function isCmsCategoryLink($short_link)
    {
        $explode_cms_link = explode("/", $short_link);
        $count = count($explode_cms_link);
        $sql = 'SELECT l.`id_cms_category`
			FROM `' . _DB_PREFIX_ . 'cms_category_lang` l
			WHERE l.`link_rewrite` = \'' . psql($explode_cms_link[$count - 1]) . '\'';
        $id_cms_category = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        if ($id_cms_category > 0) {
            //$_POST['id_cms_category'] = $id_cms_category;
            $_GET['id_cms_category'] = $id_cms_category;
        }

        return ($id_cms_category > 0) ? true : false;
    }

    public static function isCmsLink($short_link)
    {
        if (Configuration::get('purls_cms') == 1) {
            $explode_cms_link = explode("/", $short_link);
            $count = count($explode_cms_link);
            $sql = 'SELECT l.`id_cms`
    			FROM `' . _DB_PREFIX_ . 'cms_lang` l
    			LEFT JOIN `' . _DB_PREFIX_ . 'cms_shop` s ON (l.`id_cms` = s.`id_cms`)
    			WHERE l.`link_rewrite` = \'' . str_replace(".html", "", psql($explode_cms_link[$count - 1])) . '\'';
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sql .= ' AND s.`id_shop` = ' . (int)Shop::getContextShopID();
            }
            $id_cms = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

            if ($id_cms > 0) {
                //$_POST['id_cms'] = $id_cms;
                $_GET['id_cms'] = $id_cms;
            }

            return ($id_cms > 0) ? true : false;
        } else {
            if (Tools::getValue('id_cms', 'false') != 'false' && Tools::getValue('controller') == "cms") {
                return Tools::getValue('id_cms');
            }
        }
    }

    public static function isManufacturerLink($short_link)
    {
        $id_manufacturer = 0;
        if (Configuration::get('purls_manufacturers') == 1) {
            $explode_manufacturer_link = explode("/", $short_link);
            if ($explode_manufacturer_link[0] == "supplier") {
                return false;
            }
            $count = count($explode_manufacturer_link);
            if (isset($explode_manufacturer_link[1])) {
                $name_manufacturer = $explode_manufacturer_link[1];
            }
            $sqlall = 'SELECT * FROM `' . _DB_PREFIX_ . 'manufacturer` m
   			LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer_shop` s ON (m.`id_manufacturer` = s.`id_manufacturer`) WHERE 1=1 ';
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sqlall .= ' AND s.`id_shop` = ' . (int)Shop::getContextShopID();
            }
            $allmanufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sqlall);
            if (isset($explode_manufacturer_link[1])) {
                foreach ($allmanufacturers as $key => $manufacturer) {
                    if ($name_manufacturer == Tools::str2url($manufacturer['name'])) {
                        $id_manufacturer = $manufacturer['id_manufacturer'];
                        $_GET['id_manufacturer'] = $id_manufacturer;
                    }
                }
            }
            if (isset($id_manufacturer)) {
                return ($id_manufacturer > 0) ? true : false;
            } else {
                return false;
            }
        } else {
            if (Tools::getValue('id_manufacturer', 'false') != 'false' && Tools::getValue('controller') == "manufacturer") {
                return Tools::getValue('id_manufacturer');
            }
        }
    }

    public static function isSupplierLink($short_link)
    {
        $id_supplier = 0;
        if (Configuration::get('purls_suppliers') == 1) {
            $explode_supplier_link = explode("/", $short_link);
            $count = count($explode_supplier_link);
            if (isset($explode_supplier_link[1])) {
                $name_supplier = $explode_supplier_link[1];
            }
            $sql = 'SELECT *
    		FROM `' . _DB_PREFIX_ . 'supplier` sp
    		LEFT JOIN `' . _DB_PREFIX_ . 'supplier_shop` s ON (sp.`id_supplier` = s.`id_supplier`) WHERE 1=1';
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sql .= ' AND s.`id_shop` = ' . (int)Shop::getContextShopID();
            }
            $allsuppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
            if (isset($explode_supplier_link[1])) {
                foreach ($allsuppliers as $key => $supplier) {
                    if ($name_supplier == Tools::str2url($supplier['name'])) {
                        $id_supplier = $supplier['id_supplier'];
                        $_GET['id_supplier'] = $id_supplier;
                    }
                }
            }
            if (isset($id_supplier)) {
                return ($id_supplier > 0) ? true : false;
            } else {
                return false;
            }
        } else {
            if (Tools::getValue('id_supplier', 'false') != 'false' && Tools::getValue('controller') == "supplier") {
                return Tools::getValue('id_supplier');
            }
        }
    }

    public function getController($id_shop = null)
    {
        if (defined('_PS_ADMIN_DIR_')) {
            $_GET['controllerUri'] = Tools::getvalue('controller');
        }
        if ($this->controller) {
            $_GET['controller'] = $this->controller;
            return $this->controller;
        }
        if (null === $id_shop) {
            $id_shop = (int)Context::getContext()->shop->id;
        }
        $controller = Tools::getValue('controller');
        $curr_lang_id = Context::getContext()->language->id;
        if (isset($controller) && is_string($controller) && preg_match('/^([0-9a-z_-]+)\?(.*)=(.*)$/Ui', $controller, $m)) {
            $controller = $m[1];
            if (isset($_GET['controller'])) {
                $_GET[$m[2]] = $m[3];
            } elseif (isset($_POST['controller'])) {
                $_POST[$m[2]] = $m[3];
            }
        }
        if (!Validate::isControllerName($controller)) {
            $controller = false;
        }

        if ($this->use_routes && !$controller && !defined('_PS_ADMIN_DIR_')) {
            if (!$this->request_uri) {
                return strtolower($this->controller_not_found);
            }
            $controller = $this->controller_not_found;
            if (!preg_match('/\.(gif|jpe?g|png|css|js|ico)$/i', $this->request_uri)) {
                if ($this->empty_route) {
                    $this->addRoute($this->empty_route['routeID'], $this->empty_route['rule'], $this->empty_route['controller'], $curr_lang_id, array(), array(), $id_shop);
                }


                $language_ids = Language::getIDs();
                if (isset($context->language) && !in_array($context->language->id, $language_ids)) {
                    $language_ids[] = (int)$context->language->id;
                }

                foreach ($language_ids as $id_lang) {
                    foreach ($this->original_dispatcher_routes as $id => $route) {
                        $this->addRoute(
                            $id . "_original",
                            $route['rule'],
                            $route['controller'],
                            $id_lang,
                            $route['keywords'],
                            isset($route['params']) ? $route['params'] : array(),
                            $id_shop
                        );
                    }
                }

                list($uri) = explode('?', $this->request_uri);
                if (isset($this->routes[$id_shop][$curr_lang_id])) {
                    $route = array();
                    foreach ($this->routes[$id_shop][$curr_lang_id] as $k => $r) {
                        if (preg_match($r['regexp'], $uri, $m) && !isset($isProductLink)) {
                            $isTemplate = false;
                            $module = isset($r['params']['module']) ? $r['params']['module'] : '';
                            switch ($r['controller'] . $module) {
                                case 'supplier':
                                    if (Configuration::get('purls_suppliers') == 1) {
                                        if (isset($m['id_supplier'])) {
                                            if (Configuration::get('purls_301') == 1) {
                                                Tools::redirect(Context::getContext()->link->getSupplierLink($m['id_supplier']), __PS_BASE_URI__, null, array('HTTP/1.1 301 Moved Permanently'));
                                            }
                                            $isTemplate = false;
                                        } else {
                                            $isTemplate = false;
                                        }
                                    } else {
                                        $isTemplate = false;
                                    }
                                    break;
                                case 'manufacturer':
                                    if (Configuration::get('purls_manufacturers') == 1) {
                                        if (isset($m['id_manufacturer'])) {
                                            if (Configuration::get('purls_301') == 1) {
                                                Tools::redirect(Context::getContext()->link->getManufacturerLink($m['id_manufacturer']), __PS_BASE_URI__, null, array('HTTP/1.1 301 Moved Permanently'));
                                            }
                                            $isTemplate = false;
                                        } else {
                                            $isTemplate = false;
                                        }
                                    } else {
                                        $isTemplate = false;
                                    }
                                    break;
                                case 'cms':
                                    if (Configuration::get('purls_cms') == 1) {
                                        if (isset($m['id_cms'])) {
                                            if (Configuration::get('purls_301') == 1) {
                                                Tools::redirect(Context::getContext()->link->getCmsLink($m['id_cms']), __PS_BASE_URI__, null, array('HTTP/1.1 301 Moved Permanently'));
                                            }
                                            $isTemplate = false;
                                        } elseif (isset($m['id_cms_category'])) {
                                            if (Configuration::get('purls_301') == 1) {
                                                Tools::redirect(Context::getContext()->link->getCmsCategoryLink($m['id_cms_category']), __PS_BASE_URI__, null, array('HTTP/1.1 301 Moved Permanently'));
                                            }
                                            $isTemplate = false;
                                        } else {
                                            $isTemplate = true;
                                        }
                                    } else {
                                        $isTemplate = false;
                                    }
                                    break;
                                case 'product':
                                    if (Configuration::get('purls_products') == 1) {
                                        if (self::isProductLink(ltrim(parse_url($uri, PHP_URL_PATH), '/'), $this->routes[$id_shop][$curr_lang_id]['product_rule']) == true) {
                                            $isProductLink = true;
                                        }
                                        if (isset($m['id_product'])) {
                                            if (self::isProductLink(ltrim(parse_url($uri, PHP_URL_PATH), '/'), $this->routes[$id_shop][$curr_lang_id]['product_rule']) == true) {
                                                $isTemplate = true;
                                            } else {
                                                if (Configuration::get('purls_301') == 1) {
                                                    Tools::redirect(Context::getContext()->link->getProductLink($m['id_product']), __PS_BASE_URI__, null, array('HTTP/1.1 301 Moved Permanently'));
                                                }
                                            }
                                        } else {
                                            $isTemplate = true;
                                        }
                                        break;

                                    } elseif (Configuration::get('purls_products') == 3) {
                                        if (self::isProductLink(ltrim(parse_url($uri, PHP_URL_PATH), '/'), $this->routes[$id_shop][$curr_lang_id]['product_rule']) == true) {
                                            $isProductLink = true;
                                        }
                                        if (isset($m['id_product_attribute'])) {
                                            if ($m['id_product_attribute'] != ""){
                                                self::isProductLinkAttribute($m['id_product_attribute']);
                                                break;
                                            }
                                        }
                                    }
                                    elseif (Configuration::get('purls_products') == 2) {
                                        if (self::isProductLink(ltrim(parse_url($uri, PHP_URL_PATH), '/'), $this->routes[$id_shop][$curr_lang_id]['product_rule']) == true) {
                                            $isProductLink = true;
                                        }
                                        if (isset($m['id_product'])) {
                                            if (self::isProductLink(ltrim(parse_url($uri, PHP_URL_PATH), '/'), $this->routes[$id_shop][$curr_lang_id]['product_rule']) == true) {
                                                $isTemplate = true;
                                            }
                                        } else {
                                            $isTemplate = true;
                                        }

                                        break;
                                    } else {
                                        $isTemplate = false;
                                        break;
                                    }
                                case 'category':
                                    if (Configuration::get('purls_categories') == 1) {
                                        if (isset($m['id_category'])) {
                                            if (self::isCategoryLink(ltrim(parse_url($uri, PHP_URL_PATH), '/'), $this->routes[$id_shop][$curr_lang_id]['category_rule']) == true) {
                                                $isTemplate = true;
                                            } else {
                                                if (Configuration::get('purls_301') == 1) {
                                                    Tools::redirect(Context::getContext()->link->getCategoryLink($m['id_category']), __PS_BASE_URI__, null, array('HTTP/1.1 301 Moved Permanently'));
                                                }
                                            }
                                        } else {
                                            $isTemplate = true;
                                        }
                                    } else {
                                        $isTemplate = false;
                                    }
                                    break;
                            }
                            if (!$isTemplate) {
                                $route = $r;
                                break;
                            }
                        }
                    }



                    if (empty($route)) {
                        $short_link = ltrim(parse_url($uri, PHP_URL_PATH), '/');
                        $route = $this->routes[$id_shop][$curr_lang_id]['product_rule'];
                        if (!self::isProductLink($short_link, $route)) {
                            $route = $this->routes[$id_shop][$curr_lang_id]['cms_rule'];
                            if (!self::isCmsLink($short_link, $route)) {
                                $route = $this->routes[$id_shop][$curr_lang_id]['category_rule'];
                                if (!self::isCategoryLink($short_link, $route)) {
                                    $route = $this->routes[$id_shop][$curr_lang_id]['cms_category_rule'];
                                    if (!self::isCmsCategoryLink($short_link, $route)) {
                                        $route = $this->routes[$id_shop][$curr_lang_id]['manufacturer_rule'];
                                        if (!self::isManufacturerLink($short_link, $route)) {
                                            $route = $this->routes[$id_shop][$curr_lang_id]['supplier_rule'];
                                            if (!self::isSupplierLink($short_link, $route)) {
                                                $route = array();
                                                $controller = $this->controller_not_found;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (!empty($route['controller'])) {
                            $controller = $route['controller'];
                        }
                    }


                    if (!empty($route)) {
                        if (preg_match($route['regexp'], $uri, $m)) {
                            foreach ($m as $k => $v) {
                                if (!is_numeric($k)) {
                                    $_GET[$k] = $v;
                                }
                            }
                            $controller = $route['controller'] ? $route['controller'] : $_GET['controller'];
                            if (!empty($route['params'])) {
                                foreach ($route['params'] as $k => $v) {
                                    $_GET[$k] = $v;
                                }
                            }
                            if (preg_match('#module-([a-z0-9_-]+)-([a-z0-9_-]+)$#i', $controller, $m)) {
                                $_GET['module'] = $m[1];
                                $_GET['fc'] = 'module';
                                $controller = $m[2];
                            }
                            if (isset($_GET['fc']) && $_GET['fc'] == 'module') {
                                $this->front_controller = self::FC_MODULE;
                            }
                        }
                    }
                }
            }
            if ($controller == 'index' || $this->request_uri == '/index.php') {
                $controller = $this->default_controller;
            }
            $this->controller = $controller;
        } else { // Default mode, take controller from url
            $this->controller = $controller;
        }
        $this->controller = str_replace('-', '', $this->controller);
        $_GET['controller'] = $this->controller;
        return $this->controller;
    }
}
