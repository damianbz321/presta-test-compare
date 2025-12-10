<?php

namespace FreshMail\Repository;

use DbQuery;
use ObjectModel;

class EmailToSynchronize extends AbstractRepository
{
    public function getAll()
    {
        $query = new DbQuery();
        $query
            ->select('*')
            ->from();
        return $this->db->executeS();
    }

   public function getCount(string $hashList)
    {
        $query = new DbQuery();
        $query
            ->select('count(*)')
            ->from(\FreshMail\Entity\EmailToSynchronize::$definition['table'])
            ->where('hash_list = "'.pSQL($hashList).'"')
        ;
        return $this->db->getValue($query);
    }

    public function getListToSync()
    {
        $query = new DbQuery();
        $query
            ->select('hash_list, count(*)')
            ->from(\FreshMail\Entity\EmailToSynchronize::$definition['table'])
            ->groupBy('hash_list')
            ->orderBy('count(*) DESC')
        ;
        return $this->db->executeS($query);
    }

}