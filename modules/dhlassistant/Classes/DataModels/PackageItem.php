<?php

namespace DhlAssistant\Classes\DataModels;

use DhlAssistant\Core\Models;
use DhlAssistant\Core\Interfaces;
use DhlAssistant\Classes\Dhl\Enums;

class PackageItem extends Models\StdDataClass implements Interfaces\ICanBeCloned
{
    public $Id = null;
    public $Type = '';
    public $Weight = null;
    public $Width = null;
    public $Height = null;
    public $Length = null;
    public $Quantity = 1;
    public $NonStandard = false;
    public $EuroReturn = false;

    protected static $S_aDataFields = [
        'Db' => ['Type', 'Weight', 'Width', 'Height', 'Length', 'Quantity', 'NonStandard', 'EuroReturn'],
        'PostGet' => ['Type', 'Weight', 'Width', 'Height', 'Length', 'Quantity', 'NonStandard', 'EuroReturn'],
        'PostSet' => ['Type', 'Weight', 'Width', 'Height', 'Length', 'Quantity', 'NonStandard', 'EuroReturn']
    ];

    protected static $S_GetFilters = [
        'Db' => [
            'NonStandard' => '|ToInt',
            'EuroReturn' => '|ToInt',
        ],
    ];

    protected static $S_SetFilters = [
        'Db' => [
            'NonStandard' => '|ToBool',
            'EuroReturn' => '|ToBool',
        ],
        'PostSet' => [
            'Weight' => '|ToNInt',
            'Width' => '|ToNInt',
            'Height' => '|ToNInt',
            'Length' => '|ToNInt',
            'Quantity' => '|ToInt',
            'NonStandard' => '|ToBool',
            'EuroReturn' => '|ToBool',
        ],
    ];

    /**
     * Clone method.
     */
    public function __clone()
    {
        $this->Id = null;
    }

    /**
     * @param Shipment $oShipment
     * @return Models\ValidationResult
     */
    public function ValidateCheckLegal(Shipment $oShipment)
    {
        $result = new Models\ValidationResult();
        $dcs = $oShipment->GetTargetCountryService();

        if (!in_array($this->Type, $dcs->AvailablePackageTypes)) {
            $result->AddError('Type', "Wybrany typ przesyłki nie jest dostępny")->Fail();
        }

        if ($this->Type !== Enums\PackageType::ENVELOPE) {
            if (!$this->Weight) {
                $result->AddError('Weight', 'Nie może być puste')->Fail();
            }

            if (!$this->Width) {
                $result->AddError('Width', 'Nie może być puste')->Fail();
            }

            if (!$this->Height) {
                $result->AddError('Height', 'Nie może być puste')->Fail();
            }

            if (!$this->Length) {
                $result->AddError('Length', 'Nie może być puste')->Fail();
            }
        }

        if ($this->NonStandard && !$dcs->AllowNst) {
            $result->AddError('NonStandard', "Wybrana opcja nie jest dostępna")->Fail();
        }

        if ($this->NonStandard
            && ($oShipment->SendToParcelShop || $oShipment->SendToParcelLocker)
            && !$dcs->AllowNstForParcel
            && $oShipment->ServiceType != Enums\ServiceType::EK
        ) {
            $parcel_name = 'Parcelshop';

            if ($oShipment->SendToParcelLocker) {
                $parcel_name = 'Parcelstation';
            }
            $result->AddError(
                'NonStandard',
                "Wybrana opcja nie jest dostępna przy wysyłce do {$parcel_name}"
            )->Fail();
        }

        if ($this->EuroReturn && $this->Type != Enums\PackageType::PALLET) {
            $result->AddError('EuroReturn', "Wybrana opcja nie jest dostępna")->Fail();
        }

        if ($this->EuroReturn && !$dcs->AllowEuroReturn) {
            $result->AddError('EuroReturn', "Wybrana opcja nie jest dostępna")->Fail();
        }

        return $result;
    }

    /**
     * @return Models\ValidationResult
     */
    public function ValidatePreset()
    {
        $result = new Models\ValidationResult();

        if ($this->EuroReturn && $this->Type != Enums\PackageType::PALLET) {
            $result->AddError('EuroReturn', "Wybrana opcja nie jest dostępna")->Fail();
        }

        return $result;
    }
}

?>