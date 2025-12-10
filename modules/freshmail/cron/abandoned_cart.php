<?php

$flock = fopen(__FILE__, 'r');
if( !flock($flock, LOCK_EX | LOCK_NB  )){
    die('Synchronization pending');
}

use FreshMail\Repository\FreshmailSettings;


require_once __DIR__ . '/../../../config/config.inc.php';


if(!Module::isInstalled('freshmail') || !($module = Module::getInstanceByName('freshmail')) ){
    die('Module isn\'t installed');
}

if(!\FreshMail\Tools::is_cli() && Tools::getValue('token') != $module->getCronToken() ) {
    die('Bad token');
}

Module::getInstanceByName('freshmail');

global $kernel;
if(!$kernel){
    require_once _PS_ROOT_DIR_.'/app/AppKernel.php';
    $kernel = new \AppKernel('prod', false);
    $kernel->boot();
}

// 1. utwórz listę
// 2. paczkuj na elementy
// 3. wyślij
// 4. usuń listy

$abandonRepository = new \FreshMail\Repository\FreshmailAbandonCartSettings(Db::getInstance());
$activeAbandon = $abandonRepository->getActive();
$cartsRepository = new \FreshMail\Repository\Carts(Db::getInstance());
$settingsRepository = new FreshmailSettings(Db::getInstance());
$cartService = new \FreshMail\Service\AbandonCartService($cartsRepository, $settingsRepository, $abandonRepository);

foreach ($activeAbandon as $abandon){
    $settings = new \Freshmail\Entity\AbandonedCartSettings($abandon['id_freshmail_cart_setting']);
    $idCarts = $cartsRepository->collectNotifyCarts($settings);

    foreach ($idCarts as $c){
        $cart = new Cart($c);
        Context::getContext()->currency = new Currency($cart->id_currency);

        $fmCart = $cartsRepository->getByCart($cart);
        $cartService->setDiscount($settings, $cart, $fmCart);
        $customer = new Customer($cart->id_customer);
        $email = new \FreshMail\Sender\Email($customer->email, $customer->firstname.' '.$customer->lastname);
        $cartService->sendNotifications($cart, $fmCart, $email, new \FreshMail\Sender\Service\CartData()) ;
    }

}
//var_dump($activeAbandon);
