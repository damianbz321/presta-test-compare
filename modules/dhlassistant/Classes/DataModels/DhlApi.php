<?php

namespace DhlAssistant\Classes\DataModels;

use DhlAssistant\Core\Exceptions;
use DhlAssistant\Classes\Managers;

class DhlApi
{
    public $Code = '';
    public $Name = '';
    public $FullName = '';
    public $SupportedServiceCodes = [];
    protected $_AvailableServiceCodes = null;
    public $WsdlUrl = '';
    public $ConnectionIdent = '';
    public $ApiWrapperName = '';
    public $ApiClassContainer = '';
    public $SupportedFeatures = [];

    /**
     * @param string $sServiceCode
     * @return boolean
     */
    public function IsAvailableServiceByCode($sServiceCode)
    {
        return in_array($sServiceCode, $this->GetAvailableServiceCodes());
    }

    /**
     * @return DhlCountryService
     */
    public function GetAvailableServiceCodes()
    {
        if ($this->_AvailableServiceCodes === null) {
            $this->_AvailableServiceCodes = [];
            $all_available_services = [];

            foreach (Managers\DhlCountryManager::GetList() as $country) {
                $all_available_services = array_replace($all_available_services, $country->GetServices());
            }

            $all_available_service_codes = array_keys($all_available_services);

            if ($this->SupportedServiceCodes) {
                foreach ($this->SupportedServiceCodes as $service_code) {
                    if (in_array($service_code, $all_available_service_codes)) {
                        $this->_AvailableServiceCodes[] = $service_code;
                    }
                }
            }
        }

        return $this->_AvailableServiceCodes;
    }

    /**
     * @return string
     * @throws Exceptions\SourceLoggedException
     */
    public function GetWrapperClassPath()
    {
        $wrapper_class_path = "DhlAssistant\Classes\Dhl\\{$this->ApiWrapperName}";

        if (!class_exists($wrapper_class_path)) {
            throw new Exceptions\SourceLoggedException(
                "Brak zdefiniowanego wrappera dla API {$this->Name}!"
            );
        }

        return $wrapper_class_path;
    }

    /**
     * @param $sFeatureName
     * @return bool
     */
    public function IsFeatureSupported($sFeatureName)
    {
        return in_array($sFeatureName, $this->SupportedFeatures);
    }

    /**
     * @return array
     * @throws Exceptions\SourceLoggedException
     */
    public function GetAvailableCountries()
    {
        $result = [];
        $available_service_codes = $this->GetAvailableServiceCodes();

        /* @var $country DataModels\DhlCountry */
        foreach (Managers\DhlCountryManager::GetList() as $country) {
            if ($country->IsSupportingAnyOfServiceCodes($available_service_codes)) {
                $country = clone $country;

                foreach ($country->AvailableServices as $service_code => $service) {
                    if (!in_array($service_code, $available_service_codes)) {
                        unset($country->AvailableServices[$service_code]);
                    }
                }

                $result[$country->Code] = $country;
            }
        }

        return $result;
    }

    /**
     * @param $sCountryCode
     * @return bool
     * @throws Exceptions\SourceLoggedException
     */
    public function IsAvailableCountryByCode($sCountryCode)
    {
        return isset($this->GetAvailableCountries()[$sCountryCode]);
    }
}

?>