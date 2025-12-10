<?php

use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Dhl\Enums;

$country = new DataModels\DhlCountry(Enums\Country::EE);

/* EK start */
require 'DefaultEkService.php';
$service->PostalCodeMaxLength = 5;
$service->AvailableSpecialServices->SetServices(array
(
    Enums\SpecialService::S_UBEZP,
    Enums\SpecialService::S_COD,
));
$country->AddService($service);
/* EK end */

/* CM start */
require 'DefaultCmService.php';
$country->AddService($service);
$service->AvailableSpecialServices->SetServices(array
(
    Enums\SpecialService::S_UBEZP,
));
/* CM end */

/* CP start */
require 'DefaultCpService.php';
$country->AddService($service);
$service->AvailableSpecialServices->SetServices(array
(
    Enums\SpecialService::S_UBEZP,
));
/* CP end */
?>