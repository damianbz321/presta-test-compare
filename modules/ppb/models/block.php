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
class PpbBlock extends ObjectModel
{
    public $id;
    public $type;
    public $active;
    public $block_position;
    public $position;
    public $value;
    public $shop;
    public $name;
    public $titleURL;
    public $internal_name;
    public $nb;
    public $list_products;
    public $custom_before;
    public $before;
    public $random;
    public $product;
    public $global_cat;
    public $categories;
    public $global_man;
    public $manufacturers;
    public $global_prod;
    public $g_products;
    public $g_products_from_method;
    public $global_features;
    public $g_features;
    public $carousell;
    public $carousell_nb;
    public $carousell_nb_mobile;
    public $carousell_nb_tablet;
    public $carousell_auto;
    public $carousell_pager;
    public $carousell_controls;
    public $carousell_loop;
    public $searchphrase;
    public $everywhere;
    public $instock;
    public $hidewc;
    public $stockcheck;
    public $cat_price_from;
    public $cat_price_to;
    public $cat_price_from_v;
    public $cat_price_to_v;
    public $cat_feature;
    public $cat_feature_v;
    public $type_feature_v;
    public $cat_stock_filter;
    public $cat_manuf;
    public $cat_manuf_v;
    public $allconditions;
    public $custom_path;
    public $path;
    public $exc_p;
    public $exc_p_list;

    public static $definition = array(
        'table' => 'ppbp_block',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'id' => array('type' => ObjectModel::TYPE_INT),
            'type' => array(
                'type' => ObjectModel::TYPE_INT,
                'required' => true
            ),
            'block_position' => array('type' => ObjectModel::TYPE_INT),
            'active' => array('type' => ObjectModel::TYPE_BOOL),
            'random' => array('type' => ObjectModel::TYPE_INT),
            'position' => array('type' => ObjectModel::TYPE_INT),
            'shop' => array('type' => ObjectModel::TYPE_INT),
            'product' => array('type' => ObjectModel::TYPE_INT),
            'value' => array('type' => ObjectModel::TYPE_STRING,),
            'name' => array('type' => ObjectModel::TYPE_STRING, 'lang' => true, 'size' => 254),
            'titleURL' => array('type' => ObjectModel::TYPE_STRING, 'lang' => true, 'size' => 254),
            'internal_name' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 254
            ),
            'nb' => array('type' => ObjectModel::TYPE_INT),
            'list_products' => array('type' => ObjectModel::TYPE_STRING),
            'before' => array('type' => ObjectModel::TYPE_BOOL),
            'custom_before' => array(
                'type' => ObjectModel::TYPE_HTML,
                'lang' => true,
            ),
            'global_cat' => array('type' => ObjectModel::TYPE_INT),
            'categories' => array('type' => ObjectModel::TYPE_STRING),
            'global_man' => array('type' => ObjectModel::TYPE_INT),
            'manufacturers' => array('type' => ObjectModel::TYPE_STRING),
            'global_prod' => array('type' => ObjectModel::TYPE_INT),
            'g_products' => array('type' => ObjectModel::TYPE_STRING),
            'g_products_from_method' => array('type' => ObjectModel::TYPE_INT),
            'global_features' => array('type' => ObjectModel::TYPE_INT),
            'g_features' => array('type' => ObjectModel::TYPE_STRING),
            'carousell' => array('type' => ObjectModel::TYPE_BOOL),
            'carousell_nb' => array('type' => ObjectModel::TYPE_INT),
            'carousell_nb_mobile' => array('type' => ObjectModel::TYPE_INT),
            'carousell_nb_tablet' => array('type' => ObjectModel::TYPE_INT),
            'carousell_auto' => array('type' => ObjectModel::TYPE_BOOL),
            'carousell_loop' => array('type' => ObjectModel::TYPE_INT),
            'carousell_pager' => array('type' => ObjectModel::TYPE_INT),
            'carousell_controls' => array('type' => ObjectModel::TYPE_INT),
            'searchphrase' => array('type' => ObjectModel::TYPE_STRING),
            'everywhere' => array('type' => ObjectModel::TYPE_INT),
            'instock' => array('type' => ObjectModel::TYPE_INT),
            'stockcheck' => array('type' => ObjectModel::TYPE_INT),
            'hidewc' => array('type' => ObjectModel::TYPE_STRING),

            'cat_manuf' => array('type' => ObjectModel::TYPE_INT),
            'cat_manuf_v' => array('type' => ObjectModel::TYPE_INT),
            'cat_price_to' => array('type' => ObjectModel::TYPE_INT),
            'cat_price_from' => array('type' => ObjectModel::TYPE_INT),
            'cat_price_to_v' => array('type' => ObjectModel::TYPE_STRING),
            'cat_price_from_v' => array('type' => ObjectModel::TYPE_STRING),
            'cat_feature' => array('type' => ObjectModel::TYPE_INT),
            'cat_feature_v' => array('type' => ObjectModel::TYPE_STRING),
            'type_feature_v' => array('type' => ObjectModel::TYPE_STRING),
            'cat_stock_filter' => array('type' => ObjectModel::TYPE_INT),
            'allconditions' => array('type' => ObjectModel::TYPE_INT),
            'custom_path' => array('type' => ObjectModel::TYPE_INT),
            'path' => array('type' => ObjectModel::TYPE_STRING),
            'exc_p' => array('type' => ObjectModel::TYPE_INT),
            'exc_p_list' => array('type' => ObjectModel::TYPE_STRING),
        ),
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public static function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp = explode('.', $version);
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

    public static function getAllBlocks()
    {
        $context = new Context();
        $context = $context->getContext();
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') == 1 && Configuration::get('ppb_sharemulti') != 1) {
            $whereshop = 'WHERE shop="' . $context->shop->id . '"';
        } else {
            $whereshop = '';
        }
        $id_lang = context::getContext()->cookie->id_lang;
        $query = Db::getInstance()->ExecuteS('SELECT id FROM `' . _DB_PREFIX_ . 'ppbp_block` ' . (string)$whereshop . ' ORDER BY `position` ');
        $blocks = array();
        foreach ($query as $key) {
            $blocks[$key['id']] = new PpbBlock($key['id']);
            $blocks[$key['id']]->name = $blocks[$key['id']]->name["$id_lang"];
            $blocks[$key['id']]->custom_before = $blocks[$key['id']]->custom_before["$id_lang"];
        }

        return $blocks;
    }

    public static function getAllBlocksByProduct($id = null)
    {
        $context = new Context();
        $context = $context->getContext();
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') == 1 && Configuration::get('ppb_sharemulti') != 1) {
            $whereshop = 'AND shop="' . $context->shop->id . '"';
        } else {
            $whereshop = '';
        }
        $id_lang = context::getContext()->cookie->id_lang;
        $query = Db::getInstance()->ExecuteS('SELECT id FROM `' . _DB_PREFIX_ . 'ppbp_block` WHERE (' . (Tools::getValue('showall') == 1 ? '1=1 OR' : '') . ' product ="' . (int)$id . '" OR global_cat=1 OR global_man=1 OR global_prod=1 OR global_features=1 OR everywhere=1) ' . (string)$whereshop . ' ORDER BY `position` ');
        $blocks = array();

        if ($query) {
            foreach ($query as $key) {
                $blocks[$key['id']] = new PpbBlock($key['id']);
                $blocks[$key['id']]->name = $blocks[$key['id']]->name["$id_lang"];
                $blocks[$key['id']]->custom_before = $blocks[$key['id']]->custom_before["$id_lang"];
            }
        }

        return $blocks;
    }

    public static function getAllBlocksByPosition($position, $product_details = false)
    {
        if (isset($product_details['product']->id)) {
            $id_product_cart = $product_details['product']->id;
        }

        $context = new Context;
        $context = $context->getContext();
        $whereshop = '';
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') == 1 && Configuration::get('ppb_sharemulti') != 1) {
            $whereshop = 'AND shop="' . $context->shop->id . '"';
        }
        $id_lang = context::getContext()->cookie->id_lang;
        $query = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'ppbp_block` WHERE block_position=' . (string)$position . ' AND active=1 AND (product ="' . (isset($id_product_cart) ? $id_product_cart : (int)Tools::getValue('id_product')) . '" OR global_cat=1 OR global_man=1 OR global_prod=1 OR global_features=1 OR everywhere=1) ' . (string)$whereshop . ' ORDER BY position');
        $blocks = array();
        foreach ($query as $key) {
            $at_least_one_active_condition = 0;
            $dont_return_stock = 0;
            $dont_return_cat = 0;
            $dont_return_man = 0;
            $dont_return_prod = 0;
            $dont_return_global_prod = 0;
            $dont_return_global_features = 0;

            if ($key['global_features'] == 1) {
                $is_associated_with_feature = 0;
                $block_features = explode(',', $key['g_features']);
                if (is_array($product_details['features']) && is_array($block_features)) {
                    foreach ($product_details['features'] AS $feature) {
                        if (in_array($feature['id_feature_value'], $block_features)) {
                            $is_associated_with_feature = 1;
                        }
                    }
                }
                if ($is_associated_with_feature == 0) {
                    $dont_return_global_features = 1;
                } else {
                    $at_least_one_active_condition = 1;
                }
            }

            if ($key['global_prod'] == 1) {
                if ($key['g_products_from_method'] == 1) {
                    $explode_products = explode(',', trim($key['list_products']));
                } else {
                    $explode_products = explode(',', trim($key['g_products']));
                }
                $array_this_product = array();
                $array_this_product[] = (isset($id_product_cart) ? $id_product_cart : (int)Tools::getValue('id_product'));
                if (count(array_intersect($explode_products,$array_this_product)) <= 0) {
                    if ($product_details['product']->id != $key['product']) {
                        $dont_return_global_prod = 1;
                    }
                } else {
                    $at_least_one_active_condition = 1;
                }
            }

            if ($key['stockcheck'] != 0) {
                if (isset($product_details['product'])) {
                    if (isset($product_details['product']->id)) {
                        $qty = $product_details['product']->getQuantity($product_details['product']->id);
                        if ($key['stockcheck'] == 1) {
                            if ($qty <= 0) {
                                $dont_return_stock = 1;
                            } else {
                                $at_least_one_active_condition = 1;
                            }
                        } elseif ($key['stockcheck'] == 2) {
                            if ($qty > 0) {
                                $dont_return_stock = 1;
                            } else {
                                $at_least_one_active_condition = 1;
                            }
                        }
                    }
                }
            }

            if (isset($product_details['categories'])) {
                if ($key['global_cat'] == 1) {
                    $array_db = explode(',', $key['categories']);
                    $result = array_intersect($array_db, $product_details['categories']);
                    if (empty($result)) {
                        $dont_return_cat = 1;
                    } else {
                        $at_least_one_active_condition = 1;
                    }
                }
            }

            if (isset($product_details['product'])) {
                if ($key['global_man'] == 1) {
                    $array_db = explode(',', $key['manufacturers']);
                    $result = array_intersect($array_db, array($product_details['product']->id_manufacturer));
                    if (empty($result)) {
                        $dont_return_man = 1;
                    } else {
                        $at_least_one_active_condition = 1;
                    }
                }
            }

            if ($product_details['product']->id != $key['product'] && $key['global_cat'] == 0 && $key['global_man'] == 0 && $key['global_prod'] == 0 && $key['global_features'] == 0 && $key['everywhere'] == 0) {
                $dont_return_prod = 1;
            }

            if ($key['everywhere'] == 1) {
                $blocks[$key['id']] = new PpbBlock($key['id']);
                $blocks[$key['id']]->name = $blocks[$key['id']]->name["$id_lang"];
                $blocks[$key['id']]->titleURL = $blocks[$key['id']]->titleURL["$id_lang"];
                $blocks[$key['id']]->custom_before = $blocks[$key['id']]->custom_before["$id_lang"];
            } elseif ($product_details['product']->id == $key['product'] && $key['hidewc'] == 0) {
                $blocks[$key['id']] = new PpbBlock($key['id']);
                $blocks[$key['id']]->name = $blocks[$key['id']]->name["$id_lang"];
                $blocks[$key['id']]->titleURL = $blocks[$key['id']]->titleURL["$id_lang"];
                $blocks[$key['id']]->custom_before = $blocks[$key['id']]->custom_before["$id_lang"];
            } elseif ($key['allconditions'] == 1) {
                if ($dont_return_cat != 1 && $dont_return_prod != 1 && $dont_return_man != 1 && $dont_return_stock != 1 && $dont_return_global_prod != 1 && $dont_return_global_features != 1) {
                    $blocks[$key['id']] = new PpbBlock($key['id']);
                    $blocks[$key['id']]->name = $blocks[$key['id']]->name["$id_lang"];
                    $blocks[$key['id']]->titleURL = $blocks[$key['id']]->titleURL["$id_lang"];
                    $blocks[$key['id']]->custom_before = $blocks[$key['id']]->custom_before["$id_lang"];
                }
            } elseif ($key['allconditions'] == 0) {
                if ($at_least_one_active_condition == 1 || ($product_details['product']->id == $key['product'] && $key['product'] == 0)) {
                    $blocks[$key['id']] = new PpbBlock($key['id']);
                    $blocks[$key['id']]->name = $blocks[$key['id']]->name["$id_lang"];
                    $blocks[$key['id']]->titleURL = $blocks[$key['id']]->titleURL["$id_lang"];
                    $blocks[$key['id']]->custom_before = $blocks[$key['id']]->custom_before["$id_lang"];
                }
            }
        }
        return $blocks;
    }

    //FOR CMSPRODUCTS PRO PURPOSES
    public static function getAllBlocksById($position)
    {
        $context = new Context;
        $context = $context->getContext();
        $whereshop = '';
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') == 1) {
            $whereshop = 'AND shop="' . $context->shop->id . '"';
        }
        $id_lang = context::getContext()->cookie->id_lang;
        $query = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'ppbp_block` WHERE id=' . $position . ' AND active=1');
        $blocks = array();
        foreach ($query as $key) {
            $blocks[$key['id']] = new PpbBlock($key['id']);
            $blocks[$key['id']]->name = $blocks[$key['id']]->name["$id_lang"];
            $blocks[$key['id']]->custom_before = $blocks[$key['id']]->custom_before["$id_lang"];
        }

        return $blocks;
    }
}