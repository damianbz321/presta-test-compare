<?php

namespace DhlAssistant\Classes\DataModels;

use DhlAssistant\Core\Exceptions;
use DhlAssistant\Core\Interfaces;
use DhlAssistant\Classes\Dhl\Enums;

class DhlCountryService implements Interfaces\ICanBeCloned
{
    public $Code = '';
    protected $_oDhlCountry = null;
    public $PostalCodeMaxLength = 5;
    public $AvailableSpecialServices = null;
    public $AvailableShippingPaymentTypes = [];
    public $AvailableLabelTypes = [];
    public $AvailablePackageTypes = [];
    public $AllowNst = false;
    public $AllowNstForParcel = false;
    public $AllowEuroReturn = false;
    public $CodRequireUbezp = false;
    public $AllowParcelShop = false;
    public $AllowCodForParcelShop = false;
    public $AllowParcelLocker = false;
    public $AllowCodForParcelLocker = false;
    public $RequirePostnummerForParcelLocker = false;
    public $RequirePostalCodeForParcel = false;
    public $ParcelShopOnlyService = false;
    public $AllowSearchParcelShopByRange = false;
    public $AllowSearchParcelByMap = false;
    public $AllowPostalCodesServicesInfoForReceiver = false;

    /**
     * @param $sServiceCode
     * @param DhlCountry $oDhlCountry
     * @param bool $bCheckService
     * @throws Exceptions\LoggedException
     */
    public function __construct(
        $sServiceCode,
        DhlCountry $oDhlCountry,
        $bCheckService = true
    ) {
        if ($bCheckService && !isset(Enums\ServiceType::$Descriptions[$sServiceCode])) {
            throw new Exceptions\LoggedException(
                "Próba użycia usługi o niezdefiniowanym kodzie: '{$sServiceCode}'!"
            );
        }

        $this->Code = $sServiceCode;
        $this->_oDhlCountry = $oDhlCountry;
        $this->AvailableSpecialServices = new ShipmentSpecialServices();
    }

    /**
     * Clone method.
     */
    public function __clone()
    {
        $this->AvailableSpecialServices = clone $this->AvailableSpecialServices;
    }

    /**
     * @return string
     */
    public function GetName()
    {
        return Enums\ServiceType::$Descriptions[$this->Code];
    }

    /**
     * @return DhlCountry|null
     */
    public function GetCountry()
    {
        return $this->_oDhlCountry;
    }
}

?>