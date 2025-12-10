<?php

namespace FreshMail\Sender\Service;

use FreshMail\Entity\Cart;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

class CartData implements CartDataCollector
{
    public static function getProductList(\Cart $cart, \Context $context)
    {
        $priceFormatter = new PriceFormatter();
        $productList = $cart->getProducts();
        foreach ($productList as &$prod) {
            $prod['img'] = $context->link->getImageLink($prod['name'], \Product::getCover($prod['id_product'], $context)['id_image'], 'cart_default');
            $prod['img_big'] = $context->link->getImageLink($prod['name'], \Product::getCover($prod['id_product'], $context)['id_image'], 'home_default');
            $prod['price_wt'] = $priceFormatter->format($prod['price_wt'], new \Currency($cart->id_currency));
        }

        return $productList;
    }


    public static function getDiscountCode(Cart $fmCart)
    {
        $discountCode = '';
        if (!empty($fmCart->id_cart_rule)) {
            $discountCode = (new \CartRule($fmCart->id_cart_rule))->code;
        } elseif (!empty($fmCart->discount_code)) {
            $discountCode = $fmCart->discount_code;
        }
        return $discountCode;
    }

    public static function getDiscountValidTo(Cart $fmCart)
    {
        if (!empty($fmCart->id_cart_rule)) {
            $id = $fmCart->id_cart_rule;
        } elseif (!empty($fmCart->discount_code)) {
            $id = \CartRule::getIdByCode($fmCart->discount_code);
        }

        if (empty($id)) {
            return '';
        }
        $cr = new \CartRule($id);
        if (!\Validate::isLoadedObject($cr)) {
            return '';
        }
        return $cr->date_to;
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
        return new \Customer($cart->id_customer);
    }
}