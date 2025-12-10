<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-9999 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 * @version   of the vouchers engine: 5.5
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
include_once('../../../../config/config.inc.php');
include_once('../../../../init.php');
if (Tools::getValue('search', 'false') != 'false') {
    $result = searchproduct(Tools::getValue('search'));
    if (count($result) > 0) {
        foreach ($result as $key => $value) {
            echo '<p style="display:block; clear:both; padding:0px; padding-top:2px; margin:0px;">' . (string )$value['name'] . '<span style="display:inline-block; background:#FFF; cursor:pointer; border:1px solid black; padding:0px 3px;margin-left:5px;" onclick="$(\'#selectbox_' . (string )Tools::getValue('selectbox_prefix') . 'restriction_products_pr\').append(\'<option selected value=' . (int)$value['id_product'] . '>' . (string )$value['name'] . '</option>\');"> ></span></p>';
        }
    }
}
if (Tools::getValue('searchgift', 'false') != 'false') {
    $result = searchproduct(Tools::getValue('searchgift'));
    if (count($result) > 0) {
        foreach ($result as $key => $value) {
            echo '<p style="display:block; clear:both; padding:0px; padding-top:2px; margin:0px;">' . (string )$value['name'] . '<span style="display:inline-block; background:#FFF; cursor:pointer; border:1px solid black; padding:0px 3px;margin-left:5px;" onclick="loadattributes(' . (int)$value['id_product'] . '); $(\'.free_gift_search\').val(\'' . $value['name'] . '\'); $(\'#' . (string )Tools::getValue('selectbox_prefix') . '_fgp_id\').val(' . (int)$value['id_product'] . ')"> ></span></p>';
        }
    }
}
if (Tools::getValue('id_product', 'false') != 'false') {
    $id_product = Tools::getValue('id_product');
    $product = new Product($id_product, true, Configuration::get('PS_LANG_DEFAULT'));
    if ($product->hasAttributes() > 0) {
        $combination_images = $product->getCombinationImages(Configuration::get('PS_LANG_DEFAULT'));
        $combinations = array();
        $matrix_attributes = array();
        $fpget = $product->getAttributeCombinations(Configuration::get('PS_LANG_DEFAULT'));
        foreach ($fpget as $attr) {
            $combinations[$attr['id_product_attribute']]['combination'] = $attr;
            if (!isset($combinations[$attr['id_product_attribute']]['combination_name'])) {
                $combinations[$attr['id_product_attribute']]['combination_name'] = '';
            }
            $combinations[$attr['id_product_attribute']]['combination_name'] = $combinations[$attr['id_product_attribute']]['combination_name'] . $attr['group_name'] . ": " . $attr['attribute_name'] . ", ";
            if (isset($combination_images[$attr['id_product_attribute']]['0'])) {
                $combinations[$attr['id_product_attribute']]['image'] = $combination_images[$attr['id_product_attribute']]['0'];
            } else {
                $combinations[$attr['id_product_attribute']]['image'] = 0;
            }
            $gr = new AttributeGroupCore($attr['id_attribute_group']);
            $gr_atr = new Attribute($attr['id_attribute']);
            $combinations[$attr['id_product_attribute']]['attributes'][$gr->position]['name'] = $attr['attribute_name'];
            $combinations[$attr['id_product_attribute']]['attributes'][$gr->position]['type'] = $gr->group_type;
            $combinations[$attr['id_product_attribute']]['attributes'][$gr->position]['color'] = $gr_atr->color;
            $matrix_attributes[$gr->position][$attr['group_name']] = 1;
            ksort($combinations[$attr['id_product_attribute']]['attributes']);
            ksort($matrix_attributes);
        }
        foreach ($combinations as $value => $key) {
            echo '<p style="display:block; clear:both; padding:0px; padding-top:2px; margin:0px;">' . (string )$key['combination_name'] . '<span style="display:inline-block; background:#FFF; cursor:pointer; border:1px solid black; padding:0px 3px;margin-left:5px;" onclick="$(\'#' . Tools::getValue('selectbox_prefix') . '_fgc_id\').val(\'' . $value . '\');"> ></span></p>';
        }
    } else {
        return '0';
    }
}
function searchproduct($search)
{
    return Db::getInstance()->ExecuteS('SELECT a.`id_product`, a.`name` FROM `' . _DB_PREFIX_ . 'product_lang` a LEFT JOIN `' . _DB_PREFIX_ . 'product` b ON a.id_product = b.id_product WHERE (a.`name` like "%' . (string )$search . '%" OR b.`reference` like "%' . (string)$search . '%"  OR a.`id_product` = "' . (int)$search . '") AND a.id_lang="' . (string )Configuration::get('PS_LANG_DEFAULT') . Shop::addSqlRestrictionOnLang('a') . '" GROUP BY a.id_product LIMIT 50');
}