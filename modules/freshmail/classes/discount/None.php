<?php

namespace FreshMail\Discount;

use FreshMail\Entity\Cart;

class None extends AbstractDiscount{

    function apply(\Cart $cart, Cart $fmCart)
    {

    }

}