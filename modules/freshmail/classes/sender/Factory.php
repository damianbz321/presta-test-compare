<?php

namespace FreshMail\Sender;

use FreshMail\Discount\Percent;
use Freshmail\Entity\AbandonedCartSettings;
use Freshmail\Entity\FreshmailSetting;
use FreshMail\Interfaces\Sender;
use FreshMail\Repository\FreshmailAbandonCartSettings;

class Factory {

    public static function getSender(FreshmailSetting $fmSetting, FreshmailAbandonCartSettings $cartSettings) : Sender
    {
        if(empty($fmSetting->api_token)) {
            return new Legacy($fmSetting, $cartSettings);
        } else {
            return new FmSender($fmSetting, $cartSettings);
        }
    }

}