<?php
/**
* 2007-2018 PrestaShop
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
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2018 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
    exit;
require_once(_PS_MODULE_DIR_.'/pushoncart/classes/Promo.php');

class PushOnCart extends Module
{
    public $position;
    public $delete = '<i style="cursor:pointer;" class="icon-trash-o icon-2x" ';
    public $enabled = '<img style="cursor:pointer;" src="../img/admin/enabled.gif" ';
    public $disabled = '<img style="cursor:pointer;" src="../img/admin/disabled.gif" ';
    public $arrowup = '<i style="cursor:pointer;" class="icon-arrow-circle-up up" ';
    public $arrowdown = '<i style="cursor:pointer;" class="icon-arrow-circle-down down" ';
    public $plus = '<i style="cursor:pointer;" class="icon-eye icon-2x" ';
    protected $js_path = null;
    protected $css_path = null;
    protected static $lang_cache;

    public function __construct()
    {
        $this->name = 'pushoncart';
        $this->version = '2.5.1';
        $this->tab = 'pricing_promotion';
        $this->author = 'Prestashop';
        if (version_compare(_PS_VERSION_, '1.6', '>'))
            $this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
        parent::__construct();
        $this->bootstrap = true;
        $this->displayName = $this->l('Push On Cart');
        $this->description = $this->l('Offer discounted products to clients on payment page');
        $this->module_key = 'd554085b814bee87b0a9653b3f407b99';
        $this->author_address = '0x64aa3c1e4034d07015f639b0e171b0d7b27d01aa';
        $this->js_path = $this->_path.'views/js/';
        $this->css_path = $this->_path.'views/css/';
        if (version_compare(_PS_VERSION_, '1.6', '<'))
            $this->getLang();
        $this->ps_url = Tools::getCurrentUrlProtocolPrefix().htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__;
        $this->ps17 = version_compare(_PS_VERSION_, '1.7.0.0', '>=');
    }

    public function install()
    {
        if (Shop::isFeatureActive())
            Shop::setContext(Shop::CONTEXT_ALL);

        $token = uniqid(rand(), true);
        Configuration::updateValue('PUSHONCART_TOKEN', $token);
        Configuration::updateValue('PUSHONCART_ORDER_METHOD', 1);
        Configuration::updateValue('PUSHONCART_PROMO_COUNT', 1);

        Configuration::updateValue('PUSHONCART_DESIGN_TOP_BANNER_BG', '#808080');
        Configuration::updateValue('POC_DESIGN_TOP_BANNER_TEXT', '#000000');
        Configuration::updateValue('PUSHONCART_DESIGN_BUTTON', 'success');
        Configuration::updateValue('PUSHONCART_CUSTOM', 0);
        Configuration::updateValue('PUSHONCART_DESIGN_PROMO_TEXT', 0);

        return (parent::install()
            && $this->registerHook('shoppingCart')
            && $this->registerHook('displayShoppingCart')
            && $this->registerHook('actionCartSave')
            && $this->registerHook('backOfficeHeader')
            && $this->registerHook('header')
            && $this->registerHook('displayFooter')
            && $this->installDB()
            && $this->installTab());
    }

    /**
    * Install Tab
    * @return boolean
    */
    private function installTab()
    {
        $tab = new Tab();
        $tab->id_parent = -1;
        $tab->class_name = 'Admin'.get_class($this);
        $tab->module = $this->name;
        $tab->active = 1;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = $this->displayName;
        unset($lang);
        return $tab->add();
    }

    public function installDB()
    {
            $product_table = Db::getInstance()->Execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pushoncartproducts` (
                    `id_pushoncart` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_shop` INT UNSIGNED NOT NULL,
                    `id_product` INT UNSIGNED NOT NULL,
                    `discount_name` VARCHAR(20),
                    `position` INT UNSIGNED NOT NULL,
                    `active` INT UNSIGNED NOT NULL,
                    `multiple_codes_allowed` INT UNSIGNED NOT NULL,
                    `view_count` INT UNSIGNED NOT NULL DEFAULT 0,
                    `num_ranges` INT UNSIGNED NOT NULL,
                    `date_created` DATE,
                    `date_exp` DATE,
                    `archive` INT UNSIGNED NOT NULL,
                    `retail_price` DECIMAL(20,6) NOT NULL,
                    PRIMARY KEY (`id_pushoncart`)
            ) DEFAULT CHARSET=utf8');

            $range_table = Db::getInstance()->Execute('
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pushoncart_ranges` (
                    `id_range` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_pushoncart` INT UNSIGNED NOT NULL,
                    `range_type` INT UNSIGNED NOT NULL DEFAULT 0,
                    `amount1` decimal(20,6),
                    `amount2` decimal(20,6),
                    `discount` INT UNSIGNED NOT NULL,
                    `discount_type` INT UNSIGNED NOT NULL,
                    `id_cart_rule` INT UNSIGNED NOT NULL,
                    `code` VARCHAR(254),
                    PRIMARY KEY (`id_range`),
                    FOREIGN KEY (id_pushoncart) REFERENCES '._DB_PREFIX_.'pushoncartproducts(id_pushoncart)
            ) DEFAULT CHARSET=utf8');
            if ($product_table && $range_table)
                return true;
            return false;
    }

    public function uninstall()
    {
        return (parent::uninstall()
            && $this->uninstallDB()
            && $this->uninstallTab());
    }
    /* If the user wants to uninstall the module and all tables & information from the database,
     comment the uninstall method below and remove the comments from the above uninstall method.*/
    /*public function uninstall(){return parent::uninstall();}*/

    public function uninstallDB()
    {
        $query1 = Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'pushoncart_ranges`');
        $query2 = Db::getInstance()->Execute(' DROP TABLE IF EXISTS `'._DB_PREFIX_.'pushoncartproducts`');
        if ($query1 && $query2)
            return true;
        return false;
    }

    /**
    * Uninstall Tab
    * @return boolean
    */
    private function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('Admin'.get_class($this));
        if ($id_tab)
        {
            $tab = new Tab($id_tab);
            if (Validate::isLoadedObject($tab))
                return $tab->delete();
            else
                return false;
        }
        else
            return true;
    }

    public function getContent()
    {
        $this->loadAsset();

        /* Form type=date is only available in Google Chrome */
        $browser = '';
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false)
            $browser = 'Chrome';

        $conf_button = Tools::isSubmit('submitModule');
        $stats_button = Tools::isSubmit('submitStats');
        $reset_stats_button = Tools::isSubmit('resetStats');

        if ($conf_button === true)
        {
            $submit = 1;
            Configuration::updateValue('PUSHONCART_ORDER_METHOD', (int)Tools::getValue('promo_order'));
        }
        elseif ($stats_button === true || $reset_stats_button === true)
            $submit = 2;
        else
            $submit = '';
        $check = Tools::getValue('check');
        $html = '';

        if (isset($submit) && isset($check) && !empty($check))
            $html = $this->setProduct();
        /* Configuration Tab */

        $presta15 = 0;
        if ((bool)version_compare(_PS_VERSION_, '1.5.4', '<') == 1)
            $presta15 = 1;

        $this->context->smarty->assign(array(
            'is_submit' => $submit,
            'browser' => $browser,
            'promo_order' => (int)Configuration::get('PUSHONCART_ORDER_METHOD'),
            'id_lang' => $this->context->employee->id_lang,
            'currency' => $this->getCurrency(),
            'currency_sign' => $this->context->currency->getSign(),
            'productTable' => $this->createProductTable(),
            'return_html' => $html,
            'months' => $this->getMonthArray(),
            'start_year' => date('Y') - 3,
            'end_year' => date('Y') + 3,
            'promo_count' => (int)Configuration::get('PUSHONCART_PROMO_COUNT'),
            'ps_version15' => $presta15
        ));

        $stats_table = $this->createStatsTable();
        $monthly_table = $this->createMonthlyTable();

        $date_to = pSQL(Tools::getValue('stats_date_to'));
        $date_to_alt = pSQL(Tools::getValue('stats_date_to_alt'));

        $date_from = pSQL(Tools::getValue('stats_date_from'));
        $date_from_alt = pSQL(Tools::getValue('stats_date_from_alt'));

        $reset = Tools::isSubmit('resetStats');
        if ($reset == 'Reset') {
            $date_to = null;
            $date_to_alt = null;
            $date_from = null;
            $date_from_alt = null;
        }
        /* Statistics Tab */

        $this->context->smarty->assign(array(
            'stats_table' => $stats_table,
            'monthly_table' => $monthly_table,
            'date_to' => $date_to,
            'date_to_alt' => $date_to_alt,
            'date_from_alt' => $date_from_alt,
            'date_from' => $date_from
        ));
        /* Design Tab */
        $id_product_demo = $this->getAnyProduct();
        $this->context->smarty->assign(array(
            'demo_imageLink' => $this->getCoverImagePath($id_product_demo),
            'design_colors' => $this->getPromoDesignInfo()
        ));

        return $this->display(__FILE__, 'views/templates/admin/configuration.tpl');
    }

    public function getPromoDesignInfo()
    {
        $keys = array(
            'PUSHONCART_DESIGN_TOP_BANNER_BG',
            'POC_DESIGN_TOP_BANNER_TEXT',
            'PUSHONCART_DESIGN_BUTTON',
            'PUSHONCART_CUSTOM',
            'PUSHONCART_DESIGN_PROMO_TEXT'
            );
        return Configuration::getMultiple($keys);
    }

    public function getCurrency()   /* Back-office form */
    {
        $currencies = Context::getContext()->currency->getCurrencies();
        $num_of_currencies = count($currencies);
        $temp = array();
        for ($i = 0; $i < $num_of_currencies; $i++)
            $temp[] = $currencies[$i];
        return $temp;
    }

    public function getShop($position)
    {
        $query = 'SELECT id_shop FROM '._DB_PREFIX_.'pushoncartproducts
                  WHERE position = '.(int)$position;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
        return (int)$result['id_shop'];
    }

    public function getShopIDs($p_id)
    {
        $query = 'SELECT ps.id_shop, sh.name FROM '._DB_PREFIX_.'product_shop ps, '._DB_PREFIX_.'shop sh
                  WHERE id_product = '.(int)$p_id.' AND ps.id_shop = sh.id_shop';

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
        return $result;
    }

    public function getShopIDArray($shop_ids)
    {
        $indexes = array();
        $shop_count = count($shop_ids);
        for ($i = 0; $i < $shop_count; $i++)
            array_push($indexes, $i);
        return array_combine($indexes, $shop_ids);
    }

    public function shopCount()
    {
        $query = 'SELECT COUNT(id_shop) FROM '._DB_PREFIX_.'shop WHERE active = 1';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
        return (int)$result['COUNT(id_shop)'];
    }

    public function setProduct()
    {
        $discount_name = Tools::getValue('discountName');

        $id_product = (int)Tools::getValue('product');
        $product = new Product($id_product);
        if (!Validate::isLoadedObject($product)) {
            return false;
        }
        /* Adding product price to table. If site owner changes price, statistics page won't be affected */
        $retail_price = $product->price;

        $use_ranges = (int)Tools::getValue('use_discount_range');
        $multiple_codes_allowed = (int)Tools::getValue('multiple_codes_allowed');
        $num_of_ranges = 1;
        if ($use_ranges == 1)
            $num_of_ranges = (int)Tools::getValue('num_of_ranges');

        $date = $this->getDateExp();

        $multiple_codes_allowed = (int)Tools::getValue('multiple_codes_allowed');

        $html = '';
        $query = '';

        if (!empty($id_product) && isset($id_product) && $date != null)
        {
            $chosen_shop_id = Tools::getValue('shop_id');
            /* Create alert offering to change promotion for already listed product */
            if ($this->databaseContains($id_product, $chosen_shop_id))
                $html .= '<script>
                            alert("'.$this->l('Attention ! This product is already registered with a promotion.').'")
                        </script>';
            else
            {
                $date_created = 'NOW()';
                /* Add to PushOnCart */

                $count_shops_with_product = null;
                $shop_ids = null;
                if ($chosen_shop_id != null && (!empty($chosen_shop_id) || isset($chosen_shop_id)))
                {
                    $shop_ids = $this->getShopIDArray($chosen_shop_id);
                    $shops_with_product = $this->getShopIDs($id_product);
                    $count_shops_with_product = count($shops_with_product);
                }
                if ($count_shops_with_product > 1 && (!empty($shop_ids) || isset($shop_ids)) && $shop_ids != null)
                {
                    $count_shop_ids = count($shop_ids);

                    foreach ($shop_ids as $id_shop)
                    {
                        if (empty($discount_name))
                            $discount_name = $this->l('Special Offer');

                        $query = 'INSERT INTO `'._DB_PREFIX_.'pushoncartproducts`
                                    (id_product, id_shop, position, discount_name, active, multiple_codes_allowed, date_exp, date_created, num_ranges, archive, retail_price)
                                    VALUES ('.(int)$id_product.', '.(int)$id_shop.', '.(int)$this->getNextAvailablePosition().', "'.pSQL($discount_name).'", 1, '
                                        .(int)$multiple_codes_allowed.', "'.pSQL($date).'", '.pSQL($date_created).', '.(int)$num_of_ranges.', 0, '.(float)$retail_price.'); ';
                        Db::getInstance()->Execute($query);

                        $range = $this->insertRanges();
                        if (!$range)
                            $html .= '<script language="javascript">
                                        alert("'.$this->l('There is a problem with the creation of the ranges.').'");
                                    </script>';
                    }
                }
                else
                {
                    if (empty($discount_name))
                            $discount_name = $this->l('Special Offer');
                    $query = 'INSERT INTO `'._DB_PREFIX_.'pushoncartproducts`
                            (id_product, id_shop, position, discount_name, active, multiple_codes_allowed, date_exp, date_created, num_ranges, archive, retail_price)
                            VALUES ('.$id_product.', '.(int)$this->getShopByProduct($id_product).', '
                                .(int)$this->getNextAvailablePosition().', "'.pSQL($discount_name).'", 1, '
                                .(int)$multiple_codes_allowed.', "'.pSQL($date).'", '.pSQL($date_created).', '
                                .(int)$num_of_ranges.', 0, '.(float)$retail_price.'); ';

                    Db::getInstance()->Execute($query);
                    $range = $this->insertRanges();
                    if (!$range)
                        $html .= '<script language="javascript">
                                    alert("'.$this->l('There is a problem with the creation of the ranges.').'");
                                </script>';
                }

                $html .= $this->displayConfirmation($this->l('Promotion created successfully.'));
            }
        }
        else
            $html .= '<script language="javascript">
                        alert("'.$this->l('Attention! Please ensure you have chosen a product and an expiration date for this promotion.').'");
                    </script>';

        return $html;
    }

    public function insertRanges()  /* into ranges table in the database */
    {
        $use_ranges = (int)Tools::getValue('use_discount_range');
        $id_pushoncart = $this->getPushOnCartIDFromProduct();   /* Get pushoncart_id that has just been created */
        $promo = new Promo($id_pushoncart);

        $id_cart_rule = 0;
        $code = '';
        $query = '';

        if ($use_ranges == 1)
        {
            $num_of_ranges = (int)Tools::getValue('num_of_ranges');
            if ($num_of_ranges > 1)
            {
                for ($i = 1; $i <= $num_of_ranges; $i++)
                {
                    $discount_type_range_name = 'discount_type_range'.$i;
                    $discount_type = (int)Tools::getValue($discount_type_range_name);
                    $discount = (float)Tools::getValue('discount_range'.$i);
                    $more_than = (float)Tools::getValue('more_than'.$i);
                    $less_than = (float)Tools::getValue('less_than'.$i);
                    $code = pSQL(Tools::getValue('promo_code'.$i));
                    if ($code == '')
                        $code = $this->generateCode();

                    $id_cart_rule = $this->createCartRule($code, $discount, $discount_type, $more_than);   /* Create Cart Rule */

                    $result = $promo->insertRange($more_than, $less_than, $discount, $discount_type, $id_cart_rule, $code);
                    if (!$result)
                        return null;
                }
            }
            else
            {
                $cart_max_min = (int)Tools::getValue('cart_max_min');

                if ($cart_max_min == 1)
                {
                    $code = Tools::getValue('promo_code1');
                    if ($code == '')
                        $code = pSQL($this->generateCode());
                    $discount = (float)Tools::getValue('discount_range1');
                    $discount_type = (int)Tools::getValue('discount_type_range1');
                    $less_than = (float)Tools::getValue('less_than1');
                    $id_cart_rule = $this->createCartRule($code, $discount, $discount_type, 0);   /* Create Cart Rule */

                    $result = $promo->insertRange(null, $less_than, $discount, $discount_type, $id_cart_rule, $code);
                    $query = 'INSERT INTO `'._DB_PREFIX_.'pushoncart_ranges` (id_pushoncart, discount, discount_type, amount2, code, id_cart_rule)
                            VALUES ('.$id_pushoncart.', '.$discount.', '.$discount_type.', '.$less_than.', '."'".$code."'".', '.$id_cart_rule.')';
                }
                else
                {
                    $code = pSQL(Tools::getValue('promo_code2'));
                    if ($code == '')
                        $code = pSQL($this->generateCode());
                    $discount = (float)Tools::getValue('discount_range2');
                    $discount_type = (int)Tools::getValue('discount_type_range2');
                    $more_than = (float)Tools::getValue('more_than2');
                    $id_cart_rule = $this->createCartRule($code, $discount, $discount_type, $more_than);   /* Create Cart Rule */
                    $result = $promo->insertRange($more_than, null, $discount, $discount_type, $id_cart_rule, $code);
                }
                if (!$result)
                    return null;
            }
        }
        else
        {
            $cart_minimum_option = (float)Tools::getValue('NO_cart_minimum');
            $code = pSQL(Tools::getValue('promo_code'));

            $discount = (float)Tools::getValue('discount');
            $discount_type = (int)Tools::getValue('discount_type');

            if ($code == '')
                $code = pSQL($this->generateCode());

            if ($cart_minimum_option == 1)
            {
                $cart_minimum = (float)Tools::getValue('more_than');
                $id_cart_rule = $this->createCartRule($code, $discount, $discount_type, $cart_minimum);
                $result = $promo->insertRange($cart_minimum, null, $discount, $discount_type, $id_cart_rule, $code);
            }
            else
            {
                $id_cart_rule = $this->createCartRule($code, $discount, $discount_type, 0);
                $result = $promo->insertRange(null, null, $discount, $discount_type, $id_cart_rule, $code);
            }
            if (!$result)
                return null;
        }

        return 1;
    }

    public function createCartRule($promo_code, $discount_value, $discount_type, $cart_minimum)
    {
        $languages = Language::getLanguages();
        $name = pSQL(Tools::getValue('discountName'));
        if (!isset($name) || empty($name) || $name == null)
            $name = $this->l('Special Offer');

        $date = $this->getDateExp();
        $default_currency = Currency::getDefaultCurrency();
        $discount = new CartRule();
        foreach ($languages as $language)
                $discount->name[$language['id_lang']] = $name;
        unset($languages, $language);
        $discount->description = 'pushoncart';
        $discount->code = $promo_code;
        $discount->partial_use = 0;
        $discount->minimum_amount_currency = $default_currency->id;
        $discount->minimum_amount = $cart_minimum;
        if ($discount_type != 0)
        {
            $discount->reduction_currency = $discount_type;
            $discount->reduction_amount = $discount_value;
            $discount->reduction_tax = 1;
        }
        else
            $discount->reduction_percent = $discount_value;
        $discount->reduction_product = (int)Tools::getValue('product');
        $discount->quantity_per_user = 1;
        $discount->quantity = 100000;
        $discount->product_restriction = 1;
        $discount->id_customer = null;
        $discount->active = 1;
        $discount->date_from = date('Y-m-d H:i:s', strtotime('yesterday 00:00'));
        $date_future = $date;

        $discount->date_to = $date_future;

        if ($discount->add())
        {
            $this->makeRuleProductSpecific($discount->id, Tools::getValue('product'));
            return $discount->id;
        }
        return 0;
    }

    public function makeRuleProductSpecific($cart_rule_id, $id_product)
    {
        $query3 = 'INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (id_cart_rule, quantity) VALUES ('.(int)$cart_rule_id.', 1)';
        /* PS_CART_RULE_PRODUCT_RULE_GROUP */
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($query3);

        $iprg = (int)Db::getInstance()->getValue('
                SELECT Max(id_product_rule_group)
                FROM `'._DB_PREFIX_.'cart_rule_product_rule_group`');
        $query4 = 'INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule` (id_product_rule_group, type) VALUES ('.(int)$iprg.', \'products\')';
        /* PS_CART_RULE_PRODUCT_RULE */
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($query4);

        $ipr = (int)Db::getInstance()->getValue('
                SELECT Max(id_product_rule)
                FROM `'._DB_PREFIX_.'cart_rule_product_rule`');
        $query5 = 'INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_value` (id_product_rule, id_item) VALUES ('.(int)$ipr.', '.(int)$id_product.')';
        /* PS_CART_RULE_PRODUCT_RULE_VALUE */
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($query5);
    }

    public function createProductTable()    /* Table in the module's back-office */
    {
        $query_promos = 'SELECT id_pushoncart
                            FROM '._DB_PREFIX_.'pushoncartproducts WHERE archive = 0 ORDER BY position ASC';
        $html = '';
        $ids_pushoncart = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query_promos);

        if (!empty($ids_pushoncart) && isset($ids_pushoncart))
        {
            foreach ($ids_pushoncart as $id_pushoncart)
            {
                $promo = new Promo($id_pushoncart['id_pushoncart']);

                $available_stock = $this->checkStock(trim($promo->parameters['id_product']), (int)$promo->parameters['id_shop']);
                $stock_warning = '';
                if ($available_stock === 0)
                    $stock_warning .= '<span style="color:red;">&nbsp;&nbsp;-&nbsp;&nbsp;'.$this->l('Out of Stock').'</span>';

                $product_name = Product::getProductName(trim($promo->parameters['id_product']));
                $shop = new Shop($promo->parameters['id_shop']);

                $html .= '<tr id="'.trim($promo->id_pushoncart).'">
                            <td>'.$this->plus.' onClick="showDiscountInfo(\''.trim($promo->id_pushoncart).'\');"></i></td>
                            <td>'.$shop->name.'</td>
                            <td>'.$product_name.$stock_warning.'</td>
                            <td align="center">'.trim($promo->id_pushoncart).'</td>
                            <td align="center">'.$promo->parameters['discount_name'].'</td>
                            <td align="center">'.$promo->parameters['date_exp'].'</td>
                            <td align="center">'
                                .($promo->parameters['multiple_codes_allowed'] == 1 ? $this->enabled : $this->disabled).'
                                onClick="enableMultipleCodes(\''.trim($promo->id_pushoncart).'\');">
                            </td>
                            <td align="right">'.
                                $this->arrowup.' onClick="move(\'up\', \''.trim($promo->parameters['id_product']).'\');"></i>
                                '.$this->arrowdown.' onClick="move(\'down\', \''.trim($promo->parameters['id_product']).'\');"></i>
                            </td>
                            <td align="center">'.
                                ($promo->parameters['active'] == 1 ? $this->enabled : $this->disabled).'
                                onClick="enable(\''.trim($promo->id_pushoncart).'\');">
                            </td>
                            <td align="center">'.
                                $this->delete.' onClick="if(confirm(\''.$this->l('Attention ! Are you sure you want to delete this promotion?').'\'))
                                archivePromo(\''.trim($promo->id_pushoncart).'\');"></td>
                        </tr>';
            }
        }

        return $html;
    }

    public function databaseContains($p_id, $id_shop = null)
    {
        $shops_with_product = $this->getShopIDs($p_id);
        $query = '';
        $count_shops_with_product = count($shops_with_product);

        $result = array();
        foreach ($shops_with_product as $shop)
        {
            $query = 'SELECT id_product FROM '._DB_PREFIX_.'pushoncartproducts
                WHERE id_product = '.(int)$p_id.' AND id_shop = '.(int)$shop['id_shop'].' AND archive = 0';

            $temp = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
            array_push($result, $temp);
        }

        $products = array();

        foreach ($result as $product)
            if (!empty($product))
                $products[] = ($product[0]);

        if (count($products) > 0)
            return true;
        return false;
    }

    public function specificPricesContains($p_id, $ids_shop = null)
    {
        $unlimited_date = '0000-00-00 00:00:00';
        $query = 'SELECT id_product FROM '._DB_PREFIX_.'specific_price
            WHERE id_product = '.(int)$p_id.' AND (`to` > NOW() OR `to` = "'.$unlimited_date.'")';
        if ($ids_shop != null && !empty($ids_shop))
        {
            array_push($ids_shop, 0);
            $query .= ' AND id_shop NOT IN ('.implode(array_map('intval', $ids_shop), ', ').')';
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);

        $products = array();
        foreach ($result as $product)
                $products[] = ($product['id_product']);

        if (count($products) > 0)
            return true;
        return false;
    }

    public function getProductID($name)
    {
        $query = 'SELECT id_product FROM '._DB_PREFIX_.'product_lang
                  WHERE name = "'.pSQL($name).'"';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);

        if (isset($result) && !empty($result))
            return $result['id_product'];
        return 0;
    }

    public function getDateExp()
    {
        $use_ranges = (int)Tools::getValue('use_discount_range');
        $browser = pSQL(Tools::getValue('browser'));

        $date_yes = pSQL(Tools::getValue('date_exp_yes'));
        $date_yes_alt = pSQL(Tools::getValue('date_exp_yes_alt'));

        $date_no = pSQL(Tools::getValue('date_exp_no'));
        $date_no_alt = pSQL(Tools::getValue('date_exp_no_alt'));

        $date = '';
        if ($use_ranges == 1)
        {
            if ($browser == 'Chrome')
            {
                if ($date_yes != null || !isset($date_yes))
                    $date = $date_yes;
            }
            else
                if ($date_yes_alt != null || !isset($date_yes_alt))
                    $date = $date_yes_alt;
        }
        else
        {
            if ($browser == 'Chrome')
            {
                if ($date_no != null)
                    $date = $date_no;
            }
            else
                if ($date_no_alt != null || !isset($date_no_alt))
                    $date = $date_no_alt;
        }
        if ($date != null)
            $date = $date.' 00:00:00';

        return $date;
    }

    public function up()
    {
        return $this->move('up');
    }

    public function down()
    {
        return $this->move('down');
    }

    public function afterDelete($id_product)
    {
        /* Rearranges tables after promotion is deleted */
        $position = (int)$this->getPositionByProduct($id_product);
        if ($position != 0)
        {
            $new_position = $position + 1;
            $tmp_product = $this->getProductByPosition($new_position);
            while ($tmp_product)
            {
                $this->move('up', $tmp_product);
                $new_position++;
                $tmp_product = $this->getProductByPosition($new_position);
            }
        }
        /* Deleting the cart rules from the prestashop store */
        $cart_rules = Db::getInstance()->ExecuteS('
                SELECT id_cart_rule
                FROM `'._DB_PREFIX_.'pushoncartproducts` pr, `'._DB_PREFIX_.'pushoncart_ranges` ranges
                WHERE pr.id_product = '.(int)$id_product.' AND id_shop = '.(int)$this->getShopByProduct((int)$id_product).
                ' AND pr.id_pushoncart = ranges.id_pushoncart');
        $count = count($cart_rules);
        for ($i = 0; $i < $count; $i++)
        {
            $cart_rule = new CartRule($cart_rules[$i]['id_cart_rule']);
            if (!Validate::isLoadedObject($cart_rule))
                Tools::displayError('Error loading Cart_Rule (function (afterDelete)).');
            $cart_rule->delete();
        }
    }

    public function move($action = 'down', $id_product)
    {
        $query = 'SELECT position FROM '._DB_PREFIX_.'pushoncartproducts
                  WHERE id_product = '.(int)$id_product.' AND position != 0 AND archive = 0';
        $old_position = Db::getInstance()->getValue($query);

        /* get new position according to current */
        $new_position = ($action == 'up' ? ($old_position - 1) : ($old_position + 1));
        if ($new_position != 0)
        {
            $tmp_product = $this->getProductByPosition($new_position);

            if ($tmp_product)
            {
                $this->setPosition($tmp_product, $old_position);
                $this->setPosition($id_product, $new_position);
            }
        }
        return false;
    }

    protected function getProductByPosition($position = 1)
    {
        $id = (int)Db::getInstance()->getValue('
            SELECT id_product
            FROM `'._DB_PREFIX_.'pushoncartproducts`
            WHERE position = '.(int)$position).' AND archive = 0';

        if ($id > 0)
            return $id;
        return false;
    }

    private function setPosition($id_product, $position)
    {
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'pushoncartproducts
                SET position = '.(int)$position.' WHERE id_product = '.(int)$id_product.' AND archive = 0');
    }

    protected function getPositionByProduct($product_id)
    {
        $pos = (int)Db::getInstance()->getValue('
            SELECT position
            FROM `'._DB_PREFIX_.'pushoncartproducts`
            WHERE id_product = '.(int)$product_id);

        if (!is_null($pos))
            return $pos;
        return false;
    }

    public function getNextAvailablePosition() /* Determine next available position */
    {
        $last_position = (int)Db::getInstance()->getValue('
            SELECT Max(position)
            FROM `'._DB_PREFIX_.'pushoncartproducts`');
        return ++$last_position;
    }

    public function getRandomPromo($excluded_products, $id_cart)
    {
        $cart = new Cart((int)$id_cart);
        $total = $cart->getOrderTotal();
        if (!Validate::isLoadedObject($cart))
                Tools::displayError('Error loading Cart (function (getRandomPromo)).');

        $previous_rules = $cart->getCartRules();
        foreach ($previous_rules as $rule)
        {
            if ($rule['obj']->description == 'pushoncart')
                return 0;
        }

        $existing_cart_rule_count = (int)Db::getInstance()->getValue('
            SELECT COUNT(id_cart)
            FROM `'._DB_PREFIX_.'cart_cart_rule` WHERE id_cart = '.(int)$id_cart);
        $end_of_query = '';
        if ($existing_cart_rule_count != 0)
            $end_of_query = ' AND p.multiple_codes_allowed = 1';

        $promos = Db::getInstance()->ExecuteS('SELECT DISTINCT p.id_product
            FROM `'._DB_PREFIX_.'pushoncartproducts` p, `'._DB_PREFIX_.'pushoncart_ranges` r
            WHERE p.archive = 0 AND p.active = 1 AND p.id_product NOT IN ('.implode(array_map('intval', $excluded_products), ', ').' )'.$end_of_query.'
            AND p.id_shop = '.(int)$this->context->shop->id.'
            AND (r.amount1 <= '.(float)$total.' OR r.amount1 IS NULL)
            AND (r.amount2 > '.(float)$total.' OR r.amount2 IS NULL)
            AND r.id_pushoncart = p.id_pushoncart');
        foreach ($promos as $key => $promo)
        {
            $stock_ok = $this->checkStock($promo['id_product'], $this->context->shop->id);
            if ($stock_ok !== 1)
                unset($promos[$key]);
        }
        $count = count($promos);

        if ($count > 0)
        {
            $random_index = rand(0, $count - 1);
            return $promos[$random_index]['id_product'];
        }
        return 0;
    }

    public function save($null_values = false, $autodate = true)
    {
        /* Set position if needed */
        if (!(int)$this->position > 0)
            $this->position = $this->getNextAvailablePosition();
        parent::save($null_values, $autodate);
    }

    public function active($p_id, $id_shop)   /* Checks for active promotions */
    {
        $query = 'SELECT active FROM '._DB_PREFIX_.'pushoncartproducts WHERE archive = 0 AND id_product = '.(int)$p_id.' AND id_shop = '.(int)$id_shop;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
        return (int)$result['active'];
    }

    public function getProductPrices($id_product, $range)
    {
        $product = new Product((int)$id_product);
        if (!Validate::isLoadedObject($product))
                Tools::displayError('Error loading $product (function (getProductPrices)).');
        $retail_price = $product->getPrice();
        $discount_type = $range['discount_type'];
        $discount_amount = 0;

        $id_cart_rule = (int)$range['id_cart_rule'];

        $rule = new CartRule((int)$id_cart_rule);
        $discount_amount = $this->getDiscountAmount($range, $retail_price);

        $discount = '-'.$discount_amount;

        $symbol = $this->context->currency->getSign();
        if ($discount_type != '0')
            $discount .= $symbol;
        else
            $discount .= '%';
        $sale_price = $retail_price - $discount_amount;

        $reduction_percent = 0;
        if ($discount_type != '0')
            $reduction_percent = round(($discount_amount * 100) / $retail_price);
        else
            $reduction_percent = $rule->reduction_percent;

        $prices = array(
            'price' =>number_format($retail_price, 2),
            'sale_price' => number_format($sale_price, 2),
            'discount_amount' => $discount_amount,
            'discount_type' => $discount_type,
            'symbol' => $symbol,
            'discount_in_cash' => number_format($discount_amount, 2),
            'reduction_percent' => number_format($reduction_percent, 0),
            'id_cart_rule' => $id_cart_rule,
            'product_reference' => $product->reference);
        return $prices;
    }

    /*
    * Given range for this cart, the amount of the reduction is calculated
    */
    public function getDiscountAmount($range, $price)
    {
        $rule = new CartRule((int)$range['id_cart_rule']);
        if (!Validate::isLoadedObject($rule))
                Tools::displayError('Error loading $rule (function (getProductPrices)).');
        if ($range['discount_type'] != '0')
        {
            $rule_currency = new Currency((int)$rule->reduction_currency);
            $cart_currency = new Currency(Context::getContext()->cart->id_currency);
            if (!Validate::isLoadedObject($rule))
                Tools::displayError('Error loading $rule_currency (function (getProductPrices)).');
            return $rule->reduction_amount / $rule_currency->conversion_rate * $cart_currency->conversion_rate;
        }
        else
            return $price * $rule->reduction_percent / 100;
    }

    public function getProductsFromCurrentCart()
    {
        $products = $this->context->cart->getProducts();
        $product_array = array();

        foreach ($products as $product)
            array_push($product_array, $product['id_product']);
        return $product_array;
    }

    public function getPushOnCartIDFromProduct($id_product = null)
    {
        $query = 'SELECT max(id_pushoncart) as id FROM '._DB_PREFIX_.'pushoncartproducts';
        if ($id_product != null)
            $query .= ' WHERE id_product = '.(int)$id_product;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        return $result;
    }

    public function getShopByProduct($p_id)
    {
        $query = 'SELECT id_shop_default FROM '._DB_PREFIX_.'product
                  WHERE id_product = '.(int)$p_id;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
        return $result['id_shop_default'];
    }

    public function getAnyProduct()
    {
        $query = 'SELECT id_product FROM '._DB_PREFIX_.'product';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public function getPurchasedProducts()
    {
        $context = Context::getContext();

        $query = 'SELECT DISTINCT product_id FROM '._DB_PREFIX_.'order_detail, '._DB_PREFIX_.'orders
                WHERE '._DB_PREFIX_.'order_detail.id_order = '._DB_PREFIX_.'orders.id_order
                AND '._DB_PREFIX_.'orders.id_customer = '.(int)$context->customer->id;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
        $products = array();
        foreach ($result as $product)
                $products[] = ((int)$product['product_id']);

        return $products;
    }

    /*
    Checks if product is in customer's order history.
    Checks if the product is in the customer's current cart.
    Checks if the product is in this shop.
    Checks if there are other cart rules associated with the cart.
    */
    public function getTopProduct($skip = 0, $already_promoted = null)
    {
        $context = Context::getContext();
        $id_shop = $context->shop->id;
        $cart = new Cart($context->cart->id);
        $customer = new Customer((int)$cart->id_customer);

        $id_product = 0;
        if ($customer->id == null)
            $excluded_products = $this->getProductsFromCurrentCart();
        else
            $excluded_products = array_unique(array_merge($this->getProductsFromCurrentCart(), $this->getPurchasedProducts()));
        $excluded_products = array_merge($excluded_products, $already_promoted);
        $promo_order = (int)Configuration::get('PUSHONCART_ORDER_METHOD');

        if ($promo_order == 1)
        {
            $random_promo = $this->getRandomPromo($excluded_products, $cart->id);
            return $random_promo;
        }
        else
        {
            $skip = 0;
            for ($position = 1; $position < $this->getNextAvailablePosition(); $position++)
            {
                if ($this->getShop($position) == $context->shop->id)
                {
                    $id_product = (int)$this->getProductByPosition($position);

                    $stock_ok = $this->checkStock($id_product, $context->shop->id);

                    $active = $this->active($id_product, $context->shop->id);

                    if (!in_array($id_product, $excluded_products) && $active == 1 && $stock_ok == 1)
                    {
                        $multiple_rules_allowed = $this->multipleCodesAllowed($id_product);
                        $previous_rules = $cart->getCartRules();

                        foreach ($previous_rules as $rule)
                        {
                            if ($rule['obj']->description == 'pushoncart')
                                return 0;
                        }

                        if ($multiple_rules_allowed == 1)
                            unset($previous_rules);

                        if (empty($previous_rules))
                        {
                            $range = $this->returnRange($id_product, $id_shop);

                            if ($range != 0)
                            {
                                if ($skip == 0)
                                    return $id_product;
                                $skip--;
                            }
                        }
                    }
                }
            }
        }
        return 0;
    }

    public function checkStock($id_product, $id_shop, $id_product_attribute = null)
    {
        $enable_stock_management = (int)Configuration::get('PS_STOCK_MANAGEMENT');
        $general_stock_management = (int)Configuration::get('PS_ORDER_OUT_OF_STOCK');
        /* General flag that checks if shop can sell out of stock products */
        $flag = StockAvailable::outOfStock($id_product, $id_shop);
        /* Flag on specific products that checks if shop can sell the product when out of stock */
        $quantity_available = StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute, $id_shop);
        /* Checks the quantity available of the current product */

        if ($enable_stock_management == 0)
            return 1;
        if (($general_stock_management == 1 && $flag == 2) || $flag == 1)   /* Checking if available stock */
            return 1;
        else {
            if ($quantity_available > 0)
                return 1;
            return 0;
        }
        return 0;
    }

    public function returnRange($p_id, $id_shop)
    {
        $cart = Context::getContext()->cart;
        $cart_total = $cart->getOrderTotal();

        $query = 'SELECT id_range FROM '._DB_PREFIX_.'pushoncart_ranges
                        WHERE id_pushoncart = (SELECT id_pushoncart FROM '._DB_PREFIX_.'pushoncartproducts
                        WHERE active=1 AND archive=0
                        AND id_product = '.(int)$p_id.'
                        AND id_shop = '.(int)$id_shop.')
                        AND (amount1 <= '.(float)$cart_total.' OR amount1 IS NULL )
                        AND (amount2 > '.(float)$cart_total.' OR amount2 IS NULL)';

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);

        if (!empty($result['id_range']))
            return (int)$result['id_range'];
        return 0;
    }

    public function removeCartRules($id_pushoncart)
    {
        $query = 'SELECT `id_cart_rule` FROM `'._DB_PREFIX_.'pushoncart_ranges` WHERE `id_pushoncart` = '.(int)$id_pushoncart;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
        foreach ($result as $row)
        {
            $cart_rule = new CartRule((int)$row['id_cart_rule']);
            if (!Validate::isLoadedObject($cart_rule))
                Tools::displayError('Error loading $prod (function (getProductInfo)).');

            $cart_rule->delete();
        }
        return $result;
    }

    public function getCoverImagePath($id_product, $id_combination = null)
    {
        $context = Context::getcontext();
        $id_lang = (int)$context->language->id;
        $product = new Product($id_product);
        $cover_dir = Image::getImages($id_lang, $id_product, $id_combination);

        $id_cover_image = null;
        foreach ($cover_dir as $img)
            if ($img['cover'] == 1)
                $id_cover_image = $img['id_image'];

        $link = new Link();
        return Tools::getCurrentUrlProtocolPrefix().$link->getImageLink($product->link_rewrite[$id_lang], $id_cover_image);
    }

    public function getImage($p_id, $id_image = null)
    {
        $context = Context::getcontext();
        $query_image_id = 'SELECT id_image
                            FROM '._DB_PREFIX_.'image
                            WHERE id_product = '.(int)$p_id;

        if ($id_image != null)
            $query_image_id .= ' AND id_image = '.(int)$id_image;

        $query_image_id .= ' ORDER BY cover DESC';

        $images = Db::getInstance()->ExecuteS($query_image_id);

        $query = 'SELECT link_rewrite FROM '._DB_PREFIX_.'product_lang
              WHERE id_product = '.(int)$p_id.' AND id_lang = '.(int)$context->language->id;
        $link_rewrite = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);

        $link = $context->link;
        $image_type = ImageType::getImagesTypes();
        if (!empty($images[0]['id_image']))
        {
            if (Configuration::get('PS_LEGACY_IMAGES'))
                $image_link = Tools::getShopDomain(true).'/img/p/'.(int)$p_id.'-'.$images[0]['id_image'].'-large.jpg';
            else
                $image_link = $link->getImageLink($link_rewrite[0]['link_rewrite'], $images[0]['id_image'], $image_type[0]['name']);

            $image_link = str_replace('category', 'large', $image_link);
            $image_link = str_replace('cart', 'large', $image_link);

            return $image_link;
        }
        else return null;
    }

    public function getProductInfo($lang, $value)
    {
        $context = Context::getContext();
        $currency = Currency::getDefaultCurrency();

        $query = 'SELECT DISTINCT pl.id_product, pl.name FROM '._DB_PREFIX_.'product_lang pl
                    LEFT JOIN `'._DB_PREFIX_.'product` p
                    ON (pl.`id_product` = p.`id_product`)
                    WHERE pl.id_lang = '.(int)$lang.' AND pl.name LIKE "%'.pSQL($value).'%" AND p.active = 1';
        $result = Db::getInstance()->ExecuteS($query);
        $products = array();
        $products['html'] = '<ul style="list-style-type: none;padding:0px;">';
        $products['found_results'] = 0;
        if (count($result) > 0)
            $products['found_results'] = 1;
        foreach ($result as $row)
        {
            $image_link = $this->getCoverImagePath($row['id_product']);

            $prod = new Product((int)$row['id_product']);
            if (!Validate::isLoadedObject($prod))
                Tools::displayError('Error loading $prod (function (getProductInfo)).');

            /* Get it to tpl */
            $this->context->smarty->assign(array(
                'id_product' => $row['id_product'],
                'product_name' =>  str_replace('"', '', $row['name']),
                'product_image' => $image_link,
                'val' => str_replace("'", ' ', $row['name']),
                'lang' => $lang,
                'currency_symbol' => $currency->sign,
                'product_price' => number_format($prod->price, 2),
                'has_specific_price' => $this->specificPricesContains((int)$row['id_product']),
                'specific_price_warning' => $this->l('Contains specific price')
            ));
            $products['html'] .= $context->smarty->fetch(dirname(__FILE__).'/views/templates/admin/tabs/liveSearchResult.tpl');
        }
        $products['html'] .= '</ul>';
        return $products;
    }

    public function clear() /* Clears out the old cart_rules and promotions. */
    {
        $query = 'UPDATE '._DB_PREFIX_.'cart_rule SET active=0 WHERE date_to < NOW() AND description=\'pushoncart\'';
        Db::getInstance()->Execute($query);
        $query2 = 'UPDATE '._DB_PREFIX_.'pushoncartproducts SET position=0, archive=1, active=0 WHERE date_exp < NOW()';
        Db::getInstance()->Execute($query2);
        $this->sortPositions();
    }

    /*
    Quickly verifies that the positions on the table are correct and there are no gaps.
    */
    public function sortPositions()
    {
        $query = 'SELECT id_pushoncart, position, archive FROM '._DB_PREFIX_.'pushoncartproducts WHERE position != 0 ORDER BY position';
        $positions = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);

        if (!empty($positions) || !isset($positions))
        {
            $index = 0;
            $new_positions = array();
            $query_update = '';
            foreach ($positions as $value)
            {
                $index++;
                $new_positions[$index - 1]['position'] = $index;
                $new_positions[$index - 1]['id_pushoncart'] = $value['id_pushoncart'];
                $query_update = 'UPDATE '._DB_PREFIX_.'pushoncartproducts
                                    SET position = '.(int)$index.'
                                    WHERE id_pushoncart = '.(int)$value['id_pushoncart'].'';
                $result = Db::getInstance()->Execute($query_update);
                if (!$result)
                    return null;
            }
            return 1;
        }
        return null;
    }

    public function multipleCodesAllowed($p_id) /* Checks the customer can benefit from the PushOnCart promotion with other cart rules */
    {
        $query = 'SELECT multiple_codes_allowed FROM '._DB_PREFIX_.'pushoncartproducts
                    WHERE archive = 0 AND id_product = '.(int)$p_id;
        $result = (Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query));

        return (int)$result['multiple_codes_allowed'];
    }

    public function getAttributeText($product_attributes)
    {
        $attributes = '';
        $count_product_attributes = count($product_attributes);
        for ($i = 0; $i < $count_product_attributes; $i++)
            $attributes .= $product_attributes[$i]['public_name'].' : '.$product_attributes[$i]['name'].'<br />';
        return $attributes;
    }

    public function pullImage($id_product, $product_attributes)
    {
        $controller = pSQL(Tools::getValue('controller'));
        $product = new Product($id_product);
        $link = new Link();

        $images = $product->getImages(Context::getContext()->language->id);
        $test_link = $link->getImageLink($product->link_rewrite[1], $id_product, 'large');

        if (version_compare(_PS_VERSION_, '1.6', '<') || $controller == 'AdminPushOnCart')
            $id_image = null;
        else
            $id_image = Product::_getAttributeImageAssociations($product_attributes[0]['id_attribute']);

        $id_attribute = $product_attributes[0]['id_attribute'];
        $image_link = $this->getImage($id_product, $id_image);

        $image_link = str_replace('category', 'large', $image_link);
        return $image_link;
    }

    /* Hooks */

    public function hookShoppingCart()
    {
        $display = '';
        $promo_count = (int)Configuration::get('PUSHONCART_PROMO_COUNT');
        $this->clear();
        $already_promoted = array();

        for ($i = 0; $i < $promo_count; $i++)
        {
            $top_product = $this->getTopProduct($i, $already_promoted);

            if ($top_product != 0)
            {
                if (!in_array($top_product, $already_promoted))
                {
                    array_push($already_promoted, $top_product);

                    $product = new Product($top_product);

                    if ($top_product != 0)
                    {
                        $id_pushoncart = $this->getPushOnCartIDFromProduct($top_product);
                        $promo = new Promo($id_pushoncart);
                        $cart_promo_range = $promo->returnRange(Context::getContext()->cart->id);

                        $promo->viewCount();

                        $token = pSQL(Configuration::get('PUSHONCART_TOKEN'));
                        $product_prices = $this->getProductPrices($top_product, $cart_promo_range);

                        $pushoncart_url_product_offer = $this->ps_url.'index.php?fc=module&module=pushoncart&controller=productoffers';

                        if ($this->ps17)
                        {
                            Media::addJsDef(array(
                                'poc_ajax_url' => $pushoncart_url_product_offer
                            ));
                        }
                        else
                            $this->context->smarty->assign('poc_ajax_url', $pushoncart_url_product_offer);

                        $design_colours = $this->getPromoDesignInfo();
                        $this->context->smarty->assign(array(
                                'promo' => $promo,
                                'name'=> $product->getProductName((int)$promo->parameters['id_product']),
                                'description' => $product->description_short[Context::getContext()->cart->id_lang],
                                'product_prices' => $product_prices,
                                'currency_format' => (($this->context->currency->format % 2) != 0) ? 1 : 0, /* Should the currency symbol go before or after the figure */
                                'design_colours' => $design_colours,
                                'requestUri' => $this->ps_url.'modules/'.$this->name.'/addToCart.php',
                                'token' => $token,
                                'ps17' => $this->ps17
                                ));

                        $display .= $this->display(__FILE__, '/views/templates/front/productOffer.tpl');
                    }
                }
            }
        }
        if ($display != '')
            $display = $this->display(__FILE__, '/views/templates/front/productOfferHeader.tpl').$display;
        return $display;
    }

    public function hookHeader()
    {
        $pushoncart_url_product_offer = $this->ps_url.'index.php?fc=module&module=pushoncart&controller=productoffers';

        if ($this->ps17)
        {
            Media::addJsDef(array(
                'poc_ajax_url' => $pushoncart_url_product_offer
            ));
        }

        $this->context->controller->addJS($this->js_path.'front.js');
    }

    public function hookBackOfficeHeader()
    {
        $controller = pSQL(Tools::getValue('controller'));
        $configure = pSQL(Tools::getValue('configure'));

        if ($controller == 'AdminModules' && $configure == 'pushoncart')
        {
            $this->loadSmartyData();
            $this->sortPositions();
            $this->clear();
        }
    }

    public function hookFooter()
    {
        //$this->context->controller->addJS($this->js_path.'front.js');
    }
/* End of Hooks */

/* Statistics */
    public function createStatsTable()
    {
        $context = Context::getContext();

        $stat_table_html = '';
        $info = $this->getStatTableInfo();
        $symbol = Context::getContext()->currency->getSign();
        $count_info = count($info);
        for ($i = 0; $i < $count_info; $i++)
        {
            $products_sold = 0;
            if (isset($info[$i]['SUM(od.product_quantity)'])
                && !empty($info[$i]['SUM(od.product_quantity)'])
                && array_key_exists($info[$i]['SUM(od.product_quantity)'], $info)
                || isset($info[$i]['SUM(od.product_quantity)']))
                $products_sold = $info[$i]['SUM(od.product_quantity)'];

            $view_more = '';
            if ($products_sold != 0)
                $view_more = $this->plus.' onClick="showStatsOrders(\''.trim($info[$i]['id_pushoncart']).'\');"></i>';

            $product_name = Product::getProductName((int)$info[$i]['id_product']);

            $value_tax_excl = 0;
            if (isset($info[$i]['value_tax_excl']) && !empty($info[$i]['value_tax_excl']))
                $value_tax_excl = $info[$i]['value_tax_excl'];
            $stat_table_html .= '<tr id="pushoncart_'.$info[$i]['id_pushoncart'].'">
                                    <td>'.$view_more.'</td>
                                    <td>'.$info[$i]['id_pushoncart'].'</td>
                                    <td>'.$info[$i]['discount_name'].'</td>
                                    <td>'.$info[$i]['id_product'].'</td>
                                    <td>'.$product_name.'</td>
                                    <td>'.$symbol.number_format($info[$i]['retail_price'], 2).'</td>
                                    <td>'.$info[$i]['view_count'].'</td>
                                    <td>'.$products_sold.'</td>';
                                    if ($info[$i]['view_count'] == 0)
                                        $stat_table_html .= '<td>0%</td>';
                                    else
                                        $stat_table_html .= '<td>'.number_format(($products_sold / $info[$i]['view_count'] * 100), 2).'%</td>';

                                    $stat_table_html .= '<td>'.$symbol.number_format(($info[$i]['retail_price'] * $products_sold) - $value_tax_excl, 2).'</td>
                                    <td align="center">'
                                        .$this->delete.' onClick="
                                                            if(confirm(\''.
                                                                $this->l(
                                                                    'Attention ! Are you sure you want to delete this promotion completely? This will erase the statistics and it is irreversible.').
                                                                '\'))
                                                                erasePromo(\''.trim($info[$i]['id_pushoncart']).'\', \''.$info[$i]['id_product'].'\');">
                                    </td>
                                </tr>';
        }
        return $stat_table_html;
    }

    public function getStatTableInfo()
    {
        $browser = Tools::getValue('browser');
        $d1 = '';
        $d2 = '';
        if ($browser == 'Chrome')
        {
            $d1 = Tools::getValue('stats_date_to');
            $d2 = Tools::getValue('stats_date_from');
        }
        else
        {
            $d1 = Tools::getValue('stats_date_to_alt');
            $d2 = Tools::getValue('stats_date_from_alt');
        }

        $date_to = date('Y-m-d H:i:s', strtotime($d1));
        $date_from = date('Y-m-d H:i:s', strtotime($d2));

        $query = 'SELECT DISTINCT pp.id_pushoncart, pp.id_product, pp.id_pushoncart, pp.discount_name, pp.view_count, pp.num_ranges, pp.retail_price,
                         pr.id_range, pr.amount1, pr.amount2, pr.discount, pr.discount_type, pr.id_cart_rule, pr.code,
                         ocr.id_order, ocr.value_tax_excl,
                         SUM(od.product_quantity), SUM(od.product_price), od.unit_price_tax_excl, od.product_price
                    FROM '._DB_PREFIX_.'pushoncartproducts pp
                    JOIN '._DB_PREFIX_.'pushoncart_ranges pr ON pp.id_pushoncart = pr.id_pushoncart
                    JOIN '._DB_PREFIX_.'order_cart_rule ocr ON pr.id_cart_rule = ocr.id_cart_rule
                    JOIN '._DB_PREFIX_.'order_detail od ON ocr.id_order = od.id_order
                    JOIN '._DB_PREFIX_.'orders pso ON od.id_order = pso.id_order
                        WHERE pp.id_product = od.product_id';

        $submit = Tools::isSubmit('submitStats');
        $reset = Tools::isSubmit('resetStats');
        if ($reset == $this->l('Reset'))
            $submit = null;

        if (isset($submit) && !empty($submit) && isset($d1) && !empty($d1) && isset($d2) && !empty($d2))
            $query .= ' AND pso.date_add >= "'.$date_from.'" AND pso.date_add <= "'.$date_to.'"';

        $query .= ' GROUP BY pp.id_pushoncart';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);

        $pp_products = '';
        foreach ($result as $pp_product)
            $pp_products = $pp_products."'".$pp_product['id_pushoncart']."', ";

        $query_presales = 'SELECT *
                    FROM '._DB_PREFIX_.'pushoncartproducts pp';
                    if ($pp_products != null || !empty($pp_products))
                        $query_presales .= ' WHERE pp.id_pushoncart NOT IN ( '.rtrim($pp_products, ', ').')
                    ';

        $result_presales = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query_presales);
        $final = array_merge($result, $result_presales);

        return $final;
    }

    public function createMonthlyTable()
    {
        $browser = Tools::getValue('browser');
        $d1 = '';
        $d2 = '';
        if ($browser == 'Chrome')
        {
            $d1 = Tools::getValue('stats_date_to');
            $d2 = Tools::getValue('stats_date_from');
        }
        else
        {
            $d1 = Tools::getValue('stats_date_to_alt');
            $d2 = Tools::getValue('stats_date_from_alt');
        }
        if (isset($d1) && !empty($d1) && isset($d2) && !empty($d2) && $d1 < $d2)
            return '<script>
                            alert("'.$this->l('The dates you have chosen are not valid.').'")
                        </script>';
        $monthly_table_html = '';
        $info = $this->getMonthlyTableInfo();

        $symbol = Context::getContext()->currency->getSign();
        $sizeof_info = count($info);
        for ($i = 0; $i < $sizeof_info; $i++)
        {
            $month = $this->getMonth($info[$i]['date_add']);
            $revenue = ($info[$i]['SUM(pp.retail_price)']) - $info[$i]['SUM(ocr.value_tax_excl)'];
                $monthly_table_html .= '<tr>
                                            <td>'.$month.'</td>
                                            <td>'.$symbol.number_format($revenue, 2).'</td>
                                     </tr>';
        }

        return $monthly_table_html;
    }

    public function getMonthlyTableInfo()
    {
        $browser = Tools::getValue('browser');
        $d1 = '';
        $d2 = '';
        if ($browser == 'Chrome')
        {
            $d1 = Tools::getValue('stats_date_to');
            $d2 = Tools::getValue('stats_date_from');
        }
        else
        {
            $d1 = Tools::getValue('stats_date_to_alt');
            $d2 = Tools::getValue('stats_date_from_alt');

        }
        $date_to = date('Y-m-d H:i:s', strtotime($d1));
        $date_from = date('Y-m-d H:i:s', strtotime($d2));

        $query = 'SELECT SUM(pp.retail_price),
                         ocr.value_tax_excl, SUM(ocr.value_tax_excl), pso.date_add,
                         SUM(od.product_quantity), SUM(od.product_price), SUM(od.unit_price_tax_excl), od.unit_price_tax_excl, od.product_price
                    FROM '._DB_PREFIX_.'pushoncartproducts pp
                    JOIN '._DB_PREFIX_.'pushoncart_ranges pr ON pp.id_pushoncart = pr.id_pushoncart
                    JOIN '._DB_PREFIX_.'order_cart_rule ocr ON pr.id_cart_rule = ocr.id_cart_rule
                    JOIN '._DB_PREFIX_.'order_detail od ON ocr.id_order = od.id_order
                    JOIN '._DB_PREFIX_.'orders pso ON od.id_order = pso.id_order
                        WHERE pp.id_product = od.product_id';

        $submit = Tools::isSubmit('submitStats');
        $reset = Tools::isSubmit('resetStats');
        if ($reset == $this->l('Reset'))
            $submit = null;

        if (isset($submit) && !empty($submit) && isset($d1) && !empty($d1) && isset($d2) && !empty($d2))
            $query .= ' AND pso.date_add >= "'.$date_from.'" AND pso.date_add <= "'.$date_to.'"';

        $query .= ' GROUP BY MONTH(pso.date_add), YEAR(pso.date_add)';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);

        return $result;
    }

    public function getMonth($date)
    {
        $months = $this->getMonthArray();
        $month = (int)Tools::substr($date, 5, 2);
        $year = (int)Tools::substr($date, 0, 4);
        return $months[$month].' '.$year;
    }

    public function getMonthArray()
    {
        return array('', $this->l('January'), $this->l('February'), $this->l('March'), $this->l('April'), $this->l('May'),
            $this->l('June'), $this->l('July'), $this->l('August'), $this->l('September'), $this->l('October'), $this->l('November'), $this->l('December'));
    }

    public function generateCode()
    {
        $length = 8;
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random_string = '';
        for ($i = 0; $i < $length; $i++)
            $random_string .= $characters[rand(0, Tools::strlen($characters) - 1)];

        return $random_string;
    }

    /**
    * Loads asset resources
    */
    public function loadSmartyData()
    {
        $controller_name = 'Admin'.get_class($this);
        $current_id_tab = (int)$this->context->controller->id;
        $controller_url = $this->context->link->getAdminLink($controller_name);
        $token = pSQL(Configuration::get('PUSHONCART_TOKEN'));

        $guide = $this->getGuideLink();
        $this->context->smarty->assign(array(
            'version' => $this->version,
            'requestUri' => pSQL($_SERVER['REQUEST_URI']),
            'module_name' => $this->name,
            'module_path' => $this->_path,
            'module_display' => $this->displayName,
            'pushoncart_token' => $token,
            'current_id_tab_poc' => $current_id_tab,
            'controller_url_poc' => $controller_url,
            'controller_name_poc' => $controller_name,
            'guide_link' => $guide['link'],
            'lang' => Tools::strtoupper($guide['lang']),
            'ps_version' => (bool)version_compare(_PS_VERSION_, '1.6', '>'),
        ));
    }

    public function loadAsset()
    {
        $css_compatibility = $js_compatibility = array();

        /* Load CSS */
        $css = array(
            $this->css_path.'bootstrap-select.min.css',
            $this->css_path.'bootstrap-dialog.min.css',
            $this->css_path.'bootstrap.vertical-tabs.min.css',
            $this->css_path.'DT_bootstrap.css',
            $this->css_path.'datepicker.css',
            $this->css_path.'config.css',
            $this->css_path.'sweet-alert.scss',
            $this->css_path.'fix.css',
        );
        if (version_compare(_PS_VERSION_, '1.6', '<'))
        {
            $css_compatibility = array(
                $this->css_path.'bootstrap.min.css',
                $this->css_path.'bootstrap.extend.css',
                $this->css_path.'bootstrap-responsive.min.css',
                $this->css_path.'font-awesome.min.css',
            );
            $css = array_merge($css_compatibility, $css);
        }
        $this->context->controller->addCSS($css, 'all');

        /* Load JS */
        $js = array(
            $this->js_path.'bootstrap-select.min.js',
            $this->js_path.'bootstrap-dialog.js',
            $this->js_path.'jquery.autosize.min.js',
            $this->js_path.'jquery.dataTables.js',
            $this->js_path.'DT_bootstrap.js',
            $this->js_path.'dynamic_table_init.js',
            $this->js_path.'bootstrap-datepicker.js',
            $this->js_path.'sweet-alert.js',
            $this->js_path.$this->name.'.js'
        );
        if (version_compare(_PS_VERSION_, '1.6', '<'))
        {
            $js_compatibility = array(
                $this->js_path.'bootstrap.min.js'
            );
            $js = array_merge($js_compatibility, $js);
        }

        $this->context->controller->addJS($js);

        /* Clean memory */
        unset($js, $css, $js_compatibility, $css_compatibility);

        if (version_compare(_PS_VERSION_, '1.6', '<'))
        {
            /* Clean the code use tpl file for html */
            $tab = '&tab_module='.$this->tab;
            $token_mod = '&token='.Tools::getAdminTokenLite('AdminModules');
            $token_pos = '&token='.Tools::getAdminTokenLite('AdminModulesPositions');
            $token_trad = '&token='.Tools::getAdminTokenLite('AdminTranslations');

            $this->context->smarty->assign(array(
                'module_active' => (bool)$this->active,
                'lang_select' => self::$lang_cache,
                'module_trad' => 'index.php?controller=AdminTranslations'.$token_trad.'&type=modules&lang=',
                'module_hook' => 'index.php?controller=AdminModulesPositions'.$token_pos.'&show_modules='.$this->id,
                'module_back' => 'index.php?controller=AdminModules'.$token_mod.$tab.'&module_name='.$this->name,
                'module_form' => 'index.php?controller=AdminModules&configure='.$this->name.$token_mod.$tab.'&module_name='.$this->name,
                'module_reset' => 'index.php?controller=AdminModules'.$token_mod.'&module_name='.$this->name.'&reset'.$tab,
            ));
            /* Clean memory */
            unset($tab, $token_mod, $token_pos, $token_trad);
        }
    }

    public function getGuideLink()
    {
        $guide = array();
        $lang = 'en';   /* Language for documentation in back-office */
        if ($this->context->language->iso_code == 'fr' || $this->context->language->iso_code == 'FR')
            $lang = 'fr';
        if ($this->context->language->iso_code == 'es' || $this->context->language->iso_code == 'ES')
            $lang = 'es';
        $guide['lang'] = $lang;
        $guide['link'] = 'docs/promo_panier_guide_'.$lang.'.pdf';
        return $guide;
    }

    private function getLang()
    {
        if (self::$lang_cache == null && !is_array(self::$lang_cache))
        {
            self::$lang_cache = array();
            if ($languages = Language::getLanguages())
            {
                foreach ($languages as $row)
                {
                        $exprow = explode(' (', $row['name']);
                        $subtitle = (isset($exprow[1]) ? trim(Tools::substr($exprow[1], 0, -1)) : '');
                        self::$lang_cache[$row['iso_code']] = array (
                                'title' => trim($exprow[0]),
                                'subtitle' => $subtitle
                        );
                }
                /* Clean memory */
                unset($row, $exprow, $subtitle, $languages);
            }
        }
    }
}
