<?php

require_once __DIR__.'/classes/AuthDSSuperPack.php';

class PhSuperPack extends Module
{
    public $compat;

    public function __construct()
    {
        $this->name = 'phsuperpack';
		$this->module_key = '7e2486evb3e8dc2a2a6a7e56ea1d82b4';
        $this->tab = 'administration';        
        $this->version = '2.0.7';

        $this->author = 'prestahelp.com';
        $this->need_instance = 1;
        $this->bootstrap = true;

        parent::__construct();
        $this->_coreClassName = Tools::strtolower(get_class());

        $this->displayName = $this->l('Super pack');
        $this->description = $this->l('Create super pack products for your customers');

        $this->compat = version_compare(_PS_VERSION_, 1.6, '>=') ? false : true;

        $this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);
    }

    private function firstTax()
    {
        $sql = "SELECT id_tax FROM "._DB_PREFIX_."tax ORDER BY id_tax LIMIT 1";
        $query = Db::getInstance()->executeS($sql);

        if (!empty($query)) {
            return $query[0]['id_tax'];
        }
        return 1;
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        $sql_ok = true;
        $sql = array();
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'phsuperpack_product` (
				  `id_superpack_product` int(11) NOT NULL AUTO_INCREMENT,				  
				  `id_product` int(11) NOT NULL,
				  `id_product_attribute` int(11) NOT NULL,
				  `id_product_pack` int(11) NOT NULL,
				  `id_shop` int(11) NOT NULL,
				  `active` BOOL DEFAULT 1,
				  `position` int(11) NOT NULL,				  
				  PRIMARY KEY (`id_superpack_product`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

        foreach ($sql as $query) {
            if (Db::getInstance()->execute($query) === false) {
                $sql_ok = false;
            }
        }

        if (!Db::getInstance()->Execute("ALTER TABLE `"._DB_PREFIX_."product_lang` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;")) {
            return false;
        }

        if ((int) Configuration::get('PH_CATEGORY_PACK_ID') <= 0) {
            $default_language_id = (int) Configuration::get('PS_LANG_DEFAULT');

            $category_to_create = new Category();
            $shop_is_feature_active = Shop::isFeatureActive();
            if (!$shop_is_feature_active) {
                $category_to_create->id_shop_default = 1;
            } else {
                $category_to_create->id_shop_default = (int) Context::getContext()->shop->id;
            }
            $category_name = 'Kategoria zestawy';
            $category_to_create->name = $this->createMultiLangField(trim($category_name));
            $category_to_create->active = 0;
            $category_to_create->id_parent = (int)Configuration::get('PS_HOME_CATEGORY');
            $category_link_rewrite = Tools::link_rewrite($category_to_create->name[$default_language_id]);
            $category_to_create->link_rewrite = $this->createMultiLangField($category_link_rewrite);
            $category_to_create->add();
            Configuration::updateValue('PH_CATEGORY_PACK_ID', $category_to_create->id);
        }

        // Prepare tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminProductPack';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'AdminProductPack';
        }
        $tab->id_parent = -1;
        $tab->module = $this->name;

        Configuration::updateValue('PH_SUPERPACK_TAX', $this->firstTax());

        return parent::install() && $tab->add() &&
        $this->registerHook('displayHeader') &&
        $this->registerHook('displayAdminProductsExtra') &&
        (int)Configuration::get('PH_CATEGORY_PACK_ID') > 0 &&
        $this->setNewHook() &&
        $this->registerHook('displayFooterProduct') &&
        $this->registerHook('actionUpdateQuantity') &&
        $this->registerHook('actionAdminControllerSetMedia') &&
        $this->registerHook('displayPack') &&
        $this->registerHook('updateProduct') &&
        $this->registerHook('displayCartPack') && $sql_ok;
    }

    private function createMultiLangField($field)
    {
        $res = array();
        foreach (Language::getIDs(false) as $id_lang) {
            $res[$id_lang] = $field;
        }
        return $res;
    }

    private function setNewHook()
    {
        $nameHook = 'displayCartPack';
        $issetHook = Hook::getIdByName($nameHook);
        $res = true;
        if (empty($issetHook)) {
            $hook = new Hook();
            $hook->name = $nameHook;
            $hook->title = $nameHook;
            $hook->description = 'display pack in cart';
            $res &= $hook->add();
        }
        $nameHook = 'displayPack';
        $issetHook = Hook::getIdByName($nameHook);
        if (empty($issetHook)) {
            $hook = new Hook();
            $hook->name = $nameHook;
            $hook->title = $nameHook;
            $hook->description = 'display pack in product';
            $res &= $hook->add();
        }
        return $res;
    }

    public function uninstall()
    {
        $sql = array();
        $sql[] = 'DROP TABLE `'._DB_PREFIX_.'phsuperpack_product`';
        $sql_ok = true;
        foreach ($sql as $query) {
            if (Db::getInstance()->execute($query) === false) {
                $sql_ok = false;
            }
        }
        $id_tab = (int)Tab::getIdFromClassName('AdminProductPack');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        if (!parent::uninstall() || !$sql_ok) {
            return false;
        }
        return true;
    }

    public function hookDisplayHeader()
    {
        $this->retroCompatIncludes();
    }

    protected function postProcess()
    {
        if (isset($_FILES['product_image_mod'])
            && isset($_FILES['product_image_mod']['tmp_name'])
            && !empty($_FILES['product_image_mod']['tmp_name'])) {
            if ($error = ImageManager::validateUpload($_FILES['product_image_mod'], 4000000)) {
                return $error;
            } else {
                $ext = substr($_FILES['product_image_mod']['name'], strrpos($_FILES['product_image_mod']['name'], '.') + 1);
                $file_name = md5($_FILES['product_image_mod']['name']) . '.' . $ext;
                if (!move_uploaded_file($_FILES['product_image_mod']['tmp_name'], dirname(__FILE__) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file_name)) {
                    return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
                } else {
                    if (Configuration::get('PH_BANNER_IMG') != $file_name) {
                        @unlink(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . Configuration::get('PH_BANNER_IMG'));
                    }
                    Configuration::updateValue('PH_BANNER_IMG', $file_name);
                }
            }
        }
        Configuration::updateValue('PH_CHECK_PRICE', (int)$_POST['PH_CHECK_PRICE']);
        Configuration::updateValue('PH_PACK_UNZIP', (int)$_POST['PH_PACK_UNZIP']);
        Configuration::updateValue('pack_category', Tools::getValue('pack_category'));
        Configuration::updateValue('groupbox_fields', json_encode(Tools::getValue("groupbox_fields")));
        Configuration::updateValue('PH_SUPERPACK_TAX', (int)$_POST['ph_superpack_taxes']);

        $this->_clearCache('*');
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&conf=4&module_name='.$this->name);

    }

    private function getPrestahelp()
    {
        $this->context->controller->addCSS(__DIR__ . '/assets/css/phelp.css');
        $ssl = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://';
        $updateModule = false;
        $module = Module::getInstanceByName($this->name);
        $mversion = $module->version;

        $auth = new AuthDSSuperPack($this->name);
        $productsShow = $auth->getBaners();
        $authorInfo = $auth->getAuthor();
        $chlogInfo = $auth->getChangelog();
        $chlogInfoOther = $auth->getChangelogOther();
        $currentVersion = $auth->getCurrentModuleVersion();

        $phelpBtm = __DIR__ . '/views/templates/admin/phelp-bottom.tpl';
        $phelpTop = __DIR__ . '/views/templates/admin/phelp-top.tpl';
        $moduleAssets = $ssl . $this->context->shop->domain . $this->context->shop->physical_uri . 'modules/'.$this->name.'/assets/';
        $lastestVersion = $currentVersion['version'] == $mversion ? true : false;
        $updateLink = 'https://modules.prestahelp.com/moduly/'.$this->name.'/'.$this->name . $currentVersion['version'] . '.zip';
        $indexLink = 'https://modules.prestahelp.com/moduly/'.$this->name.'/';
        $banersHtml = AuthDSSuperPack::getBanersHtml();

        $this->context->smarty->assign(array(
            'moduleVersion' => $mversion,
            'moduleAssets' => $moduleAssets,
            'phelpBtm' => $phelpBtm,
            'phelpTop' => $phelpTop,
            'authorInfo' => $authorInfo,
            'chlogInfo' => $chlogInfo,
            'moduleName' => $module->displayName,
            'moduleNameInfo' => $module->name,
            'currentModuleVersion' => $currentVersion['version'],
            'lastestVersion' => $lastestVersion,
            'updateLink' => $updateLink,
            'chlogInfoOther' => $chlogInfoOther,
            'indexLink' => $indexLink,
            'banersHtml' => $banersHtml,
        ));
    }

    /**
     * Get taxes
     */
    private function getTaxes()
    {
        $sql = "SELECT id_tax, name FROM "._DB_PREFIX_."tax_lang WHERE id_lang=".Context::getContext()->language->id." ORDER BY name";
        $query = Db::getInstance()->executeS($sql);

        if (!empty($query)) {
            return $query;
        }
        return null;
    }

    public function getContent()
    {
        $this->getPrestahelp();

        if (((bool) Tools::isSubmit('submitPhSuperPackModule')) == true) {
            $this->postProcess();
        }

        $ready1 = false;
        if (version_compare(_PS_VERSION_, 1.7, '<')) {
            $proTpl = file_get_contents(_PS_ALL_THEMES_DIR_.$this->context->shop->theme_name.'/shopping-cart-product-line.tpl');
            $isset1 = mb_strpos($proTpl, "{hook h='displayCartPack' id_product=");
            if ($isset1 > 0) {
                $ready1 = true;
            }
        } else {
            $proTpl = file_get_contents(_PS_ALL_THEMES_DIR_.$this->context->shop->theme_name.'/templates/checkout/_partials/cart-detailed-product-line.tpl');
            $isset1 = mb_strpos($proTpl, "{hook h='displayCartPack' id_product=");
            if ($isset1 > 0) {
                $ready1 = true;
            }
        }

        $product_image_value = Configuration::get('PH_BANNER_IMG');
        // Link to Ajax connect
        $link = new Link();
        $parameters_history = array("action" => "ajax");
        $ajax_get_history = $link->getModuleLink('phsuperpack', 'ajax', $parameters_history);

        $cat_name = new Category((int) Configuration::get('pack_category'));
        $this->context->smarty->assign(array(
            'taxes' => $this->getTaxes(),
            'choosed_tax' => (int)Configuration::get('PH_SUPERPACK_TAX'),
            'module_dir' => $this->_path,
            'path' => $this->_path,
            'request_uri' => Tools::safeOutput($_SERVER['REQUEST_URI']),
            'ready1' => $ready1,
            'psVersion' => version_compare(_PS_VERSION_, 1.7, '<'),
            'themeName' => $this->context->shop->theme_name,
            'compat' => $this->compat,
            'uri' => $this->getPathUri(),
            'groups_client' => $this->getPsGroups(),
            'product_image_value' => $product_image_value,
            'product_pack_unzip' => (int)Configuration::get('PH_PACK_UNZIP'),
            'groups_client_choose' => json_decode(Configuration::get('groupbox_fields'), true),
            'all_category' => Category::getCategories(Context::getContext()->language->id, true, false),
            'choosed_category' => (int) Configuration::get('pack_category'),
            'choosed_category_name' => $cat_name->name,
            'url_ajax_controller_search' => $ajax_get_history,
            'product_check_price' => (int)Configuration::get('PH_CHECK_PRICE'),

        ));

        return $this->context->smarty->fetch('module:phsuperpack/views/templates/admin/module.tpl');
    }
    private function getPsGroups()
    {
        return Db::getInstance()->executeS('SELECT `id_group`, `name` FROM ' . _DB_PREFIX_ . 'group_lang WHERE id_lang='.Context::getContext()->language->id);
    }
    public function hookActionAdminControllerSetMedia()
    {
        $this->context->controller->addJqueryUI('ui.sortable');
    }

    public function getPack($id_product)
    {
        $id_shop = (int)Context::getContext()->shop->id;

        $q = 'SELECT hs.`id_superpack_product`, hs.`id_product_pack`, hs.id_product, hs.`id_product_attribute` FROM '._DB_PREFIX_.'phsuperpack_product hs
			WHERE hs.id_shop = ' . (int)$id_shop . '
			AND hs.id_product = ' . (int)$id_product . ' AND hs.`active` = 1 ORDER BY hs.`position` asc ';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($q);
    }

    public function getPackItem($id_product)
    {
        $q = 'SELECT hs.`id_product_pack`, hs.id_product_item FROM '._DB_PREFIX_.'pack hs WHERE hs.id_product_item = ' . (int)$id_product;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($q);
    }

    public function hookDisplayPack($params)
    {
        $id_product = (int)$params['id_product'];
        if ($id_product > 0) {
            $pack = $this->getPackProductId($id_product);
            if ($pack != null) {
                $lang_id = (int)Configuration::get('PS_LANG_DEFAULT');
                $pr = new Product((int)$pack['id_product_pack']);

                $price = $pr->getPrice(true, null, 6);
                $regular_price = $pr->getPriceWithoutReduct(false, null, 6);
                $priced = Tools::displayPrice($price);
                $regular_priced = Tools::displayPrice($regular_price);// $pr->regular_price;
                $no_pack = $pr->getNoPackPrice();
                $no_pack_priced = Tools::displayPrice($no_pack);

                $has_discount = false;
                $discount = 0;
                $discount_proc = 0;
                if ($regular_price > $price) {
                    $has_discount = true;
                    $discount = Tools::displayPrice($regular_price - $price);
                    $discount_proc = "-".Tools::ps_round(100 - ((100 * $price) / $regular_price), 2)."%";
                }

                $items = Pack::getItemTable((int)$pack['id_product_pack'], $lang_id, true);

                $arrayItem = array();
                foreach ($items as $item) {
                    if ((int)$item['id_product'] == (int)$pack['id_product']) {
                        $arrayItem[] = $item;
                    }
                }

                foreach ($items as $item) {
                    if ((int)$item['id_product'] != (int)$pack['id_product']) {
                        $arrayItem[] = $item;
                    }
                }
                $items = $arrayItem;

                $this->context->smarty->assign(array(
                    'price' => $priced,
                    'regular_price' => $regular_priced,
                    'no_pack_price' => $no_pack_priced,
                    'has_discount' => $has_discount,
                    'discount' => $discount,
                    'discount_proc' => $discount_proc,
                    'items' => $items,
                    'static_token' => Tools::getToken(false),
                    'pack_template' => 'module:phsuperpack/views/templates/front/pack-product.tpl',
                    'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
                    'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
                ));
                return $this->context->smarty->fetch('module:phsuperpack/views/templates/front/display_cart_product.tpl');
            }
        }

        // return $this->hookDisplayFooterProduct($params);
    }

    public function hookDisplayFooterProduct($params)
    {


        // Get the current customer's group ID
        $groupId = Context::getContext()->customer->id_default_group;


        $acceptedGroup = json_decode(Configuration::get('groupbox_fields'));

        if (empty($acceptedGroup)) {
            return;
        }

        if (!in_array($groupId, $acceptedGroup)) {
            return;
        }

        $id_product = (int)Tools::getValue('id_product');
        if ($id_product <= 0) {
            return;
        }

        $list = $this->getPack($id_product);
        if (count($list) <= 0) {
            return;
        }

        $max_pack = 0;
        $lang_id = (int)Configuration::get('PS_LANG_DEFAULT');
        $list2 = array();
        foreach ($list as $key => $pack) {
            $pr = new Product((int)$pack['id_product_pack']);

            if ($pr == null || !Validate::isLoadedObject($pr)) {
                Db::getInstance()->delete('phsuperpack_product', 'id_superpack_product = '.(int)$pack['id_superpack_product']);
                continue;
            }

            if (!Pack::isPack($pack['id_product_pack'])) {
                continue;
            }

            //			$q = Pack::getQuantity($pack['id_product_pack']);
            //echo $q . ' ';
            $q = Db::getInstance()->getValue('SELECT quantity FROM `'._DB_PREFIX_.'pack` WHERE id_product_pack='.pSQL($pack['id_product_pack']));
            if ($q <= 0) {
                continue;
            }

            $list[$key]['quantity_item_pack'] = $q;

            $price = $pr->getPrice(true, null, 6);
            $regular_price = $pr->getPriceWithoutReduct(false, null, 6);
            $list[$key]['price'] = Tools::displayPrice($price); // Tools::displayPrice($pr->price);// Tools::ps_round($pr->price, 2);//$pr->price; //$product_full['price'];
            $list[$key]['regular_price'] = Tools::displayPrice($regular_price);// $pr->regular_price;
            $no_pack = $pr->getNoPackPrice();
            $list[$key]['no_pack_price'] = Tools::displayPrice($no_pack);

            $list[$key]['has_discount'] = false;
            if ($regular_price > $price) {
                $list[$key]['has_discount'] = true;
                $list[$key]['discount'] = Tools::displayPrice($regular_price - $price);
                $list[$key]['discount_proc'] = "-".Tools::ps_round(100 - ((100 * $price) / $regular_price), 2)."%";

            }

            $list[$key]['items'] = Pack::getItemTable((int)$pack['id_product_pack'], $lang_id, true);
            $list[$key]['id_product'] = (int)$pack['id_product'];
            if (count($list[$key]['items']) <= 0) {
                continue;
            }

            $arrayItem = array();
            foreach ($list[$key]['items'] as $item) {
                if ((int)$item['id_product'] == (int)$pack['id_product']) {
                    $arrayItem[] = $item;
                }
            }

            foreach ($list[$key]['items'] as $item) {
                if ((int)$item['id_product'] != (int)$pack['id_product']) {
                    $arrayItem[] = $item;
                }
            }
            $list[$key]['items'] = $arrayItem;

            if ($max_pack < count($list[$key]['items'])) {
                $max_pack = count($list[$key]['items']);
            }

            $list2[$key] = $list[$key];
        }

        //dump($list);

        $this->context->smarty->assign(array(
            'list' => $list2,
            'max_pack' => $max_pack,
            'static_token' => Tools::getToken(false),
            'pack_template' => 'module:phsuperpack/views/templates/front/pack-product.tpl',
            'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
            'mediumSize' => Image::getSize(ImageType::getFormattedName('medium')),
            'psVersion' => version_compare(_PS_VERSION_, 1.7, '<'),
        ));
        return $this->context->smarty->fetch('module:phsuperpack/views/templates/front/display_footer_product.tpl');
    }

    public function getActivePackProduct($id_product_pack, $id_product)
    {
        $sql = "SELECT DISTINCT active, position FROM "._DB_PREFIX_."phsuperpack_product WHERE id_product=".$id_product." AND id_product_pack=".$id_product_pack;
        $query = Db::getInstance()->executeS($sql);

        if (!empty($query)) {
            return $query[0];
        }
        return false;
    }

    private function getProductsNames($id_pack, $id_product_main)
    {
        $sql = "SELECT id_product_item, quantity FROM "._DB_PREFIX_."pack WHERE id_product_pack=".$id_pack." AND id_product_attribute_item=0";
        $products = Db::getInstance()->executeS($sql);

        $productsString = '';
        foreach ($products as $product) {
            if ($product['id_product_item'] != $id_product_main) {
                $tmp = ' | '.Product::getProductName($product['id_product_item'], null, Context::getContext()->language->id).' ('.$product['quantity'].')';

                $productsString .= $tmp;
            }
        }

        return $productsString;
    }

    public function getPacks($id_product)
    {
        $sql = "SELECT DISTINCT id_product_pack FROM "._DB_PREFIX_."phsuperpack_product WHERE id_product=".$id_product." ORDER BY position ASC";
        $products = Db::getInstance()->executeS($sql);

        if (!empty($products)) {
            $data = [];
            foreach ($products as $product) {
                $id_product_pack = $product['id_product_pack'];
                $productObj = new Product($id_product_pack);

                $packData = $this->getActivePackProduct($id_product_pack, $id_product);
                $data[] = [
                    'id' => $id_product_pack,
                    'name' => $productObj->name[Context::getContext()->language->id],
                    'products_names' => $this->getProductsNames($productObj->id, Tools::getValue('id_product')),
                    'price_nett' => Tools::displayPrice($productObj->getPriceWithoutReduct(true)),
                    'price_gross' => Tools::displayPrice($productObj->getPriceWithoutReduct()),
                    'discount_nett' => Tools::displayPrice(Product::getPriceStatic($id_product_pack, false)),
                    'discount_gross' => Tools::displayPrice(Product::getPriceStatic($id_product_pack)),
                    'quantity' => StockAvailable::getQuantityAvailableByProduct($id_product_pack),
                    'active' => $packData['active'],
                    'position' => $packData['position'],
                ];
            }

            return $data;
        }

        return null;
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $output = '';
        $id_product = (int)Tools::getValue('id_product');
        if ($id_product <= 0) {
            $id_product = (int)$params['id_product'];
        }

        $token_products = Tools::getAdminToken('AdminProducts'.(int)Tab::getIdFromClassName('AdminProducts').(int)$this->context->employee->id);
        /*
        $this->context->smarty->assign(array(
            //'list' => $list,
            'product_pack_get_list' => $this->context->link->getAdminLink('AdminProductPack')
                . '&ajax=1&action=getpacklist',
            'product_pack_get_form' => $this->context->link->getAdminLink('AdminProductPack')
                . '&ajax=1&action=getpackform',
            'save_product_pack' => $this->context->link->getAdminLink('AdminProductPack')
                . '&ajax=1&action=savepack',
            'get_product_pack_price' => $this->context->link->getAdminLink('AdminProductPack')
                . '&ajax=1&action=getpackprice',
            'get_product_pack_price_disc' => $this->context->link->getAdminLink('AdminProductPack')
                . '&ajax=1&action=getpackpricedisc',
            'delete_product_pack' => $this->context->link->getAdminLink('AdminProductPack')
                . '&ajax=1&action=deletepack',
            'active_product_pack' => $this->context->link->getAdminLink('AdminProductPack')
                . '&ajax=1&action=activepack',
            'position_product_pack' => $this->context->link->getAdminLink('AdminProductPack')
                . '&ajax=1&action=positionpack',
            'id_product' => $id_product,
            'base_admin' => $this->context->link->getBaseLink() . basename(_PS_ADMIN_DIR_) . '/',
            'products_list' => $this->context->link->getAdminLink('AdminProductPack')
                . '&ajax=1&action=productslist&forceJson=1&excludeIds=xexcludeidsx&disableCombination=0&exclude_packs=1&excludeVirtuals=0&limit=20&id_product='.$id_product,
            'static_token' => $token_products,
            'psVersion' => version_compare(_PS_VERSION_, 1.7, '<'),
            'currency' => Context::getContext()->currency
        ));
        */
        $tax = new Tax(Configuration::get('PH_SUPERPACK_TAX'));

        $this->context->smarty->assign([
            'id_product' => $id_product,
            'search_product' => $this->context->link->getModuleLink('phsuperpack', 'pack', ['action' => 'search_product']),
            'get_product_data' => $this->context->link->getModuleLink('phsuperpack', 'pack', ['action' => 'get_searched_product_data']),
            'create_pack' => $this->context->link->getModuleLink('phsuperpack', 'pack', ['action' => 'createPack']),
            'fetch_packs' => $this->context->link->getModuleLink('phsuperpack', 'pack', ['action' => 'fetchPacks']),
            'getPackData' => $this->context->link->getModuleLink('phsuperpack', 'pack', ['action' => 'getPackData']),
            'deletePack' => $this->context->link->getModuleLink('phsuperpack', 'pack', ['action' => 'deletePack']),
            'changeActive' => $this->context->link->getModuleLink('phsuperpack', 'pack', ['action' => 'changeActive']),
            'changePosition' => $this->context->link->getModuleLink('phsuperpack', 'pack', ['action' => 'changePosition']),
            'currency' => Context::getContext()->currency,
            'packs' => $this->getPacks($id_product),
            'tax_rate' => ($tax->rate / 100) + 1,
            'basic_price' => (float)Product::getPriceStatic($id_product),
            'basic_price_nett' => (float)Product::getPriceStatic($id_product, false),
        ]);

        $output .= $this->context->smarty->fetch('module:phsuperpack/views/templates/admin/new_product.tpl');
        return $output;
    }

    public function getPackProductId($id_product_pack)
    {
        $id_shop = (int)Context::getContext()->shop->id;

        $q = 'SELECT hs.`id_superpack_product`, hs.`id_product_pack`, hs.id_product FROM '._DB_PREFIX_.'phsuperpack_product hs
			WHERE hs.id_shop = ' . (int)$id_shop . '
			AND hs.id_product_pack = ' . (int)$id_product_pack;
        return DB::getInstance()->getRow($q);
    }

    public function hookDisplayCartPack($params)
    {


        $id_product = (int)$params['id_product'];
        if ($id_product > 0) {
            $pack = $this->getPackProductId($id_product);
            if ($pack != null) {
                $lang_id = (int)Configuration::get('PS_LANG_DEFAULT');
                $pr = new Product((int)$pack['id_product_pack']);

                $price = $pr->getPrice(true, null, 6);
                $regular_price = $pr->getPriceWithoutReduct(false, null, 6);
                $priced = Tools::displayPrice($price);
                $regular_priced = Tools::displayPrice($regular_price);// $pr->regular_price;
                $no_pack = $pr->getNoPackPrice();
                $no_pack_priced = Tools::displayPrice($no_pack);

                $has_discount = false;
                $discount = 0;
                $discount_proc = 0;
                if ($regular_price > $price) {
                    $has_discount = true;
                    $discount = Tools::displayPrice($regular_price - $price);
                    $discount_proc = "-".Tools::ps_round(100 - ((100 * $price) / $regular_price), 2)."%";
                }

                $items = Pack::getItemTable((int)$pack['id_product_pack'], $lang_id, true);

                $arrayItem = array();
                foreach ($items as $item) {
                    if ((int)$item['id_product'] == (int)$pack['id_product']) {
                        $arrayItem[] = $item;
                    }
                }

                foreach ($items as $item) {
                    if ((int)$item['id_product'] != (int)$pack['id_product']) {
                        $arrayItem[] = $item;
                    }
                }
                $items = $arrayItem;

                $this->context->smarty->assign(array(
                    'price' => $priced,
                    'regular_price' => $regular_priced,
                    'no_pack_price' => $no_pack_priced,
                    'has_discount' => $has_discount,
                    'discount' => $discount,
                    'discount_proc' => $discount_proc,
                    'items' => $items,
                    'static_token' => Tools::getToken(false),
                    'pack_template' => 'module:phsuperpack/views/templates/front/pack-product.tpl',
                    'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
                    'mediumSize' => Image::getSize(ImageType::getFormattedName('medium')),
                ));
                return $this->context->smarty->fetch('module:phsuperpack/views/templates/front/display_cart_product.tpl');
            }
        }
    }

    protected function assignPhSearchVars()
    {
        $link = $action = $this->context->link->getModuleLink('phsuperpack', 'phsearch', array());
        $controller_name = 'phsearch';

        if (Configuration::get('PH_DISABLE_AC')) {
            $use_autocomplete = 0;
        } elseif (Configuration::get('PH_ENABLE_AC_PHONE')) {
            $use_autocomplete = 2;
        } else {
            $use_autocomplete = 1;
        }

        $templateVars = array(
            'ph_search_action' => $action,
            'ph_search_link' => $link,
            'ph_search_controller' => $controller_name,
            'blocksearch_type' => 'top',
            'id_lang' => $this->context->language->id,
            'url_rewriting' => $link,
            'use_autocomplete' => $use_autocomplete,
            'minwordlen' => (int) Configuration::get('PS_SEARCH_MINWORDLEN'),
            'l_products' => $this->l('Products'),
            'l_categories' => $this->l('Categories'),
            'l_no_results_found' => $this->l('No results found'),
            'l_more_results' => $this->l('More results'),
            'ENT_QUOTES' => ENT_QUOTES,
            'search_ssl' => Tools::usingSecureMode(),
            'self' => dirname(__FILE__),
        );

        $this->context->smarty->assign($templateVars);
        if (method_exists('Media', 'addJsDef') && version_compare(_PS_VERSION_, 1.7, '>=')) {
            Media::addJsDef(array('phsearch' => $templateVars));
        }
    }

    public function hookActionUpdateQuantity($params)
    {
        $id_product = (int)$params['id_product'];
        $id_product_attribute = (int)$params['id_product_attribute'];

        $quantity = (int)$params['quantity'];
        $context = Context::getContext();
        $id_shop = (int)$context->shop->id;

        if ($id_product <= 0) {
            return;
        }

        $list = $this->getPack($id_product);
        if (count($list) <= 0) {
            $list = $this->getPackItem($id_product);
        }

        if (count($list) <= 0) {
            return;
        }

        $lang_id = (int)Configuration::get('PS_LANG_DEFAULT');
        foreach ($list as $key => $pack) {
            $pr = new Product((int)$pack['id_product_pack']);
            $q = Pack::getQuantity($pack['id_product_pack']);

            $packQuantity = 0;
            $index = -1;
            $qty = 1;
            $pack_products_tab = Pack::getItemTable((int)$pack['id_product_pack'], $lang_id, true);
            foreach ($pack_products_tab as $line) {
                $item_id = (int)$line['id_product'];
                $item_id_attribute = (int)$line['id_product_attribute'];
                if ($item_id > 0) {
                    $index++;
                    if ($item_id_attribute > 0) {
                        $itemQuantity = Product::getQuantity($item_id, $item_id_attribute);
                    } else {
                        $itemQuantity = Product::getQuantity($item_id);
                    }

                    $nbPackAvailableForItem = floor($itemQuantity / $qty);

                    if ($index === 0) {
                        $packQuantity = $nbPackAvailableForItem;
                        continue;
                    }

                    if ($nbPackAvailableForItem < $packQuantity) {
                        $packQuantity = $nbPackAvailableForItem;
                    }
                }
            }
            StockAvailable::setQuantity($pr->id, 0, (int)$packQuantity);
        }
    }

    public function adaptConfigForm($arr)
    {
        if (is_array($arr)) {
            foreach ($arr as &$fieldset) {
                if (is_array($fieldset['form']['input'])) {
                    foreach ($fieldset['form']['input'] as &$input) {
                        if ($input['type'] == 'switch') {
                            $input['type'] = 'radio';
                            $input['class'] = 't';
                        }
                    }
                }
            }
        }
        return $arr;
    }

    public function retroCompatIncludes()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/phsuperpack17.css', 'all');
        if (version_compare(_PS_VERSION_, 1.7, '<')) {
            $this->context->controller->addCSS($this->_path . 'views/css/phsuperpack16.css', 'all');
        }
    }
    public function hookUpdateProduct($params)
    {
        $check_price = Configuration::get('PH_CHECK_PRICE');
        if ($check_price) {
            $packs = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'pack` WHERE `id_product_item` = '.(int)$params['id_product']);
            if (!empty($packs)) {
                foreach ($packs as $pack) {
                    $products = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'pack` WHERE `id_product_pack` = '.$pack['id_product_pack']);
                    if (!empty($products)) {
                        $total = 0;
                        foreach ($products as $product) {
                            $pro = new Product($product['id_product_item'], false, $this->context->cookie->id_lang);
                            $total += ($product['quantity'] * $pro->getPrice(false));
                        }
                        $pack_product = new Product($pack['id_product_pack'], false, $this->context->cookie->id_lang);
                        $pack_product->price = number_format($total, 6, '.', '');
                        $pack_product->update();
                    }
                }
            }
        }
    }
}
