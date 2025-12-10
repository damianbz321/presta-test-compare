<?php

namespace FreshMail\Sender;

use FreshMail\Entity\Cart;
use Freshmail\Entity\FreshmailSetting;
use FreshMail\Freshmail;
use FreshMail\FreshmailApiV3;
use FreshMail\Interfaces\Sender;
use FreshMail\Repository\FreshmailAbandonCartSettings;
use FreshMail\Repository\FreshmailSettings;
use FreshMail\Sender\Service\CartDataCollector;
use FreshMail\TransactionalEmail;

class FmSender extends AbstractSender implements Sender{

    private $freshmailApi = null;

    public function __construct(FreshmailSetting $fmSettings, FreshmailAbandonCartSettings $cartSettings)
    {
        parent::__construct($fmSettings, $cartSettings);
        $this->freshmailApi = new Freshmail($this->fmSettings->api_token);
    }

    public function send(\Cart $cart, Cart $fmCart, Email $email, CartDataCollector $cartDataCollector) : bool
    {
        $shop = new \Shop($cart->id_shop);
        $fmApi = new FreshmailApiV3($this->fmSettings->api_token);
        $cs = $this->cartSettings->findForShop($cart->id_shop);

        $context = \Context::getContext();
        $context->cart = $cart;
        $context->shop = new \Shop($cart->id_shop);

        $customer = $cartDataCollector::getCustomer($cart);

        if (\Configuration::get('PS_LOGO_MAIL') !== false && file_exists(_PS_IMG_DIR_.\Configuration::get('PS_LOGO_MAIL', null, null, $cart->id_shop))) {
            $logo = _PS_IMG_DIR_.\Configuration::get('PS_LOGO_MAIL', null, null, $cart->id_shop);
        } else {
            if (file_exists(_PS_IMG_DIR_.\Configuration::get('PS_LOGO', null, null, $cart->id_shop))) {
                $logo = _PS_IMG_DIR_.\Configuration::get('PS_LOGO', null, null, $cart->id_shop);
            }
        }

        $logo_url = '';
        if(file_exists($logo)){
            $logo_url = $context->shop->getBaseURL() . _PS_IMG_ . \Configuration::get('PS_LOGO', null, null, $cart->id_shop);
        }

        $replaceFrom = [
            '{products_list}',
            '{discount_code}',
            '{shop_name}',
            '{shop_url}',
            '{shop_logo}',
            '{cart_url}',
            '{company_name}',
            '{company_address}',
            '{firstname}',
            '{lastname}',
            '{cartrule_validto}',
            '{preheader}'
        ];
        $productsHtml = AbstractSender::getProductsFM($this->getProductHtml($cart, $fmCart), $cartDataCollector::getProductList($cart, $context));
        if(empty($productsHtml)){
            $productsHtml = AbstractSender::getProductsLegacy($context, $cartDataCollector::getProductList($cart, $context));
        }
        $replaceTo = [
            $productsHtml,
            $cartDataCollector::getDiscountCode($fmCart),
            $shop->name, //\Configuration::get('PS_SHOP_NAME'),
            $context->shop->getBaseURL(), //$context->link->getPageLink('index', true, $cart->id_lang, null, false, $cart->id_shop),
            $logo_url,
            $cartDataCollector::getCartUrl($fmCart),
            \Configuration::get('PS_SHOP_NAME', $cart->id_lang, null, $cart->id_shop),
            $cartDataCollector::getShopAddress(),
            $customer->firstname,
            $customer->lastname,
            $cartDataCollector::getDiscountValidTo($fmCart),
            $cs->email_preheader[$cart->id_lang],
        ];

        $html = str_replace(
            $replaceFrom,
            $replaceTo,
            $this->getMailHtml($cart, $fmCart)
        );
        $sender = new Email(\Configuration::get('PS_SHOP_EMAIL'), $shop->name);


        return $fmApi->sendTransactionalEmail(
            new TransactionalEmail($email, $sender, $cs->email_subject[$cart->id_lang], $html)
        );
    }

    private function getMailHtml(\Cart $cart, Cart $fmCart) : string
    {
        $cs = $this->cartSettings->findForShop($cart->id_shop);
        if(!empty($cs->template_id_hash)){
            return $this->freshmailApi->getTemplateHtml($cs->template_id_hash);
        }
        $lang = new \Language($cart->id_lang);
        return file_get_contents(_PS_MODULE_DIR_.'freshmail/mails/'.$lang->iso_code.'/abandoned-cart.html');
    }

    private function getProductHtml(\Cart $cart, Cart $fmCart) : string
    {
        $cs = $this->cartSettings->findForShop($cart->id_shop);
        if(!empty($cs->template_id_hash)){
            return $this->freshmailApi->getProductHtml($cs->template_id_hash);
        }

        return '';
    }
}