<?php

use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Dhl\Enums;

$country = new DataModels\DhlCountry(Enums\Country::PL);

/* AH start */
$service = new DataModels\DhlCountryService(Enums\ServiceType::AH, $country);
$service->PostalCodeMaxLength = 5;
$service->AvailableSpecialServices->SetServices(array
(
	Enums\SpecialService::S_1722,
	Enums\SpecialService::S_SOBOTA,
	Enums\SpecialService::S_NAD_SOBOTA,
	Enums\SpecialService::S_UBEZP,
	Enums\SpecialService::S_COD,
	Enums\SpecialService::S_PDI,
	Enums\SpecialService::S_ROD,
	Enums\SpecialService::S_POD,
	Enums\SpecialService::S_SAS,
	Enums\SpecialService::S_ODB,
));

$service->AvailableShippingPaymentTypes = array
(
    Enums\ShippingPaymentType::SHIPPER => array(Enums\PaymentType::BANK_TRANSFER), //Enums\PaymentType::CASH
);


$service->AvailableShippingPaymentTypes = array

(
	Enums\ShippingPaymentType::SHIPPER => array(Enums\PaymentType::BANK_TRANSFER), //Enums\PaymentType::CASH
//	Enums\ShippingPaymentType::RECEIVER => array(Enums\PaymentType::CASH),
	Enums\ShippingPaymentType::USER => array(Enums\PaymentType::BANK_TRANSFER),
);
$service->AvailableLabelTypes = array
(
	Enums\LabelType::LP,
	Enums\LabelType::BLP,
	Enums\LabelType::ZBLP,
	Enums\LabelType::LBLP,
);
$service->AvailablePackageTypes = array
(
	Enums\PackageType::ENVELOPE,
	Enums\PackageType::PACKAGE,
	Enums\PackageType::PALLET,
);
$service->AllowNst = true;
$service->AllowNstForParcel = true;
$service->AllowEuroReturn = true;
$service->CodRequireUbezp = true;
$service->AllowParcelShop = false;
$service->AllowCodForParcelShop = false;
$service->AllowParcelLocker = false;
$service->AllowCodForParcelLocker = false;
$service->RequirePostnummerForParcelLocker = false;
$service->RequirePostalCodeForParcel = false;
$service->ParcelShopOnlyService = false;
$service->AllowSearchParcelShopByRange = false;
$service->AllowSearchParcelByMap = false;
$service->AllowPostalCodesServicesInfoForReceiver = true;

$country->AddService($service);
/* AH end */

/* PR start */
$service = new DataModels\DhlCountryService(Enums\ServiceType::PR, $country);
$service->PostalCodeMaxLength = 5;
$service->AvailableSpecialServices->SetServices(array
(
    Enums\SpecialService::S_1722,
    Enums\SpecialService::S_SOBOTA,
    Enums\SpecialService::S_NAD_SOBOTA,
    Enums\SpecialService::S_UBEZP,
    Enums\SpecialService::S_COD,
    Enums\SpecialService::S_PDI,
    Enums\SpecialService::S_ROD,
    Enums\SpecialService::S_POD,
    Enums\SpecialService::S_SAS,
    Enums\SpecialService::S_ODB,
));

$service->AvailableShippingPaymentTypes = array
(
    Enums\ShippingPaymentType::SHIPPER => array(Enums\PaymentType::BANK_TRANSFER), //Enums\PaymentType::CASH
);


$service->AvailableShippingPaymentTypes = array

(
    Enums\ShippingPaymentType::SHIPPER => array(Enums\PaymentType::BANK_TRANSFER), //Enums\PaymentType::CASH
//    Enums\ShippingPaymentType::RECEIVER => array(Enums\PaymentType::CASH),
    Enums\ShippingPaymentType::USER => array(Enums\PaymentType::BANK_TRANSFER),
);
$service->AvailableLabelTypes = array
(
    Enums\LabelType::LP,
    Enums\LabelType::BLP,
    Enums\LabelType::ZBLP,
    Enums\LabelType::LBLP,
);
$service->AvailablePackageTypes = array
(
    Enums\PackageType::ENVELOPE,
    Enums\PackageType::PACKAGE,
    Enums\PackageType::PALLET,
);
$service->AllowNst = true;
$service->AllowNstForParcel = true;
$service->AllowEuroReturn = true;
$service->CodRequireUbezp = true;
$service->AllowParcelShop = false;
$service->AllowCodForParcelShop = false;
$service->AllowParcelLocker = false;
$service->AllowCodForParcelLocker = false;
$service->RequirePostnummerForParcelLocker = false;
$service->RequirePostalCodeForParcel = false;
$service->ParcelShopOnlyService = false;
$service->AllowSearchParcelShopByRange = false;
$service->AllowSearchParcelByMap = false;
$service->AllowPostalCodesServicesInfoForReceiver = true;

$country->AddService($service);
/* AH end */


/* D09 start */
$service = new DataModels\DhlCountryService(Enums\ServiceType::D09, $country);
$service->PostalCodeMaxLength = 5;
$service->AvailableSpecialServices->SetServices(array
(
	Enums\SpecialService::S_1722,
	Enums\SpecialService::S_SOBOTA,
	Enums\SpecialService::S_NAD_SOBOTA,
	Enums\SpecialService::S_UBEZP,
	Enums\SpecialService::S_COD,
	Enums\SpecialService::S_PDI,
	Enums\SpecialService::S_ROD,
	Enums\SpecialService::S_POD,
	Enums\SpecialService::S_SAS,
	Enums\SpecialService::S_ODB,
));
$service->AvailableShippingPaymentTypes = array
(
	Enums\ShippingPaymentType::SHIPPER => array(Enums\PaymentType::BANK_TRANSFER), //Enums\PaymentType::CASH
//	Enums\ShippingPaymentType::RECEIVER => array(Enums\PaymentType::CASH),
	Enums\ShippingPaymentType::USER => array(Enums\PaymentType::BANK_TRANSFER),
);
$service->AvailableLabelTypes = array
(
	Enums\LabelType::LP,
	Enums\LabelType::BLP,
	Enums\LabelType::ZBLP,
	Enums\LabelType::LBLP,
);
$service->AvailablePackageTypes = array
(
	Enums\PackageType::ENVELOPE,
	Enums\PackageType::PACKAGE,
);
$service->AllowNst = true;
$service->AllowNstForParcel = false;
$service->AllowEuroReturn = true;
$service->CodRequireUbezp = true;
$service->AllowParcelShop = false;
$service->AllowCodForParcelShop = false;
$service->AllowParcelLocker = false;
$service->AllowCodForParcelLocker = false;
$service->RequirePostnummerForParcelLocker = false;
$service->RequirePostalCodeForParcel = true;
$service->ParcelShopOnlyService = false;
$service->AllowSearchParcelShopByRange = false;
$service->AllowSearchParcelByMap = false;
$service->AllowPostalCodesServicesInfoForReceiver = true;

$country->AddService($service);
/* D09 end */

/* D12 start */
$service = new DataModels\DhlCountryService(Enums\ServiceType::D12, $country);
$service->PostalCodeMaxLength = 5;
$service->AvailableSpecialServices->SetServices(array
		(
				Enums\SpecialService::S_1722,
				Enums\SpecialService::S_SOBOTA,
				Enums\SpecialService::S_NAD_SOBOTA,
				Enums\SpecialService::S_UBEZP,
				Enums\SpecialService::S_COD,
				Enums\SpecialService::S_PDI,
				Enums\SpecialService::S_ROD,
				Enums\SpecialService::S_POD,
				Enums\SpecialService::S_SAS,
				Enums\SpecialService::S_ODB,
		));
$service->AvailableShippingPaymentTypes = array
(
		Enums\ShippingPaymentType::SHIPPER => array(Enums\PaymentType::BANK_TRANSFER), //Enums\PaymentType::CASH
//		Enums\ShippingPaymentType::RECEIVER => array(Enums\PaymentType::CASH),
		Enums\ShippingPaymentType::USER => array(Enums\PaymentType::BANK_TRANSFER),
);
$service->AvailableLabelTypes = array
(
		Enums\LabelType::LP,
		Enums\LabelType::BLP,
		Enums\LabelType::ZBLP,
		Enums\LabelType::LBLP,
);
$service->AvailablePackageTypes = array
(
		Enums\PackageType::ENVELOPE,
		Enums\PackageType::PACKAGE,
);
$service->AllowNst = true;
$service->AllowNstForParcel = false;
$service->AllowEuroReturn = true;
$service->CodRequireUbezp = true;
$service->AllowParcelShop = false;
$service->AllowCodForParcelShop = false;
$service->AllowParcelLocker = false;
$service->AllowCodForParcelLocker = false;
$service->RequirePostnummerForParcelLocker = false;
$service->RequirePostalCodeForParcel = false;
$service->ParcelShopOnlyService = false;
$service->AllowSearchParcelShopByRange = false;
$service->AllowSearchParcelByMap = false;
$service->AllowPostalCodesServicesInfoForReceiver = true;

$country->AddService($service);
/* D12 end */

/* LM start */
$service = new DataModels\DhlCountryService(Enums\ServiceType::LM, $country);
$service->PostalCodeMaxLength = 5;
$service->AvailableSpecialServices->SetServices(array
		(
				Enums\SpecialService::S_UBEZP,
                Enums\SpecialService::S_COD,
		));
$service->AvailableShippingPaymentTypes = array
(
		Enums\ShippingPaymentType::SHIPPER => array(Enums\PaymentType::BANK_TRANSFER), //Enums\PaymentType::CASH
		Enums\ShippingPaymentType::USER => array(Enums\PaymentType::BANK_TRANSFER),
);
$service->AvailableLabelTypes = array
(
		Enums\LabelType::BLP,
		Enums\LabelType::ZBLP,
		Enums\LabelType::LBLP,
);
$service->AvailablePackageTypes = array
(
		Enums\PackageType::ENVELOPE,
		Enums\PackageType::PACKAGE,
);
$service->AllowNst = true;
$service->AllowNstForParcel = true;
$service->AllowEuroReturn = false;
$service->CodRequireUbezp = true;
$service->AllowParcelShop = true;
$service->AllowCodForParcelShop = true;
$service->AllowParcelLocker = false;
$service->AllowCodForParcelLocker = false;
$service->RequirePostnummerForParcelLocker = false;
$service->RequirePostalCodeForParcel = false;
$service->ParcelShopOnlyService = true;
$service->AllowSearchParcelShopByRange = true;
$service->AllowSearchParcelByMap = true;
$service->AllowPostalCodesServicesInfoForReceiver = true;

$country->AddService($service);
/* LM end */

?>