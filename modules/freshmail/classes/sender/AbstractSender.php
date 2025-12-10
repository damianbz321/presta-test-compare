<?php

namespace FreshMail\Sender;

use Freshmail\Entity\AbandonedCartSettings;
use FreshMail\Entity\Cart;
use Freshmail\Entity\FreshmailSetting;
use FreshMail\Repository\FreshmailAbandonCartSettings;
use FreshMail\Sender\Service\CartDataCollector;

class AbstractSender{

    protected $fmSettings = null;

    protected $cartSettings = null;

    public function __construct(FreshmailSetting $fmSettings, FreshmailAbandonCartSettings $cartSettings)
    {
        $this->fmSettings = $fmSettings;
        $this->cartSettings = $cartSettings;
    }

    public static function getProductsLegacy(\Context $context, $products)
    {
        $context->smarty->assign('list', $products);
        return $context->smarty->fetch(_PS_MODULE_DIR_ . 'freshmail/views/templates/email/order_conf_product_list.tpl');
    }

    public static function getProductsFM($html, $products){
        $output = '';
        foreach ($products as $product){
            $output .= str_replace(
                ['{product_cover}', '{product_cover_big}' , '{product_name}', '{product_quantity}', '{unit_price_tax_incl}'],
                [ $product['img'], $product['img_big'], $product['name'], $product['quantity'], $product['price_wt'] ],
                $html
            );
        }
        return $output;
    }
}