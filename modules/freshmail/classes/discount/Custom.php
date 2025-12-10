<?php

namespace FreshMail\Discount;

use FreshMail\Entity\Cart;

class Custom extends AbstractDiscount{

    function apply(\Cart $cart, Cart $fmCart){
        $fmCart->discount_code = $this->settings->discount_code;
        $fmCart->save();
    }
}
