<?php

use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Dhl\Enums;

$country = new DataModels\DhlCountry(Enums\Country::MC);

/* PI start */
require 'DefaultPiService.php';
$service->AllowNst = false;
$service->PostalCodeMaxLength = 5;
$country->AddService($service);
/* PI end */
?>