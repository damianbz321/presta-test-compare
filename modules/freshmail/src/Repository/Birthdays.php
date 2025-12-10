<?php

namespace FreshMail\Repository;

use DbQuery;
use Freshmail\Entity\AbandonedCartSettings;
use FreshMail\Entity\Birthday;

class Birthdays extends AbstractRepository
{

    public function findForShop($idShop): Birthday
    {
        $query = new DbQuery();
        $query
            ->select(Birthday::$definition['primary'])
            ->from(Birthday::$definition['table'])
            ->where(Birthday::$definition['primary']. ' = ' . (int)$idShop);

        return new Birthday($this->db->getValue($query));
    }

    public function getActive(){
        $query = new DbQuery();
        $query
            ->select(Birthday::$definition['primary'])
            ->from(Birthday::$definition['table'])
            ->where('enable = 1');

        return \Db::getInstance()->executeS($query);
    }

}