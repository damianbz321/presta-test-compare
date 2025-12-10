<?php

use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Dhl\Enums;

$country = new DataModels\DhlCountry(Enums\Country::MT);

/* PI start */
require 'DefaultPiService.php';
$service->PostalCodeMaxLength = 8;
$country->AddService($service);
/* PI end */
?>