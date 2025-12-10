<?php

namespace FreshMail\Repository;

use DbQuery;
use FreshMail\Entity\Email;
use Freshmail\Entity\FreshmailSetting;

class Subscribers extends AbstractRepository
{

    public function deleteByEmailAndList($email,$list)
    {
        $this->db->delete(
            Email::$definition['table'],
            sprintf('email = "%s" AND hash_list = "%s" ', pSQL($email), pSQL($list))
        );
    }

    public function deleteByList($list){
        $this->db->delete(
            Email::$definition['table'],
            sprintf('hash_list = "%s" ', pSQL($list))
        );
    }

}