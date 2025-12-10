<?php

namespace FreshMail\Repository;

use Freshmail\Entity\AbandonedCartSettings;
use FreshMail\Entity\Cart;
use Freshmail\Entity\FreshmailSetting;

class Carts extends AbstractRepository {

    public function collectNotifyCarts(AbandonedCartSettings $abandonSettings)
    {
        if(!$abandonSettings->enabled){
            return [];
        }

        $whereNot = sprintf(
            '%s NOT IN (SELECT %s FROM %s)',
            Cart::$definition['primary'],Cart::$definition['primary'],_DB_PREFIX_.'freshmail_cart_notify'
        );

        $query = new \DbQuery();
        $query
            ->select('fc.id_cart')
            ->from(\CartCore::$definition['table'], 'c')
            ->innerJoin(Cart::$definition['table'], 'fc', 'c.id_cart = fc.id_cart')
            ->where(sprintf('c.date_upd < (NOW() - INTERVAL %s HOUR)', $abandonSettings->send_after) )
            ->where($whereNot)
        ;

        $carts = $this->db->executeS($query);

        return array_column($carts, 'id_cart');
    }


    public function getByToken(\Cart $cart): Cart
    {
        $query = new DbQuery();
        $query
            ->select(Cart::$definition['primary'])
            ->from(Cart::$definition['table'])
            ->where('id_cart = '.$cart->id);

        return new Cart($this->db->getValue($query));
    }

    public function getByCart(\Cart $cart): Cart
    {
        $query = new \DbQuery();
        $query
            ->select(Cart::$definition['primary'])
            ->from(Cart::$definition['table'])
            ->where('id_cart = ' . $cart->id);

        return new Cart($this->db->getValue($query));
    }

    public function getByHash($hash): Cart
    {
        $query = new \DbQuery();
        $query
            ->select(Cart::$definition['primary'])
            ->from(Cart::$definition['table'])
            ->where('cart_token = "' . pSQL($hash).'"');

        return new Cart($this->db->getValue($query));
    }


}