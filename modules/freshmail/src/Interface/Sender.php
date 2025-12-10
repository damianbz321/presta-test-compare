<?php

namespace FreshMail\Interfaces;

use FreshMail\Entity\Cart;
use FreshMail\Sender\Email;
use FreshMail\Sender\Service\CartDataCollector;

interface Sender{

    public function send(\Cart $cart, Cart $fmCart, Email $email, CartDataCollector $cartDataCollector) : bool;

}