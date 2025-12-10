<?php
require_once('../../config/config.inc.php');
require_once('../../init.php');
require_once(dirname(__FILE__).'/labsearch.php');

$labsearch = new Labsearch();
$result_products = array();
$products = array();
$labsearch_key = Tools::getValue('search_key');
$context = Context::getContext();
$count = false;
$product_link = $context->link;

if (Tools::strlen($labsearch_key) >= 3) {
    $products = Product::searchByName($context->language->id, $labsearch_key);
    $total_products = count($products);
    if ($total_products) {
        for ($i = 0; $i < $total_products; $i++) {
            if (($products[$i]['name']) && ($products[$i]['active'])) {
                $images = Image::getImages($context->language->id, $products[$i]['id_product']);
                $product = new Product($products[$i]['id_product']);
                $products[$i]['link'] = $product_link->getProductLink($products[$i]['id_product'], $product->link_rewrite[1], $product->id_category_default, $product->ean13);
                $products[$i]['link_rewrite'] = $product->link_rewrite[1];
                $products[$i]['id_image'] = $images[0]['id_image'];
                $products[$i]['price'] = Tools::displayPrice(Tools::convertPrice($products[$i]['price_tax_incl'], $context->currency), $context->currency);
                if ($count < Configuration::get('LAB_SEARCH_COUNT')) {
                    $result_products[] = $products[$i];
                    $count ++;
                } else {
                    break;
                }
            }
        }
    }

    $context->smarty->assign(array(
        'search_count' => Configuration::get('LAB_SEARCH_COUNT'),
        'enable_image' => Configuration::get('LAB_SEARCH_IMAGE'),
        'enable_price' => Configuration::get('LAB_SEARCH_PRICE'),
        'enable_name' => Configuration::get('LAB_SEARCH_NAME'),
        'search_alert'   => $labsearch->no_product,
        'link' => $context->link,
        'products' => $result_products,
    ));

    $context->smarty->display(dirname(__FILE__).'/views/templates/hook/popupsearch.tpl');
} else {
    echo '<div class="wrap_item">'.$labsearch->three_character.'</div>';
}
