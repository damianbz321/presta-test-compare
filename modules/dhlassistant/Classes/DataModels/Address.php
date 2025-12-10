<?php

namespace DhlAssistant\Classes\DataModels;

use DhlAssistant\Core;
use DhlAssistant\Core\Interfaces;
use DhlAssistant\Core\Models;
use DhlAssistant\Classes\Managers;

class Address extends Models\StdDataClass implements Interfaces\ICanBeCloned
{
    public $Id = null;
    public $Name = '';
    public $PostalCode = '';
    public $City = '';
    public $Street = '';
    public $HouseNumber = '';
    public $ApartmentNumber = '';
    public $Phone = '';
    public $Email = '';
    public $Country = '';
    public $OriginalAddressString = '';
    public $ParseAlert = false;

    protected static $S_aDataFields = array
    (
        'Db' => [
            'Name',
            'PostalCode',
            'City',
            'Street',
            'HouseNumber',
            'ApartmentNumber',
            'Phone',
            'Email',
            'Country',
            'OriginalAddressString',
            'ParseAlert'
        ],
        'PostGet' => [
            'Name',
            'PostalCode',
            'City',
            'Street',
            'HouseNumber',
            'ApartmentNumber',
            'Phone',
            'Email',
            'Country',
            'OriginalAddressString',
            'ParseAlert'
        ],
        'PostSet' => [
            'Name',
            'PostalCode',
            'City',
            'Street',
            'HouseNumber',
            'ApartmentNumber',
            'Phone',
            'Email',
            'Country'
        ]
    );

    protected static $S_GetFilters = [
        'Db' => ['ParseAlert' => '|ToInt']
    ];

    protected static $S_SetFilters = [
        'Db' => ['ParseAlert' => '|ToBool'],
        'PostSet' => ['PostalCode' => null]
    ];

    /**
     * Clone method.
     */
    public function __clone()
    {
        $this->Id = null;
    }

    /**
     * @return Models\ValidationResult
     */
    public function ValidateShipper()
    {
        $result = new Models\ValidationResult();

        return $result;
    }

    /**
     * @param Shipment $oShipment
     * @return Models\ValidationResult
     * @throws Core\Exceptions\LoggedException
     * @throws Core\Exceptions\SourceLoggedException
     */
    public function ValidateCheckLegalReceiver(Shipment $oShipment)
    {
        $result = new Models\ValidationResult();
        $dcs = $oShipment->GetTargetCountryService();
        $address = $oShipment->Receiver->Address;
        $target_country = Managers\DhlCountryManager::GetCountryByCode($oShipment->GetTargetCountryCode());

        if (!$oShipment->GetDhlUser()->IsAvailableCountryByCode($target_country->Code)) {
            $result->AddError(
                'Country',
                "wybrany użytkownik DHL nie wspiera wysyłek do tego kraju docelowego"
            );
            $result->Fail();
        }

        if (!$target_country->IsSupportingServiceCode($oShipment->ServiceType)) {
            $service_type_name = Enums\ServiceType::$Descriptions[$oShipment->ServiceType];
            $result->AddError(
                'Country',
                "wybrany kraj docelowy nie obsługuje usługi {$service_type_name}"
            );
            $result->Fail();
        }

        if (!Core\Validators::MaxLen($address->PostalCode, $dcs->PostalCodeMaxLength)) {
            $result->AddError('PostalCode', 'Wartość zbyt długa');
            $result->Fail();
        }

        return $result;
    }

    /**
     * @param Shipment $oShipment
     * @return Models\ValidationResult
     */
    public function ValidateCheckLegalNeighbour(Shipment $oShipment)
    {
        $result = new Models\ValidationResult();

        if ($oShipment->SpecialServices->S_SAS) {
            $req_fields = ['Name', 'PostalCode', 'City', 'Street', 'HouseNumber'];

            foreach ($req_fields as $field_name) {
                if (!Core\Validators::IsNotNullOrEmpty($this->$field_name)) {
                    $result->AddError($field_name, 'Nie może być puste');
                    $result->Fail();
                }
            }

            if (!Core\Validators::MaxLen($this->PostalCode, 5)) {
                $result->AddError('PostalCode', 'Wartość zbyt długa');
                $result->Fail();
            }
        }

        return $result;
    }
}

?>