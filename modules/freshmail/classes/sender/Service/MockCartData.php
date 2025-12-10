<?php

namespace FreshMail\Sender\Service;

use FreshMail\Entity\Cart;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

class MockCartData implements CartDataCollector
{
    public static function getProductList(\Cart $cart, \Context $context)
    {
        $query = new \DbQuery();
        $query->select('p.*, pl.*')
            ->from('product', 'p')
            ->leftJoin('product_lang', 'pl', 'p.id_product = pl.id_product AND pl.id_lang = '.(int)$context->language->id)
            ->leftJoin('product_sale', 'ps', 'p.id_product = ps.id_product')
            ->where('p.active = 1')
            ->where('p.available_for_order = 1')
            ->orderBy('ps.quantity desc')
            ->limit(1)
        ;
        $productList = \Db::getInstance()->executeS($query);

        $priceFormatter = new PriceFormatter();
        foreach ($productList as &$prod) {
            $prod['name'] = \Product::getProductName($prod['id_product']);
            $prod['quantity'] = 1;
            $prod['price_wt'] = $priceFormatter->format(
                \Product::getPriceStatic((int)$prod['id_product'], true),
                new \Currency($cart->id_currency)
            );
            $prod['img'] = $context->link->getImageLink($prod['name'], \Product::getCover($prod['id_product'], $context)['id_image'], 'cart_default');
            $prod['img_big'] = $context->link->getImageLink($prod['name'], \Product::getCover($prod['id_product'], $context)['id_image'], 'home_default');
        }

        return $productList;
    }

    public static function getDiscountCode(Cart $fmCart)
    {
        return 'PROMO_CODE';
    }

    public static function getDiscountValidTo(Cart $fmCart)
    {
        return 'YYYY-MM-DD';
    }

    public static function getCartUrl(Cart $fmCart)
    {
        return \Context::getContext()->link->getModuleLink('freshmail', 'restore', ['hash' => $fmCart->cart_token]);
    }

    public static function getShopAddress()
    {
        $city = \Configuration::get('PS_SHOP_CITY');
        $postal = \Configuration::get('PS_SHOP_CODE');
        $addr1 = \Configuration::get('PS_SHOP_ADDR1');
        $addr2 = \Configuration::get('PS_SHOP_ADDR2');

        return $city . ' ' . $postal . ' ' . $addr1 . ' ' . $addr2;
    }

    public static function getCustomer(\Cart $cart) : \Customer
    {
        $query = new \DbQuery();
        $query->select('DISTINCT(firstname)')->from('customer')->limit(100);
        $firstame = \Db::getInstance()->executeS($query);

        $query = new \DbQuery();
        $query->select('DISTINCT(lastname)')->from('customer')->limit(100);
        $lastname = \Db::getInstance()->executeS($query);

        $customer = new \Customer();
        $customer->firstname = $firstame[array_rand($firstame)]['firstname'];
        $customer->lastname = $lastname[array_rand($lastname)]['lastname'];

        return $customer;
    }
}