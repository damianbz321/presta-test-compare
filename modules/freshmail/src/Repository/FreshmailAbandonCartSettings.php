<?php

namespace FreshMail\Repository;

use DbQuery;
use Freshmail\Entity\AbandonedCartSettings;

class FreshmailAbandonCartSettings extends AbstractRepository
{

    public function findForShop($idShop): AbandonedCartSettings
    {
        $query = new DbQuery();
        $query
            ->select(AbandonedCartSettings::$definition['primary'])
            ->from(AbandonedCartSettings::$definition['table'])
            ->where('id_shop = ' . (int)$idShop);

        return new AbandonedCartSettings($this->db->getValue($query));
    }

    public function getActive()
    {
        $query = new DbQuery();
        $query
            ->select('*')
            ->from(AbandonedCartSettings::$definition['table'])
            ->where('enabled = 1');

        return \Db::getInstance()->executeS($query);
    }

}