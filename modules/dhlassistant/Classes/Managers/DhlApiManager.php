<?php

namespace DhlAssistant\Classes\Managers;

use DhlAssistant\Core;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Dhl\Enums;

class DhlApiManager
{
    public static $S_DhlApis = [];

    /**
     * Autoload
     */
    public static function __AutoloadInit()
    {
        foreach (Enums\DhlWebApi::$Descriptions as $api_code => $api_name) {
            $api = null;
            $filepath = Core::$BASEDIR . '/Classes/Dhl/Apis/' . $api_code . '.php';

            if (is_file($filepath)) {
                require_once($filepath);
                self::$S_DhlApis[$api_code] = $api;
            }
        }
    }

    /**
     * @param $sApiCode
     * @param bool $bRaiseException
     * @return mixed|null
     * @throws Exceptions\SourceLoggedException
     */
    public static function GetDhlApiByCode($sApiCode, $bRaiseException = true)
    {
        if (!isset(self::$S_DhlApis[$sApiCode])) {
            if ($bRaiseException) {
                throw new Exceptions\SourceLoggedException("Nieznany kod API: {$sApiCode} !");
            } else {
                return null;
            }
        }

        return self::$S_DhlApis[$sApiCode];
    }

    /**
     * @param $sServiceCode
     * @return DataModels\DhlApi|null
     * @throws Exceptions\SourceLoggedException
     */
    public static function GetDhlApiForServiceCode($sServiceCode)
    {
        if (!self::$S_DhlApis) {
            throw new Exceptions\SourceLoggedException("Brak zdefiniowanych API !");
        }

        foreach (self::$S_DhlApis as $api) /* @var $api DataModels\DhlApi */ {
            if ($api->IsAvailableServiceByCode($sServiceCode)) {
                return $api;
            }
        }

        return null;
    }

    /**
     * @return array
     * @throws Exceptions\SourceLoggedException
     */
    public static function GetList()
    {
        if (!self::$S_DhlApis) {
            throw new Exceptions\SourceLoggedException("Brak zdefiniowanych API !");
        }

        return self::$S_DhlApis;
    }
}

?>