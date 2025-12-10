<?php

namespace DhlAssistant\Wrappers;

use DhlAssistant\Core;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Managers;
use DhlAssistant\Classes\Dhl\Enums;

/**
 *
 */
class ConfigWrapper
{
    protected static $S_aVars = [];

    protected static $S_aWritableElements = [
        'CodPaymentModules',
    ];

    protected static $S_aHandledByFunctions = [
        'HasDefaultDhlUser',
        'DefaultDhlUser',
        'HasDefaultShipperPreset',
        'DefaultShipperPreset',
        'IsModuleConfigured',
        'BaseUrl',
        'AvailableServices',
        'AvailableCountryCodes',
        'CodPaymentModules',
    ];

    protected static $S_oDefaultDhlUser = null;
    protected static $S_oDefaultShipperPreset = null;
    protected static $S_sBaseUrl = null;
    protected static $S_aAvailableCountryCodes = null;

    /**
     * Autoload init.
     */
    public static function __AutoloadInit()
    {
        $source_config = [];
        $source_config = SourceWrapper::LoadConfig();
        self::$S_aVars = array_merge(self::$S_aVars, $source_config);

        $local_config = [];
        require_once(Core::$BASEDIR . 'LocalConfig.php');
        self::$S_aVars = array_merge(self::$S_aVars, $local_config);

        $db_config = [];
        $db_config = DbWrapper::LoadConfig();
        self::$S_aVars = array_merge(self::$S_aVars, $db_config);
        self::$S_aWritableElements = array_merge(self::$S_aWritableElements, array_keys($db_config));
    }

    /**
     * @param $sName
     * @return mixed
     * @throws Exceptions\LoggedException
     */
    public static function Get($sName)
    {
        if (isset(self::$S_aVars[$sName])) {
            return self::$S_aVars[$sName];
        }

        if (in_array($sName, self::$S_aHandledByFunctions)) {
            $func_name = '_Get' . $sName;

            return self::$func_name();
        }

        throw new Exceptions\LoggedException(
            "Próba dostępu (Get) do niezdefiniowanej zmiennej konfiguracyjnej {$sName}!"
        );
    }

    /**
     * @param $sName
     * @param $mDefault
     * @return mixed
     * @throws Exceptions\LoggedException
     */
    public static function GetOrDefault($sName, $mDefault)
    {
        if (!self::ExistsVariable($sName)) {
            return $mDefault;
        }

        return self::Get($sName);
    }

    /**
     * @param $sName
     * @param $mValue
     * @return bool|mixed
     * @throws Exceptions\LoggedException
     */
    public static function Set($sName, $mValue)
    {
        if (isset($S_aVars[$sName]) && !in_array($sName, self::$S_aWritableElements)) {
            throw new \Exception(
                "Próba zapisu (Set) do zmiennej konfiguracyjnej {$sName} typu read-only!"
            );
        }

        if (in_array($sName, self::$S_aHandledByFunctions)) {
            $func_args = func_get_args();
            unset($func_args[0]);
            $func_name = 'self::_Set' . $sName;

            return call_user_func_array($func_name, $func_args);
        } else {
            if (!isset($S_aVars[$sName])) {
                self::$S_aWritableElements[] = $sName;
            }

            self::$S_aVars[$sName] = $mValue;

            return DbWrapper::SaveConfigValue($sName, $mValue);
        }

        throw new Exceptions\LoggedException(
            "Próba dostępu (Set) do niezdefiniowanej zmiennej konfiguracyjnej {$sName}!"
        );
    }

    /**
     * @param $sName
     * @return bool
     */
    public static function ExistsVariable($sName)
    {
        return isset(self::$S_aVars[$sName]) || in_array($sName, self::$S_aHandledByFunctions);
    }

    /**
     * @param $aVars
     */
    public static function AddVariables($aVars)
    {
        if ($aVars && is_array($aVars)) {
            self::$S_aVars = array_merge(self::$S_aVars, $aVars);
        }
    }

    /**
     * @return bool
     */
    protected static function _GetHasDefaultDhlUser()
    {
        return self::$S_oDefaultDhlUser !== null || DbWrapper::Exists(DataModels\DhlUser::GetTreeDataObjectInfo(), 1);
    }

    /**
     * @return mixed|null
     * @throws Exceptions\LoggedException
     */
    protected static function _GetDefaultDhlUser()
    {
        if (self::$S_oDefaultDhlUser === null) {
            if (!self::Get('HasDefaultDhlUser')) {
                throw new Exceptions\LoggedException('Nie zdefiniowano domyślnego użytkownika DHL!');
            }

            self::$S_oDefaultDhlUser = DbWrapper::Load(DataModels\DhlUser::GetTreeDataObjectInfo(), 1);
        }

        return self::$S_oDefaultDhlUser;
    }

    /**
     * @return bool
     * @throws Exceptions\LoggedException
     */
    protected static function _GetHasDefaultShipperPreset()
    {
        return self::$S_oDefaultShipperPreset !== null
            || DbWrapper::Exists(DataModels\ShipperPreset::GetTreeDataObjectInfo(), 1);
    }

    /**
     * @return mixed|null
     * @throws Exceptions\LoggedException
     */
    protected static function _GetDefaultShipperPreset()
    {
        if (self::$S_oDefaultShipperPreset === null) {
            if (!self::Get('HasDefaultShipperPreset')) {
                throw new Exceptions\LoggedException('Nie zdefiniowano domyślnej predefinicji nadawcy!');
            }

            self::$S_oDefaultShipperPreset = DbWrapper::Load(DataModels\ShipperPreset::GetTreeDataObjectInfo(), 1);
        }

        return self::$S_oDefaultShipperPreset;
    }

    /**
     * @return bool
     * @throws Exceptions\LoggedException
     */
    protected static function _GetIsModuleConfigured()
    {
        return version_compare(PHP_VERSION, '5.4.0', '>=')
            && self::_GetHasDefaultDhlUser()
            && self::_GetHasDefaultShipperPreset()
            && SourceWrapper::IsModuleActive();
    }

    /**
     * @return string|null
     * @throws Exceptions\LoggedException
     */
    protected static function _GetBaseUrl()
    {
        if (self::$S_sBaseUrl === null) {
            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443)
                ? "https://"
                : "http://";

            $host = $_SERVER['HTTP_HOST'];
            $module_path = self::Get('ModulePath');

            self::$S_sBaseUrl = $protocol . $host . self::Get('PsUri') . $module_path;
        }

        return self::$S_sBaseUrl;
    }

    /**
     * @return array|DataModels\DhlAssistant\Classes\DataModels\DhlCountryService[]
     * @throws Exceptions\SourceLoggedException
     */
    protected static function _GetAvailableServices()
    {
        $result = [];
        /* @var $api DataModels\DhlApi */
        foreach (Managers\DhlApiManager::GetList() as $api) {
            $api->GetAvailableServiceCodes();
            $result = array_merge($result, $api->GetAvailableServiceCodes());
        }

        return $result;
    }

    /**
     * @return array|mixed|null
     * @throws Exceptions\SourceLoggedException
     */
    protected static function _GetAvailableCountryCodes()
    {
        if (self::$S_aAvailableCountryCodes === null) {
            $available_countries = [];
            $dhl_users = Managers\DhlUserManager::GetList();

            /* @var $dhl_user DataModels\DhlUser */
            foreach ($dhl_users as $dhl_user) {
                $available_countries = array_merge($available_countries, $dhl_user->GetAvailableCountryCodes());
            }

            $available_countries = array_unique($available_countries);

            self::$S_aAvailableCountryCodes = Core\Tools::ArraySortWithExampleByValue(
                $available_countries,
                array_keys(Enums\Country::$Descriptions),
                false
            );
        }

        return self::$S_aAvailableCountryCodes;
    }

    /**
     * @return false|string[]
     * @throws Exceptions\LoggedException
     */
    protected static function _GetCodPaymentModules()
    {
        return explode(
            ',',
            self::GetOrDefault('CodPaymentModulesString', 'cashondelivery')
        );
    }

    /**
     * @param array $aValues
     * @return bool|mixed
     * @throws Exceptions\LoggedException
     */
    protected static function _SetCodPaymentModules($aValues = array())
    {
        return self::Set('CodPaymentModulesString', implode(',', $aValues));
    }

    /**
     * @throws Exceptions\LoggedException
     */
    public static function CheckIsModuleConfigured()
    {
        if (!self::_GetIsModuleConfigured()) {
            throw new Exceptions\LoggedException("Moduł nie jest skonfigurowany!");
        }
    }
}

?>