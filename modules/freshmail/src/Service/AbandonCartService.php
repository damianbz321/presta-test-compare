<?php

namespace FreshMail\Service;

use Freshmail\Entity\AbandonedCartSettings;
use FreshMail\Entity\Cart;
use FreshMail\Entity\CartNotify;
use FreshMail\Repository\Carts;
use FreshMail\Repository\FreshmailAbandonCartSettings;
use FreshMail\Repository\FreshmailSettings;
use FreshMail\Sender\Email;
use FreshMail\Sender\Factory;
use FreshMail\Sender\Service\CartDataCollector;

class AbandonCartService
{
    private $cartRepository = null;
    private $settingsRepository = null;
    private $abandonCartSettings = null;
    private $activeShops = [];

    public function __construct(Carts $cartRepository, FreshmailSettings $fmSetting, FreshmailAbandonCartSettings $abandonCartSettings)
    {
        $this->cartRepository = $cartRepository;
        $this->settingsRepository = $fmSetting;
        $this->abandonCartSettings = $abandonCartSettings;

        $this->activeShops = array_column($abandonCartSettings->getActive(), 'id_shop');

    }

    public function setDiscount(AbandonedCartSettings $settings, \Cart $cart, Cart $fmCart)
    {
        if(!\Validate::isLoadedObject($cart) || !\Validate::isLoadedObject($fmCart) || !\Validate::isLoadedObject($settings)){
            return;
        }
        $discountClass = 'FreshMail\\Discount\\'.ucfirst(strtolower($settings->discount_type));
        if(class_exists($discountClass) ){
            $discount = new $discountClass($settings);
            $discount->apply($cart, $fmCart);

        }
    }

    public function sendNotifications(\Cart $cart, Cart $fmCart, Email $email, CartDataCollector $cartDataCollector ){
        if(!in_array($cart->id_shop, $this->activeShops)){
            return;
        }

        if(!\Validate::isEmail($email->email)){
            return;
        }

        if( !Factory::getSender($this->settingsRepository->findForShop($cart->id_shop),$this->abandonCartSettings)->send($cart, $fmCart, $email, $cartDataCollector)) {
            return;
        }

        if(\Validate::isLoadedObject($fmCart)) {
            $fmNotify = new CartNotify();
            $fmNotify->id_freshmail_cart = $fmCart->id;
            $fmNotify->save();
        }
        return true;
    }

}