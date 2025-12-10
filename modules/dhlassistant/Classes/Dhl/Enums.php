<?php

namespace DhlAssistant\Classes\Dhl\Enums;

use DhlAssistant\Classes\Forms\Translations;

class SettingsUserData
{
    /**
     * @return Translations
     */
    public function GetTranslation()
    {
        return new Translations();
    }

    /**
     * @param $value
     * @return mixed|string
     */
    public function ValueFieldApi($value)
    {
        $description = [
            'DhlUserValue' => $this->GetTranslation()->l('DHL user data', 'enums'),
            'DhlLinksValue' => $this->GetTranslation()->l('Link for Api', 'enums'),
            'Dhl24LinkValue' => $this->GetTranslation()->l('Link for API DHL24', 'enums'),
            'DhlPsLinkValue' => $this->GetTranslation()->l('Link for API DHLPS', 'enums'),
            'Dhl24Value' => $this->GetTranslation()->l('DHL24 WebAPI', 'enums'),
            'Dhl24LoginValue' => $this->GetTranslation()->l('Login Dhl24', 'enums'),
            'Dhl24PasswordValue' => $this->GetTranslation()->l('Password Dhl24', 'enums'),
            'DhlPsValue' => $this->GetTranslation()->l('Parcelshop Manager WebAP', 'enums'),
            'DhlPsLoginValue' => $this->GetTranslation()->l('Login DhlPS', 'enums'),
            'DhlPsPasswordValue' => $this->GetTranslation()->l('Password DhlPS', 'enums'),
            'AccountTypeValue' => $this->GetTranslation()->l('Enviroment', 'enums'),
        ];

        return $description[$value] ?? "";
    }

    /**
     * @param $value
     * @return mixed|string
     */
    public function ButtonsAndValidations($value)
    {
        $description = [
            'BtnCancelValue' => $this->GetTranslation()->l('Cancel', 'enums'),
            'BtnSaveValue' => $this->GetTranslation()->l('Save', 'enums'),
            'ValidTooLongValue' => $this->GetTranslation()->l('Too long value', 'enums'),
            'ValidTooLongValueContain100' => $this->GetTranslation()->l(
                'The field can contain up to 100 characters',
                'enums'
            ),
            'ValidTooLongValueContain32' => $this->GetTranslation()->l(
                'The field may contain up to 32 characters',
                'enums'
            ),
            'ValidTooLongValueContain20' => $this->GetTranslation()->l(
                'The field may contain up to 20 characters',
                'enums'
            ),
            'ValidTooLongValueContain' => $this->GetTranslation()->l(
                'The field may contain up to 7 characters',
                'enums'
            ),
            'ValidTooLongValueContain7' => $this->GetTranslation()->l(
                'The field may contain up to 7 characters and must be a number',
                'enums'
            ),
            'ValidTooLongValueContain9' => $this->GetTranslation()->l(
                'The field may contain up to 9 characters and must be a number',
                'enums'
            ),
            'ValidWrongvalue' => $this->GetTranslation()->l('Wrong value', 'enums'),
            'ValidIsNotEmptyValue' => $this->GetTranslation()->l('It cannot be empty', 'enums'),
            'ValidWrongPostcodevalue' => $this->GetTranslation()->l(
                'The field value must be a valid postal code',
                'enums'
            ),
            'ValidMustBeNumber' => $this->GetTranslation()->l('The value must be a number', 'enums'),
        ];

        return $description[$value] ?? "";
    }

    /**
     * @param $value
     * @return mixed|string
     */
    public function ValueFieldShipper($value)
    {
        $description = [
            'SP_Value' => $this->GetTranslation()->l('Sender\'s details', 'enums'),
            'SP_BillingAccountNumberValue' => $this->GetTranslation()->l('Nr. SAP', 'enums'),
            'SP_CostsCenterValue' => $this->GetTranslation()->l('Cost center', 'enums'),
            'SA_AddressValue' => $this->GetTranslation()->l('Shipper address', 'enums'),
            'SA_NameOrCompanyAddressValue' => $this->GetTranslation()->l('Company name', 'enums'),
            'SA_StreetValue' => $this->GetTranslation()->l('Street', 'enums'),
            'SA_HouseNumberValue' => $this->GetTranslation()->l('Nr. House', 'enums'),
            'SA_ApartmentNumberValue' => $this->GetTranslation()->l('Apartment Number', 'enums'),
            'SA_PostCodeContactValue' => $this->GetTranslation()->l('Post Code', 'enums'),
            'SA_CityContactValue' => $this->GetTranslation()->l('City', 'enums'),
            'SC_ShipperPresetContactValue' => $this->GetTranslation()->l('Contact details', 'enums'),
            'SC_ContactNameOrCompanyValue' => $this->GetTranslation()->l('Full name', 'enums'),
            'SC_ContactPhoneValue' => $this->GetTranslation()->l('Phone', 'enums'),
            'SC_ContactEmail' => $this->GetTranslation()->l('Email', 'enums'),
            'COD_Module' => $this->GetTranslation()->l('Module cash on delivery', 'enums')
        ];

        return $description[$value] ?? "";
    }

    /**
     * @param $value
     * @return mixed|string
     */
    public function ValueTabsMenu($value)
    {
        $description = [
            'TabConfigurationValue' => $this->GetTranslation()->l('Configuration', 'enums'),
            'TabShipmentPredefinitionsValue' => $this->GetTranslation()->l('Shipment predefinitions', 'enums'),
            'TabHelpValue' => $this->GetTranslation()->l('Help', 'enums'),
            'TabShipmentListValue' => $this->GetTranslation()->l('Shipment list', 'enums'),
            'ReportPNP' => $this->GetTranslation()->l('Report PNP', 'enums'),
        ];

        return $description[$value] ?? "";
    }

    /**
     * @param $value
     * @return mixed
     */
    public function ValuePnpReport($value)
    {
        $description = [
            'PNPTypeApiValue' => $this->GetTranslation()->l('Type API', 'enums'),
            'PNPTypeShippingValue' => $this->GetTranslation()->l('Type Shipping', 'enums'),
            'PNPReportDayValue' => $this->GetTranslation()->l('Report for day', 'enums'),
            'PNPReportDownloadValue' => $this->GetTranslation()->l('Download the report', 'enums'),
        ];

        return $description[$value];
    }

    /**
     * @param $value
     * @return mixed
     */
    public function Headings($value)
    {
        $description = [
            'ConfigValue' => $this->GetTranslation()->l('Configuration', 'enums'),
            'ShipmentValue' => $this->GetTranslation()->l('Shipment', 'enums'),
            'EditShipmentPValue' => $this->GetTranslation()->l('Editing shipment predefinition', 'enums')
        ];

        return $description[$value];
    }

    /**
     * @param $value
     * @return mixed
     */
    public function Shipment($value)
    {
        $description = [
            'NameValue' => $this->GetTranslation()->l('Name', 'enums'),
            'ActionValue' => $this->GetTranslation()->l('Action', 'enums'),
            'NoPreShipmentValue' => $this->GetTranslation()->l('No pre-definition of shipments', 'enums'),
            'PreShipmentListValue' => $this->GetTranslation()->l('Shipment pre-definition list', 'enums'),
            'EditValue' => $this->GetTranslation()->l('Edit', 'enums'),
        ];

        return $description[$value];
    }

    /**
     * @param $value
     * @return mixed
     */
    public function ShipmentList($value)
    {
        $desciption = [
            'RecipientValue' => $this->GetTranslation()->l('Recipient', 'enums'),
            'ContentsValue' => $this->GetTranslation()->l('Contents', 'enums'),
            'CreationDateValue' => $this->GetTranslation()->l('Creation Date', 'enums'),
            'StatusValue' => $this->GetTranslation()->l('Status', 'enums'),
            'ActionValue' => $this->GetTranslation()->l('Action', 'enums'),
            'SentValue' => $this->GetTranslation()->l('Sent', 'enums'),
            'WaitingValue' => $this->GetTranslation()->l('Waiting', 'enums'),
            'SearchValue' => $this->GetTranslation()->l('Search', 'enums'),
            'ClearValue' => $this->GetTranslation()->l('Clear', 'enums'),
            'ViewValue' => $this->GetTranslation()->l('View', 'enums'),
            'BackOffValue' => $this->GetTranslation()->l('Back off', 'enums'),
            'EditValue' => $this->GetTranslation()->l('Edit', 'enums'),
            'DeleteValue' => $this->GetTranslation()->l('Delete', 'enums'),
        ];

        return $desciption[$value];
    }

    /**
     * @param $value
     * @return mixed
     */
    public function ShipmentPresetList($value)
    {
        $description = [
            'NoPreOfShipmentsValue' => $this->GetTranslation()->l('No pre-definition of shipments', 'enums'),
            'PreOfShipmentValue' => $this->GetTranslation()->l('Shipment pre-definition list', 'enums'),
            'AddNewValue' => $this->GetTranslation()->l('Shipment pre-definition list', 'enums'),
            'EditValue' => $this->GetTranslation()->l('Edit', 'enums'),
            'DeleteValue' => $this->GetTranslation()->l('Delete', 'enums'),
        ];

        return $description[$value];
    }

    /**
     * @param $value
     * @return mixed
     */
    public function OtherTranslation($value)
    {
        $description = [
            'GeneralErrorValue' => $this->GetTranslation()->l('General error', 'enums')
        ];

        return $description[$value];
    }
}

/**
 *
 */
class AccountType
{
    const SANDBOX = "SANDBOX";
    const PRODUCTION = "PRODUCTION";

    /**
     * @return string[]
     */
    public function SelectAccount()
    {
        return [
            self::PRODUCTION => 'Produkcyjne',
            self::SANDBOX => 'Testowe',

        ];
    }


    public function getDHL24Link()
    {
        return [
            'https://dhl24.com.pl/webapi2' => self::PRODUCTION,
            'https://sandbox.dhl24.com.pl/webapi2' => self::SANDBOX,

        ];
    }

    public function getDHLPSLink()
    {
        return [
            'https://dhl24.com.pl/servicepoint' => self::PRODUCTION,
            'https://sandbox.dhl24.com.pl/servicepoint' => self::SANDBOX,

        ];
    }

}


abstract class PackageType
{
    const ENVELOPE = "ENVELOPE";
    const PACKAGE = "PACKAGE";
    const PALLET = "PALLET";

    public static $Descriptions = array
    (
        PackageType::ENVELOPE => "Przesyłka kopertowa",
        PackageType::PACKAGE => "Paczka",
        PackageType::PALLET => "Paleta",
    );
}

abstract class ServiceType
{
    const AH = "AH";
    const PR = "PR";
    const D09 = "09";
    const D12 = "12";
    const DW = "DW";
    const EK = "EK";
    const PI = "PI";
    const CP = "CP";
    const CM = "CM";
    const LM = "LM";

    public static $Descriptions = array
    (
        ServiceType::AH => "DHL Parcel Polska",
        ServiceType::PR => "DHL Parcel Premium",
        ServiceType::D09 => "DHL Parcel 9",
        ServiceType::D12 => "DHL Parcel 12",
        ServiceType::DW => "Doręczenie wieczorne",
        ServiceType::EK => "DHL Parcel Connect",
        ServiceType::PI => "DHL Parcel International",
        ServiceType::CP => "Connect plus – paczki",
        ServiceType::CM => "Connect plus - palety",
        ServiceType::LM => "Doręczenie do DHL POP",
    );
}

abstract class Carrier
{
    const FOREIGN_CARRIER_PARCELSHOP = "FOREIGN_CARRIER_PARCELSHOP";
    const FOREIGN_CARRIER_STANDARD = "FOREIGN_CARRIER_STANDARD";
    const PL_CARRIER_POP_COD = "PL_CARRIER_POP_COD";
    const PL_CARRIER_POP = "PL_CARRIER_POP";
    const PL_CARRIER_STANDARD_COD = "PL_CARRIER_STANDARD_COD";
    const CARRIER_STANDARD = "CARRIER_STANDARD";

    public static $Options = [
        Carrier::FOREIGN_CARRIER_PARCELSHOP => [
            'support_send_to_parcelshop' => true,
            'support_send_to_parcellocker' => true,
        ],
        Carrier::FOREIGN_CARRIER_STANDARD => [
            'support_send_to_parcelshop' => false,
            'support_send_to_parcellocker' => false,
        ],
        Carrier::PL_CARRIER_POP_COD => [
            'support_send_to_parcelshop' => true,
            'support_send_to_parcellocker' => false,
        ],
        Carrier::PL_CARRIER_POP => [
            'support_send_to_parcelshop' => true,
            'support_send_to_parcellocker' => false,
        ],
        Carrier::PL_CARRIER_STANDARD_COD => [
            'support_send_to_parcelshop' => false,
            'support_send_to_parcellocker' => false,
        ],
        Carrier::CARRIER_STANDARD => [
            'support_send_to_parcelshop' => false,
            'support_send_to_parcellocker' => false,
        ]
    ];
}

class ServiceType1 extends PnpReportPackageType
{
    public function Descriptions()
    {
        return [
            "AH" => $this->getTranslations()->l("Domestic express up to 31.5 kg"),
            "D09" => $this->getTranslations()->l("Domestic Service 09"),
        ];
    }

    public function getTranslations()
    {
        return new Translations();
    }
}

abstract class LabelType
{
    const LP = "LP";
    const BLP = "BLP";
    const ZBLP = "ZBLP";
    const LBLP = "LBLP";

    public static $Descriptions = array
    (
        LabelType::LP => "List przewozowy (PDF)",
        LabelType::BLP => "Etykieta BLP - (PDF) Drukarka termiczna",
        LabelType::ZBLP => "Etykieta ZBLP - (ZPL) Drukarka termiczna",
        LabelType::LBLP => "Etykieta LBLP - (PDF) Drukarka biurowa",

    );
}

abstract class PaymentType
{
    const CASH = "CASH";
    const BANK_TRANSFER = "BANK_TRANSFER";

    public static $Descriptions = array
    (
        PaymentType::CASH => "Gotówka",
        PaymentType::BANK_TRANSFER => "Przelew",
    );
}

abstract class ShippingPaymentType
{
    const SHIPPER = "SHIPPER";
    const RECEIVER = "RECEIVER";
    const USER = "USER";

    public static $Descriptions = array
    (
        ShippingPaymentType::SHIPPER => "Nadawca",
        ShippingPaymentType::RECEIVER => "Odbiorca",
        ShippingPaymentType::USER => "Trzecia strona",
    );
}

abstract class DropOffType
{
    const REGULAR_PICKUP = "REGULAR_PICKUP";
    const REQUEST_COURIER = "REQUEST_COURIER";
    public static $Descriptions = array
    (
        DropOffType::REGULAR_PICKUP => "Odbiór stały",
        DropOffType::REQUEST_COURIER => "Zamówienie kuriera",
    );
}

abstract class PnpReportPackageType
{
    const EX = "EX";
    const DR = "DR";
    const ALL = "ALL";
    const EUROPE = "2EUROPE";
    const PR = "PR";
    const DHLPS = "DHLPS";

    public static $Descriptions = array
    (
        PnpReportPackageType::ALL => "Wszystkie przesyłki",
        PnpReportPackageType::EX => "Przesyłki krajowe do 31,5 kg - kurier",
        PnpReportPackageType::DHLPS => "Przesyłki do POP",
        PnpReportPackageType::DR => "Przesyłki powyżej 31,5kg",
        PnpReportPackageType::EUROPE => "Przesyłki DHL Connect i International",
        PnpReportPackageType::PR => "Przesyłki Premium",
    );
}

abstract class DhlWebApi
{
    const DHL24 = "DHL24";
    const DHLPS = "DHLPS";

    public static $Descriptions = array
    (
        DhlWebApi::DHL24 => "DHL24 WebAPI",
        DhlWebApi::DHLPS => "DHL ParcelShop Manager WebAPI",
    );
}

abstract class SpecialService
{
    const S_1722 = '1722';
    const S_SOBOTA = 'SOBOTA';
    const S_NAD_SOBOTA = 'NAD_SOBOTA';
    const S_UBEZP = 'UBEZP';
    const S_COD = 'COD';
    const S_PDI = 'PDI';
    const S_ROD = 'ROD';
    const S_POD = 'POD';
    const S_SAS = 'SAS';
    const S_ODB = 'ODB';
    const S_UTIL = 'UTIL';

    public static $Descriptions = array
    (
        SpecialService::S_1722 => 'Doręczenie w godzinach 18-22',
        SpecialService::S_SOBOTA => 'Doręczenie w sobotę',
        SpecialService::S_NAD_SOBOTA => 'Nadanie w sobotę',
        SpecialService::S_UBEZP => 'Ubezpieczenie przesyłki',
        SpecialService::S_COD => 'Zwrot pobrania',
        SpecialService::S_PDI => 'Informacje przed doręczeniem',
        SpecialService::S_ROD => 'Zwrot potwierdzonych dokumentów',
        SpecialService::S_POD => 'Potwierdzenie doręczenia',
        SpecialService::S_SAS => 'Doręczenie do sąsiada',
        SpecialService::S_ODB => 'Odbiór własny',
        SpecialService::S_UTIL => 'Utylizacja (nieodebranej) przesyłki',
    );
}

abstract class Country
{
    const PL = 'PL';
    const AT = 'AT';
    const BE = 'BE';
    const BG = 'BG';
    const HR = 'HR';
    const CY = 'CY';
    const CZ = 'CZ';
    const DK = 'DK';
    const EE = 'EE';
    const FI = 'FI';
    const FR = 'FR';
    const GR = 'GR';
    const ES = 'ES';
    const NL = 'NL';
    const IE = 'IE';
    const LT = 'LT';
    const LU = 'LU';
    const LV = 'LV';
    const MT = 'MT';
    const MC = 'MC';
    const DE = 'DE';
    const PT = 'PT';
    const RO = 'RO';
    const SK = 'SK';
    const SI = 'SI';
    const SE = 'SE';
    const HU = 'HU';
    const GB = 'GB';
    const IT = 'IT';


    public static $Descriptions = array
    (
        Country::PL => 'Polska',
        Country::AT => 'Austria',
        Country::BE => 'Belgia',
        Country::BG => 'Bułgaria',
        Country::HR => 'Chorwacja',
        Country::CY => 'Cypr',
        Country::CZ => 'Czechy',
        Country::DK => 'Dania',
        Country::EE => 'Estonia',
        Country::FI => 'Finlandia',
        Country::FR => 'Francja',
        Country::GR => 'Grecja',
        Country::ES => 'Hiszpania',
        Country::NL => 'Holandia',
        Country::IE => 'Irlandia',
        Country::LT => 'Litwa',
        Country::LU => 'Luksemburg',
        Country::LV => 'Łotwa',
        Country::MT => 'Malta',
        Country::MC => 'Monako',
        Country::DE => 'Niemcy',
        Country::PT => 'Portugalia',
        Country::RO => 'Rumunia',
        Country::SK => 'Słowacja',
        Country::SI => 'Słowenia',
        Country::SE => 'Szwecja',
        Country::HU => 'Węgry',
        Country::GB => 'Wielka Brytania',
        Country::IT => 'Włochy',
    );

    public static $Currency = array
    (
        Country::PL => 'PLN',
        Country::AT => 'EUR',
        Country::BE => 'EUR',
        Country::BG => 'BGN',
        Country::HR => 'HRK',
        Country::CY => 'EUR',
        Country::CZ => 'CZK',
        Country::DK => 'DKK',
        Country::EE => 'EUR',
        Country::FI => 'EUR',
        Country::FR => 'EUR',
        Country::GR => 'EUR',
        Country::ES => 'EUR',
        Country::NL => 'EUR',
        Country::IE => 'EUR',
        Country::LT => 'EUR',
        Country::LU => 'EUR',
        Country::LV => 'EUR',
        Country::MT => 'EUR',
        Country::MC => 'EUR',
        Country::DE => 'EUR',
        Country::PT => 'EUR',
        Country::RO => 'RON',
        Country::SK => 'EUR',
        Country::SI => 'EUR',
        Country::SE => 'SEK',
        Country::HU => 'HUF',
        Country::GB => 'GBP',
        Country::IT => 'EUR',
    );
}

?>