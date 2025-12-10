<?php

namespace DhlAssistant\Classes\DataModels;

use DhlAssistant\Core;
use DhlAssistant\Core\Interfaces;
use DhlAssistant\Core\Models;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\Managers;
use DhlAssistant\Classes\Dhl\Enums;

class DhlUser extends Models\StdDataClass implements Interfaces\ICanBeCloned
{
    public $Id = null;
    public $Name = 'Default';
    public $Dhl24Login = null;
    public $Dhl24Password = null;
    public $DhlPsLogin = null;
    public $DhlPsPassword = null;
    public $Dhl24Link = null;
    public $DhlPsLink = null;

    protected static $S_aDataFields = [
        'Db' => ['Name', 'Dhl24Login', 'Dhl24Password', 'DhlPsLogin', 'DhlPsPassword', 'Dhl24Link', 'DhlPsLink'],
        'PostGet' => ['Dhl24Login', 'DhlPsLogin', 'Dhl24Link', 'DhlPsLink'],
        'PostSet' => ['Dhl24Login', 'Dhl24Password', 'DhlPsLogin', 'DhlPsPassword', 'Dhl24Link', 'DhlPsLink'],
    ];

    protected static $S_GetFilters = [];
    protected static $S_SetFilters = [];

    /**
     * Clone method.
     */
    public function __clone()
    {
        $this->Id = null;
    }

    /**
     * @return bool
     */
    private function _HasDhl24AuthData()
    {
        return
            $this->Dhl24Login !== ''
            && $this->Dhl24Login !== null
            && $this->Dhl24Password !== ''
            && $this->Dhl24Password !== null
            && $this->Dhl24Link !== '';
    }

    /**
     * @return bool
     */
    private function _HasDhlPsAuthData()
    {
        return
            $this->DhlPsLogin !== ''
            && $this->DhlPsLogin !== null
            && $this->DhlPsPassword !== ''
            && $this->DhlPsPassword !== null
            && $this->DhlPsLink !== '';
    }

    /**
     * @param string $sApiCode
     * @return bool
     */
    public function HasAuthDataForApiCode($sApiCode)
    {
        switch ($sApiCode) {
            case Enums\DhlWebApi::DHL24:
                return $this->_HasDhl24AuthData();
                break;
            case Enums\DhlWebApi::DHLPS:
                return $this->_HasDhlPsAuthData();
                break;
            default:
                return false;
        }
    }

    /**
     * @return array<string>
     */
    public function GetSupportedApiCodesList()
    {
        $result = [];

        if ($this->_HasDhl24AuthData()) {
            $result[] = Enums\DhlWebApi::DHL24;
        }

        if ($this->_HasDhlPsAuthData()) {
            $result[] = Enums\DhlWebApi::DHLPS;
        }

        return $result;
    }

    public function GetAvailableServiceCodes()
    {
        $result = [];
        $available_api_codes = $this->GetSupportedApiCodesList();

        if (!$available_api_codes) {
            return $result;
        }

        foreach ($available_api_codes as $api_code) {
            $result = array_merge(
                $result,
                Managers\DhlApiManager::GetDhlApiByCode($api_code)->GetAvailableServiceCodes()
            );
        }

        $result = Core\Tools::ArraySortWithExampleByValue(
            $result,
            array_keys(Enums\ServiceType::$Descriptions),
            false
        );

        return $result;
    }

    /**
     * Get available countries.
     *
     * @return array
     * @throws Exceptions\SourceLoggedException
     */
    public function GetAvailableCountries()
    {
        $result = [];
        $available_service_codes = $this->GetAvailableServiceCodes();

        /* @var $country DhlCountry */
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
     * Get available country codes.
     *
     * @return array
     * @throws Exceptions\SourceLoggedException
     */
    public function GetAvailableCountryCodes()
    {
        $result = [];
        $available_service_codes = $this->GetAvailableServiceCodes();

        /* @var $country DhlCountry */
        foreach (Managers\DhlCountryManager::GetList() as $country) {
            if ($country->IsSupportingAnyOfServiceCodes($available_service_codes)) {
                $result[] = $country->Code;
            }
        }

        return array_unique($result);
    }

    /**
     * Is country available.
     *
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