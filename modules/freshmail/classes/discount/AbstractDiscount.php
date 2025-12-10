<?php

namespace FreshMail\Discount;

use FreshMail\Entity\Cart;

abstract class AbstractDiscount{

    protected $settings;

    public function __construct(\Freshmail\Entity\AbandonedCartSettings $settings)
    {
        $this->settings = $settings;
    }

    abstract function apply(\Cart $cart, Cart $fmCart);

}
