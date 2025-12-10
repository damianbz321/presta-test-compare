<?php

use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Dhl\Enums;

$country = new DataModels\DhlCountry(Enums\Country::SK);

/* EK start */
$service = new DataModels\DhlCountryService(Enums\ServiceType::EK, $country);
$service->PostalCodeMaxLength = 5;
$service->AvailableSpecialServices->SetServices(array
(
    Enums\SpecialService::S_UBEZP,
    Enums\SpecialService::S_COD,
));
$service->AvailableShippingPaymentTypes = array
(
    Enums\ShippingPaymentType::SHIPPER => array(Enums\PaymentType::BANK_TRANSFER),
);
$service->AvailableLabelTypes = array
(
    Enums\LabelType::BLP,
    Enums\LabelType::ZBLP,
    Enums\LabelType::LBLP,
);
$service->AvailablePackageTypes = array
(
    Enums\PackageType::PACKAGE,
);
$service->AllowNst = true;
$service->AllowNstForParcel = false;
$service->AllowEuroReturn = false;
$service->CodRequireUbezp = false;
$service->AllowParcelShop = true;
$service->AllowCodForParcelShop = true;
$service->AllowParcelLocker = false;
$service->AllowCodForParcelLocker = true;
$service->RequirePostnummerForParcelLocker = false;
$service->RequirePostalCodeForParcel = true;
$service->ParcelShopOnlyService = false;
$service->AllowSearchParcelShopByRange = false;
$service->AllowSearchParcelByMap = true;
$service->AllowPostalCodesServicesInfoForReceiver = false;

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