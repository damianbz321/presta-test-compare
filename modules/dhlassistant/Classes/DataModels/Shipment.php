<?php

namespace DhlAssistant\Classes\DataModels;

use DhlAssistant\Core;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Core\Interfaces;
use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes;
use DhlAssistant\Classes\Managers;
use DhlAssistant\Classes\Dhl\Enums;

class Shipment extends Models\StdDataClass implements Interfaces\ValidatableObject
{
    public $Id = null;
    public $IdDhlUser = 1;
    public $IdSource = null;
    public $DhlShipmentId = null;
    public $DhlOrderId = null;
    public $DhlShipmentCreationDateTime = null;

    public $DropOffType = '';
    public $ServiceType = '';
    public $LabelType = '';
    public $Content = '';
    public $Comment = '';
    public $Reference = '';
    public $ShippingPaymentType = Enums\ShippingPaymentType::SHIPPER;
    public $BillingAccountNumber = null;
    public $PaymentType = Enums\PaymentType::BANK_TRANSFER;
    public $CostsCenter = '';

    /**
     * @var ShipmentSpecialServices
     */
    public $SpecialServices;
    public $ShipmentDate = null;
    public $ShipmentStartHour = null;
    public $ShipmentEndHour = null;

    /**
     * @var ShipmentSide
     */
    public $Shipper;

    /**
     * @var ShipmentSide
     */
    public $Receiver;

    /**
     * @var Address
     */
    public $Neighbour;

    /**
     * @var PackageItem
     */
    public $Package;
    public $CreationDateTime = null;
    public $ModificationDateTime = null;
    public $ReceiverNick = "";
    public $SendToParcelShop = false;
    public $SendToParcelLocker = false;
    public $ParcelIdent = "";
    public $Postnummer = "";
    public $ParcelPostalCode = "";
    public $HasError = false;
    public $ErrorMessage = '';
    public $IdShipperPreset = null;
    public $IdShipmentPreset = null;

    protected static $S_aDataFields = [
        'Db' => [
            'IdDhlUser',
            'IdSource',
            'DhlShipmentId',
            'DhlOrderId',
            'DhlShipmentCreationDateTime',
            'DropOffType',
            'ServiceType',
            'LabelType',
            'Content',
            'Comment',
            'Reference',
            'ShippingPaymentType',
            'BillingAccountNumber',
            'PaymentType',
            'CostsCenter',
            'ShipmentDate',
            'ShipmentStartHour',
            'ShipmentEndHour',
            'CreationDateTime',
            'ModificationDateTime',
            'ReceiverNick',
            'SendToParcelShop',
            'SendToParcelLocker',
            'ParcelIdent',
            'Postnummer',
            'ParcelPostalCode',
            'HasError',
            'ErrorMessage',
            'IdShipperPreset',
            'IdShipmentPreset',
            'SenderName',
            'SenderCompany',
            'SenderPostalCode',
            'SenderCity',
            'SenderStreet',
            'SenderHouseNumber',
            'SenderApartmentNumber',
            'SenderPhone',
            'SenderEmail'
        ],
        'PostGet' => [
            'Id',
            'DropOffType',
            'ServiceType',
            'LabelType',
            'Content',
            'Comment',
            'Reference',
            'ShippingPaymentType',
            'BillingAccountNumber',
            'PaymentType',
            'CostsCenter',
            'ShipmentDate',
            'ShipmentStartHour',
            'ShipmentEndHour',
            'CreationDateTime',
            'ModificationDateTime',
            'ReceiverNick',
            'SendToParcelShop',
            'SendToParcelLocker',
            'ParcelIdent',
            'Postnummer',
            'ParcelPostalCode',
            'HasError',
            'ErrorMessage',
            'DhlShipmentId',
            'DhlOrderId',
            'DhlShipmentCreationDateTime',
            'DhlTrackingLink',
            'SenderName',
            'SenderCompany',
            'SenderPostalCode',
            'SenderCity',
            'SenderStreet',
            'SenderHouseNumber',
            'SenderApartmentNumber',
            'SenderPhone',
            'SenderEmail'
        ],
        'PostSet' => [
            'DropOffType',
            'ServiceType',
            'LabelType',
            'Content',
            'Comment',
            'Reference',
            'ShippingPaymentType',
            'BillingAccountNumber',
            'PaymentType',
            'CostsCenter',
            'ShipmentDate',
            'ShipmentStartHour',
            'ShipmentEndHour',
            'ReceiverNick',
            'SendToParcelShop',
            'SendToParcelLocker',
            'ParcelIdent',
            'Postnummer',
            'ParcelPostalCode',
            'SenderName',
            'SenderCompany',
            'SenderPostalCode',
            'SenderCity',
            'SenderStreet',
            'SenderHouseNumber',
            'SenderApartmentNumber',
            'SenderPhone',
            'SenderEmail'
        ],
    ];

    protected static $S_GetFilters = [
        'Db' => [
            'DhlShipmentCreationDateTime' => '|ToSqlDateTimeString',
            'ShipmentDate' => '|ToDateString',
            'ShipmentStartHour' => '|ToHMString',
            'ShipmentEndHour' => '|ToHMString',
            'CreationDateTime' => '|ToSqlDateTimeString',
            'ModificationDateTime' => '|ToSqlDateTimeString',
            'SendToParcelShop' => '|ToInt',
            'SendToParcelLocker' => '|ToInt',
            'HasError' => '|ToInt',
        ],
        'PostGet' => [
            'ShipmentDate' => '|ToDateString',
            'ShipmentStartHour' => '|ToHMString',
            'ShipmentEndHour' => '|ToHMString',
            'CreationDateTime' => '|ToSqlDateTimeString',
            'ModificationDateTime' => '|ToSqlDateTimeString',
            'DhlShipmentCreationDateTime' => '|ToSqlDateTimeString',
        ],
    ];

    protected static $S_SetFilters = [
        'Db' => [
            'DhlShipmentCreationDateTime' => '|ToNDateTime',
            'ShipmentDate' => '|ToNDateTime',
            'ShipmentStartHour' => '|ToNDateTime',
            'ShipmentEndHour' => '|ToNDateTime',
            'CreationDateTime' => '|ToNDateTime',
            'ModificationDateTime' => '|ToNDateTime',
            'SendToParcelShop' => '|ToBool',
            'SendToParcelLocker' => '|ToBool',
            'HasError' => '|ToBool',
        ],
        'PostSet' => [
            'ShipmentDate' => '|ToNDateTime',
            'ShipmentStartHour' => '|ToNDateTime',
            'ShipmentEndHour' => '|ToNDateTime',
            'SendToParcelShop' => '|ToBool',
            'SendToParcelLocker' => '|ToBool',
            'BillingAccountNumber' => '|ToNInt',
            'PostalCode' => null, //Defined by __AutoloadInit
        ],
    ];

    /**
     * @throws Exceptions\LoggedException
     */
    public function __construct()
    {
        $this->SpecialServices = new ShipmentSpecialServices();
        $this->Shipper = new ShipmentSide();
        $this->Receiver = new ShipmentSide();
        $this->Neighbour = new Address();
        $this->Package = new PackageItem();

        $this->ShipmentDate = new \DateTime(date('Y-m-d'));
        $this->ShipmentStartHour = Wrappers\ConfigWrapper::Get('ShipmentHoursStart');
        $this->ShipmentEndHour = Wrappers\ConfigWrapper::Get('ShipmentHoursEnd');
    }

    /**
     * Autoload.
     */
    public static function __AutoloadInit()
    {
        self::$S_SetFilters['PostSet']['PostalCode'] = function ($sNewValue) {
            return str_replace('-', '', $sNewValue);
        };
    }

    /**
     * @return Models\TreeDataObjectInfo
     * @throws Exceptions\LoggedException
     */
    public static function GetTreeDataObjectInfo()
    {
        $info = parent::GetTreeDataObjectInfo();
        $info->AddDependedObject(
            ShipmentSpecialServices::GetTreeDataObjectInfo(),
            'SpecialServices',
            'IdShipmentSpecialServices'
        );
        $info->AddDependedObject(
            ShipmentSide::GetTreeDataObjectInfo(),
            'Shipper',
            'IdShipper'
        );
        $info->AddDependedObject(
            ShipmentSide::GetTreeDataObjectInfo(),
            'Receiver',
            'IdReceiver'
        );
        $info->AddDependedObject(
            Address::GetTreeDataObjectInfo(),
            'Neighbour',
            'IdNeighbour'
        );
        $info->AddDependedObject(
            PackageItem::GetTreeDataObjectInfo(),
            'Package',
            'IdPackageItem'
        );

        return $info;
    }

    /**
     * @return bool
     */
    public function IsSended()
    {
        return $this->DhlShipmentId !== null;
    }

    /**
     * @return bool
     */
    public function HasBookedCourier()
    {
        return $this->IsSended() && $this->DhlOrderId != null;
    }

    /**
     * @param bool $bRaiseException
     * @return string
     * @throws Exceptions\LoggedException
     */
    public function GetTargetCountryCode($bRaiseException = true)
    {
        if ($this->Receiver->Address->Country === '') {
            throw new Exceptions\LoggedException("Nie wybrano kraju docelowego dla przesyłki!");
        }

        return $this->Receiver->Address->Country;
    }

    /**
     * @param bool $bRaiseException
     * @return DhlCountry|NULL
     * @throws Exceptions\LoggedException
     * @throws Exceptions\SourceLoggedException
     */
    public function GetTargetCountry($bRaiseException = true)
    {
        return Managers\DhlCountryManager::GetCountryByCode(
            $this->GetTargetCountryCode($bRaiseException),
            $bRaiseException
        );
    }

    /**
     * @param bool $bRaiseException
     * @return DhlCountryService|null
     * @throws Exceptions\LoggedException
     * @throws Exceptions\SourceLoggedException
     */
    public function GetTargetCountryService($bRaiseException = true)
    {
        $country = $this->GetTargetCountry($bRaiseException);

        if ($country === null) {
            return null;
        }
        $country_service = $country->GetServiceByCode($this->ServiceType, $bRaiseException);

        return $country_service;
    }

    /**
     * @param ShipmentPreset $oPreset
     * @return bool
     * @throws Exceptions\LoggedException
     * @throws Exceptions\SourceLoggedException
     */
    public function CheckShipmentPresetCompatibile(ShipmentPreset $oPreset)
    {
        $dc = Managers\DhlCountryManager::GetCountryByCode($this->GetTargetCountryCode());

        if (!$dc->IsSupportingServiceCode($oPreset->ServiceType)) {
            return false;
        }

        $dcs = $dc->GetServiceByCode($oPreset->ServiceType);

        foreach (Enums\SpecialService::$Descriptions as $service_code => $service_description) {
            $service_name = 'S_' . $service_code;

            if ($oPreset->SpecialServices->$service_name && !$dcs->AvailableSpecialServices->$service_name) {
                return false;
            }
        }

        if (!in_array($oPreset->LabelType, $dcs->AvailableLabelTypes)) {
            return false;
        }

        if (!in_array($oPreset->Package->Type, $dcs->AvailablePackageTypes)) {
            return false;
        }

        if ($oPreset->Package->NonStandard && !$dcs->AllowNst) {
            return false;
        }

        if ($oPreset->Package->NonStandard && ($this->SendToParcelShop || $this->SendToParcelLocker) && !$dcs->AllowNstForParcel) {
            return false;
        }

        if ($oPreset->Package->EuroReturn && !$dcs->AllowEuroReturn) {
            return false;
        }

        return true;
    }

    /**
     * @param ShipmentPreset $oPreset
     * @throws Exceptions\LoggedException
     * @throws Exceptions\SourceLoggedException
     */
    public function ApplyShipmentPreset(ShipmentPreset $oPreset)
    {
        $dcs = $this->GetTargetCountryService();

        if (!$this->CheckShipmentPresetCompatibile($oPreset)) {
            throw new Exceptions\LoggedException(
                "Wybrana predefinicja przesyłki jest niekompatybilna z tą przesyłką!"
            );
        }

        if ($oPreset->DropOffType !== null) {
            $this->DropOffType = $oPreset->DropOffType;
        }

        $this->ServiceType = $oPreset->ServiceType;

        if ($oPreset->LabelType !== null) {
            $this->LabelType = $oPreset->LabelType;
        }

        if (strlen($this->Content) == 0 && strlen($oPreset->Content) > 0) {
            $this->Content = $oPreset->Content;
        }

        if (strlen($oPreset->Comment) > 0) {
            $this->Comment = $oPreset->Comment;
        }

        $temp = $this->SpecialServices;
        $special_services_id = $this->SpecialServices->Id;
        $this->SpecialServices = clone $oPreset->SpecialServices;
        $this->SpecialServices->Id = $special_services_id;
        $this->SpecialServices->OriginalCurrencyUnit = $temp->OriginalCurrencyUnit;
        $this->SpecialServices->OriginalUBEZPValue = $temp->OriginalUBEZPValue;
        $this->SpecialServices->OriginalCODValue = $temp->OriginalCODValue;

        if ($temp->S_UBEZP) {
            $this->SpecialServices->S_UBEZP = true;
            $this->SpecialServices->UBEZP_Value = $temp->UBEZP_Value;
            $this->SpecialServices->UBEZP_CurrencyUnitAlert = $temp->UBEZP_CurrencyUnitAlert;
        } else {
            if ($this->SpecialServices->UBEZP_Value === null) {
                $this->SpecialServices->UBEZP_Value = $temp->UBEZP_Value;
            }
        }

        if ($temp->S_COD) {
            $this->SpecialServices->S_COD = true;
            $this->SpecialServices->COD_Value = $temp->COD_Value;
            $this->SpecialServices->COD_CurrencyUnitAlert = $temp->COD_CurrencyUnitAlert;
        } else {
            if ($this->SpecialServices->COD_Value === null) {
                $this->SpecialServices->COD_Value = $temp->UBEZP_Value;
            }
        }

        if ($this->SpecialServices->S_COD && $dcs->CodRequireUbezp) {
            $this->SpecialServices->S_UBEZP = true;
        }

        if ($oPreset->ShipmentStartHour !== null) {
            $this->ShipmentStartHour = clone $oPreset->ShipmentStartHour;
        }

        if ($oPreset->ShipmentEndHour !== null) {
            $this->ShipmentEndHour = clone $oPreset->ShipmentEndHour;
        }

        $this->Package->Type = $oPreset->Package->Type;
        $this->Package->Weight = $oPreset->Package->Weight;
        $this->Package->Width = $oPreset->Package->Width;
        $this->Package->Height = $oPreset->Package->Height;
        $this->Package->Length = $oPreset->Package->Length;
        $this->Package->Quantity = $oPreset->Package->Quantity;
        $this->Package->NonStandard = $oPreset->Package->NonStandard;
        $this->Package->EuroReturn = $oPreset->Package->EuroReturn;
        $this->IdShipmentPreset = $oPreset->Id;
    }

    /**
     * @param ShipperPreset $oPreset
     */
    public function ApplyShipperPreset(ShipperPreset $oPreset)
    {
        $this->Shipper = clone $oPreset->Shipper;
        $this->BillingAccountNumber = $oPreset->BillingAccountNumber;

        if (strlen($this->CostsCenter) == 0 && strlen($oPreset->CostsCenter) > 0) {
            $this->CostsCenter = $oPreset->CostsCenter;
        }

        $this->IdShipperPreset = $oPreset->Id;
    }

    /**
     * @return array
     * @throws Exceptions\LoggedException
     * @throws Exceptions\SourceLoggedException
     */
    public function GetAvailableLabelTypes()
    {
        $available_labels = [];

        if ($this->IsSended()) {
            if ($this->LabelType === Enums\LabelType::LP) {
                $available_labels = [Enums\LabelType::LP];
            } else {
                $available_labels = $this->GetTargetCountryService()->AvailableLabelTypes;

                if (in_array(Enums\LabelType::LP, $available_labels)) {
                    $available_labels = array_diff($available_labels, [Enums\LabelType::LP]);
                }
            }
        } else {
            $available_labels = $this->GetTargetCountryService()->AvailableLabelTypes;
        }

        return $available_labels;
    }

    /**
     * @param bool $bRaiseException
     * @return mixed|null
     * @throws Exceptions\LoggedException
     * @throws Exceptions\SourceLoggedException
     */
    public function GetDhlUser($bRaiseException = true)
    {
        if ($this->IdDhlUser === null) {
            if ($bRaiseException) {
                throw new Exceptions\LoggedException("Przesyłka nie ma zdefiniowanego użytkownika DHL!");
            } else {
                return null;
            }
        }

        return Managers\DhlUserManager::GetById($this->IdDhlUser);
    }

    /**
     * @param null $sContextName
     * @param array $aValues
     * @return Core\DhlAssistant\Core\Models\ValidationResult
     * @throws Exceptions\LoggedException
     */
    public function Validate($sContextName = null, $aValues = [])
    {
        $known_contexts = ['CheckLegal'];

        if (!in_array($sContextName, $known_contexts)) {
            throw new Exceptions\LoggedException(
                "Nie znany kontekst walidacji Shipment: '{$sContextName}'!"
            );
        }

        $method_name = 'Validate' . $sContextName;

        return $this->$method_name($aValues);
    }

    /**
     * @return Models\ValidationResult
     * @throws Exceptions\LoggedException
     * @throws Exceptions\SourceLoggedException
     */
    public function ValidateCheckLegal()
    {
        $result = new Models\ValidationResult();
        $dcs = $this->GetTargetCountryService();

        if ($this->IsSended()) {
            throw new Exceptions\LoggedException("Ta przesyłka została już zlecona!");
        }

        $service_type_name = Enums\ServiceType::$Descriptions[$this->ServiceType];
        if (!in_array($this->ServiceType, $this->GetDhlUser()->GetAvailableServiceCodes())) {
            $result->AddError(
                'ServiceType',
                "Wybrany użytkownik DHL nie wspiera tej usługi"
            )->Fail();
        }

        if (!in_array($this->LabelType, $dcs->AvailableLabelTypes)) {
            $result->AddError(
                'LabelType',
                "Wybrany typ etykiety nie jest dostępny"
            )->Fail();
        }

        $shipment_payment_type_name = Enums\ShippingPaymentType::$Descriptions[$this->ShippingPaymentType];

        if (!isset($dcs->AvailableShippingPaymentTypes[$this->ShippingPaymentType])) {
            $result->AddError(
                'ShippingPaymentType',
                "Wybrane rozliczenie przesyłki nie jest dostępne"
            )->Fail();
        }

        if (!in_array($this->PaymentType, $dcs->AvailableShippingPaymentTypes[$this->ShippingPaymentType])) {
            $payment_type_name = Enums\PaymentType::$Descriptions[$this->PaymentType];

            $result->AddError(
                'PaymentType',
                "Rozliczenie przesyłki wybranego typu przez '{$shipment_payment_type_name}' nie jest dostępne"
            )->Fail();
        }

        if ($this->SendToParcelShop || $this->SendToParcelLocker) {
            if ($this->SendToParcelShop && !$dcs->AllowParcelShop) {
                $result->AddError(
                    'SendToParcelShop',
                    '"Dostarczenie do Parcelshop nie jest dostępne w tej usłudze'
                )->Fail();
            }

            if ($this->SendToParcelLocker && !$dcs->AllowParcelLocker) {
                $result->AddError(
                    'SendToParcelShop',
                    '"Dostarczenie do Parcelstation nie jest dostępne w tej usłudze'
                )->Fail();
            }

            if ($this->SendToParcelShop && $this->SendToParcelLocker) {
                $result->AddError(
                    'SendToParcelShop',
                    'Nie można zlecić jednocześnie wysyłki do Parcelshop i Parcelstation'
                )->Fail();

                $result->AddError(
                    'SendToParcelLocker',
                    'Nie można zlecić jednocześnie wysyłki do Parcelshop i Parcelstation'
                )->Fail();
            }

            if (strlen($this->ParcelIdent) == 0) {
                $result->AddError(
                    'ParcelIdent', 'Nie może być puste'
                )->Fail();
            }

            if ($this->SendToParcelLocker && $dcs->RequirePostnummerForParcelLocker) {
                if (strlen($this->Postnummer) == 0) {
                    $result->AddError('Postnummer', 'Nie może być puste')->Fail();
                } else {
                    if (!Core\Validators::IsInt($this->Postnummer)) {
                        $result->AddError('Postnummer', 'Nieprawidłowa wartość')->Fail();
                    }
                }
            }

            if ($dcs->RequirePostalCodeForParcel) {
                if (strlen($this->ParcelPostalCode) == 0) {
                    $result->AddError('ParcelPostalCode', 'Nie może być puste')->Fail();
                } else {
                    if (!Core\Validators::MaxLen($this->ParcelPostalCode, $dcs->PostalCodeMaxLength)) {
                        $result->AddError('ParcelPostalCode', 'Wartość zbyt długa')->Fail();
                    }
                }
            }
        }

        $result->Merge($this->SpecialServices->ValidateCheckLegal($this), 'SpecialServices');
        $result->Merge($this->Shipper->ValidateShipper(), 'Shipper');
        $result->Merge($this->Receiver->ValidateCheckLegalReceiver($this), 'Receiver');
        $result->Merge($this->Neighbour->ValidateCheckLegalNeighbour($this), 'Neighbour');
        $result->Merge($this->Package->ValidateCheckLegal($this), 'Package');

        if ($this->HasError) {
            foreach (explode("\n", $this->ErrorMessage) as $err_msg) {
                $result->AddGeneralError($err_msg);
            }
            $result->Fail();
        }

        return $result;
    }

    /**
     * Clear errors.
     */
    public function ClearErrors()
    {
        $this->HasError = false;
        $this->ErrorMessage = '';
        $this->Receiver->Address->ParseAlert = false;
        $this->SpecialServices->UBEZP_CurrencyUnitAlert = false;
        $this->SpecialServices->COD_CurrencyUnitAlert = false;
    }

    /**
     * @param $sContextName
     * @param false $bWithDependedObjects
     * @param false $bWithKeys
     * @return array
     */
    public function GetTreeDataValues($sContextName, $bWithDependedObjects = false, $bWithKeys = false)
    {
        if ($sContextName == 'Db') {
            if ($this->Id) {
                $this->ModificationDateTime = new \DateTime("now");
            } else {
                $this->CreationDateTime = new \DateTime("now");
            }
        }

        $result = parent::GetTreeDataValues($sContextName, $bWithDependedObjects, $bWithKeys);

        return $result;
    }

    /**
     * @param $sVarName
     * @return false|mixed|string|void
     */
    public function __get($sVarName)
    {
        switch ($sVarName) {
            case 'DhlTrackingLink':
                if ($this->IsSended()) {
                    return Wrappers\DhlWrapper::GetTrackLink($this);
                } else {
                    return '';
                }
        }
    }
}

?>