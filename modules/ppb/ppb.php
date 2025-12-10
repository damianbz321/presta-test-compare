<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2023 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
if (file_exists(_PS_MODULE_DIR_ . 'ppb/models/block.php')) {
    require_once _PS_MODULE_DIR_ . 'ppb/models/block.php';
}

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;


class Ppb extends Module
{
    public $smartyTemplatesManager;

    public function __construct()
    {

        $this->name = 'ppb';
        $this->tab = 'advertising_marketing';
        $this->author = 'MyPresta.eu';
        $this->version = '2.7.7';
        $this->module_key = '6169e3dab76c5c67fb99739410409ba9';
        $this->mypresta_link = 'https://mypresta.eu/modules/front-office-features/related-products-pro.html';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Related products pro');
        $this->description = $this->l('Module allows to create & display blocks or tabs with products product page');
        $this->addproduct = $this->l('Add');
        $this->noproductsfound = $this->l('No results found');
        $this->checkforupdates();
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        if (Tools::getValue('controller', 'false') != 'IqitElementorEditor') {
            if (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.7.6', '>=') && !$this->psversion(0) >= 8) {
                $this->context->controller->addJqueryPlugin('autocomplete');
            } else {
                $this->context->controller->addCSS($this->_path . 'views/admin/autocomplete.css', 'all');
                $this->context->controller->addJS($this->_path . 'views/admin/autocomplete.js');
                $this->context->controller->addJS($this->_path . 'views/admin/jquery-migrate-1.2.1.min.js');
            }
            $this->context->controller->addJqueryUI('ui.sortable');
            $this->context->controller->addCSS($this->_path . 'views/admin/ppb.css', 'all');
            if (version_compare(substr(_PS_VERSION_, 0, 5), '8.1.0', '>=')) {
                $this->context->controller->addJS($this->_path . 'views/admin/ppb8.js');
            } else {
                $this->context->controller->addJS($this->_path . 'views/admin/ppb.js');
            }
        }
    }

    public function displayFlags($languages, $default_language, $ids, $id, $return = false, $use_vars_instead_of_ids = false)
    {
        if (count($languages) == 1) {
            return false;
        }

        $language = new Language($default_language);

        $output = '
        <button type="button" class="btn btn-default dropdown-toggle" onclick="toggleLanguageFlags(this);" alt="" tabindex="-1" data-toggle="dropdown"/>' . $language->iso_code . '<i class="icon-caret-down"></i></button>
        <ul class="dropdown-menu">';
        foreach ($languages as $language) {
            if ($use_vars_instead_of_ids) {
                $output .= '<li class="languageli"><a tabindex="-1" onclick="changeMain($(this),"' . trim($language['iso_code']) . '");" href="javascript:changeLanguage(\'' . $id . '\', ' . $ids . ', ' . $language['id_lang'] . ', \'' . $language['iso_code'] . '\');" />' . $language['name'] . '</a></li>';
            } else {
                $output .= '<li class="languageli"><a tabindex="-1" onclick="changeMain($(this),\'' . trim($language['iso_code']) . '\');" href="javascript:changeLanguage(\'' . $id . '\', \'' . $ids . '\', ' . $language['id_lang'] . ', \'' . $language['iso_code'] . '\');" />' . $language['name'] . '</a></li>';
            }
        }
        $output .= '</ul>';

        if ($return) {
            return $output;
        }
        echo $output;
    }

    public static function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp = explode('.', $version);
        if ($part == 0) {
            return $exp[0];
        }
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

    public function install()
    {
        if (parent::install() == false or
            !$this->registerHook('actionAdminControllerSetMedia') or
            !$this->registerHook('displayLeftColumnProduct') or
            !$this->registerHook('displayRightColumnProduct') or
            !$this->registerHook('displayShoppingCartFooter') or
            !$this->registerHook('displayProductExtraContent') or
            !$this->registerHook('displayHeader') or
            !$this->registerHook('displayFooterProduct') or
            !$this->registerHook('displayAdminProductsExtra') or
            !$this->registerHook('displayPpbModule') or
            !$this->registerHook('displayOrderConfirmation') or
            !$this->registerHook('displayPpbAccessories') or
            !$this->installdb() or
            !$this->registerHook('productTab') or
            !$this->registerHook('productTabContent')) {
            return false;
        }

        return true;
    }

    private function installdb()
    {
        $prefix = _DB_PREFIX_;
        $statements = array();
        $statements[] = "CREATE TABLE IF NOT EXISTS `${prefix}ppbp_block` (" . '`id` int(10) NOT NULL AUTO_INCREMENT,' . '`type` int(3),' . '`block_position` int(3),' . '`active` int(1),' . '`position` int(5),' . '`value` TEXT,' . '`nb` int(4),' . '`shop` int(4) DEFAULT 4,' . '`list_products` TEXT,' . '`before` int(1) DEFAULT 0,' . '`after` int(1) DEFAULT 1, ' . ' PRIMARY KEY (`id`)' . ')';
        $statements[] = "CREATE TABLE IF NOT EXISTS `${prefix}ppbp_block_lang` (" . '`id` int(10) NOT NULL,' . '`id_lang` int(10) NOT NULL,' . '`name` VARCHAR(230),' . '`custom_before` TEXT,' . '`custom_after` TEXT' . ') COLLATE="utf8_general_ci"';
        foreach ($statements as $statement) {
            if (!Db::getInstance()->Execute($statement)) {
                return false;
            }
        }
        $this->inconsistency(0);

        return true;
    }

    public static function jsonEncode($data, $options = 0, $depth = 512)
    {
        return json_encode($data, $options, $depth);
    }

    public function searchTool($type, $resultInput, $replacementType = 'replace')
    {
        return '<input style="width:40px; font-size:10px; " type="text" placeholder="' . $this->l('Search') . '" id="searchTool_' . $type . '" data-replacementtype="' . $replacementType . '" data-resultinput="' . $resultInput . '" data-type="' . $type . '" class="searchToolInput searchTool_' . $type . '"/>';
    }

    public function searchForID($table, $field, $term, $shop = false)
    {
        $result = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . $table . '` WHERE ' . $field . " LIKE '%" . psql($term) . "%' " . ($shop != false ? 'AND id_shop="' . $shop . '"' : '') . ' GROUP BY id_' . str_replace('_lang', '', $table));
        return $result;
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $this->context->smarty->assign('PPBLink', $this->context->link->getAdminLink('AdminModules', true) . '&configure=ppb&tab_module=ppb&ajax=1&module_name=ppb');
        $this->context->smarty->assign('psversion', (version_compare(substr(_PS_VERSION_, 0, 5), '8.1.0', '>=') ? true : false));

        $_GET['id_product'] = $params['id_product'];

        if (Tools::isSubmit('add_new_block')) {
            $block = new PpbBlock();
            $block->name = Tools::getValue('name');
            $block->titleURL = Tools::getValue('titleURL');
            $block->type = Tools::getValue('ppb_type');
            $block->shop = $this->context->shop->id;
            $block->block_position = Tools::getValue('ppbp_block_position');
            $block->active = Tools::getValue('ppb_active');
            $block->value = trim(Tools::getValue('ppb_value'));
            $block->nb = Tools::getValue('ppb_nb');
            $block->list_products = trim(Tools::getValue('ppb_products'));
            $block->before = Tools::getValue('ppb_before');
            $block->custom_before = Tools::getValue('custombefore');
            $block->random = Tools::getValue('ppb_random');
            $block->product = Tools::getValue('id_product');
            $block->global_cat = Tools::getValue('ppb_global_categories');
            $block->global_man = Tools::getValue('ppb_global_manufacturers');
            $block->categories = trim(str_replace(' ', '', Tools::getValue('ppb_categories')));
            $block->manufacturers = trim(str_replace(' ', '', Tools::getValue('ppb_manufacturers')));
            $block->global_prod = Tools::getValue('ppb_global_products');
            $block->g_products = trim(str_replace(' ', '', Tools::getValue('ppb_g_products')));
            $block->g_products_from_method = Tools::getValue('ppb_g_products_from_method');
            $block->global_features = Tools::getValue('ppb_global_features');
            $block->g_features = trim(Tools::getValue('ppb_features'));
            $block->carousell = Tools::getValue('ppb_carousell');
            $block->carousell_auto = Tools::getValue('ppb_carousell_auto');
            $block->carousell_loop = Tools::getValue('ppb_carousell_loop');
            $block->carousell_pager = Tools::getValue('ppb_carousell_pager');
            $block->carousell_controls = Tools::getValue('ppb_carousell_controls');
            $block->carousell_nb = Tools::getValue('ppb_carousell_nb');
            $block->carousell_nb_mobile = Tools::getValue('ppb_carousell_nb_mobile');
            $block->carousell_nb_tablet = Tools::getValue('ppb_carousell_nb_tablet');
            $block->searchphrase = Tools::getValue('ppb_type_searchphrase');
            $block->everywhere = Tools::getValue('everywhere');
            $block->instock = Tools::getValue('instock');
            $block->hidewc = Tools::getValue('hidewc', 0);
            $block->stockcheck = Tools::getValue('stockcheck', 0);
            $block->internal_name = Tools::getValue('internal_name', '');
            $block->cat_price_from = Tools::getValue('cat_price_from', 0);
            $block->cat_price_to = Tools::getValue('cat_price_to', 0);
            $block->cat_price_from_v = str_replace(',', '.', Tools::getValue('cat_price_from_v', 0));
            $block->cat_price_to_v = str_replace(',', '.', Tools::getValue('cat_price_to_v', 0));
            $block->cat_feature = Tools::getValue('cat_feature', 0);
            $block->cat_feature_v = implode(',', Tools::getValue('cat_feature_v', array()));
            $block->type_feature_v = implode(',', Tools::getValue('type_feature_v', array()));
            $block->cat_stock_filter = Tools::getValue('cat_stock_filter', 0);
            $block->cat_manuf = Tools::getValue('cat_manuf', 0);
            $block->cat_manuf_v = Tools::getValue('cat_manuf_v', 0);
            $block->allconditions = Tools::getValue('ppb_allconditions', 0);
            $block->custom_path = Tools::getValue('ppb_custom_path', 0);
            $block->path = Tools::getValue('ppb_path', '');
            $block->exc_p = Tools::getValue('exc_p');
            $block->exc_p_list = Tools::getValue('exc_p_list');
            $block->add();
        }
        if (Tools::isSubmit('edit_block')) {
            $block = new ppbBlock(Tools::getValue('editblock'));
            $block->name = Tools::getValue('name');
            $block->titleURL = Tools::getValue('titleURL');
            $block->type = Tools::getValue('ppb_type');
            $block->shop = $this->context->shop->id;
            $block->block_position = Tools::getValue('ppbp_block_position');
            $block->active = Tools::getValue('ppb_active');
            $block->value = trim(Tools::getValue('ppb_value'));
            $block->nb = Tools::getValue('ppb_nb');
            $block->list_products = trim(Tools::getValue('ppb_products'));
            $block->before = Tools::getValue('ppb_before');
            $block->custom_before = Tools::getValue('custombefore');
            $block->random = Tools::getValue('ppb_random');
            $block->global_cat = Tools::getValue('ppb_global_categories');
            $block->global_man = Tools::getValue('ppb_global_manufacturers');
            $block->categories = trim(str_replace(' ', '', Tools::getValue('ppb_categories')));
            $block->manufacturers = trim(str_replace(' ', '', Tools::getValue('ppb_manufacturers')));
            $block->global_prod = Tools::getValue('ppb_global_products');
            $block->g_products = trim(str_replace(' ', '', Tools::getValue('ppb_g_products')));
            $block->g_products_from_method = Tools::getValue('ppb_g_products_from_method');
            $block->global_features = Tools::getValue('ppb_global_features');
            $block->g_features = trim(Tools::getValue('ppb_features'));
            $block->carousell = Tools::getValue('ppb_carousell');
            $block->carousell_auto = Tools::getValue('ppb_carousell_auto');
            $block->carousell_loop = Tools::getValue('ppb_carousell_loop');
            $block->carousell_pager = Tools::getValue('ppb_carousell_pager');
            $block->carousell_controls = Tools::getValue('ppb_carousell_controls');
            $block->carousell_nb = Tools::getValue('ppb_carousell_nb');
            $block->carousell_nb_mobile = Tools::getValue('ppb_carousell_nb_mobile');
            $block->carousell_nb_tablet = Tools::getValue('ppb_carousell_nb_tablet');
            $block->searchphrase = Tools::getValue('ppb_type_searchphrase');
            $block->everywhere = Tools::getValue('everywhere');
            $block->instock = Tools::getValue('instock');
            $block->hidewc = Tools::getValue('hidewc', 0);
            $block->stockcheck = Tools::getValue('stockcheck', 0);
            $block->internal_name = Tools::getValue('internal_name', '');
            $block->cat_price_from = Tools::getValue('cat_price_from', 0);
            $block->cat_price_to = Tools::getValue('cat_price_to', 0);
            $block->cat_price_from_v = str_replace(',', '.', Tools::getValue('cat_price_from_v', 0));
            $block->cat_price_to_v = str_replace(',', '.', Tools::getValue('cat_price_to_v', 0));
            $block->cat_feature = Tools::getValue('cat_feature', 0);
            $block->cat_feature_v = implode(',', Tools::getValue('cat_feature_v', array()));
            $block->type_feature_v = implode(',', Tools::getValue('type_feature_v', array()));
            $block->cat_stock_filter = Tools::getValue('cat_stock_filter', 0);
            $block->cat_manuf = Tools::getValue('cat_manuf', 0);
            $block->cat_manuf_v = Tools::getValue('cat_manuf_v', 0);
            $block->allconditions = Tools::getValue('ppb_allconditions', 0);
            $block->custom_path = Tools::getValue('ppb_custom_path', 0);
            $block->path = Tools::getValue('ppb_path', '');
            $block->exc_p = Tools::getValue('exc_p');
            $block->exc_p_list = Tools::getValue('exc_p_list');
            $block->update();
        }
        if (Tools::getValue('editblock', 'false') != 'false') {
            $this->context->smarty->assign('editform', $this->returnAddForm(Tools::getValue('editblock')));
        }
        if (Tools::getValue('addtab', 'false') != 'false') {
            $this->context->smarty->assign('addform', $this->returnAddForm());
        }
        $blocks_prepare = ppbBlock::getAllBlocksByProduct(Tools::getValue('id_product'));
        if (count($blocks_prepare) > 0) {
            $this->context->smarty->assign(array(
                'product_ppb' => (Tools::getValue('showall') == 1 ? $blocks_prepare : $this->prepareblocks($blocks_prepare)),
                'employee_idlang' => $this->context->cookie->id_lang,
                'thismodule' => $this,
                'languages' => $this->context->controller->getLanguages()
            ));
        } else {
            $this->context->smarty->assign(array(
                'employee_idlang' => $this->context->cookie->id_lang,
                'thismodule' => $this,
                'languages' => $this->context->controller->getLanguages()
            ));
        }
        $this->context->smarty->assign('link', $this->context->link);
        $this->context->smarty->assign('bolink', strtok($_SERVER["REQUEST_URI"], '?'));
        $this->context->smarty->assign(array(
            'ppb_config_page' => Context::getContext()->link->getAdminLink('AdminModules', true) . '&configure=ppb&ajax=1'
        ));
        return $this->display(__file__, 'views/templates/admin/tabs.tpl');
    }

    public function returnProducts($block)
    {
        $blocks = $this->prepareblocks($this->prepareBlocksFromDbCmsProductsPro(Ppbblock::getAllBlocksById($block)), true);
        return $this->prepareBlocksProducts($blocks)[0]->products;
    }

    public function hookdisplayOrderConfirmation($params)
    {
        $blocks_before = array();
        $blocks = array();
        $products = array();
        $cart_products = array();

        if (isset($params['objOrder']) || Tools::getValue('id_order', 'false') != 'false' || Tools::getValue('id_cart', 'false') != false) {
            if (Tools::getValue('id_order', 'false') != false && Tools::getValue('id_order') != false) {
                $id_order = Tools::getValue('id_order');
            } elseif (Tools::getValue('id_cart', 'false') != false && Tools::getValue('id_cart') != false) {
                $id_order = Order::getOrderByCartId(Tools::getValue('id_cart'));
            } elseif (isset($params['objOrder']->id)) {
                $id_order = $params['objOrder']->id;
            } else {
                $id_order = false;
            }

            if ($id_order != false) {
                $order = new Order($id_order);
                foreach ($order->getProducts() as $product) {
                    $productt = new Product($product['product_id'], false, $this->context->language->id);
                    $product_details['product'] = $productt;
                    $product_details['categories'] = Product::getProductCategories($product['product_id']);
                    $product_details['features'] = Product::getFeaturesStatic($product['product_id']);
                    $blocks_before[] = $this->prepareBlocksFromDb(PpbBlock::getAllBlocksByPosition(8, $product_details), $product_details);
                    $cart_products[] = $product['product_id'];
                }


                if (count($blocks_before) > 0) {
                    foreach ($blocks_before AS $bk => $blk) {
                        if (isset($blk)) {
                            foreach ($blk AS $bkk => $blkk) {
                                foreach ($blkk->products AS $item) {
                                    if (!in_array($item['id_product'], $cart_products)) {
                                        $products[$item['id_product']] = $item;
                                    }
                                }
                            }
                            if (isset($blkk)) {
                                $blocks[0] = $blkk;
                            }
                        }
                    }
                    if (count($blocks_before) > 1) {
                        $blocks[0]->products = $products;
                        $blocks[0]->name = $this->l('Check also');
                    }
                }

                if (count($products) <= 0) {
                    $blocks = array();
                }

                if (count($blocks) > 0) {
                    $this->context->smarty->assign(array(
                        'blocksProductsOrder' => $this->prepareBlocksProducts($blocks),
                        'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
                    ));
                    $this->context->smarty->assign('ppb_customtplpath', $this->return_ppb_customtplpath());
                    return $this->context->smarty->fetch('module:ppb/views/templates/hook/' . $this->usedThemeName() . 'blocksProductsOrder.tpl');
                }
            }
        }
    }

    public function hookdisplayShoppingCartFooter($params)
    {
        $blocks_before = array();
        $blocks = array();
        $products = array();
        $cart_products = array();
        if (Configuration::get('ppb_rel_cart') == 1) {
            foreach ($this->context->cart->getProducts() as $product) {
                $productt = new Product($product['id_product'], false, $this->context->language->id);
                $product_details['product'] = $productt;
                $product_details['categories'] = Product::getProductCategories($product['id_product']);
                $product_details['features'] = Product::getFeaturesStatic($product['id_product']);
                $blocks_before[] = $this->prepareBlocksFromDb(PpbBlock::getAllBlocksByPosition(3, $product_details), $product_details);
                $cart_products[] = $product['id_product'];
            }

            if (count($blocks_before) > 0) {
                foreach ($blocks_before AS $bk => $blk) {
                    if (isset($blk)) {
                        foreach ($blk AS $bkk => $blkk) {
                            foreach ($blkk->products AS $item) {
                                if (!in_array($item['id_product'], $cart_products)) {
                                    $products[$item['id_product']] = $item;
                                }
                            }
                        }
                        if (isset($blkk)) {
                            $blocks[0] = $blkk;
                        }
                    }
                }
                if (count($blocks_before) > 1) {
                    $blocks[0]->products = $products;
                    $blocks[0]->name = $this->l('Check also');
                }
            }

            if (count($products) <= 0) {
                $blocks = array();
            }

            if (count($blocks) > 0) {
                $this->context->smarty->assign(array(
                    'blocksProductsCart' => $this->prepareBlocksProducts($blocks),
                    'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
                ));
                $this->context->smarty->assign('ppb_customtplpath', $this->return_ppb_customtplpath());
                return $this->context->smarty->fetch('module:ppb/views/templates/hook/' . $this->usedThemeName() . 'blocksProductsCart.tpl');
            }
        }
    }

    public function usedThemeName()
    {
        $theme = Configuration::get('ppb_theme');
        if ($theme !== 'classic') {
            return $theme;
        }
    }

    public function hookdisplayRightColumnProduct()
    {
        $product = new Product(Tools::getValue('id_product'));
        $product_details['product'] = $product;
        $product_details['categories'] = Product::getProductCategories($product->id);
        $blocks = $this->prepareblocks($this->prepareBlocksFromDb(PpbBlock::getAllBlocksByPosition(4, $product_details)));

        $this->context->smarty->assign(array('blocksProductFooter' => $this->prepareBlocksProducts($blocks)));
        $this->context->smarty->assign('ppb_customtplpath', $this->return_ppb_customtplpath());

        return $this->context->smarty->fetch('module:ppb/views/templates/hook/' . $this->usedThemeName() . 'productFooter.tpl');
    }

    public function hookdisplayLeftColumn()
    {
        return $this->hookdisplayLeftColumnProduct();
    }

    public function hookdisplayRightColumn()
    {
        return $this->hookdisplayRightColumnProduct();
    }

    public function hookdisplayLeftColumnProduct()
    {
        $product = new Product(Tools::getValue('id_product'));
        $product_details['product'] = $product;
        $product_details['categories'] = Product::getProductCategories($product->id);
        $blocks = $this->prepareblocks($this->prepareBlocksFromDb(PpbBlock::getAllBlocksByPosition(5, $product_details)));

        $this->context->smarty->assign(array('blocksProductFooter' => $this->prepareBlocksProducts($blocks)));
        $this->context->smarty->assign('ppb_customtplpath', $this->return_ppb_customtplpath());

        return $this->context->smarty->fetch('module:ppb/views/templates/hook/' . $this->usedThemeName() . 'productFooter.tpl');
    }

    public function hookdisplayFooterProduct($params)
    {
        $product = new Product(Tools::getValue('id_product'), true, $this->context->language->id);
        $product_details['product'] = $product;
        $product_details['features'] = Product::getFeaturesStatic($product->id);
        $product_details['categories'] = Product::getProductCategories($product->id);
        $blocks1 = $this->prepareblocks($this->prepareBlocksFromDb(PpbBlock::getAllBlocksByPosition(1, $product_details)));
        $blocks2 = $this->prepareblocks($this->prepareBlocksFromDb(PpbBlock::getAllBlocksByPosition(2, $product_details)));
        $blocks = array_merge($blocks1, $blocks2);
        $this->context->smarty->assign(array('blocksProductFooter' => $this->prepareBlocksProducts($blocks)));
        $this->context->smarty->assign('ppb_customtplpath', $this->return_ppb_customtplpath());
        return $this->context->smarty->fetch('module:ppb/views/templates/hook/' . $this->usedThemeName() . 'productFooter.tpl');
    }

    public function hookdisplayPpbAccessories($params)
    {
        $product = new Product(Tools::getValue('id_product'), true, $this->context->language->id);
        $product_details['product'] = $product;
        $product_details['features'] = Product::getFeaturesStatic($product->id);
        $product_details['categories'] = Product::getProductCategories($product->id);
        $this->context->smarty->assign(array('blocksProductFooter' => $this->prepareBlocksProducts($this->prepareblocks($this->prepareBlocksFromDb(PpbBlock::getAllBlocksByPosition(7, $product_details))))));

        $this->smarty->assign(array(
            'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
        ));

        return $this->display(__file__, 'views/templates/hook/accessories.tpl');
    }

    public function hookdisplayPpbModule($params)
    {
        $product = new Product(Tools::getValue('id_product'));
        $product_details['product'] = $product;
        $product_details['categories'] = Product::getProductCategories($product->id);
        $this->context->smarty->assign(array('blocksProductFooter' => $this->prepareBlocksProducts($this->prepareblocks($this->prepareBlocksFromDb(PpbBlock::getAllBlocksByPosition(6, $product_details))))));
        $this->context->smarty->assign('ppb_customtplpath', $this->return_ppb_customtplpath());
        return $this->context->smarty->fetch('module:ppb/views/templates/hook/' . $this->usedThemeName() . 'productFooter.tpl');
    }

    public function return_ppb_customtplpath()
    {
        $ppb_customtplpath = Configuration::get('ppb_customtplpath');
        if ($ppb_customtplpath != '' && $ppb_customtplpath != false) {
            return $ppb_customtplpath;
        } else {
            return 'catalog/_partials/miniatures/product.tpl';
        }
    }

    public function hookProductTabContent($params)
    {
        if (Configuration::get('ppb_tabs') == 0) {
            $product = new Product(Tools::getValue('id_product'));
            $product_details['product'] = $product;
            $product_details['categories'] = Product::getProductCategories($product->id);
            $blocks = $this->prepareblocks($this->prepareBlocksFromDb(PpbBlock::getAllBlocksByPosition(0, $product_details)));
            $this->context->smarty->assign(array('blocks' => $this->prepareBlocksProducts($blocks)));
            $this->context->smarty->assign('ppb_customtplpath', $this->return_ppb_customtplpath());

            return $this->context->smarty->fetch('module:ppb/views/templates/hook/' . $this->usedThemeName() . 'productTabContent.tpl');
        }
    }

    public function hookProductTab($params)
    {
        if (Configuration::get('ppb_tabs') == 0) {
            $product = new Product(Tools::getValue('id_product'));
            $product_details['product'] = $product;
            $product_details['categories'] = Product::getProductCategories($product->id);
            $blocks = $this->prepareblocks($this->prepareBlocksFromDb(Ppbblock::getAllBlocksByPosition(0, $product_details)));
            $this->context->smarty->assign(array('blocks' => $this->prepareBlocksProducts($blocks)));
            $this->context->smarty->assign('ppb_customtplpath', $this->return_ppb_customtplpath());

            return $this->context->smarty->fetch('module:ppb/views/templates/hook/' . $this->usedThemeName() . 'productTab.tpl');
        }
    }

    public static function geImagesByID($id_product, $limit = 0)
    {
        $id_image = Db::getInstance()->ExecuteS('SELECT `id_image` FROM `' . _DB_PREFIX_ . 'image` WHERE cover=1 AND `id_product` = ' . (int)$id_product . ' ORDER BY position ASC LIMIT 0, ' . (int)$limit);
        $toReturn = array();
        if (!$id_image) {
            return;
        } else {
            foreach ($id_image as $image) {
                $toReturn[] = $id_product . '-' . $image['id_image'];
            }
        }

        return $toReturn;
    }

    public function getOrderProducts(array $products_id, $limit)
    {
        $q_orders = 'SELECT o.id_order
        FROM ' . _DB_PREFIX_ . 'orders o
        LEFT JOIN ' . _DB_PREFIX_ . 'order_detail od ON (od.id_order = o.id_order)
        WHERE o.valid = 1 AND od.product_id IN (' . implode(',', $products_id) . ')';
        $orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($q_orders);
        $final_products_list = array();
        $final_products_listt = array();
        if (count($orders) > 0) {
            $list = '';
            foreach ($orders as $order) {
                $list .= (int)$order['id_order'] . ',';
            }
            $list = rtrim($list, ',');
            $list_product_ids = join(',', $products_id);
            $order_products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT DISTINCT od.product_id
                FROM ' . _DB_PREFIX_ . 'order_detail od
                WHERE od.id_order IN (' . $list . ')
                LIMIT ' . $limit);
            foreach ($order_products as $tproduct) {
                $tproduct = $tproduct['product_id'];
                if ($tproduct != '' && $tproduct != Tools::getValue('id_product')) {
                    $x = (array)new Product($tproduct, true, $this->context->language->id);
                    $pr = new Product($tproduct, true, $this->context->language->id);
                    if ($pr->active == 1) {
                        $final_products_list[$tproduct] = $x;
                        $final_products_list[$tproduct]['id_product'] = $tproduct;
                        $image = self::geImagesByID($tproduct, 1);
                        $picture = explode('-', $image[0]);
                        $final_products_list[$tproduct]['id_image'] = $picture[1];
                    }
                }
            }
            $final_products_listt = Product::getProductsProperties($this->context->language->id, $final_products_list);
        }

        return $final_products_listt;
    }

    public function prepareBlocksFromDbCmsProductsPro($blocks)
    {
        $blockss = array();
        if (count($blocks) <= 0) {
            return $blockss;
        }

        foreach ($blocks as $key => $value) {
            if ($value->type == 1) {
                $explode = explode(',', $value->value);
                $explode_features = explode(',', $value->cat_feature_v);

                if (count($explode) >= 1) {
                    // IMPLODE CATEGORIES
                    $to_implode = array();
                    foreach ($explode AS $k) {
                        if ($k != "" && (int)$k > 0) {
                            $to_implode[] = (int)$k;
                        }
                    }
                    $implode = implode(',', $to_implode);

                    // IMPLODE FEATURES
                    $to_implode_features = array();
                    foreach ($explode_features AS $k) {
                        if ($k != "" && (int)$k > 0) {
                            $to_implode_features[] = (int)$k;
                        }
                    }
                    $implode_features = implode(',', $to_implode_features);


                    //CHECK PRICES
                    if ($value->cat_price_from != 0) {
                        $value->cat_price_from_v = (float)$value->cat_price_from_v;
                    } else {
                        $value->cat_price_from_v = false;
                    }

                    if ($value->cat_price_to != 0) {
                        $value->cat_price_to_v = (float)$value->cat_price_to_v;
                    } else {
                        $value->cat_price_to_v = false;
                    }

                    if ($value->random == 1) {
                        $blocks[$key]->products = $this->getProductsPro($this->context->language->id, 0, $value->nb, null, null, null, true, true, $value->nb, true, null, $implode, $value->cat_price_from_v, $value->cat_price_to_v, $implode_features, (bool)$value->cat_stock_filter, $value->cat_manuf, $value->cat_manuf_v);
                    } else {
                        $blocks[$key]->products = $this->getProductsPro($this->context->language->id, 0, $value->nb, null, null, null, true, false, $value->nb, true, null, $implode, $value->cat_price_from_v, $value->cat_price_to_v, $implode_features, (bool)$value->cat_stock_filter, $value->cat_manuf, $value->cat_manuf_v);
                    }
                }
            }
            if ($value->type == 2) {
                $blocks[$key]->products = Product::getNewProducts($this->context->language->id, 0, $value->nb);
            }
            if ($value->type == 3) {
                $blocks[$key]->products = ProductSale::getBestSales($this->context->language->id, 0, $value->nb);
            }
            if ($value->type == 4) {
                $blocks[$key]->products = Product::getPricesDrop($this->context->language->id, 0, $value->nb, false);
            }
            if ($value->type == 5) {
                $explode = explode(',', trim($value->list_products));
                $explode = array_slice($explode, 0, $value->nb);
                foreach ($explode as $tproduct) {
                    if ($tproduct != '') {
                        $x = (array)new Product($tproduct, true, $this->context->language->id);
                        $pr = new Product($tproduct, true, $this->context->language->id);
                        if ($value->cat_stock_filter == 1) {
                            if ($pr->quantity <= 0) {
                                continue;
                            }
                        }

                        if (!isset($blockss[$key])) {
                          $blockss[$key] = new StdClass();
                        }

                        if ($pr->active == 1 && $pr->id != null) {
                            if ($value->instock == 1) {
                                if ($pr->quantity > 0) {
                                    $blockss[$key]->products[$tproduct] = $x;
                                    $blockss[$key]->products[$tproduct]['id_product'] = $tproduct;
                                }
                            } else {
                                {
                                    $blockss[$key]->products[$tproduct] = $x;
                                    $blockss[$key]->products[$tproduct]['id_product'] = $tproduct;
                                }
                            }
                        }
                    }
                }
                $blocks[$key]->products = Product::getProductsProperties($this->context->language->id, $blockss[$key]->products);
                if ($value->random == 1) {
                    shuffle($blocks[$key]->products);
                }
            }
            if ($value->type == 6) {
                $category = new Category(Configuration::get('related_category' . Tools::getValue('id_product')));
                $blocks[$key]->products = $category->getProducts($this->context->cookie->id_lang, 0, Configuration::get('related_nb' . Tools::getValue('id_product')));
            }
            if ($value->type == 7) {
                $blocks[$key]->products = $this->getOrderProducts(array((int)Tools::getValue('id_product')), $blocks[$key]->nb);
            }
            if ($value->type == 8) {
                $search_products = Search::find($this->context->language->id, $blocks[$key]->searchphrase, 1, $blocks[$key]->nb);
                if ($search_products['total'] > 0) {
                    $blocks[$key]->products = $search_products['result'];
                }
            }
            if ($value->type == 9) {
                if (Tools::getValue('id_product', 'false') != 'false') {
                    $product = new Product(Tools::getValue('id_product'), true, $this->context->language->id);
                    $category = new Category($product->id_category_default, $this->context->language->id, $this->context->shop->id);

                    $explode = explode(',', $product->id_category_default);
                    $explode_features = explode(',', $value->cat_feature_v);

                    if (count($explode) > 1) {
                        // IMPLODE CATEGORIES
                        $implode = $product->id_category_default;

                        // IMPLODE FEATURES
                        $to_implode_features = array();
                        foreach ($explode_features AS $k) {
                            if ($k != "" && (int)$k > 0) {
                                $to_implode_features[] = (int)$k;
                            }
                        }
                        $implode_features = implode(',', $to_implode_features);


                        //CHECK PRICES
                        if ($value->cat_price_from != 0) {
                            $value->cat_price_from_v = (float)$value->cat_price_from_v;
                        } else {
                            $value->cat_price_from_v = false;
                        }

                        if ($value->cat_price_to != 0) {
                            $value->cat_price_to_v = (float)$value->cat_price_to_v;
                        } else {
                            $value->cat_price_to_v = false;
                        }

                        if ($value->random == 1) {
                            $blocks[$key]->products = $this->getProductsPro($this->context->language->id, 0, $value->nb, null, null, null, true, true, $value->nb, true, null, $implode, $value->cat_price_from_v, $value->cat_price_to_v, $implode_features, (bool)$value->cat_stock_filter, $value->cat_manuf, $value->cat_manuf_v);
                        } else {
                            $blocks[$key]->products = $this->getProductsPro($this->context->language->id, 0, $value->nb, null, null, null, true, false, $value->nb, true, null, $implode, $value->cat_price_from_v, $value->cat_price_to_v, $implode_features, (bool)$value->cat_stock_filter, $value->cat_manuf, $value->cat_manuf_v);
                        }
                    }
                }
            }
            if ($value->type == 16) {
                if (Tools::getValue('id_product', 'false') != 'false') {
                    $product = new Product(Tools::getValue('id_product'), true, $this->context->language->id);
                    $category = new Category($product->id_category_default, $this->context->language->id, $this->context->shop->id);

                    $explode = explode(',', $product->id_category_default);
                    $explode_features = explode(',', $value->cat_feature_v);

                    if (count($explode) > 1) {
                        // IMPLODE CATEGORIES
                        $implode = $product->id_category_default;

                        // IMPLODE FEATURES
                        $to_implode_features = array();
                        foreach ($explode_features AS $k) {
                            if ($k != "" && (int)$k > 0) {
                                $to_implode_features[] = (int)$k;
                            }
                        }
                        $implode_features = implode(',', $to_implode_features);


                        //CHECK PRICES
                        if ($value->cat_price_from != 0) {
                            $value->cat_price_from_v = (float)$value->cat_price_from_v;
                        } else {
                            $value->cat_price_from_v = false;
                        }

                        if ($value->cat_price_to != 0) {
                            $value->cat_price_to_v = (float)$value->cat_price_to_v;
                        } else {
                            $value->cat_price_to_v = false;
                        }

                        if ($value->random == 1) {
                            $blocks[$key]->products = $this->getProductsPro($this->context->language->id, 0, $value->nb, null, null, null, true, true, $value->nb, true, null, $implode, $value->cat_price_from_v, $value->cat_price_to_v, $implode_features, (bool)$value->cat_stock_filter, $value->cat_manuf, $value->cat_manuf_v, true, true);
                        } else {
                            $blocks[$key]->products = $this->getProductsPro($this->context->language->id, 0, $value->nb, null, null, null, true, false, $value->nb, true, null, $implode, $value->cat_price_from_v, $value->cat_price_to_v, $implode_features, (bool)$value->cat_stock_filter, $value->cat_manuf, $value->cat_manuf_v, true, true);
                        }
                    }
                }
            }
            if ($value->type == 10) {
                $productsViewed = (isset(Context::getContext()->cookie->viewed) && !empty(Context::getContext()->cookie->viewed)) ? array_slice(array_reverse(explode(',', Context::getContext()->cookie->viewed)), 0, $value->nb * 2) : array();
                if (count($productsViewed) > 0) {
                    foreach ($productsViewed as $tproduct) {
                        if ($tproduct != '') {
                            $x = (array)new Product($tproduct, true, $this->context->language->id);
                            $pr = new Product($tproduct, true, $this->context->language->id);

                            if ($pr->active == 1 && $pr->id != null) {
                                if ($value->instock == 1) {
                                    if ($pr->quantity > 0) {
                                        $blockss[$key]->products[$tproduct] = $x;
                                        $blockss[$key]->products[$tproduct]['id_product'] = $tproduct;
                                    }
                                } else {
                                    {
                                        $blockss[$key]->products[$tproduct] = $x;
                                        $blockss[$key]->products[$tproduct]['id_product'] = $tproduct;
                                    }
                                }
                            }
                        }
                    }
                    foreach ($blockss[$key]->products AS $k => $v) {
                        if (Tools::getValue('id_product') == $v['id_product']) {
                            unset($blockss[$key]->products[$k]);
                        }
                    }
                    $blockss[$key]->products = array_slice($blockss[$key]->products, 0, $value->nb);
                }
                $blocks[$key]->products = Product::getProductsProperties($this->context->language->id, $blockss[$key]->products);

            }
        }

        return $this->prepareblocksCmsProductsPro($blocks);
    }

    public function prepareblocksCmsProductsPro($blocks)
    {
        $array = array();
        $product = new Product(Tools::getValue('id_product'), true);
        foreach ($blocks as $block) {
            $array[] = $block;
        }

        $slices = (int)Configuration::get('ppb_limit_boxes');
        if (is_int($slices)) {
            if ($slices > 0) {
                $array = array_slice($array, 0, $slices);
            }
        }
        return $array;
    }

    public function excludeViewedProduct($id_product, $products, $nb)
    {
        if (is_array($products)) {
            foreach ($products AS $key => $product) {
                if ($product['id_product'] == $id_product) {
                    unset ($products[$key]);
                }
            }

            if (is_array($products)) {
                if (count($products) > $nb) {
                    array_pop($products);
                }
            }
        }
        return $products;
    }

    public function prepareBlocksFromDb($blocks, $product_details = false)
    {
        $blockss = array();
        if (count($blocks) <= 0) {
            return $blockss;
        }
        $id_viewed_product = Tools::getValue('id_product', 'false');
        if ($product_details != false) {
            if (isset($product_details['product']->id)) {
                $id_viewed_product = $product_details['product']->id;
            }
        }

        foreach ($blocks as $key => $value) {
            if ($value->type == 1) {
                $explode = explode(',', $value->value);
                $explode_features = explode(',', $value->cat_feature_v);
                if (1 == 1 || $value->cat_price_from != 0 || $value->cat_price_to != 0 || $value->cat_feature != 0) {
                    // IMPLODE CATEGORIES
                    $to_implode = array();
                    foreach ($explode AS $k) {
                        if ($k != "" && (int)$k > 0) {
                            $to_implode[] = (int)$k;
                        }
                    }
                    $implode = implode(',', $to_implode);

                    // IMPLODE FEATURES
                    $to_implode_features = array();
                    foreach ($explode_features AS $k) {
                        if ($k != "" && (int)$k > 0) {
                            $to_implode_features[] = (int)$k;
                        }
                    }
                    $implode_features = implode(',', $to_implode_features);


                    //CHECK PRICES
                    if ($value->cat_price_from != 0) {
                        $value->cat_price_from_v = (float)$value->cat_price_from_v;
                    } else {
                        $value->cat_price_from_v = false;
                    }

                    if ($value->cat_price_to != 0) {
                        $value->cat_price_to_v = (float)$value->cat_price_to_v;
                    } else {
                        $value->cat_price_to_v = false;
                    }

                    if ($value->random == 1) {
                        $blocks[$key]->products = $this->getProductsPro($this->context->language->id, 0, $value->nb + 1, null, null, null, true, true, $value->nb, true, null, $implode, $value->cat_price_from_v, $value->cat_price_to_v, $implode_features, $value->cat_stock_filter, $value->cat_manuf, $value->cat_manuf_v);
                    } else {
                        $blocks[$key]->products = $this->getProductsPro($this->context->language->id, 0, $value->nb + 1, null, null, null, true, false, $value->nb, true, null, $implode, $value->cat_price_from_v, $value->cat_price_to_v, $implode_features, $value->cat_stock_filter, $value->cat_manuf, $value->cat_manuf_v);
                    }
                    $blocks[$key]->products = $this->excludeViewedProduct($id_viewed_product, $blocks[$key]->products, $value->nb);

                }
            }
            if ($value->type == 2) {
                $blocks[$key]->products = Product::getNewProducts($this->context->language->id, 0, $value->nb + 1);
                $blocks[$key]->products = $this->excludeViewedProduct($id_viewed_product, $blocks[$key]->products, $value->nb);
            }
            if ($value->type == 3) {
                $blocks[$key]->products = ProductSale::getBestSales($this->context->language->id, 0, $value->nb + 1);
                $blocks[$key]->products = $this->excludeViewedProduct($id_viewed_product, $blocks[$key]->products, $value->nb);

            }
            if ($value->type == 4) {
                $blocks[$key]->products = Product::getPricesDrop($this->context->language->id, 0, $value->nb + 1, false);
                $blocks[$key]->products = $this->excludeViewedProduct($id_viewed_product, $blocks[$key]->products, $value->nb);
            }
            if ($value->type == 5) {

                $explode = explode(',', trim($value->list_products));
                foreach ($explode as $tproduct) {
                    if ($tproduct != '') {
                        $x = (array)new Product($tproduct, true, $this->context->language->id);
                        $pr = new Product($tproduct, true, $this->context->language->id);
                        if ($value->cat_stock_filter == 1) {
                            if ($pr->quantity <= 0) {
                                continue;
                            }
                        }
                        if (!isset($blockss[$key])) {
                            $blockss[$key] = new StdClass();
                        }
                        if ($pr->active == 1 && $pr->id != null) {
                            if ($value->instock == 1) {
                                if ($pr->quantity > 0) {

                                    $blockss[$key]->products[$tproduct] = $x;
                                    $blockss[$key]->products[$tproduct]['id_product'] = $tproduct;
                                }
                            } else {
                                {
                                    $blockss[$key]->products[$tproduct] = $x;
                                    $blockss[$key]->products[$tproduct]['id_product'] = $tproduct;
                                }
                            }
                        }

                    }
                }

                    $blocks[$key]->products = Product::getProductsProperties($this->context->language->id, $blockss[$key]->products);
                    $blocks[$key]->products = $this->excludeViewedProduct($id_viewed_product, $blocks[$key]->products, 999);
                    $blocks[$key]->products = array_slice($blocks[$key]->products, 0, $value->nb);
                    if ($value->random == 1) {
                        shuffle($blocks[$key]->products);
                    }

            }
            if ($value->type == 6) {
                $category = new Category(Configuration::get('related_category' . $id_viewed_product));
                $blocks[$key]->products = $category->getProducts($this->context->cookie->id_lang, 0, Configuration::get('related_nb' . $id_viewed_product) + 1);
                $blocks[$key]->products = $this->excludeViewedProduct($id_viewed_product, $blocks[$key]->products, Configuration::get('related_nb' . $id_viewed_product));
            }
            if ($value->type == 7) {
                $blocks[$key]->products = $this->getOrderProducts(array((int)$id_viewed_product), $blocks[$key]->nb + 1);
                $blocks[$key]->products = $this->excludeViewedProduct($id_viewed_product, $blocks[$key]->products, $blocks[$key]->nb);
            }
            if ($value->type == 8) {
                $search_products = Search::find($this->context->language->id, $blocks[$key]->searchphrase, 1, $blocks[$key]->nb + 1);
                if ($search_products['total'] > 0) {
                    $blocks[$key]->products = $search_products['result'];
                    $blocks[$key]->products = $this->excludeViewedProduct($id_viewed_product, $blocks[$key]->products, $blocks[$key]->nb);
                }
            }
            if ($value->type == 9) {
                if ($id_viewed_product != 'false') {
                    $product = new Product($id_viewed_product, true, Context::getContext()->language->id);
                    $explode = explode(',', $product->id_category_default);
                    $explode_features = explode(',', $value->cat_feature_v);

                    if (1 == 1 || $value->cat_price_from != 0 || $value->cat_price_to != 0 || $value->cat_feature != 0) {
                        // IMPLODE CATEGORIES
                        $implode = implode(',', $explode);

                        // IMPLODE FEATURES
                        $to_implode_features = array();
                        foreach ($explode_features AS $k) {
                            if ($k != "" && (int)$k > 0) {
                                $to_implode_features[] = (int)$k;
                            }
                        }
                        $implode_features = implode(',', $to_implode_features);


                        //CHECK PRICES
                        if ($value->cat_price_from != 0) {
                            $value->cat_price_from_v = (float)$value->cat_price_from_v;
                        } else {
                            $value->cat_price_from_v = false;
                        }

                        if ($value->cat_price_to != 0) {
                            $value->cat_price_to_v = (float)$value->cat_price_to_v;
                        } else {
                            $value->cat_price_to_v = false;
                        }

                        if ($value->random == 1) {
                            $blocks[$key]->products = $this->getProductsPro($this->context->language->id, 0, $value->nb + 1, null, null, null, true, true, $value->nb, true, null, $implode, $value->cat_price_from_v, $value->cat_price_to_v, $implode_features, $value->cat_stock_filter, $value->cat_manuf, $value->cat_manuf_v);
                        } else {
                            $blocks[$key]->products = $this->getProductsPro($this->context->language->id, 0, $value->nb + 1, null, null, null, true, false, $value->nb, true, null, $implode, $value->cat_price_from_v, $value->cat_price_to_v, $implode_features, $value->cat_stock_filter, $value->cat_manuf, $value->cat_manuf_v);
                        }
                        $blocks[$key]->products = $this->excludeViewedProduct($id_viewed_product, $blocks[$key]->products, $value->nb);

                    }
                }
            }

            if ($value->type == 16) {
                if ($id_viewed_product != 'false') {
                    $product = new Product($id_viewed_product, true, Context::getContext()->language->id);
                    $explode = explode(',', $product->id_category_default);
                    $explode_features = explode(',', $value->cat_feature_v);

                    if (1 == 1 || $value->cat_price_from != 0 || $value->cat_price_to != 0 || $value->cat_feature != 0) {
                        // IMPLODE CATEGORIES
                        $implode = implode(',', $explode);

                        // IMPLODE FEATURES
                        $to_implode_features = array();
                        foreach ($explode_features AS $k) {
                            if ($k != "" && (int)$k > 0) {
                                $to_implode_features[] = (int)$k;
                            }
                        }
                        $implode_features = implode(',', $to_implode_features);


                        //CHECK PRICES
                        if ($value->cat_price_from != 0) {
                            $value->cat_price_from_v = (float)$value->cat_price_from_v;
                        } else {
                            $value->cat_price_from_v = false;
                        }

                        if ($value->cat_price_to != 0) {
                            $value->cat_price_to_v = (float)$value->cat_price_to_v;
                        } else {
                            $value->cat_price_to_v = false;
                        }

                        if ($value->random == 1) {
                            $blocks[$key]->products = $this->getProductsPro($this->context->language->id, 0, $value->nb + 1, null, null, null, true, true, $value->nb, true, null, $implode, $value->cat_price_from_v, $value->cat_price_to_v, $implode_features, $value->cat_stock_filter, $value->cat_manuf, $value->cat_manuf_v, true, true);
                        } else {
                            $blocks[$key]->products = $this->getProductsPro($this->context->language->id, 0, $value->nb + 1, null, null, null, true, false, $value->nb, true, null, $implode, $value->cat_price_from_v, $value->cat_price_to_v, $implode_features, $value->cat_stock_filter, $value->cat_manuf, $value->cat_manuf_v, true, true);
                        }
                        $blocks[$key]->products = $this->excludeViewedProduct($id_viewed_product, $blocks[$key]->products, $value->nb);

                    }
                }
            }


            if ($value->type == 10) {
                $productsViewed = (isset(Context::getContext()->cookie->viewed) && !empty(Context::getContext()->cookie->viewed)) ? array_slice(array_reverse(explode(',', Context::getContext()->cookie->viewed)), 0, $value->nb * 2) : array();
                if (count($productsViewed) > 0) {
                    foreach ($productsViewed as $tproduct) {
                        if ($tproduct != '') {
                            $x = (array)new Product($tproduct, true, $this->context->language->id);
                            $pr = new Product($tproduct, true, $this->context->language->id);

                            if (!isset($blockss[$key])) {
                                $blockss[$key] = new StdClass();
                            }
                            if ($pr->active == 1 && $pr->id != null) {
                                if ($value->instock == 1) {
                                    if ($pr->quantity > 0) {
                                        $blockss[$key]->products[$tproduct] = $x;
                                        $blockss[$key]->products[$tproduct]['id_product'] = $tproduct;
                                    }
                                } else {
                                    {
                                        $blockss[$key]->products[$tproduct] = $x;
                                        $blockss[$key]->products[$tproduct]['id_product'] = $tproduct;
                                    }
                                }
                            }
                        }
                    }
                    foreach ($blockss[$key]->products AS $k => $v) {
                        if ($id_viewed_product == $v['id_product']) {
                            unset($blockss[$key]->products[$k]);
                        }
                    }
                    $blockss[$key]->products = array_slice($blockss[$key]->products, 0, $value->nb + 1);
                    $blockss[$key]->products = $this->excludeViewedProduct($id_viewed_product, $blockss[$key]->products, $value->nb);
                }
                $blocks[$key]->products = Product::getProductsProperties($this->context->language->id, $blockss[$key]->products);
            }
            if ($value->type == 11) {
                $explode_features = explode(',', $value->type_feature_v);

                // IMPLODE FEATURES
                $to_implode_features = array();
                foreach ($explode_features AS $k) {
                    if ($k != "" && (int)$k > 0) {
                        $to_implode_features[] = (int)$k;
                    }
                }
                $implode_features = implode(',', $to_implode_features);

                $blocks[$key]->products = $this->getProductsByTheSameFeature($this->context->language->id, 0, $value->nb + 1, null, null, null, true, false, $value->nb, true, null, null, false, false, false, false, false, false, $to_implode_features, $value->allconditions);
                $blocks[$key]->products = $this->excludeViewedProduct($id_viewed_product, $blocks[$key]->products, $value->nb);
            }
            if ($value->type == 12) {
                if ($value->cat_manuf_v != 0) {
                    $blocks[$key]->products = Manufacturer::getProducts($value->cat_manuf_v, $this->context->language->id, 1, $value->nb + 1);
                    $blocks[$key]->products = $this->excludeViewedProduct($id_viewed_product, $blocks[$key]->products, $value->nb);
                } else {
                    $blocks[$key]->products = array();
                }

            }

            if ($value->type == 13) {
                if ($id_viewed_product != 'false') {
                    $product_main = new Product($id_viewed_product, true, $this->context->language->id);

                    if ($product_main->id_manufacturer != 0) {
                        $blocks[$key]->products = Manufacturer::getProducts($product_main->id_manufacturer, $this->context->language->id, 1, $value->nb + 1);
                        $blocks[$key]->products = $this->excludeViewedProduct($id_viewed_product, $blocks[$key]->products, $value->nb);

                    } else {
                        $blocks[$key]->products = array();
                    }
                }
            }

            if ($value->type == 14) {
                if ($id_viewed_product != 'false') {
                    $explode = $this->getProductsByTags($id_viewed_product, $this->getTags($id_viewed_product));
                    foreach ($explode as $tproduct) {
                        if ($tproduct != '') {
                            $x = (array)new Product($tproduct, true, $this->context->language->id);
                            $pr = new Product($tproduct, true, $this->context->language->id);

                            if ($pr->active == 1 && $pr->id != null) {
                                if ($value->instock == 1) {
                                    if ($pr->quantity > 0) {
                                        $blockss[$key]->products[$tproduct] = $x;
                                        $blockss[$key]->products[$tproduct]['id_product'] = $tproduct;
                                    }
                                } else {
                                    {
                                        $blockss[$key]->products[$tproduct] = $x;
                                        $blockss[$key]->products[$tproduct]['id_product'] = $tproduct;
                                    }
                                }
                            }
                        }
                    }
                    $blocks[$key]->products = Product::getProductsProperties($this->context->language->id, $blockss[$key]->products);
                    $blocks[$key]->products = $this->excludeViewedProduct($id_viewed_product, $blocks[$key]->products, 999);
                    if ($value->random == 1) {
                        shuffle($blocks[$key]->products);
                    }
                }
            }

            if ($value->type == 15) {
                $blocks[$key]->products = Product::getAccessoriesLight($this->context->language->id, (int)Tools::getValue('id_product'));
                $blocks[$key]->products = array_slice($blocks[$key]->products, 0, $value->nb);
            }
        }

        return $blocks;
    }

    public function getTags($id_product)
    {
        $tags = array();
        $tagsArray = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT pt.id_tag FROM ' . _DB_PREFIX_ . 'product_tag pt WHERE pt.id_product = ' . (int)$id_product);
        foreach ($tagsArray as $tag) {
            $tags[] = $tag['id_tag'];
        }
        return $tags;
    }

    public function getProductsByTags($id_product, $tags)
    {
        $toReturn = array();
        if (is_array($tags)) {
            if (count($tags) > 0) {
                $tags = implode(",", $tags);
                $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT pt.id_product FROM ' . _DB_PREFIX_ . 'product_tag pt WHERE pt.id_tag IN (' . $tags . ') AND pt.id_product NOT IN (' . $id_product . ') GROUP BY pt.id_product');
                if (is_array($products)) {
                    if (count($products) > 0) {
                        foreach ($products AS $pr) {
                            $toReturn[] = $pr['id_product'];
                        }
                        return $toReturn;
                    }
                }
            }
        }
        return false;
    }

    public function prepareblocks($blocks, $shortcode = false)
    {
        $check_all_conditions = array();
        $check_all_conditions['categories'] = 0;
        $check_all_conditions['manufacturer'] = 0;
        $check_all_conditions['products'] = 0;
        $check_all_conditions['features'] = 0;
        $block_included = 0;
        $at_least_one_active_condition = 0;
        $array = array();
        $product = new Product(Tools::getValue('id_product'), true);

        foreach ($blocks as $block) {
            $remove_block = 1;
            $block_included = 0;
            if ($block->everywhere == 1 || $shortcode == true) {
                $at_least_one_active_condition = 1;
                if ($block_included != 1) {
                    $block_included = 1;
                    $array[] = $block;
                }
            }

            if ($block->product == Tools::getValue('id_product')) {
                if ($block->hidewc == 0) {
                    if ($block_included != 1) {
                        $block_included = 1;
                        $array[] = $block;
                    }
                }
            }

            if ($block->global_man == 1) {
                $check_all_conditions['manufacturer'] = 1;
                $explode_manufacturers = explode(',', $block->manufacturers);
                if (in_array($product->id_manufacturer, $explode_manufacturers)) {
                    $at_least_one_active_condition = 1;
                    $remove_block_manufacturer = 0;
                    if ($block->allconditions == 0) {
                        if ($block_included != 1) {
                            $block_included = 1;
                            $array[] = $block;
                        }
                    }
                } else {
                    $remove_block_manufacturer = 1;
                }
            }

            if ($block->global_cat == 1) {
                $check_all_conditions['categories'] = 1;
                $explode_categories = explode(',', $block->categories);
                if (count(array_intersect($product->getCategories(), $explode_categories)) > 0) {
                    $remove_block_categories = 0;
                    $at_least_one_active_condition = 1;
                    if ($block->allconditions == 0) {
                        if ($block_included != 1) {
                            $block_included = 1;
                            $array[] = $block;
                        }
                    }
                } else {
                    $remove_block_categories = 1;
                }
            }

            if ($block->global_prod == 1) {
                $check_all_conditions['products'] = 1;
                if ($block->g_products_from_method == 1) {
                    $explode_products = explode(',', trim($block->list_products));
                } else {
                    $explode_products = explode(',', trim($block->g_products));
                }
                $array_this_product = array();
                $array_this_product[] = Tools::getValue('id_product');
                if (count(array_intersect($array_this_product, $explode_products)) > 0) {
                    $remove_block_products = 0;
                    $at_least_one_active_condition = 1;
                    if ($block->allconditions == 0) {
                        if ($block_included != 1) {
                            $block_included = 1;
                            $array[] = $block;
                        }
                    }
                } else {
                    $remove_block_products = 1;
                }
            }

            if ($block->global_features == 1) {
                $explode_features = explode(',', $block->g_features);
                $found_feature = 0;
                $check_all_conditions['features'] = 1;
                foreach (Product::getFeaturesStatic(Tools::getValue('id_product')) as $feature) {
                    if (in_array($feature['id_feature_value'], $explode_features)) {
                        if ($found_feature != 1) {
                            $at_least_one_active_condition = 1;
                            if ($block->allconditions == 0) {
                                if ($block_included != 1) {
                                    $block_included = 1;
                                    $array[] = $block;
                                }
                            }
                            $found_feature = 1;
                        }
                    }
                }
            }

            if ($block->exc_p == 1) {
                $excluded = 0;
                if (strlen(trim($block->exc_p_list)) > 0) {
                    $exploded_exc_p = explode(",", trim($block->exc_p_list));
                    if (in_array(Tools::getValue('id_product'), $exploded_exc_p)) {
                        $excluded = 1;
                    }
                }
            }

            if ($block_included != 1 && $block->allconditions == 1) {
                if ($check_all_conditions['manufacturer'] == 1) {
                    if (isset($remove_block_manufacturer)) {
                        if ($remove_block_manufacturer == 0) {
                            $passed_manufacturer = 1;
                        } else {
                            $passed_manufacturer = 0;
                        }
                    } else {
                        $passed_manufacturer = 1;
                    }
                } else {
                    $passed_manufacturer = 1;
                }

                if ($check_all_conditions['categories'] == 1) {
                    if (isset($remove_block_categories)) {
                        if ($remove_block_categories == 0) {
                            $passed_categories = 1;
                        } else {
                            $passed_categories = 0;
                        }
                    } else {
                        $passed_categories = 1;
                    }
                } else {
                    $passed_categories = 1;
                }

                if ($check_all_conditions['products'] == 1) {
                    if (isset($remove_block_products)) {
                        if ($remove_block_products == 0) {
                            $passed_products = 1;
                        } else {
                            $passed_products = 0;
                        }
                    } else {
                        $passed_products = 1;
                    }
                } else {
                    $passed_products = 1;
                }

                if ($check_all_conditions['features'] == 1) {
                    if (isset($found_feature)) {
                        if ($found_feature == 1) {
                            $passed_features = 1;
                        } else {
                            $passed_features = 0;
                        }
                    } else {
                        $passed_features = 0;
                    }
                } else {
                    $passed_features = 1;
                }

                if ($passed_categories == 1 && $passed_features == 1 && $passed_manufacturer == 1 && $passed_products == 1 && $excluded == 0) {
                    $array[] = $block;
                }
            }
        }



        $slices = (int)Configuration::get('ppb_limit_boxes');
        if (is_int($slices)) {
            if ($slices > 0) {
                $array = array_slice($array, 0, $slices);
            }
        }

        return $array;
    }

    public function prepareBlocksProducts($blocks)
    {
        $blocks_for_template = [];
        $products_for_template = [];
        foreach ($blocks AS $key => $block) {
            if (!isset($block->products)) {
                continue;
            } else {
                if (!is_array($block->products)) {
                    continue;
                } else {
                    if (count($block->products) <= 0) {
                        continue;
                    }
                }
            }

            $assembler = new ProductAssembler($this->context);
            $presenterFactory = new ProductPresenterFactory($this->context);
            $presentationSettings = $presenterFactory->getPresentationSettings();
            $presenter = new ProductListingPresenter(new ImageRetriever($this->context->link), $this->context->link, new PriceFormatter(), new ProductColorsRetriever(), $this->context->getTranslator());
            $products_for_template = [];
            foreach ($block->products as $rawProduct) {
                $products_for_template[] = $presenter->present($presentationSettings, $assembler->assembleProduct($rawProduct), $this->context->language);
            }

            $blocks_for_template[$key] = $block;
            $blocks_for_template[$key]->products = $products_for_template;
        }

        return $blocks_for_template;
    }

    public function hookdisplayProductExtraContent($params)
    {
        if (Configuration::get('ppb_tabs') == 1) {
            $ps17tabz = array();
            $product = new Product(Tools::getValue('id_product'));
            $product_details['product'] = $product;
            $product_details['categories'] = Product::getProductCategories($product->id);
            $blocks = $this->prepareblocks($this->prepareBlocksFromDb(Ppbblock::getAllBlocksByPosition(0, $product_details)));
            if (isset($blocks) && Configuration::get('ppb_tabs') == 1) {
                if (count($blocks) > 0) {
                    foreach ($blocks as $tab => $value) {
                        $this->context->smarty->assign(array('blocks' => $this->prepareBlocksProducts(array($value))));
                        $this->context->smarty->assign(array('link' => Context::getContext()->link));
                        $this->context->smarty->assign('ppb_customtplpath', $this->return_ppb_customtplpath());
                        $ps17tabz[] = (new PrestaShop\PrestaShop\Core\Product\ProductExtraContent())->setTitle($value->name)->setContent($this->context->smarty->fetch('module:ppb/views/templates/hook/' . $this->usedThemeName() . 'ProductExtraContent.tpl'));
                    }
                }
            }
            return $ps17tabz;
        } else {
            return array();
        }
    }

    public function hookdisplayHeader($params)
    {

        $id_product = (int)Tools::getValue('id_product');
        $productsViewed = (isset($params['cookie']->viewed) && !empty($params['cookie']->viewed)) ? array_slice(array_reverse(explode(',', $params['cookie']->viewed)), 0, Configuration::get('PRODUCTS_VIEWED_NBR')) : array();

        if ($id_product && !in_array($id_product, $productsViewed)) {
            $product = new Product((int)$id_product);
            if ($product->checkAccess((int)$this->context->customer->id)) {
                if (isset($params['cookie']->viewed) && !empty($params['cookie']->viewed)) {
                    $params['cookie']->viewed .= ',' . (int)$id_product;
                } else {
                    $params['cookie']->viewed = (int)$id_product;
                }
            }
        }
        $this->context->controller->addJS($this->_path . 'views/js/accessories.js');

        if (Configuration::get('ppb_bxslider') == 1) {
            $this->context->controller->addJqueryPlugin('fancybox');
            $this->context->controller->addJS($this->_path . 'lib/src/js/lightslider.js');
            $this->context->controller->addCss($this->_path . 'lib/src/css/lightslider.css', 'all');
            $this->context->controller->addCss($this->_path . 'views/css/ppbcarousell.css', 'all');
            $this->context->controller->addCss($this->_path . 'views/css/ppb.css', 'all');
        }
        $this->context->controller->addCss($this->_path . 'views/css/ppb_custom.css', 'all');
        $this->context->controller->addCss($this->_path . 'views/css/ppb-table.css', 'all');
    }

    public function renderForm()
    {
        $this->smartyTemplatesManager = new ppbsmartyTemplatesManager($this->name);
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings of the module'),
                    'icon' => 'icon-cubes',
                ),
                'description' => $this->l('With this form you will configure global settings of the module. If you are looking for form to define "related products" instances - you can find it on each product edit page (there is a section with modules where you can manage this module settings)'),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Custom theme support'),
                        'name' => 'ppb_theme',
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => 'classic',
                                    'label' => $this->l('Classic themes (based on classic theme structure)'),
                                ),
                                array(
                                    'value' => 'warehouse-',
                                    'label' => $this->l('Warehouse theme'),
                                ),
                                array(
                                    'value' => 'panda-',
                                    'label' => $this->l('Panda theme'),
                                ),
                                array(
                                    'value' => 'zone-',
                                    'label' => $this->l('ZOne theme'),
                                ),
                            ),
                            'id' => 'value',
                            'name' => 'label',
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Path to .tpl file of product miniature'),
                        'name' => 'ppb_customtplpath',
                        'desc' => $this->l('default value for Prestashop 1.7 classic theme: catalog/_partials/miniatures/product.tpl') . '<br/>' . $this->l('If you want to use non-default path - make sure that this file exists, otherwise website will spawn error about "unable to load template file"') . '<div class="alert alert-info">' . $this->l('Module to build list of products will use theme file that is responsible for "miniature" of product on list of products. If your theme does not follow prestashop standards and uses own not-default .tpl files - you can type here the path to this .tpl file. This option makes the module compatible with all themes.') . '</div>' . $this->smartyTemplatesManager->generateSmartyTemplatesManagerButton(),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('How many blocks to display?'),
                        'name' => 'ppb_limit_boxes',
                        'desc' => $this->l('You can define the maximum number of blocks/tabs with related products that will appear on product page. Module will not show more blocks than value you will define here If you do not want this limit - leave this field empty'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Search product limit'),
                        'name' => 'PPB_LIMIT_SEARCH',
                        'desc' => $this->l('Module during configuration of "list of related products" gives possibility to search item. By default it displays 10 search result items, here you can increase this limit'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Related products in cart'),
                        'name' => 'ppb_rel_cart',
                        'desc' => '<div class="alert alert-info">' . $this->l('Option if enabled will display blocks with related products during checkout') . '</div>',
                        'values' => array(
                            array(
                                'id' => 'a_active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'a_active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Load lightslider'),
                        'name' => 'ppb_bxslider',
                        'desc' => '<div class="alert alert-info">' . $this->l('If you want to use carousel slider for blocks and if your theme doesnt support lightslider - use this option to load lightslider library') . '.' . $this->l('If option will be disabled and if lightslider is not inclded to your theme - carousel with "related products" will not work - so you will have to turn this option on') . '</div>',
                        'values' => array(
                            array(
                                'id' => 'a_active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'a_active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Share related products instances between shops'),
                        'name' => 'ppb_sharemulti',
                        'desc' => '<div class="alert alert-info">' . $this->l('If you run shops with multistore environment you can share related products instances between shops. Option when disabled will show created "related products" instances in shops, where these instances were created only') . '</div>',
                        'values' => array(
                            array(
                                'id' => 'a_active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'a_active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select tabs type'),
                        'name' => 'ppb_tabs',
                        'options' => array(
                            'query' => array(
                                array(
                                    'value' => 1,
                                    'label' => $this->l('Default "classic" theme tabs'),
                                ),
                                array(
                                    'value' => 2,
                                    'label' => $this->l('Old tabs (based on displayProductTab)'),
                                ),
                            ),
                            'id' => 'value',
                            'name' => 'label',
                        ),
                        'desc' => '<div class="alert alert-info">' . $this->l('PrestaShop has great tool to create product tabs that can be used in each theme. Some theme developers still uses old method because of this you can define here the tabs type that module will use to build product page tabs with related products. You can select default method or non-default method still used by many theme developers.') . '</div>',
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
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
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
            'ppb_bxslider' => Tools::getValue('ppb_bxslider', Configuration::get('ppb_bxslider')),
            'ppb_rel_cart' => Tools::getValue('ppb_rel_cart', Configuration::get('ppb_rel_cart')),
            'ppb_limit_boxes' => Tools::getValue('ppb_limit_boxes', Configuration::get('ppb_limit_boxes')),
            'ppb_tabs' => Tools::getValue('ppb_tabs', Configuration::get('ppb_tabs')),
            'ppb_sharemulti' => Tools::getValue('ppb_sharemulti', Configuration::get('ppb_sharemulti')),
            'ppb_customtplpath' => Tools::getValue('ppb_customtplpath', $this->return_ppb_customtplpath()),
            'ppb_theme' => Tools::getValue('ppb_theme', Configuration::get('ppb_theme')),
            'PPB_LIMIT_SEARCH' => Tools::getValue('PPB_LIMIT_SEARCH', ((int)Configuration::get('PPB_LIMIT_SEARCH') == 0 ? 10 : Configuration::get('PPB_LIMIT_SEARCH'))),
        );
    }

    public function getContent()
    {
        if (Tools::getValue('searchType', 'false') != 'false' && Tools::getValue('ajax') == 1) {
            if (Tools::getValue('searchType') == 'manufacturer') {
                echo self::jsonEncode($this->searchForID('manufacturer', 'name', trim(Tools::getValue('q')), false));
                die();
            } elseif (Tools::getValue('searchType') == 'product') {
                echo self::jsonEncode($this->searchForID('product_lang', 'name', trim(Tools::getValue('q')), true));
                die();
            } elseif (Tools::getValue('searchType') == 'category') {
                echo self::jsonEncode($this->searchForID('category_lang', 'name', trim(Tools::getValue('q')), true));
                die();
            } elseif (Tools::getValue('searchType') == 'supplier') {
                echo self::jsonEncode($this->searchForID('supplier', 'name', trim(Tools::getValue('q')), false));
                die();
            } elseif (Tools::getValue('searchType') == 'cms_category') {
                echo self::jsonEncode($this->searchForID('cms_category_lang', 'name', trim(Tools::getValue('q')), true));
                die();
            } elseif (Tools::getValue('searchType') == 'cms') {
                echo self::jsonEncode($this->searchForID('cms_lang', 'meta_title', trim(Tools::getValue('q')), true));
                die();
            }
        }

        if (Tools::getValue('ajax') == 1) {
            if (Tools::getValue('action') == 'updateSlidesPosition') {
                $slides = Tools::getValue('ppbp');
                foreach ($slides as $position => $idb) {
                    $res = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ppbp_block` SET `position` = ' . (int)$position . ' WHERE `id` = ' . (int)$idb);
                }
                die();
            }

            if (Tools::getValue('search', 'false') != 'false') {
                $result = $this->searchproduct(Tools::getValue('search'));
                if (count($result) > 0) {
                    foreach ($result as $key => $value) {
                        echo '<p style="display:block; clear:both; padding:0px; padding-top:3px; margin:0px;">#' . $value['id_product'] . ' - <strong>' . $value['name'] . '</strong> - ' . $value['reference'] . '<span style="display:inline-block; background:#FFF; cursor:pointer; border:1px solid black; padding:1px 3px;margin-left:5px;" onclick="$(\'.ppb_products\').val($(\'.ppb_products\').val()+\'' . $value['id_product'] . ',\')">' . $this->addproduct . '</span></p>';
                    }
                } else {
                    echo $this->noproductsfound;
                }
                die();
            }

            if (Tools::getValue('search_feature_type', 'false') != 'false') {
                $result = $this->searchfeatureName(Tools::getValue('search_feature_type'));
                if (count($result) > 0) {
                    foreach ($result as $key => $value) {
                        echo '<p style="display:block; clear:both; padding:0px; padding-top:3px; margin:0px;">' . $value['feature'] . '
                <span class="addbtn" onclick="$(\'.type_feature_selected\').append(addinputName(\'' . $value['feature'] . '\',\'' . $value['id_feature'] . '\'));">
                ' . $this->addproduct . '
                </span>
                </p>';
                    }
                } else {
                    echo $this->noproductsfound;
                }
                die();
            }


            if (Tools::getValue('search_feature', 'false') != 'false') {
                $result = $this->searchfeature(Tools::getValue('search_feature'));
                if (count($result) > 0) {
                    foreach ($result as $key => $value) {
                        echo '<p style="display:block; clear:both; padding:0px; padding-top:3px; margin:0px;">' . $value['name'] . '<span style="display:inline-block; background:#FFF; cursor:pointer; border:1px solid black; padding:1px 3px;margin-left:5px;" onclick="$(\'.ppb_features\').val($(\'.ppb_features\').val()+\'' . $value['id_feature_value'] . ',\')">' . $this->addproduct . '</span></p>';
                    }
                } else {
                    echo $this->noproductsfound;
                }
                die();
            }

            if (Tools::getValue('search_feature_cat', 'false') != 'false') {
                $result = $this->searchfeature(Tools::getValue('search_feature_cat'));
                if (count($result) > 0) {
                    foreach ($result as $key => $value) {
                        echo '<p style="display:block; clear:both; padding:0px; padding-top:3px; margin:0px;">' . $value['feature'] . ': ' . $value['name'] . '
                <span class="addbtn" onclick="$(\'.cat_feature_selected\').append(addinput(\'' . $value['feature'] . ': ' . $value['name'] . '\',\'' . $value['id_feature_value'] . '\'));">
                ' . $this->addproduct . '
                </span>
                </p>';
                    }
                } else {
                    echo $this->noproductsfound;
                }
                die();
            }


            if (Tools::getValue('action') == 'removeTab' && Tools::getValue('id')) {
                $extratab = new PpbBlock(Tools::getValue('id'));
                $extratab->delete();
                die();
            }

            if (Tools::getValue('action') == 'duplicateTab' && Tools::getValue('id')) {
                $extratab = new PpbBlock(Tools::getValue('id'));
                $newtab = $extratab->duplicateObject();
                $newtab->internal_name = "[duplicated] " . $extratab->internal_name;
                $newtab->save();
                echo "location.reload();";
                die();
            }


            if (Tools::getValue('action') == 'toggleTab' && Tools::getValue('id')) {
                $id = Tools::getValue('id');
                $res = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ppbp_block` SET `active` = !active
        WHERE `id` = ' . (int)$id . '');
                $res = Db::getInstance()->executeS('SELECT active  FROM `' . _DB_PREFIX_ . 'ppbp_block` WHERE `id` = ' . (int)$id . '');
                if ($res[0]['active'] == 1) {
                    echo "$(\"#ppbp_$id span.off\").attr('class','on');";
                } else {
                    echo "$(\"#ppbp_$id span.on\").attr('class','off');";
                }
                die();
            }
        }

        if (Tools::isSubmit('ppb_bxslider')) {
            Configuration::updateValue('PPB_LIMIT_SEARCH', Tools::getValue('PPB_LIMIT_SEARCH'));
            Configuration::updateValue('ppb_bxslider', Tools::getValue('ppb_bxslider'));
            Configuration::updateValue('ppb_tabs', Tools::getValue('ppb_tabs'));
            Configuration::updateValue('ppb_rel_cart', Tools::getValue('ppb_rel_cart'));
            Configuration::updateValue('ppb_limit_boxes', Tools::getValue('ppb_limit_boxes'));
            Configuration::updateValue('ppb_sharemulti', Tools::getValue('ppb_sharemulti'));
            Configuration::updateValue('ppb_theme', Tools::getValue('ppb_theme'));
            if (Tools::getValue('ppb_customtplpath') == "" || Tools::getValue('ppb_customtplpath', 'false') == 'false') {
                $this->context->controller->errors[] = $this->l('Path to tpl file can\'t be empty. Module saved it\'s default value instead.');
                Configuration::updateValue('ppb_customtplpath', 'catalog/_partials/miniatures/product.tpl');
            } else {
                Configuration::updateValue('ppb_customtplpath', Tools::getValue('ppb_customtplpath'));
            }
            $this->context->controller->confirmations[] = $this->l('Settings saved');
        }
        if (Configuration::get('ppb_theme') == 'warehouse-' && Tools::getValue('ajax') != 1) {
            $this->context->controller->warnings[] = $this->l('You use warehouse theme, try to use one from these paths to .tpl file of product miniature') .
                ' <br/><br/> catalog/_partials/miniatures/_partials/product-miniature-1.tpl' . '<br/>' .
                ' catalog/_partials/miniatures/_partials/product-miniature-2.tpl' . '<br/>' .
                ' catalog/_partials/miniatures/_partials/product-miniature-3.tpl' . '<br/>';
        }

        return $this->renderForm() . $this->checkforupdates(0, 1);
    }

    public function searchproduct($search)
    {
        return Db::getInstance()->ExecuteS('SELECT p.`reference`, pl.`id_product`, pl.`name` FROM `' . _DB_PREFIX_ . 'product_lang` AS pl INNER JOIN `' . _DB_PREFIX_ . 'product` AS p ON (p.`id_product` = pl.`id_product`) WHERE pl.`id_product` like "%' . trim((string)pSQL($search)) . '%" OR pl.`name` like "%' . trim((string)pSQL($search)) . '%" AND pl.id_lang="' . Configuration::get('PS_LANG_DEFAULT') . '" AND pl.id_shop="' . (int)$this->context->shop->id . '" GROUP BY pl.id_product LIMIT ' . ((int)Configuration::get('PPB_LIMIT_SEARCH') == 0 ? 10 : Configuration::get('PPB_LIMIT_SEARCH')));
    }

    public function searchfeature($search)
    {
        return Db::getInstance()->ExecuteS('SELECT fl.`name` AS feature, fvl.`id_feature_value`, fvl.`value` as name FROM `' . _DB_PREFIX_ . 'feature_value_lang` AS fvl
        INNER JOIN `' . _DB_PREFIX_ . 'feature_value` fv ON (fvl.`id_feature_value` = fv.`id_feature_value`)
        INNER JOIN `' . _DB_PREFIX_ . 'feature_lang` fl ON (fl.`id_feature` = fv.`id_feature` AND fl.id_lang="' . Configuration::get('PS_LANG_DEFAULT') . '")
        WHERE fvl.`value` like "%' . pSQL($search) . '%" AND fvl.id_lang="' . Configuration::get('PS_LANG_DEFAULT') . '" LIMIT 10');
    }

    public function searchfeatureName($search)
    {
        return Db::getInstance()->ExecuteS('SELECT fv.`id_feature` AS id_feature, fl.`name` AS feature, fvl.`id_feature_value`, fvl.`value` as name FROM `' . _DB_PREFIX_ . 'feature_value_lang` AS fvl
        INNER JOIN `' . _DB_PREFIX_ . 'feature_value` fv ON (fvl.`id_feature_value` = fv.`id_feature_value`)
        INNER JOIN `' . _DB_PREFIX_ . 'feature_lang` fl ON (fl.`id_feature` = fv.`id_feature` AND fl.id_lang="' . Configuration::get('PS_LANG_DEFAULT') . '")
        WHERE (fvl.`value` like "%' . pSQL($search) . '%" AND fvl.id_lang="' . Configuration::get('PS_LANG_DEFAULT') . '") OR (fl.`name` like "%' . pSQL($search) . '%" AND fvl.id_lang="' . Configuration::get('PS_LANG_DEFAULT') . '") GROUP BY fv.`id_feature` LIMIT 10');
    }

    public function runStatement($statement)
    {
        if (@ !Db::getInstance()->Execute($statement)) {
            return false;
        }

        return true;
    }

    public function returnAddForm($block = null)
    {
        $cat_feature_v = '';
        $type_feature_v = '';
        if ($block != null) {
            $block = new ppbBlock($block);
            $product = new Product($block->product, false, $this->context->language->id);
            if ($block->cat_feature_v) {
                foreach (explode(',', $block->cat_feature_v) AS $key) {
                    $feature_value = FeatureValue::getFeatureValueLang($key);
                    $cat_feature_v .= "<div><input type='hidden' name='cat_feature_v[]' value=" . $key . "> " . $feature_value[0]['value'] . " <span class=\"remove\" onclick=\"$(this).parent().remove();\"></span> </div>";
                }
            }
            if ($block->type_feature_v) {
                foreach (explode(',', $block->type_feature_v) AS $key) {
                    $feature = Feature::getFeature($this->context->language->id, $key);
                    $type_feature_v .= "<div><input type='hidden' name='type_feature_v[]' value=" . $key . "> " . $feature['name'] . " <span class=\"remove\" onclick=\"$(this).parent().remove();\"></span> </div>";
                }
            }
        }
        $languages = Language::getLanguages(false);
        $form = '';
        $title = '<div class="col-lg-10 row">';
        $id_lang_default = (int)$this->context->language->id;
        foreach (Language::getLanguages(false) as $language) {
            $title .= '
                     <div id="ppbname_' . $language['id_lang'] . '" style="width:100%; margin-bottom:10px; clear:both; display: ' . ($language['id_lang'] == $id_lang_default ? 'block' : 'none') . '; float: left;">
                      <input class="form-control" type="text" id="name_' . $language['id_lang'] . '" name="name[' . $language['id_lang'] . ']" value="' . ($block != null ? $block->name[$language['id_lang']] : '') . '">
                     </div>';
        }
        $title .= '</div><div class="col-lg-2 row">' . $this->displayFlags($languages, $id_lang_default, 'ppbname', 'ppbname', true) . '</div>';

        $titleURL = '<div class="col-lg-10 row">';
        $id_lang_default = (int)$this->context->language->id;
        foreach (Language::getLanguages(false) as $language) {
            $titleURL .= '
                     <div id="ppbtitleURL_' . $language['id_lang'] . '" style="width:100%; margin-bottom:10px; clear:both; display: ' . ($language['id_lang'] == $id_lang_default ? 'block' : 'none') . '; float: left;">
                      <input class="form-control" type="text" id="titleURL_' . $language['id_lang'] . '" name="titleURL[' . $language['id_lang'] . ']" value="' . ($block != null ? $block->titleURL[$language['id_lang']] : '') . '">
                     </div>';
        }
        $titleURL .= '</div><div class="col-lg-2 row">' . $this->displayFlags($languages, $id_lang_default, 'ppbtitleURL', 'ppbtitleURL', true) . '</div>';


        $custom_before = '<div class="col-lg-10 row">';
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        foreach (Language::getLanguages(false) as $language) {
            $custom_before .= '
                     <div id="ppbcustombefore_' . $language['id_lang'] . '" style="width:100%; margin-bottom:10px; clear:both; display: ' . ($language['id_lang'] == $id_lang_default ? 'block' : 'none') . '; float: left;">
                      <textarea class="form-control" id="custombefore_' . $language['id_lang'] . '" name="custombefore[' . $language['id_lang'] . ']">' . ($block != null ? $block->custom_before[$language['id_lang']] : '') . '</textarea>
                     </div>';
        }
        $custom_before .= '</div><div class="col-lg-2 row">' . $this->displayFlags($languages, $id_lang_default, 'ppbcustombefore', 'ppbcustombefore', true) . "</div>";
        $form .= '


    <div class="panel">
        <h2 class="tab">
            ' . $this->l('Related products') . '
        </h2>
	        <fieldset id="blockedit">
                <table>
                    <tbody>
                        <tr>
                            <td class="col-left">
                                <label>' . $this->l('Product') . '</label>
                                <p class="product_description">' . $this->l('Decide if you want to display tab on this product page') . '</p>
                            </td>
                            <td>
                                <select name="hidewc" class="form-control">
                                    <option value="0" ' . ($block != null ? ($block->hidewc != 1 ? 'selected' : '') : '') . '>' . $this->l('Display') . '</option>
                                    <option value="1" ' . ($block != null ? ($block->hidewc == 1 ? 'selected' : '') : '') . '>' . $this->l('Hide') . '</option>
                                </select>
                                <div class="alert alert-info" style="margin-top:10px; max-width:382px;">
                                    ' . (isset($product->name) ? ($this->l('You created this related products instance for product: ') . $product->name . '. ' . $this->l('With option above you can decide if you want to display this related products instance on this product page.')) : '') . '
                                    ' . (!isset($product->name) ? ($this->l('With option above you can decide if you want to display this related products instance on this product page.')) : '') . '
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-left">
                                <label>' . $this->l('Custom template file') . '</label>
                                <p class="product_description">' . $this->l('Decide if you want to use custom template file to build list of products') . '</p>
                            </td>
                            <td>
                                <table style="width:100%">
                                <td>
                                    <select name="ppb_custom_path" class="form-control">
                                        <option value="1" ' . ($block != null ? ($block->custom_path == 1 ? 'selected' : '') : '') . '>' . $this->l('Yes') . '</option>
                                        <option value="0" ' . ($block != null ? ($block->custom_path != 1 ? 'selected' : '') : '') . '>' . $this->l('No') . '</option>
                                    </select>
                                </td>
                                    <td>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-addon input-group-text">' . $this->l('Path to file') . '</span>
                                            </div>
                                            <input class="form-control ppb_path" type="text" name="ppb_path" value="' . ($block != null ? $block->path : '') . '">
                                            <div class="input-group-append">
                                                <span class="input-group-addon input-group-text">' . $this->l('or select:') . '</span>
                                                <select name="ppb_custom_path_predefined" class="form-control ppb_custom_path_predefined">
                                                    <option value="">-</option>
                                                    <option value="modules/ppb/views/templates/hook/custom-pictures-only.tpl">' . $this->l('Pictures only') . '</option>
                                                    <option value="modules/ppb/views/templates/hook/table-list.tpl">' . $this->l('Table with list of products') . '</option>
                                                </select>
                                            </div>
                                        </div>

                                        <p class="product_description">' . $this->l('Example') . ': modules/ppb/views/templates/hook/custom-pictures-only.tpl</p>
                                    </td>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-left">
                                <label>' . $this->l('Name of block / tab') . '</label>
                                <p class="product_description">' . $this->l('Define the heading of the list of products. It will be visible for the customer as a name of tab or block.') . '</p>
                            </td>
                            <td>
                                ' . $title . '
                            </td>
                        </tr>
                        <tr>
                            <td class="col-left">
                                <label>' . $this->l('Add URL to name') . '</label>
                                <p class="product_description">' . $this->l('If you want you can create a clickable heading. Just type there url to pages where you want to redirect users. If you do not want to create such urls - leave this field empty. This feature is applicable to blocks only (tabs do not have heading just a tab name).') . '</p>
                            </td>
                            <td>
                                ' . $titleURL . '
                            </td>
                        </tr>
                        <tr>
                            <td class="col-left">
                                <label>' . $this->l('Internal name') . '</label>
                                <p class="product_description">' . $this->l('Internal name is for your eyes only and helps you distinct lists of created blocks / tabs') . '</p>
                            </td>
                            <td>
                                <input class="form-control" type="text" name="internal_name" value="' . ($block != null ? $block->internal_name : '') . '">
                            </td>
                        </tr>
				        <tr>
                            <td class="col-left">
                                <label>' . $this->l('Method of appearing') . '</label>
                                <p class="product_description">' . $this->l('Select the way of how products will appear.') . '</p>
                            </td>
                            <td>
                                <select type="text" name="ppbp_block_position" style="max-width:200px;" class="form-control">
                                    <option value="1" ' . ($block != null ? ($block->block_position == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Block') . '</option>
                                    <option value="0" ' . ($block != null ? ($block->block_position == 0 ? 'selected="yes"' : '') : '') . '>' . $this->l('Tab') . '</option>
                                    <option value="2" ' . ($block != null ? ($block->block_position == 2 ? 'selected="yes"' : '') : '') . '>' . $this->l('Popup') . '</option>
                                    <option value="3" ' . ($block != null ? ($block->block_position == 3 ? 'selected="yes"' : '') : '') . '>' . $this->l('Show in cart during checkout') . '</option>
                                    <option value="4" ' . ($block != null ? ($block->block_position == 4 ? 'selected="yes"' : '') : '') . '>' . $this->l('Block in product\'s right column') . '</option>
                                    <option value="5" ' . ($block != null ? ($block->block_position == 5 ? 'selected="yes"' : '') : '') . '>' . $this->l('Block in product\'s left column') . '</option>
                                    <option value="6" ' . ($block != null ? ($block->block_position == 6 ? 'selected="yes"' : '') : '') . '>' . $this->l('Custom module\'s hook displayPpbModule') . '</option>
                                    <option value="7" ' . ($block != null ? ($block->block_position == 7 ? 'selected="yes"' : '') : '') . '>' . $this->l('Custom module\'s hook displayPpbAccessories') . '</option>
                                    <option value="8" ' . ($block != null ? ($block->block_position == 8 ? 'selected="yes"' : '') : '') . '>' . $this->l('On order confirmation page') . '</option>
                                </select>
                                <div class="alert alert-info" style="margin-top:10px; max-width:382px;">
                                    <strong>' . $this->l('Block') . '</strong> - ' . $this->l('List of products will appear as a wide block on product page, at the bottom of product page') . '<br/>
                                    <strong>' . $this->l('Tab') . '</strong> - ' . $this->l('Related products will appear inside a product TAB, near other tabs that you have on product page') . '<br/>
                                    <strong>' . $this->l('Popup') . '</strong> - ' . $this->l('List of products will appear inside a popup. Popup will be speawned once someone will access to product page') . '<br/>
                                    <strong>' . $this->l('Cart (during checkout)') . '</strong> - ' . $this->l('List of products will in cart during checkout process as a kind of cross-selling products. Module will show list of products based on products that customer put into the cart') . '<br/>
                                    <strong>' . $this->l('Block in product\'s right/left column') . '</strong> - ' . $this->l('List of products will appear inside right column / left column on product page.') . $this->l('Please note that you need to activate visibility of this column for product pages in your theme settings (if it is not active)') . '<br/>
                                    <strong>' . $this->l('displayPpbModule') . '</strong> - ' . $this->l('This is custom hook available exclusively for this module.') . ' ' . $this->l('To use custom hook you need to place hook execution code somewhere in your theme\'s product page files:') . ' <span class="">{hook h=\'displayPpbModule\'}</span><br/>
                                    <strong>' . $this->l('displayPpbAccessories') . '</strong> - ' . $this->l('This is custom hook available exclusively for this module.') . ' '
            . $this->l('To use custom hook you need to place hook execution code somewhere in your theme\'s product page files:')
            . ' <span class="">{hook h=\'displayPpbAccessories\'}</span>'
            . $this->l('Please note that this hook creates accessories feature in shop where customers will have possibility to click checkbox near product to add it to cart with product that is currently viewed')
            . '<br/>'
            . $this->l('Carousel will not work in this position') . '<br/>
                                    <strong>' . $this->l('Order confirmation page') . '</strong> - ' . $this->l('Module will show products related to products purchased, on "thank you" page (order confirmation page)') . '<br/>
                                </div>
                            </td>
				        </tr>
                        <tr>
                            <td class="col-left">
                                <label>' . $this->l('What to display?') . '</label>
                                <p class="product_description">' . $this->l('Select what kind of products module will display in this block.') . '</p>
                            </td>
                            <td>
                                <select class="form-control" type="text" name="ppb_type" id="ppb_type" style="max-width:200px;">
                                    <option value="1" ' . ($block != null ? ($block->type == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Products from category') . '</option>
                                    <option value="9" ' . ($block != null ? ($block->type == 9 ? 'selected="yes"' : '') : '') . '>' . $this->l('Products from the same category') . '</option>
                                    <option value="16" ' . ($block != null ? ($block->type == 16 ? 'selected="yes"' : '') : '') . '>' . $this->l('Products with the same main category') . '</option>
                                    <option value="2" ' . ($block != null ? ($block->type == 2 ? 'selected="yes"' : '') : '') . '>' . $this->l('New products') . '</option>
                                    <option value="3" ' . ($block != null ? ($block->type == 3 ? 'selected="yes"' : '') : '') . '>' . $this->l('Best sellers') . '</option>
                                    <option value="4" ' . ($block != null ? ($block->type == 4 ? 'selected="yes"' : '') : '') . '>' . $this->l('Specials') . '</option>
                                    <option value="5" ' . ($block != null ? ($block->type == 5 ? 'selected="yes"' : '') : '') . '>' . $this->l('Selected products') . '</option>
                                    <option value="6" ' . ($block != null ? ($block->type == 6 ? 'selected="yes"' : '') : '') . '>' . $this->l('Import from related products free') . '</option>
                                    <option value="7" ' . ($block != null ? ($block->type == 7 ? 'selected="yes"' : '') : '') . '>' . $this->l('Cross selling products') . '</option>
                                    <option value="8" ' . ($block != null ? ($block->type == 8 ? 'selected="yes"' : '') : '') . '>' . $this->l('Selected phrase search results') . '</option>
                                    <option value="10" ' . ($block != null ? ($block->type == 10 ? 'selected="yes"' : '') : '') . '>' . $this->l('Last viewed products') . '</option>
                                    <option value="11" ' . ($block != null ? ($block->type == 11 ? 'selected="yes"' : '') : '') . '>' . $this->l('Products with the same feature') . '</option>
                                    <option value="12" ' . ($block != null ? ($block->type == 12 ? 'selected="yes"' : '') : '') . '>' . $this->l('Products from selected manufacturer') . '</option>
                                    <option value="13" ' . ($block != null ? ($block->type == 13 ? 'selected="yes"' : '') : '') . '>' . $this->l('Products from the same manufacturer') . '</option>
                                    <option value="14" ' . ($block != null ? ($block->type == 14 ? 'selected="yes"' : '') : '') . '>' . $this->l('Products with at least one the same tag') . '</option>
                                    <option value="15" ' . ($block != null ? ($block->type == 15 ? 'selected="yes"' : '') : '') . '>' . $this->l('Products from Prestashop\'s default "related products" feature') . '</option>
                                </select>
                            </td>
                        </tr>

                        <tr class="ppb_type_feature " ' . ($block != null ? ($block->type == 11 ? '' : 'style="display:none;"') : 'style="display:none;"') . '>
                            <td class="col-left">
                                <label>' . $this->l('Features filter') . '</label>
                                <p class="product_description">' . $this->l('Option if enabled will get products that have the same feature value') . '</p>
                            </td>
                            <td class="">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">' . $this->l('Feature search') . '</span>
                                            </div>
                                            <input type="text" name="type_feature_s" class="type_feature_s form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-12 search_result">
                                    </div>
                                    <div class="col-lg-12 type_feature_selected">
                                    ' . $type_feature_v . '
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr class="ppb_type_searchphrase" ' . ($block != null ? ($block->type == 8 ? '' : 'style="display:none;"') : 'style="display:none;"') . '>
                            <td class="col-left non-free">
                                <label>' . $this->l('Search phrase') . '</label>
                                <p class="product_description">
                                    ' . $this->l('Type here The word, module will display search results for this phrase as a related products') . '
                                </p>
                            </td>
                            <td class="non-free">
                                <input class="form-control" type="text" name="ppb_type_searchphrase" value="' . ($block != null ? $block->searchphrase : '') . '">
                            </td>
                        </tr>
                        <tr class="ppb_type_category" ' . ($block != null ? ($block->type == 1 || $block->type == 9 || $block->type == 16 ? '' : 'style="display:none;"') : '') . '>
                            <td class="col-left non-free">
                                <label>' . $this->l('Category to display') . '</label>
                                <p class="product_description">' . $this->l('Type here ID of category you want to display. Read') . '
                                    <a href="http://mypresta.eu/en/art/basic-tutorials/prestashop-how-to-get-category-id.html" target="_blank">' . $this->l('how to get Category ID') . '</a><br/><br/>
                                    ' . $this->l('Hey! Do you know that you can get products from several different categories? just separate categories ID numbers by comma like:') . ' <strong>3,4,5,6</strong>
                                </p>
                            </td>
                            <td class="non-free">
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">' . $this->searchTool('category', 'ppb_value', 'addition') . '</span>
                                        </div>
                                        <input class="form-control ppb_value" type="text" name="ppb_value" value="' . ($block != null ? $block->value : '') . '">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="ppb_type_category" ' . ($block != null ? ($block->type == 1 || $block->type == 9 || $block->type == 16 ? '' : 'style="display:none;"') : '') . '>
                            <td class="col-left non-free">
                                <label>' . $this->l('Features filter') . '</label>
                                <p class="product_description">' . $this->l('Option if enabled will get products from selected category that have at least one defined feature') . '</p>
                            </td>
                            <td class="non-free">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <select name="cat_feature" class="form-control">
                                            <option value="0" ' . ($block != null ? ($block->cat_feature != 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
                                            <option value="1" ' . ($block != null ? ($block->cat_feature == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">' . $this->l('Feature search') . '</span>
                                            </div>
                                            <input type="text" name="cat_feature_s" class="cat_feature_s form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-12 search_result">

                                    </div>
                                    <div class="col-lg-12 cat_feature_selected">
                                    ' . $cat_feature_v . '
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="ppb_type_category ppb_stock_filter" ' . ($block != null ? ($block->type == 1 || $block->type == 9 || $block->type == 5 || $block->type == 16 ? '' : 'style="display:none;"') : '') . '>
                            <td class="col-left non-free">
                                <label>' . $this->l('Stock filter') . '</label>
                                <p class="product_description">' . $this->l('You can show products that are in stock only') . '</p>
                            </td>
                            <td class="non-free">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <select name="cat_stock_filter" class="form-control">
                                            <option value="0" ' . ($block != null ? ($block->cat_stock_filter != 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Show all products (in stock + out of stock)') . '</option>
                                            <option value="1" ' . ($block != null ? ($block->cat_stock_filter == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Show in stock products only') . '</option>
                                        </select>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="ppb_type_category ppb_type_manufacturer" ' . ($block != null ? ($block->type == 1 || $block->type == 12 || $block->type == 9 || $block->type == 16 ? '' : 'style="display:none;"') : '') . '>
                            <td class="col-left non-free">
                                <label>' . $this->l('Manufacturer filter') . '</label>
                                <p class="product_description">' . $this->l('This option (when it is enabled) will filter products from category by manufacturer.') . '</p>
                            </td>
                            <td class="non-free">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <select class="form-control" name="cat_manuf" onchange="blink_field(\'cat_manuf_v\');">
                                            <option value="0" ' . ($block != null ? ($block->cat_manuf != 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
                                            <option value="1" ' . ($block != null ? ($block->cat_manuf == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">' . $this->searchTool('manufacturer', 'cat_manuf_v') . '</span>
                                            </div>
                                            <input type="text" name="cat_manuf_v" class="form-control cat_manuf_v" value="' . ($block != null ? $block->cat_manuf_v : '') . '">
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr class="ppb_type_category" ' . ($block != null ? ($block->type == 1 || $block->type == 9 || $block->type == 16 ? '' : 'style="display:none;"') : '') . '>
                            <td class="col-left non-free">
                                <label>' . $this->l('Minimal price') . '</label>
                                <p class="product_description">' . $this->l('This option (when it is enabled) will get products from category that are worth at least X. Module checks base price of product.') . '</p>
                            </td>
                            <td class="non-free">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <select class="form-control" name="cat_price_from" onchange="blink_field(\'cat_price_from_v\');">
                                            <option value="0" ' . ($block != null ? ($block->cat_price_from != 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
                                            <option value="1" ' . ($block != null ? ($block->cat_price_from == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">' . $this->context->currency->sign . '</span>
                                            </div>
                                            <input type="text" name="cat_price_from_v" class="form-control cat_price_from_v" value="' . ($block != null ? $block->cat_price_from_v : '') . '">
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="ppb_type_category" ' . ($block != null ? ($block->type == 1 || $block->type == 9 || $block->type == 16 ? '' : 'style="display:none;"') : '') . '>
                            <td class="col-left non-free">
                                <label>' . $this->l('Maximal price') . '</label>
                                <p class="product_description">' . $this->l('This option (when it is enabled) will get products from category that are cheaper than X. Module checks base price of product.') . '</p>
                            </td>
                            <td class="non-free">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <select class="form-control" name="cat_price_to" onchange="blink_field(\'cat_price_to_v\');">
                                            <option value="0" ' . ($block != null ? ($block->cat_price_to != 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
                                            <option value="1" ' . ($block != null ? ($block->cat_price_to == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">' . $this->context->currency->sign . '</span></div>
                                            <input type="text" name="cat_price_to_v" class="form-control cat_price_to_v" value="' . ($block != null ? $block->cat_price_to_v : '') . '">
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="ppb_type_category ppb_random_field" ' . ($block != null ? ($block->type == 1 || $block->type == 9 || $block->type == 5 || $block->type == 13 || $block->type == 16 ? '' : 'style="display:none;"') : '') . '>
                            <td class="col-left non-free">
                                <label>' . $this->l('Random order') . '</label>
                                <p class="product_description">' . $this->l('Turn this option on if you want to display products randomly') . '</p>
                            </td>
                            <td class="non-free">
                                <select class="form-control" name="ppb_random">
                                    <option value="0" ' . ($block != null ? ($block->random != 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
                                    <option value="1" ' . ($block != null ? ($block->random == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
                                </select>
                            </td>
                        </tr>
                        <tr id="ppb_type_products" ' . ($block != null ? ($block->type == 5 ? '' : 'style="display:none;"') : 'style="display:none;"') . ' class="separated">
                            <td class="col-left non-free">
                                <label>' . $this->l('Products') . '</label>
                                <p class="product_description">' . $this->l('enter above ID numbers of products you want to display, or search for products below.') . '
                                    <a href="http://mypresta.eu/en/art/basic-tutorials/how-to-get-product-id-in-prestashop.html" target="_blank">' . $this->l('How to get product ID number') . '</a> ' . $this->l('Alternatively you can search for product.') . '
                                </p>
                            </td>
                            <td class="non-free">
                                <div type="clearfix">
                                    <select name="instock" class="form-control">
                                        <option value="0" ' . ($block != null ? ($block->instock != 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Display all selected products') . '</option>
                                        <option value="1" ' . ($block != null ? ($block->instock == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Display in stock products only') . '</option>
                                    </select>
                                </div>
                                <textarea name="ppb_products" class="ppb_products form-control"> ' . ($block != null ? ($block->type == 5 ? trim($block->list_products) : '') : '') . '</textarea>
                                <label>' . $this->l('Search for product') . '</label>
                                <div class="margin-form">
                                    <input type="text" name="ppb_search" class="form-control ppb_search" style="max-width:200px;">
                                    </a>
                                    <div id="ppb_search_result" style="margin-top:10px;"></div>
                                </div>
                            </td>
                        </tr>
                        <tr id="ppb_type_nb" ' . ($block != null ? ($block->type != 6 ? '' : 'style="display:none;"') : '') . '>
                            <td class="col-left non-free">
                                <label class="non-free">' . $this->l('Number of products you want to display') . '</label>
                                <p class="non-free product_description">' . $this->l('Define the number of products that module will display in this product list') . '</p>
                            </td>
                            <td class="non-free">
                                <input class="form-control non-free" type="text" name="ppb_nb" style="max-width:200px;" value="' . ($block != null ? $block->nb : '') . '">
                            </td>
                        </tr>
						<tr >
							<td class="col-left">
								<label>' . $this->l('Active') . '</label>
								<p class="product_description">' . $this->l('Check if you want activate block (it will be visible for customers once you will save it)') . '</p>
							</td>
							<td>
								<select class="form-control" type="text" name="ppb_active" style="max-width:200px;">
									<option value="0" ' . ($block != null ? ($block->active != 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
									<option value="1" ' . ($block != null ? ($block->active == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="col-left">
								<label>' . $this->l('First custom code element') . '</label>
								<p class="product_description">' . $this->l('Turn this option on if you want to add custom code block as first element of list') . '</p>
								<p class="product_description">' . $this->l('This option works only for first row of products') . '</p>
							</td>
							<td>
								<select class="form-control" type="text" id="ppb_before" name="ppb_before" style="max-width:200px;" onchange="if($(\'#ppb_before\').val()==1){$(\'#customcodebefore\').show();}else{$(\'#customcodebefore\').hide();}">
									<option value="0" ' . ($block != null ? ($block->before != 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
									<option value="1" ' . ($block != null ? ($block->before == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
								</select>
							</td>
						</tr>
						<tr  id="customcodebefore" ' . ($block != null ? ($block->before == 1 ? '' : 'style="display:none;"') : 'style="display:none;"') . '>
							<td class="col-left">
								<label>' . $this->l('Custom code') . '</label>
								<p class="product_description">' . $this->l('Enter custom html code here. It will appear as a first element in row.') . '</p>
								<p class="product_description">' . $this->l('Example of code for bootstrap templates.') . '</p>
								<textarea class="form-control" style="resize:none; box-shadow: 0 1px 2px rgba(0,0,0,0.0) inset; background:none; border:0px; width:200px; height:110px;">
									<li class="ajax_block_product col-xs-12 col-sm-4 col-md-3">' . $this->l('Example of custom contents') . '</li>
								</textarea>
							</td>
							<td>
		                        ' . $custom_before . '
							</td>
						</tr>
						<tr>
							<td class="col-left" colspan="2">
								<h2 style="margin-top:20px; margin-bottom:20px;">' . $this->l('Carousell settings') . '</h2>
							</td>
						</tr>
						<tr>
                            <td colspan="2">
                            <div class="alert alert-info">
                                <p>
                                ' . $this->l('Carousell feature works only with "block", it does not work for tabs currently.') . '
                                </p>
                            </div>
                            </td>
						</tr>
						<tr>
							<td class="col-left">
								<label>' . $this->l('Carousell') . '</label>
								<p class="product_description">' . $this->l('This feature requires default PrestaShop carousell script: lightslider. If your theme doesnt support it, please go to module global settings and enable library.') . '
								</div>
							</td>
							<td>
								<select class="form-control" type="text" name="ppb_carousell" style="max-width:200px;">
									<option value="0" ' . ($block != null ? ($block->carousell == 0 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
									<option value="1" ' . ($block != null ? ($block->carousell == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="col-left">
								<label>' . $this->l('Autoplay') . '</label>
								<p class="product_description">' . $this->l('Autostart the carousell, this option will play carousell automatically') . '
								</div>
							</td>
							<td>
								<select class="form-control" type="text" name="ppb_carousell_auto" style="max-width:200px;">
									<option value="0" ' . ($block != null ? ($block->carousell_auto == 0 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
									<option value="1" ' . ($block != null ? ($block->carousell_auto == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="col-left">
								<label>' . $this->l('Infinite Loop') . '</label>
								<p class="product_description">' . $this->l('If you will enable this option - module will loop back to the beginning of the slide when carousell will reach the last element.') . '</p>
							</td>
							<td>
								<select class="form-control" type="text" name="ppb_carousell_loop" style="max-width:200px;">
									<option value="0" ' . ($block != null ? ($block->carousell_loop == 0 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
									<option value="1" ' . ($block != null ? ($block->carousell_loop == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="col-left">
								<label>' . $this->l('Next / Prev controls') . '</label>
								<p class="product_description">' . $this->l('If you will enable this option the prev/next buttons will appear.') . '</p>
							</td>
							<td>
								<select class="form-control" type="text" name="ppb_carousell_controls" style="max-width:200px;">
									<option value="0" ' . ($block != null ? ($block->carousell_controls == 0 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
									<option value="1" ' . ($block != null ? ($block->carousell_controls == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="col-left">
								<label>' . $this->l('Pager') . '</label>
								<p class="product_description">' . $this->l('If you will enable this option - module will display small dots to navigate through the carousell.') . '</p>
							</td>
							<td>
								<select class="form-control" type="text" name="ppb_carousell_pager" style="max-width:200px;">
									<option value="0" ' . ($block != null ? ($block->carousell_pager == 0 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
									<option value="1" ' . ($block != null ? ($block->carousell_pager == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="col-left">
								<label>' . $this->l('Number of products') . '</label>
								<p class="product_description">' . $this->l('Number of products that will appear as a base of the carousell') . '</p>
							</td>
							<td>
								<input class="form-control" type="text" name="ppb_carousell_nb" style="max-width:200px;" value="' . ($block != null ? $block->carousell_nb : '4') . '">
								</td>
							</tr>
						<tr>
						<tr>
							<td class="col-left">
								<label>' . $this->l('Number of products for tablet devices') . '</label>
								<p class="product_description">' . $this->l('Number of products that will appear as a base of the carousell on tablet devices') . '</p>
							</td>
							<td>
								<input class="form-control" type="text" name="ppb_carousell_nb_tablet" style="max-width:200px;" value="' . ($block != null ? $block->carousell_nb_tablet : '2') . '">
							</td>
						</tr>
						<tr>
							<td class="col-left">
								<label>' . $this->l('Number of products for mobile devices') . '</label>
								<p class="product_description">' . $this->l('Number of products that will appear as a base of the carousell on mobile devices') . '</p>
							</td>
							<td>
								<input class="form-control" type="text" name="ppb_carousell_nb_mobile" style="max-width:200px;" value="' . ($block != null ? $block->carousell_nb_mobile : '1') . '">
							</td>
						</tr>
						<tr>
							<td class="col-left" colspan="2">
								<h1 style="margin-top:20px; margin-bottom:20px;">' . $this->l('Visbility conditions') . '</h1>
							</td>
						</tr>
						<tr>
                            <td>
                                <p class="product_description">
                                    ' . $this->l('Set the way of how module will check conditions for this list of related products') . '
                                </p>
                            </td>
                            <td>
                                <div class="form_block">
                                    <div class="alert alert-warning">
                                        <p class="alert-text">
                                            <select id="ppb_allconditions" name="ppb_allconditions" style="max-width:500px; margin-bottom:10px;">
                                                <option ' . ($block != null ? ($block->allconditions == 0 ? 'selected="yes"' : '') : '') . ' value="0">
                                                   ' . $this->l('To display this list of related products viewed product pages must meet at least one active condition') . '
                                                </option>
                                                <option ' . ($block != null ? ($block->allconditions != 0 ? 'selected="yes"' : '') : '') . ' value="1">
                                                    (all) ' . $this->l('To display this list of related products viewed product pages must meet all active conditions') . '
                                                </option>
                                            </select>
                                        </p>
                                    </div>
                                </div>
                            </td>
						</tr>

						<tr>
							<td class="col-left" colspan="2">
								<h2 style="margin-top:20px; margin-bottom:20px;">' . $this->l('Exclude this "related products" instance from selected product pages') . '</h2>
							</td>
						</tr>
						<tr id="visibility_exc">
							<td class="col-left">
								<p class="product_description">' . $this->l('If you will activate this option this "related products" instance will be excluded from selected product pages') . '</p>
							</td>
							<td>
							    <select name="exc_p" id="exc_p" class="form-control">
								    <option value="0" ' . ($block != null ? ($block->exc_p == 0 ? 'selected="yes"' : '') : '') . '>' . $this->l('Do not exclude') . '</option>
									<option value="1" ' . ($block != null ? ($block->exc_p == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Exclude from selected product pages') . '</option>
								</select>
							</td>
						</tr>
						<tr id="exc_p_list" ' . ($block != null ? ($block->exc_p == 1 ? '' : 'style="display:none;"') : 'style="display:none;"') . '>
							<td class="col-left">
								<label>' . $this->l('Products ID numbers') . '</label>
								<p class="product_description">' . $this->l('Enter ID numbers of products') . '. ' . $this->l('separate values by comma for example: 1,2,3,4,5') . '</p>
							</td>
							<td>
								<textarea name="exc_p_list" class="form-control exc_p_list">' . ($block != null ? trim($block->exc_p_list) : '') . '</textarea>
							</td>
						</tr>

						<tr>
							<td class="col-left" colspan="2">
								<h2 style="margin-top:20px; margin-bottom:20px;">' . $this->l('Visibility of list of products depending on stock') . '</h2>
							</td>
						</tr>
						<tr id="visibility_stock">
							<td class="col-left">
								<p class="product_description">' . $this->l('You can set visibility of this list of products depending on viewed product stock.') . '</p>
							</td>
							<td>
							    <select name="stockcheck" id="stockcheck" class="form-control">
								    <option value="0" ' . ($block != null ? ($block->stockcheck == 0 ? 'selected="yes"' : '') : '') . '>' . $this->l('Display it always') . '</option>
									<option value="1" ' . ($block != null ? ($block->stockcheck == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Display it when product is in stock (hide when out of stock)') . '</option>
									<option value="2" ' . ($block != null ? ($block->stockcheck == 2 ? 'selected="yes"' : '') : '') . '>' . $this->l('Display it when product is out of stock (hide when in stock)') . '</option>
								</select>
							</td>
						</tr>

						<tr>
							<td class="col-left" colspan="2">
								<h2 style="margin-top:20px; margin-bottom:20px;">' . $this->l('Where you want to display this list of products?') . '</h2>
							</td>
						</tr>
						<tr id="appearance_everywhere">
							<td class="col-left">
								<label>' . $this->l('Everywhere') . '</label>
								<p class="product_description">' . $this->l('Turn this option on if you want to display this list of products on each product page') . '</p>
							</td>
							<td>
							    <select class="form-control" name="everywhere" id="everywhere">
								    <option value="0" ' . ($block != null ? ($block->everywhere != 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
									<option value="1" ' . ($block != null ? ($block->everywhere == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
								</select>
							</td>
						</tr>
						<tr style="">
							<td class="col-left" colspan="2">
								<br/>
							</td>
						</tr>
						<tr style="border-top:1px solid #c0c0c0;">
							<td class="col-left" colspan="2">
							    <br/>
						    </td>
						</tr>
						<tr id="appearance_cat">
							<td class="col-left">
								<label>' . $this->l('Product pages') . '</label>
								<p class="product_description">' . $this->l('Turn this option on if you want to display this list of related products on selected product pages') . '</p>
							</td>
							<td>
							    <select class="form-control" name="ppb_global_products" id="ppb_global_products" onchange="if($(\'#ppb_global_products\').val()==1){$(\'#appearance_products\').show();}else{$(\'#appearance_products\').hide();}">
								    <option value="0" ' . ($block != null ? ($block->global_prod != 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
									<option value="1" ' . ($block != null ? ($block->global_prod == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
								</select>
							</td>
						</tr>
						<tr id="appearance_products" ' . ($block != null ? ($block->global_prod == 1 ? '' : 'style="display:none;"') : 'style="display:none;"') . '>
							<td class="col-left">
								<label>' . $this->l('Products ID numbers') . '</label>
								<p class="product_description">' . $this->l('Enter ID numbers of products') . '. ' . $this->l('separate values by comma for example: 1,2,3,4,5') . '</p>
							</td>
							<td>
								<textarea name="ppb_g_products" class="form-control ppb_g_products">' . ($block != null ? ($block->global_prod == 1 ? $block->g_products : '') : '') . '</textarea>
							</td>
						</tr>
						<tr id="appearance_man">
						    <td class="col-left">
							    <label>' . $this->l('List of selected products') . '</label>
								<p class="product_description">' . $this->l('Display this block on product pages that are defined above, in field "selected products"') . '</p>
							</td>
							<td>
							    <select class="form-control" name="ppb_g_products_from_method" id="ppb_g_products_from_method">
								    <option value="0" ' . ($block != null ? ($block->g_products_from_method != 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
									<option value="1" ' . ($block != null ? ($block->g_products_from_method == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
								</select>
							</td>
						</tr>
						<tr style="">
							<td class="col-left" colspan="2">
								<br/>
							</td>
						</tr>
						<tr style="border-top:1px solid #c0c0c0;">
							<td class="col-left" colspan="2">
							    <br/>
						    </td>
						</tr>
						<tr id="appearance_cat">
							<td class="col-left">
								<label>' . $this->l('Categories') . '</label>
								<p class="product_description">' . $this->l('Turn this option on if you want to display this list of related products on product pages associated with selected category') . '</p>
							</td>
							<td>
								<select class="form-control" name="ppb_global_categories" id="ppb_global_categories" onchange="if($(\'#ppb_global_categories\').val()==1){$(\'#appearance_categories\').show();}else{$(\'#appearance_categories\').hide();}">
									<option value="0" ' . ($block != null ? ($block->global_cat != 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
									<option value="1" ' . ($block != null ? ($block->global_cat == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
								</select>
							</td>
						</tr>
						<tr id="appearance_categories" ' . ($block != null ? ($block->global_cat == 1 ? '' : 'style="display:none;"') : 'style="display:none;"') . '>
							<td class="col-left">
								<label>' . $this->l('Category ID numbers') . '</label>
								<p class="product_description">' . $this->l('Enter ID numbers of categories') . '. ' . $this->l('separate values by comma for example: 1,2,3,4,5') . '</p>
							</td>
							<td>
								<textarea name="ppb_categories" class="ppb_categories form-control">' . ($block != null ? ($block->global_cat == 1 ? $block->categories : '') : '') . '</textarea>
							</td>
						</tr>
						<tr style="">
							<td class="col-left" colspan="2">
								<br/>
							</td>
						</tr>
						<tr style="border-top:1px solid #c0c0c0;">
							<td class="col-left" colspan="2">
								<br/>
							</td>
						</tr>
						<tr id="appearance_man">
							<td class="col-left">
								<label>' . $this->l('Manufacturers') . '</label>
								<p class="product_description">' . $this->l('Turn this option on if you want to display this list of related products on product pages associated with selected manufacturers') . '</p>
							</td>
							<td>
								<select class="form-control" name="ppb_global_manufacturers" id="ppb_global_manufacturers" onchange="if($(\'#ppb_global_manufacturers\').val()==1){$(\'#appearance_manufacturers\').show();}else{$(\'#appearance_manufacturers\').hide();}">
									<option value="0" ' . ($block != null ? ($block->global_man != 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
									<option value="1" ' . ($block != null ? ($block->global_man == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
								</select>
							</td>
						</tr>
						<tr id="appearance_manufacturers" ' . ($block != null ? ($block->global_man == 1 ? '' : 'style="display:none;"') : 'style="display:none;"') . '>
							<td class="col-left">
								<label>' . $this->l('Manufacturers ID numbers') . '</label>
								<p class="product_description">' . $this->l('Enter ID numbers of manufacturers') . '. ' . $this->l('separate values by comma for example: 1,2,3,4,5') . '</p>
							</td>
							<td>
								<textarea name="ppb_manufacturers" class="form-control ppb_manufacturers">' . ($block != null ? ($block->global_man == 1 ? $block->manufacturers : '') : '') . '</textarea>
							</td>
						</tr>
						<tr style="border-top:1px solid #c0c0c0;">
							<td class="col-left" colspan="2">
								<br/>
							</td>
						</tr>
						<tr id="appearance_feat">
							<td class="col-left">
								<label>' . $this->l('Features') . '</label>
								<p class="product_description">' . $this->l('Turn this option on if you want to display this list of related products on product pages associated with selected features') . '</p>
							</td>
							<td>
						    	<select class="form-control" name="ppb_global_features" id="ppb_global_features" onchange="if($(\'#ppb_global_features\').val()==1){$(\'.appearance_features\').show();}else{$(\'.appearance_features\').hide();}">
									<option value="0" ' . ($block != null ? ($block->global_features != 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('No') . '</option>
									<option value="1" ' . ($block != null ? ($block->global_features == 1 ? 'selected="yes"' : '') : '') . '>' . $this->l('Yes') . '</option>
								</select>
							</td>
						</tr>
						<tr class="appearance_features" ' . ($block != null ? ($block->global_features == 1 ? '' : 'style="display:none;"') : 'style="display:none;"') . '>
							<td class="col-left">
								<label>' . $this->l('Features ID numbers') . '</label>
								<p class="product_description">' . $this->l('Enter ID numbers of values of features') . '. ' . $this->l('separate values by comma for example: 1,2,3,4,5') . '</p>
							</td>
							<td>
								<textarea name="ppb_features" class="form-control ppb_features">' . ($block != null ? ($block->global_features == 1 ? $block->g_features : '') : '') . '</textarea>
								<label>' . $this->l('Search for feature') . '</label>
								<div class="margin-form">
									<input type="text" name="ppb_search_feature" class="form-control ppb_search_feature" style="max-width:200px;">
									</a>
									<div id="ppb_search_feature_result" style="margin-top:10px;"></div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<input type="hidden" name="selecttab" value="1">' . ($block != null ? '<input type="hidden" name="edit_block" value="1">' : '<input type="hidden" name="add_new_block" value="1">') . '
				<a href="javascript:ppbsubmit();" class="btn btn-primary uppercase">
				    ' . ($block != null ? $this->l('Save changes') : $this->l('Create it!')) . '
				</a>
		</fieldset>
	</div>
    ' . ($block != null ? '
	<div class="panel">
		<table>
			<tr>
				<td class="col-left" colspan="2">
					<h2 style="margin-top:20px; margin-bottom:20px;">' . $this->l('Shortcodes for CMS pages') . '</h2>
				</td>
			</tr>
			<tr>
				<td colspan="2">
    				<div style="padding:10px; margin:10px 0px; border:1px solid #cecece; background:#fefefe; display:block; clear:both;">
					<a href="https://mypresta.eu/modules/front-office-features/products-on-cms-pages.html" target="_blank">' . $this->l('1.  Install free cms products module') . '</a>
					<br/>
                    ' . $this->l('2.  Use shortcode inside CMS page contents') . ': {rpp:' . $block->id . '}
					<br/>
	    			</div>
				</td>
			</tr>
		</table>
	</div>' : '');

        return $form;
    }

    private function maybeUpdateDatabase($table, $column, $type = "int(8)", $default = "1", $null = "NULL", $onUpdate = '', $wtd = 'ADD')
    {
        $sql = 'DESCRIBE ' . _DB_PREFIX_ . $table;
        $columns = Db::getInstance()->executeS($sql);
        $found = false;
        foreach ($columns as $col) {
            if ($col['Field'] == $column && $wtd == 'ADD') {
                $found = true;
                break;
            }
        }
        if (!$found) {
            if (!Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . $table . '` ' . $wtd . ' `' . $column . '` ' . $type . ' DEFAULT ' . $default . ' ' . $null . ' ' . $onUpdate)) {
                return false;
            }
        }

        return true;
    }

    public function inconsistency($return_report = 1)
    {
        $prefix = _DB_PREFIX_;
        $table = array();
        $error = array();
        $form = '';

        $this->maybeUpdateDatabase('ppbp_block', 'value', "TEXT", "", "NULL", '', "MODIFY");
        $this->maybeUpdateDatabase('ppbp_block', 'list_products', "TEXT", "", "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'custom_before', "TEXT", "", "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'custom_after', "TEXT", "", "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'categories', "TEXT", "", "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'manufacturers', "TEXT", "", "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'g_products', "TEXT", "", "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'g_features', "TEXT", "", "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'searchphrase', "TEXT", "", "NULL");

        $this->maybeUpdateDatabase('ppbp_block', 'before', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'after', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'random', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'global_man', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'global_cat', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'global_prod', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'product', "INT(7)", null, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'g_products_from_method', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'global_features', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'carousell', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'carousell_auto', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'carousell_nb', "INT(2)", 4, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'carousell_nb_mobile', "INT(1)", 1, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'carousell_nb_tablet', "INT(2)", 2, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'carousell_loop', "INT(1)", 1, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'carousell_pager', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'carousell_controls', "INT(1)", 1, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'everywhere', "VARCHAR(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'instock', "VARCHAR(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'hidewc', "VARCHAR(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'internal_name', "VARCHAR(254)", "", "NULL");

        $this->maybeUpdateDatabase('ppbp_block_lang', 'titleURL', "VARCHAR(254)", "", "NULL");

        $this->maybeUpdateDatabase('ppbp_block', 'stockcheck', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'cat_price_from', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'cat_price_to', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'cat_price_from_v', "VARCHAR(15)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'cat_price_to_v', "VARCHAR(15)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'cat_feature_v', "VARCHAR(45)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'type_feature_v', "VARCHAR(45)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'cat_feature', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'cat_stock_filter', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'cat_manuf', "INT(1)", '', "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'cat_manuf_v', "INT(6)", '', "NULL");

        $this->maybeUpdateDatabase('ppbp_block', 'allconditions', "INT(1)", 1, "NULL");

        $this->maybeUpdateDatabase('ppbp_block', 'custom_path', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'path', "TEXT", "", "NULL");

        $this->maybeUpdateDatabase('ppbp_block', 'exc_p', "VARCHAR(1)", 0, "NULL");
        $this->maybeUpdateDatabase('ppbp_block', 'exc_p_list', "TEXT", "", "NULL");

        return true;
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 16 //
        // ---------- //
        // ---------- //
        $this->mkey = "nlc";
        if (@file_exists('../modules/' . $this->name . '/key.php')) {
            @require_once('../modules/' . $this->name . '/key.php');
        } else {
            if (@file_exists(dirname(__FILE__) . $this->name . '/key.php')) {
                @require_once(dirname(__FILE__) . $this->name . '/key.php');
            } else {
                if (@file_exists('modules/' . $this->name . '/key.php')) {
                    @require_once('modules/' . $this->name . '/key.php');
                }
            }
        }
        if ($form == 1) {
            return '
            <div class="panel" id="fieldset_myprestaupdates" style="margin-top:20px;">
            ' . ($this->psversion() == 6 || $this->psversion() == 7 || $this->psversion(0) >= 8 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->l('MyPresta updates') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->l('Check updates') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->l('Check now') . '
                                </button>
                            </div>
                            <label>' . $this->l('Updates notifications') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->l('-- select --') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->l('Enable') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->l('Disable') . '</option>
                                </select>
                                <p class="clear">' . $this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.') . '</p>
                            </div>
                            <label>' . $this->l('Module page') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->l('This is direct link to official addon page, where you can read about changes in the module (changelog)') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates"class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '
                                </button>
                            </div>
                        </form>
                    </fieldset>
                    <style>
                    #fieldset_myprestaupdates {
                        display:block;clear:both;
                        float:inherit!important;
                    }
                    </style>
                </div>
            </div>
            </div>';
        } else {
            if (defined('_PS_ADMIN_DIR_')) {
                if (Tools::isSubmit('submit_settings_updates')) {
                    Configuration::updateValue('mypresta_updates', Tools::getValue('mypresta_updates'));
                }
                if (Configuration::get('mypresta_updates') != 0 || (bool)Configuration::get('mypresta_updates') != false) {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = ppbUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (ppbUpdate::version($this->version) < ppbUpdate::version(Configuration::get('updatev_' . $this->name)) && Tools::getValue('ajax', 'false') == 'false') {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                } else {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = ppbUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                }
                if ($display_msg == 1) {
                    if (ppbUpdate::version($this->version) < ppbUpdate::version(ppbUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public function getProductsByTheSameFeature($id_lang, $p, $n, $order_by = null, $order_way = null, $get_total = false, $active = true, $random = false, $random_number_products = 1, $check_access = true, Context $context = null, $categories = null, $price_from = false, $price_to = false, $features = false, $instock = false, $cat_manuf = false, $cat_manuf_v = false, $type_features = false, $allconditions = 1)
    {
        $by_feature = array();
        if (Tools::getValue('id_product', 'false') != 'false') {
            $features_source = Product::getFeaturesStatic(Tools::getValue('id_product'));
            if (count($features_source) > 0) {
                foreach ($features_source AS $feature_source) {
                    if (in_array($feature_source['id_feature'], $type_features)) {
                        $by_feature[] = $feature_source['id_feature_value'];
                    }
                }
            } else {
                return array();
            }
        } else {
            return array();
        }

        if (count($by_feature) <= 0) {
            return array();
        }


        $default_currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $actual_currency = new Currency($this->context->currency->id);

        $cat_stock_filter = '';
        if ($instock == true) {
            $cat_stock_filter = ' AND stock.`quantity` > 0';
        }

        if (!$context) {
            $context = Context::getContext();
        }

        if ($check_access && !$this->checkAccess($context->customer->id)) {
            return false;
        }

        $front = in_array($context->controller->controller_type, array(
            'front',
            'modulefront'
        ));

        $id_supplier = (int)Tools::getValue('id_supplier');


        if ($p < 1) {
            $p = 1;
        }

        /** Tools::strtolower is a fix for all modules which are now using lowercase values for 'orderBy' parameter */
        $order_by = Validate::isOrderBy($order_by) ? Tools::strtolower($order_by) : 'position';
        $order_way = Validate::isOrderWay($order_way) ? Tools::strtoupper($order_way) : 'ASC';

        $order_by_prefix = false;
        if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'manufacturer' || $order_by == 'manufacturer_name') {
            $order_by_prefix = 'm';
            $order_by = 'name';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'cp';
        } elseif ($order_by = 'best') {
            $order_by_prefix = 'psale';
            $order_by = 'sale_nbr';
        }


        if ($order_by == 'price') {
            $order_by = 'orderprice';
        }

        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }

        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity' . (Combination::isFeatureActive() ? ', IFNULL(product_attribute_shop.id_product_attribute, 0) AS id_product_attribute,
					product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity' : '') . ', pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image` id_image,
					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
					INTERVAL ' . (int)$nb_days_new_product . ' DAY)) > 0 AS new, product_shop.price AS orderprice
				FROM `' . _DB_PREFIX_ . 'category_product` cp
				LEFT JOIN `' . _DB_PREFIX_ . 'product` p
					ON p.`id_product` = cp.`id_product`
				' . Shop::addSqlAssociation('product', 'p') . (Combination::isFeatureActive() ? ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int)$context->shop->id . ')' : '') . '
				' . Product::sqlStock('p', 0) . '
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('cl') . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl') . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int)$context->shop->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = ' . (int)$id_lang . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
                LEFT JOIN `' . _DB_PREFIX_ . 'product_sale` psale
					ON psale.`id_product` = p.`id_product`
				LEFT JOIN `' . _DB_PREFIX_ . 'feature_product` pfeat
					ON pfeat.`id_product` = p.`id_product`
				WHERE product_shop.`id_shop` = ' . (int)$context->shop->id . ($allconditions == 1 ? '
				    ' . (strlen(implode(",", $by_feature)) > 0 ? ' AND pfeat.`id_feature_value` IN (' . implode(",", $by_feature) . ')' : '') . '
					' . ($active ? ' AND product_shop.`active` = 1' : '') . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . ($id_supplier ? ' AND p.id_supplier = ' . (int)$id_supplier : '') . '
					GROUP BY pfeat.`id_product` HAVING count(distinct pfeat.`id_feature_value`) >= ' . count($by_feature) : '
				    ' . (strlen(implode(",", $by_feature)) > 0 ? ' AND pfeat.`id_feature_value` IN (' . implode(",", $by_feature) . ')' : '') . '
					' . ($active ? ' AND product_shop.`active` = 1' : '') . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . ($id_supplier ? ' AND p.id_supplier = ' . (int)$id_supplier : '') . '
					GROUP BY p.`id_product`');

        if ($random === true) {
            $sql .= ' ORDER BY RAND() LIMIT ' . (int)$random_number_products;
        } else {
            $sql .= ' ORDER BY ' . (!empty($order_by_prefix) ? $order_by_prefix . '.' : '') . '`' . bqSQL($order_by) . '` ' . pSQL($order_way) . '
			LIMIT ' . (((int)$p - 1) * (int)$n) . ',' . (int)$n;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

        if (!$result) {
            return array();
        }

        if ($order_by == 'orderprice') {
            Tools::orderbyPrice($result, $order_way);
        }


        /** Modify SQL result */
        return Product::getProductsProperties($id_lang, $result);
    }

    public function getProductsPro($id_lang, $p, $n, $order_by = null, $order_way = null, $get_total = false, $active = true, $random = false, $random_number_products = 1, $check_access = true, Context $context = null, $categories = null, $price_from = false, $price_to = false, $features = false, $instock = false, $cat_manuf = false, $cat_manuf_v = 0, $type_features = false, $only_main_cat = false)
    {
        $default_currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $actual_currency = new Currency($this->context->currency->id);

        $cat_stock_filter = '';
        if ($instock == true) {
            $cat_stock_filter = ' AND stock.`quantity` > 0';
        }

        if (!$context) {
            $context = Context::getContext();
        }

        if ($check_access && !$this->checkAccess($context->customer->id)) {
            return false;
        }

        $front = in_array($context->controller->controller_type, array(
            'front',
            'modulefront'
        ));

        $id_supplier = (int)Tools::getValue('id_supplier');

        if ($only_main_cat == false) {
            $sql_main_cat = '';
        } else {
            $sql_main_cat = 'AND p.id_category_default IN (' . $categories . ') ';
        }

        /** Return only the number of products */
        if ($get_total) {
            $sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `' . _DB_PREFIX_ . 'product` p
					' . Shop::addSqlAssociation('product', 'p') . '
					LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` =  IN (' . $categories . ') ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . ($active ? ' AND product_shop.`active` = 1' : '') . ($id_supplier ? 'AND p.id_supplier = ' . (int)$id_supplier : ''). ' '.$sql_main_cat;
            return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }

        if ($p < 1) {
            $p = 1;
        }

        /** Tools::strtolower is a fix for all modules which are now using lowercase values for 'orderBy' parameter */
        $order_by = Validate::isOrderBy($order_by) ? Tools::strtolower($order_by) : 'position';
        $order_way = Validate::isOrderWay($order_way) ? Tools::strtoupper($order_way) : 'ASC';

        $order_by_prefix = false;
        if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'manufacturer' || $order_by == 'manufacturer_name') {
            $order_by_prefix = 'm';
            $order_by = 'name';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'cp';
        } elseif ($order_by = 'best') {
            $order_by_prefix = 'psale';
            $order_by = 'sale_nbr';
        }


        if ($order_by == 'price') {
            $order_by = 'orderprice';
        }

        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }

        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity' . (Combination::isFeatureActive() ? ', IFNULL(product_attribute_shop.id_product_attribute, 0) AS id_product_attribute,
					product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity' : '') . ', pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image` id_image,
					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
					INTERVAL ' . (int)$nb_days_new_product . ' DAY)) > 0 AS new, product_shop.price AS orderprice
				FROM `' . _DB_PREFIX_ . 'category_product` cp
				LEFT JOIN `' . _DB_PREFIX_ . 'product` p
					ON p.`id_product` = cp.`id_product`
				' . Shop::addSqlAssociation('product', 'p') . (Combination::isFeatureActive() ? ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int)$context->shop->id . ')' : '') . '
				' . Product::sqlStock('p', null) . '
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('cl') . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl') . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int)$context->shop->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = ' . (int)$id_lang . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
                LEFT JOIN `' . _DB_PREFIX_ . 'product_sale` psale
					ON psale.`id_product` = p.`id_product`
				LEFT JOIN `' . _DB_PREFIX_ . 'feature_product` pfeat
					ON pfeat.`id_product` = p.`id_product`
				WHERE product_shop.`id_shop` = ' . (int)$context->shop->id . '
				    ' . $cat_stock_filter . '
				    ' . ($price_from != false ? ' AND product_shop.`price` >= ' . Tools::convertPrice($price_from, $actual_currency, $default_currency) : '') . '
				    ' . ($price_to != false ? ' AND product_shop.`price` <= ' . Tools::convertPrice($price_to, $actual_currency, $default_currency) : '') . '
				    ' . ($cat_manuf != false && $cat_manuf_v != 0 ? ' AND p.`id_manufacturer` = ' . (int)$cat_manuf_v . '' : '') . '
				    ' . (strlen($features) > 0 ? ' AND pfeat.`id_feature_value` IN (' . $features . ')' : '') . '
					AND cp.`id_category` IN (' . $categories . ') ' . ($active ? ' AND product_shop.`active` = 1' : '') . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . ($id_supplier ? ' AND p.id_supplier = ' . (int)$id_supplier : '') . ' ' . $sql_main_cat . '
					GROUP BY p.`id_product`';


        if ($random === true) {
            $sql .= ' ORDER BY RAND() LIMIT ' . (int)$random_number_products;
        } else {
            $sql .= ' ORDER BY ' . (!empty($order_by_prefix) ? $order_by_prefix . '.' : '') . '`' . bqSQL($order_by) . '` ' . pSQL($order_way) . '
			LIMIT ' . (((int)$p - 1) * (int)$n) . ',' . (int)$n;
        }


        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

        if (!$result) {
            return array();
        }


        if ($order_by == 'orderprice') {
            Tools::orderbyPrice($result, $order_way);
        }


        /** Modify SQL result */
        return Product::getProductsProperties($id_lang, $result);
    }

    public function checkAccess($id_customer)
    {
        return true;
    }

}


class ppbUpdate extends ppb
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3) {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2) {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1) {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0) {
            $version = (int)$version . "0000";
        }

        return (int)$version;
    }

    public static function encrypt($string)
    {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {
        if (ini_get("allow_url_fopen")) {
            if (function_exists("file_get_contents")) {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);

        return $actual_version;
    }
}

if (file_exists(_PS_MODULE_DIR_ . 'ppb/lib/smartyTemplatesManager/smartyTemplatesManager.php')) {
    require_once _PS_MODULE_DIR_ . 'ppb/lib/smartyTemplatesManager/smartyTemplatesManager.php';
}
?>