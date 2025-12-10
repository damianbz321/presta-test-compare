<?php

namespace FreshMail\Discount;

use FreshMail\Entity\Cart;
use FreshMail\Repository\Carts;

class Percent extends AbstractDiscount{

    function apply(\Cart $cart, Cart $fmCart){
        if(!empty($fmCart->id_cart_rule)){
            return;
        }

        $cartRule = new \CartRuleCore();

        $cartRule->date_from = date("Y-m-d H:i:s");
        $cartRule->date_to = date('Y-m-d H:i:s', strtotime('+'.$this->settings->discount_lifetime.' HOURS'));
        $cartRule->free_shipping = false;
        $cartRule->minimum_amount_currency = 1;
        $cartRule->reduction_percent = (int)$this->settings->discount_percent;
        $cartRule->reduction_tax = true;
        $cartRule->reduction_currency = $cart->id_currency;
        $cartRule->active = true;
        $cartRule->partial_use = false;
        $cartRule->code = 'fm_'.substr(md5(time()), 0,10);
        $cartRule->name[$cart->id_lang] = \Module::getInstanceByName('freshmail')->l('Discount for abandoned cart');
        $cartRule->save();

        $fmCart->id_cart_rule = $cartRule->id;
        $fmCart->save();
    }
}