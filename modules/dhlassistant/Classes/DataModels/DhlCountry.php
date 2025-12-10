<?php

namespace DhlAssistant\Classes\DataModels;

use DhlAssistant\Core\Exceptions;
use DhlAssistant\Core\Interfaces;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Dhl\Enums;

class DhlCountry implements Interfaces\ICanBeCloned
{
    public $Code = '';
    public $Name = '';
    public $Currency = '';
    public $AvailableServices = [];

    /**
     * @param $sCode
     * @param array $aAvailableServices
     * @throws Exceptions\SourceLoggedException
     */
    public function __construct($sCode, $aAvailableServices = [])
    {
        if (!isset(Enums\Country::$Descriptions[$sCode]) || !isset(Enums\Country::$Currency[$sCode])) {
            throw new Exceptions\SourceLoggedException("Nie zdefiniowany kraj o kodzie '{$sCode}'!");
        }

        $this->Code = $sCode;
        $this->Name = Enums\Country::$Descriptions[$sCode];
        $this->Currency = Enums\Country::$Currency[$sCode];
        $this->AddServices($aAvailableServices);
    }

    /**
     * Clone method.
     */
    public function __clone()
    {
        if ($this->AvailableServices) {
            foreach ($this->AvailableServices as $key => $value) {
                $this->AvailableServices[$key] = clone $value;
            }
        }
    }

    /**
     * @param DhlCountryService $oDhlCountryService
     */
    public function AddService(DhlCountryService $oDhlCountryService)
    {
        $this->AvailableServices[$oDhlCountryService->Code] = $oDhlCountryService;
    }

    /**
     * @param $aAvailableServices
     */
    public function AddServices($aAvailableServices)
    {
        if ($aAvailableServices && is_array($aAvailableServices)) {
            foreach ($aAvailableServices as $service) {
                $this->AddService($service);
            }
        }
    }

    /**
     * @return array
     */
    public function GetServices()
    {
        return $this->AvailableServices;
    }

    /**
     * @param string $sServiceCode
     * @param bool $bRaiseException
     * @return DhlCountryService|NULL
     * @throws Exceptions\LoggedException
     */
    public function GetServiceByCode($sServiceCode, $bRaiseException = true)
    {
        if (!$this->IsSupportingServiceCode($sServiceCode)) {
            if ($bRaiseException) {
                $service_name = Enums\ServiceType::$Descriptions[$sServiceCode];
                throw new Exceptions\SourceLoggedException(
                    "Kraj {$this->Name} nie obsługuje usługi '{$service_name}'!"
                );
            } else {
                return null;
            }
        }

        return $this->AvailableServices[$sServiceCode];
    }

    /**
     * @param string $sServiceCode
     * @return bool
     */
    public function IsSupportingServiceCode($sServiceCode)
    {
        return in_array($sServiceCode, array_keys($this->AvailableServices));
    }

    /**
     * @param array $aServiceCodes
     * @return bool
     */
    public function IsSupportingAnyOfServiceCodes($aServiceCodes)
    {
        foreach ($aServiceCodes as $service_code) {
            if ($this->IsSupportingServiceCode($service_code)) {
                return true;
            }
        }
        return false;
    }
}

?>