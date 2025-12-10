<?php

namespace FreshMail\Sender\Service;

use FreshMail\Entity\Cart;

interface CartDataCollector
{
    public static function getProductList(\Cart $cart, \Context $context);
    public static function getDiscountCode(Cart $fmCart);
    public static function getDiscountValidTo(Cart $fmCart);
    public static function getCartUrl(Cart $fmCart);
    public static function getShopAddress();
    public static function getCustomer(\Cart $cart) : \Customer;
}