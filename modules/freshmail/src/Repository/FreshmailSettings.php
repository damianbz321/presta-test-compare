<?php

namespace FreshMail\Repository;

use DbQuery;
use Freshmail\Entity\FreshmailSetting;

class FreshmailSettings extends AbstractRepository
{

    public function findForShop($idShop): FreshmailSetting
    {
        $query = new DbQuery();
        $query
            ->select(FreshmailSetting::$definition['primary'])
            ->from(FreshmailSetting::$definition['table'])
            ->where('id_shop = ' . (int)$idShop);

        return new FreshmailSetting($this->db->getValue($query));
    }

    public function getByHash($hash): FreshmailSetting
    {
        $query = new DbQuery();
        $query
            ->select(FreshmailSetting::$definition['primary'])
            ->from(FreshmailSetting::$definition['table'])
            ->where('subscriber_list_hash = "' . pSQL($hash) . '"');

        return new FreshmailSetting($this->db->getValue($query));
    }

    public function getAllSynchronize()
    {
        $query = new DbQuery();
        $query
            ->select('*')
            ->from(FreshmailSetting::$definition['table'])
            ->where('synchronize = 1');

        return $this->db->executeS($query);
    }

}