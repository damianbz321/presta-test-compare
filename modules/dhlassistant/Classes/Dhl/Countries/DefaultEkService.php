<?php
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Dhl\Enums;

$service = new DataModels\DhlCountryService(Enums\ServiceType::EK, $country);
$service->AvailableSpecialServices->SetServices(array
(
	Enums\SpecialService::S_UBEZP,
	Enums\SpecialService::S_COD,
));
$service->AvailableShippingPaymentTypes = array
(
	Enums\ShippingPaymentType::SHIPPER => array(Enums\PaymentType::BANK_TRANSFER), //Enums\PaymentType::CASH
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
$service->AllowParcelShop = false;
$service->AllowCodForParcelShop = false;
$service->AllowParcelLocker = false;
$service->AllowCodForParcelLocker = false;
$service->RequirePostnummerForParcelLocker = false;
$service->RequirePostalCodeForParcel = false;
$service->ParcelShopOnlyService = false;
$service->AllowSearchParcelShopByRange = false;
$service->AllowSearchParcelByMap = false;
$service->AllowPostalCodesServicesInfoForReceiver = false;
	
?>