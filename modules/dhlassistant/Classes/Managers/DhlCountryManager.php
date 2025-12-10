<?php

namespace DhlAssistant\Classes\Managers;

use DhlAssistant\Core;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Wrappers\ConfigWrapper;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Dhl\Enums;

/**
 *
 */
class DhlCountryManager
{
    public static $S_DhlCountries = [];

    /**
     * AutoloadInit.
     */
    public static function __AutoloadInit()
    {
        foreach (Enums\Country::$Descriptions as $country_code => $country_name) {
            $country = null;
            $filepath = Core::$BASEDIR . '/Classes/Dhl/Countries/' . $country_code . '.php';

            if (is_file($filepath)) {
                require_once($filepath);
                self::$S_DhlCountries[$country_code] = $country;
            }
        }
    }

    /**
     * @param $sCountryCode
     * @param bool $bRaiseException
     * @return mixed|null
     * @throws Exceptions\SourceLoggedException
     */
    public static function GetCountryByCode($sCountryCode, $bRaiseException = true)
    {
        if (!isset(self::$S_DhlCountries[$sCountryCode])) {
            if ($bRaiseException) {
                throw new Exceptions\SourceLoggedException("Nieznany kraj o kodzie {$sCountryCode}!");
            } else {
                return null;
            }
        }

        return self::$S_DhlCountries[$sCountryCode];
    }

    /**
     * @return array
     * @throws Exceptions\SourceLoggedException
     */
    public static function GetList()
    {
        if (!self::$S_DhlCountries) {
            throw new Exceptions\SourceLoggedException("Brak zdefiniowanych krajów!");
        }

        return self::$S_DhlCountries;
    }

    /**
     * @param $sCountryCode
     * @return bool
     */
    public static function IsAvailable($sCountryCode)
    {
        return isset(self::$S_DhlCountries[$sCountryCode]);
    }
}

?>