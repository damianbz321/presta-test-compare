<?php

namespace FreshMail\Repository;

use FreshMail\SubscriberCollection;

class EmailsSynchronized extends AbstractRepository
{
    public function addSubscribers($hashList, SubscriberCollection $subscribers)
    {
        foreach ($subscribers as $subscriber){
            $this->db->insert(
                \FreshMail\Entity\EmailsSynchronized::$definition['table'],
                [
                    'email' => pSQL($subscriber->email),
                    'hash_list' => pSQL($hashList)
                ],
                true,
                false,
                    $this->db::INSERT_IGNORE
            );
        }
    }

}