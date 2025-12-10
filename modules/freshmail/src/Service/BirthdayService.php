<?php

namespace FreshMail\Service;

use FreshMail\Repository\FreshmailSettings;
use FreshMail\Sender\Birthday;
use FreshMail\Sender\Email;

class BirthdayService{

    public function sendNotifications(\Customer $customer, \FreshMail\Entity\Birthday $birthday)
    {
        if(!\Validate::isLoadedObject($customer)){
            return;
        }

        $fs = (new FreshmailSettings(\Db::getInstance()))->findForShop($customer->id_shop);

        return (new Birthday($fs->api_token))->send($customer, $birthday);
    }
}