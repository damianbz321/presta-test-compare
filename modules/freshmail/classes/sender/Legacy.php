<?php

namespace FreshMail\Sender;

use FreshMail\Entity\Cart;
use FreshMail\Repository\Carts;
use FreshMail\Sender\Service\CartDataCollector;

class Legacy extends AbstractSender implements \FreshMail\Interfaces\Sender{

    public function send(\Cart $cart, Cart $fmCart, Email $email, CartDataCollector $cartDataCollector) : bool
    {
        $context = \Context::getContext();
        $context->cart = $cart;
        $context->shop = new \Shop($cart->id_shop);
        $customer = new \Customer($cart->id_customer);

        $vars = [
            '{products_list}' => AbstractSender::getProductsLegacy($context, $cartDataCollector::getProductList($cart, $context)),
            '{discount_code}' => $cartDataCollector::getDiscountCode($fmCart),
            '{cart_url}' => $cartDataCollector::getCartUrl($fmCart),
            '{company_name}' => \Configuration::get('PS_SHOP_NAME'),
            '{company_address}' => $cartDataCollector::getShopAddress(),
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{cartrule_validto}' => $cartDataCollector::getDiscountValidTo($fmCart),
        ];
        $cs = $this->cartSettings->findForShop($cart->id_shop);

        return \Mail::send(
            $cart->id_lang,
            'abandoned-cart',
            $cs->email_subject[$cart->id_lang],
            $vars,
            $email->email,
            $email->name,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_.'freshmail/mails/'
        );
    }
}
