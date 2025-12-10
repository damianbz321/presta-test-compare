<?php
/**
* 2012-2015 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Facebook Pixel Tracking © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2015 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD Facebook Pixel Tracking PrestaShop 1.5.x and 1.6.x Module
* @version   1.1.1
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      24-05-2016
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class PdFacebookPixelTracking extends Module
{
    private $html = '';
    private $post_errors = array();
    private $id_tracking;

    private $product_ids_type;
    private $product_ids_prefix;

    public function __construct()
    {
        $this->name = 'pdfacebookpixeltracking';
        $this->tab = 'analytics_stats';
        $this->version = '1.2.5';
        $this->author = 'PrestaDev.pl';
        $this->bootstrap = true;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->module_key = 'd5dbd7158735481bda6a082613f3cec0';

        $this->secure_key = Tools::encrypt($this->name);

        parent::__construct();

        $this->displayName = $this->l('PD Facebook Pixel Tracking');
        $this->description = $this->l('Adds Facebook pixel tracking and integrate with standard events');
        $this->confirmUninstall = $this->l('Are you sure you want uninstall module and delete your details ?');

        $this->id_tracking = Configuration::get('PD_FPT_TRACKING_ID');
        $this->product_ids_type = Configuration::get('PD_FPT_PRODUCT_ID_TYPE');
        $this->product_ids_prefix = Configuration::get('PD_FPT_PRODUCT_ID_PREFIX');

    }

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('displayOrderConfirmation') ||
            !$this->registerHook('header') ||
            !$this->registerHook('displayPaymentTop') ||
            !$this->registerHook('footer')) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!Configuration::deleteByName('PD_FPT_TRACKING_ID')
            || !Configuration::updateValue('PD_FPT_PRODUCT_ID_TYPE', 0)
            || !Configuration::updateValue('PD_FPT_PRODUCT_ID_PREFIX', '')
            || !parent::uninstall()) {
            return false;
        }
        return true;
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $this->postValidation();
            if (!count($this->post_errors)) {
                $this->postProcess();
            } else {
                foreach ($this->post_errors as $err) {
                    $this->html .= $this->displayError($err);
                }
            }
        }
        $this->html .= '<h2>'.$this->displayName.' (v'.$this->version.')</h2><p>'.$this->description.'</p>';
        $this->context->smarty->assign('module_dir', $this->_path);
        $this->html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        $this->html .= $this->renderForm();
        $this->html .= '<br />';

        return $this->html;
    }

    private function postValidation()
    {
        if (Tools::isSubmit('btnSubmit')) {
            if (!Tools::getValue('PD_FPT_TRACKING_ID')) {
                $this->post_errors[] = $this->l('You must enter tracking Ids.');
            }
        }
    }

    private function postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue('PD_FPT_TRACKING_ID', Tools::getValue('PD_FPT_TRACKING_ID'));
            Configuration::updateValue('PD_FPT_PRODUCT_ID_TYPE', Tools::getValue('PD_FPT_PRODUCT_ID_TYPE'));
            Configuration::updateValue('PD_FPT_PRODUCT_ID_PREFIX', Tools::getValue('PD_FPT_PRODUCT_ID_PREFIX'));
        }

        $this->html .= $this->displayConfirmation($this->l('Settings updated'));
    }

    public function renderForm()
    {
        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Module configuration'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Facebook Pixel Tracking ID'),
                        'name' => 'PD_FPT_TRACKING_ID',
                        'desc' => $this->l('Here you have to put your Facebook\'s Pixel identifier, you can get it anytime from your').' <a href="https://www.facebook.com/ads/manager/pixel/facebook_pixel/" title="Facebook ads Manager" target="_blank">'.$this->l('Facebook ads Manager').'</a>',
                        'required' => true
                    ),
                      array(
                        'type' => 'select',
                        'label' => $this->l('Product ids source'),
                        'name' => 'PD_FPT_PRODUCT_ID_TYPE',
                        'width' => 300,
                        'class' => 'fixed-width-ld',
                        'desc' => $this->l('You can choose passed to Facebook products id format to match Your feed products ids'),
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => '0',
                                    'name' => $this->l('Id product (default)')
                                ),
                                array(
                                    'id' => '1',
                                    'name' => $this->l('Id product - id product attribute')
                                ),
                                array(
                                    'id' => '2',
                                    'name' => $this->l('Id product _ id product attribute')
                                )
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Product id prefix'),
                        'desc' => $this->l('If you add prefix to your products id\'s in generated feed then you can as well add this prefix here to match same id values'),
                        'name' => 'PD_FPT_PRODUCT_ID_PREFIX',
                        'size' => 42,
                        'required' => false
                    ),
                ),
                'submit' => array(
                    'name' => 'btnSubmit',
                    'title' => $this->l('Save settings'),
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
        $admin_link = $this->context->link->getAdminLink('AdminModules', false);
        $helper->currentIndex = $admin_link.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form_1));
    }

    public function getConfigFieldsValues()
    {
        $return = array();
        $return['PD_FPT_TRACKING_ID'] = Tools::getValue('PD_FPT_TRACKING_ID', Configuration::get('PD_FPT_TRACKING_ID'));
        $return['PD_FPT_PRODUCT_ID_TYPE'] = Tools::getValue('PD_FPT_PRODUCT_ID_TYPE', Configuration::get('PD_FPT_PRODUCT_ID_TYPE'));
        $return['PD_FPT_PRODUCT_ID_PREFIX'] = Tools::getValue('PD_FPT_PRODUCT_ID_PREFIX', Configuration::get('PD_FPT_PRODUCT_ID_PREFIX'));

        return $return;
    }

    public static function getControlerName()
    {
        $page_name = 'index';
        if (!empty(Context::getContext()->controller->php_self)) {
            $page_name = (string)Context::getContext()->controller->php_self;
        } else {
            $page_name = (string)Tools::getValue('controller');
        }
        return $page_name;
    }



    public function hookDisplayHeader($params)
    {
        Media::addJsDef(array(
            'pd_fpt_product_ids_type' => $this->product_ids_type,
            'pd_fpt_product_ids_prefix' => $this->product_ids_prefix,
        ));

        Media::addJsDef(array(
            'pdfacebookpixeltracking_secure_key' => $this->secure_key,
            'pdfacebookpixeltracking_ajax_link' => $this->context->link->getModuleLink('pdfacebookpixeltracking', 'ajax', array()),
        ));

        $this->context->controller->registerJavascript('modules-pdfacebookpixeltracking-front', 'modules/'.$this->name.'/views/js/scripts_17.js', array('position' => 'bottom', 'priority' => 1));

        $id_currency = (int)$this->context->currency->id;
        $currency = new Currency($id_currency);
        $currency_iso = $currency->iso_code;

        if (isset($this->id_tracking) && $this->id_tracking != '') {
            $this->smarty->assign(array(
                'id_tracking' => $this->id_tracking,
                'currency' => $currency_iso,
            ));

            return $this->display(__FILE__, 'header.tpl');
        }
    }

    public function getCategoryPath($id_category)
    {
        $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ? (int)$this->context->shop->id : false;
        $id_lang = $this->context->language->id;

        $category = new Category((int)$id_category, $id_lang, $id_shop);
        $parent = new Category($category->id_parent, $id_lang, $id_shop);
        while (Validate::isLoadedObject($category) && Validate::isLoadedObject($parent) && $category->id_parent > 1 && $category->id_parent != Category::getRootCategory()->id) {
            return $this->getCategoryPath($category->id_parent).' > '.$category->name;
        }
        return $category->name;
    }


    private function getProductsIdsStringByType($products)
    {
        $ids_type = $this->product_ids_type;
        $ids_prefix = '';
        $ids_prefix = $this->product_ids_prefix;
        $ecomm_prodid = '[';


        if (count($products)) {
            foreach ($products as $p) {
                $id_product = (int)$p['id_product'];
                $id_product_attribute = '';

                if (isset($p['product_attribute_id'])) {
                    $id_product_attribute = (int)$p['product_attribute_id'];
                } elseif (isset($p['id_product_attribute'])) {
                    $id_product_attribute = (int)$p['id_product_attribute'];
                }
                $ean13 = isset($p['ean13']) ? (string)$p['ean13'] : '';
                $reference = isset($p['reference']) ? (string)$p['reference'] : '';

                switch ($ids_type) {
                    case '0':
                        $ecomm_prodid .= "'".$ids_prefix.$id_product."', ";
                        break;
                    case '1':
                        if ($id_product_attribute) {
                            $ecomm_prodid .= "'".$ids_prefix.$id_product."-".$id_product_attribute."', ";
                        } else {
                            $ecomm_prodid .= "'".$ids_prefix.$id_product."', ";
                        }
                        break;
                    case '2':
                        if ($id_product_attribute) {
                            $ecomm_prodid .= "'".$ids_prefix.$id_product."_".$id_product_attribute."', ";
                        } else {
                            $ecomm_prodid .= "'".$ids_prefix.$id_product."', ";
                        }
                        break;
                    default:
                        $ecomm_prodid .= "'".$ids_prefix.$id_product."', ";
                        break;
                }
            }

            $ecomm_prodid = Tools::substr($ecomm_prodid, 0, -2).']';
            return $ecomm_prodid;
        } else {
            return '[]';
        }
    }

    public function getProductIdStringByType($product, $id_product_attribute = false)
    {
        $ids_type = $this->product_ids_type;
        $ids_prefix = '';
        $ids_prefix = $this->product_ids_prefix;

        if ($product instanceof Product) {
            $id_product = (int)$product->id;
            $ean13 = (string)$product->ean13;
            $reference = (string)$product->reference;

            $ecomm_prodid = '';

            switch ($ids_type) {
                case '0':
                    $ecomm_prodid = $ids_prefix.$id_product;
                    break;
                case '1':
                    if ($id_product_attribute) {
                        $ecomm_prodid = $ids_prefix.$id_product.'-'.$id_product_attribute;
                    } else {
                        if ($product->cache_default_attribute != 0) {
                            $ecomm_prodid = $ids_prefix.$id_product.'-'.$product->cache_default_attribute;
                        } else {
                            $ecomm_prodid = $ids_prefix.$id_product;
                        }
                    }
                    break;
                case '2':
                    if ($id_product_attribute) {
                        $ecomm_prodid = $ids_prefix.$id_product.'_'.$id_product_attribute;
                    } else {
                        if ($product->cache_default_attribute != 0) {
                            $ecomm_prodid = $ids_prefix.$id_product.'_'.$product->cache_default_attribute;
                        } else {
                            $ecomm_prodid = $ids_prefix.$id_product;
                        }
                    }
                    break;
                    
                default:
                    $ecomm_prodid = $ids_prefix.$id_product;
                    break;
            }
            return $ecomm_prodid;
        } else {
            return "";
        }
    }

    public function hookDisplayPaymentTop($params)
    {
    	// dump('run');
     //    die();

        $id_currency = (int)$this->context->currency->id;
        $currency = new Currency($id_currency);
        $currency_iso = $currency->iso_code;
        $id_lang = (int)$this->context->language->id;

        $cart = $params['cart'];
        if (!($cart instanceof Cart)) {
            return;
        }

        if (isset($cart->id)) {
            $value = $cart->getOrderTotal(true);
            $cart_products = $cart->getProducts();

            $this->smarty->assign(array(
                'content_ids' => $this->getProductsIdsStringByType($cart_products),
                'num_items' => count($cart_products),
                'value' => Tools::ps_round($value, 2),
                'currency' => $currency_iso,
                'content_type' => 'product',
                'tagType' => 'payment',
            ));
        }
                    
        return $this->display(__FILE__, 'displayPaymentTop.tpl');
    }


    public function hookFooter($params)
    {
        $cn = self::getControlerName();
        $id_currency = (int)$this->context->currency->id;
        $currency = new Currency($id_currency);
        $currency_iso = $currency->iso_code;
        $id_lang = (int)$this->context->language->id;

        if (isset($this->context->cookie->account_created)) {
            $this->context->smarty->assign('account_created', 1);
            $this->context->smarty->assign('registration_content_name', Tools::replaceAccentedChars(Configuration::get('PS_SHOP_NAME')));
        }

        switch ($cn) {

            case 'product':

                $product = new Product((int)Tools::getValue('id_product'), false, $id_lang);
                $price = Product::getPriceStatic($product->id);

                $id_product_attribute = Tools::getValue('id_product_attribute');

                //dump($id_product_attribute);

                $this->smarty->assign(array(
                    'product_ids_type' => $this->product_ids_type,
                    'content_ids' =>  $this->getProductIdStringByType($product, $id_product_attribute),
                    'content_name' =>  Tools::replaceAccentedChars($product->name),
                    'content_category' => Tools::replaceAccentedChars($this->getCategoryPath($product->id_category_default)),
                    'content_type' => 'product',
                    'value' => Tools::ps_round($price, 2),
                    'currency' => $currency_iso,
                    'tagType' => 'product',
                ));

                return $this->display(__FILE__, 'footer.tpl');
            
            case 'cart':
                
                $cart = $params['cart'];
                if (!($cart instanceof Cart)) {
                    return;
                }

                // display only if is something in cart
                if (isset($cart->id)) {
                    $value = $cart->getOrderTotal(true);
                    $cart_products = $cart->getProducts();

                    $this->smarty->assign(array(
                        'content_ids' => $this->getProductsIdsStringByType($cart_products),
                        'num_items' => count($cart_products),
                        'value' => Tools::ps_round($value, 2),
                        'currency' => $currency_iso,
                        'content_type' => 'product',
                        'tagType' => 'cart',
                    ));
                    return $this->display(__FILE__, 'footer.tpl');
                }
                break;

            case 'order':
                
                $cart = $params['cart'];
                if (!($cart instanceof Cart)) {
                    return;
                }

                // display only if is something in cart
                if (isset($cart->id)) {
                    $value = $cart->getOrderTotal(true);
                    $cart_products = $cart->getProducts();

                    $this->smarty->assign(array(
                        'content_ids' => $this->getProductsIdsStringByType($cart_products),
                        'num_items' => count($cart_products),
                        'value' => Tools::ps_round($value, 2),
                        'currency' => $currency_iso,
                        'content_type' => 'product',
                        'tagType' => 'cart',
                    ));
                    return $this->display(__FILE__, 'footer.tpl');
                }
                break;

            case 'order-opc':
                
                $cart = $params['cart'];
                if (!($cart instanceof Cart)) {
                    return;
                }

                // display only if is something in cart
                if (isset($cart->id)) {
                    $value = $cart->getOrderTotal(true);
                    $cart_products = $cart->getProducts();

                    $this->smarty->assign(array(
                        'content_ids' => $this->getProductsIdsStringByType($cart_products),
                        'num_items' => count($cart_products),
                        'value' => Tools::ps_round($value, 2),
                        'currency' => $currency_iso,
                        'content_type' => 'product',
                        'tagType' => 'cart',
                    ));
                    return $this->display(__FILE__, 'footer.tpl');
                }
                break;

            case 'onepagecheckout':
                
                $cart = $params['cart'];
                if (!($cart instanceof Cart)) {
                    return;
                }

                // display only if is something in cart
                if (isset($cart->id)) {
                    $value = $cart->getOrderTotal(true);
                    $cart_products = $cart->getProducts();

                    $this->smarty->assign(array(
                        'content_ids' => $this->getProductsIdsStringByType($cart_products),
                        'num_items' => count($cart_products),
                        'value' => Tools::ps_round($value, 2),
                        'currency' => $currency_iso,
                        'content_type' => 'product',
                        'tagType' => 'cart',
                    ));
                    return $this->display(__FILE__, 'footer.tpl');
                }
                break;

                case 'thecheckout':
                
                $cart = $params['cart'];
                if (!($cart instanceof Cart)) {
                    return;
                }

                // display only if is something in cart
                if (isset($cart->id)) {
                    $value = $cart->getOrderTotal(true);
                    $cart_products = $cart->getProducts();

                    $this->smarty->assign(array(
                        'content_ids' => $this->getProductsIdsStringByType($cart_products),
                        'num_items' => count($cart_products),
                        'value' => Tools::ps_round($value, 2),
                        'currency' => $currency_iso,
                        'content_type' => 'product',
                        'tagType' => 'cart',
                    ));
                    return $this->display(__FILE__, 'footer.tpl');
                }
                break;

            case 'supercheckout':
                
                $cart = $params['cart'];
                if (!($cart instanceof Cart)) {
                    return;
                }

                // display only if is something in cart
                if (isset($cart->id)) {
                    $value = $cart->getOrderTotal(true);
                    $cart_products = $cart->getProducts();

                    $this->smarty->assign(array(
                        'content_ids' => $this->getProductsIdsStringByType($cart_products),
                        'num_items' => count($cart_products),
                        'value' => Tools::ps_round($value, 2),
                        'currency' => $currency_iso,
                        'content_type' => 'product',
                        'tagType' => 'cart',
                    ));
                    return $this->display(__FILE__, 'footer.tpl');
                }
                break;


            case 'payment':
                
                $cart = $params['cart'];
                if (!($cart instanceof Cart)) {
                    return;
                }

                // display only if is something in cart
                if (isset($cart->id)) {
                    $value = $cart->getOrderTotal(true);
                    $cart_products = $cart->getProducts();

                    $this->smarty->assign(array(
                        'content_ids' => $this->getProductsIdsStringByType($cart_products),
                        'num_items' => count($cart_products),
                        'value' => Tools::ps_round($value, 2),
                        'currency' => $currency_iso,
                        'content_type' => 'product',
                        'tagType' => 'payment',
                    ));
                    return $this->display(__FILE__, 'footer.tpl');
                }
                break;

            case 'category':
                
                $id_category = (int)Tools::getValue('id_category');
                $category = new Category($id_category);

                if (!($category instanceof Category)) {
                    return;
                }

                $category_products = $this->getCategoryProductsIds($id_category);
                //dump($category_products);
                if (isset($category->id)) {
                    $this->smarty->assign(array(
                        'product_ids_type' => $this->product_ids_type,
                        'content_category' => $this->getCategoryPath($category->id),
                        'content_ids' => $this->getProductsIdsStringByType($category_products),
                        'content_name' => Tools::replaceAccentedChars($category->name[$id_lang]),
                        'content_type' => 'product',
                        'tagType' => 'category',
                    ));
                    return $this->display(__FILE__, 'footer.tpl');
                }
                break;
            
            case 'search':
                
                $search_string = Tools::getValue('s');
                if (!empty($search_string)) {
                    $results = Search::find($id_lang, $search_string, 1, 9999);
                    if (count($results)) {
                        if (isset($results['result'])) {
                            $content_ids_search = $this->getProductsIdsStringByType($results['result']);
                        }
                    }
                    $this->smarty->assign(array(
                        'content_type' => 'product',
                        'tagType' => 'search',
                        'currency' => $currency_iso,
                        'search_string' => pSQL($search_string),
                        'content_ids_search' => $content_ids_search,
                    ));
                    return $this->display(__FILE__, 'footer.tpl');
                }

                break;

            case 'cms':
             
                $cms = new Cms((int)Tools::getValue('id_cms'), $id_lang);

                if ($cms instanceof CMS) {
                    $cms_category = new CmsCategory($cms->id_cms_category);

                    if ($cms_category instanceof CmsCategory) {
                        $this->smarty->assign(array(
                            'tagType' => 'cms',
                            'content_name' => Tools::replaceAccentedChars($cms->meta_title),
                            'content_category' => Tools::replaceAccentedChars($cms_category->name[$id_lang]),
                        ));
                        return $this->display(__FILE__, 'footer.tpl');
                    }
                }

                break;

            default:
                break;
        }
    }


    public function getCategoryProductsIds($id_category)
    {
        $id_shop = (int)$this->context->shop->id;
        $sql = 'SELECT p.`id_product`, p.`cache_default_attribute` as id_product_attribute
                FROM `'._DB_PREFIX_.'category_product` cp
                LEFT JOIN `'._DB_PREFIX_.'product` p
                    ON p.`id_product` = cp.`id_product`
                '.Shop::addSqlAssociation('product', 'p').'
                WHERE product_shop.`id_shop` = '.(int)$id_shop.'
                AND cp.`id_category` = '.(int)$id_category.'
                AND product_shop.`active` = 1
                LIMIT 10';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);
    }


    public function hookDisplayOrderConfirmation($params)
    {
        if (isset($this->id_tracking) && $this->id_tracking != '') {
            if (isset($params['objOrder'])) {
                $order = $params['objOrder'];
            } elseif (isset($params['order'])) {
                $order = $params['order'];
            } elseif ($id_order = Tools::getValue('id_order')) {
                if (is_numeric($id_order)) {
                    $order = new Order($id_order);
                }
            } elseif ($id_order = Tools::getValue('id') && is_numeric($id_order)) {
                $id_order = strstr($id_order, '-', true);
                $order = new Order($id_order);
            } elseif ($id_cart = Tools::getValue('id_cart') && is_numeric($id_cart)) {
                $id_order = Order::getOrderByCartId($id_cart);
                $order = new Order($id_order);
            }

            if (!(Validate::isLoadedObject($order))) {
                return;
            }

            $order_value = $order->total_paid;
            $id_currency = $order->id_currency;
            $currency = new Currency($id_currency);
            $currency_iso = $currency->iso_code;

            $value = $order->total_paid;
            $order_products = $order->getProducts();
            $order_products_count = count($order_products);

            $product_name = '';
            $product_name_id_category_default = '';
            if ($order_products_count == 1) {
                foreach ($order_products as $op) {
                    $product_name = $op['product_name'];
                    $product_name_id_category_default = $op['id_category_default'];
                }
            }

            $this->smarty->assign(array(
                'content_name' =>  $product_name,
                'content_category' => $this->getCategoryPath($product_name_id_category_default),
                'content_ids' => $this->getProductsIdsStringByType($order_products),
                'value' => Tools::ps_round($order_value, 2),
                'currency' => $currency_iso,
                'content_type' => 'product',
                'products_multiple' => ($order_products_count > 1) ? 1 : 0,
            ));

            return $this->display(__FILE__, 'order-confirmation.tpl');
        }
    }
}
