<?php
namespace DhlAssistant\Wrappers;

use DhlAssistant\Classes\Dhl\Enums\DropOffType;
use DhlAssistant\Classes\Dhl\Enums\ServiceType;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Classes\Managers;
use DhlAssistant\Classes\DataModels;

class DhlWrapper
{
	
	protected static function _CallApiFunction(DataModels\DhlApi $oApi, $sFunctionName, $params)
	{
		self::_CheckIfFeatureSupported($oApi, $sFunctionName);
		$wrapper_class_path=$oApi->GetWrapperClassPath();
		if (!method_exists($oApi->GetWrapperClassPath(), $sFunctionName))
			throw new Exceptions\LoggedException("Funkcja '{$sFunctionName}' nie została zaimplementowana w API '{$oApi->Name}'!");
		$function_name = $wrapper_class_path.'::'.$sFunctionName;
		return call_user_func_array($function_name, $params);
	}
	
	public static function Send(DataModels\Shipment $oShipment)
	{
		$api = self::_GetApiForShipment($oShipment);
		return self::_CallApiFunction($api, __FUNCTION__, func_get_args());
	}
	public static function Delete(DataModels\Shipment $oShipment)
	{
        if (ServiceType::LM == $oShipment->ServiceType && DropOffType::REQUEST_COURIER == $oShipment->DropOffType) {
            $api = Managers\DhlApiManager::GetDhlApiByCode(\DhlAssistant\Classes\Dhl\Enums\DhlWebApi::DHL24);
        } else {
            $api = self::_GetApiForShipment($oShipment);
        }

        return self::_CallApiFunction($api, __FUNCTION__, func_get_args());
	}
	public static function BookCourier(DataModels\Shipment $oShipment, \DateTime $oDate, \DateTime $oStartHour, \DateTime $oEndHour)
	{
		$api = self::_GetApiForShipment($oShipment);
		return self::_CallApiFunction($api, __FUNCTION__, func_get_args());
	}
	public static function CancelCourierBooking(DataModels\Shipment $oShipment)
	{
		$api = self::_GetApiForShipment($oShipment);
		return self::_CallApiFunction($api, __FUNCTION__, func_get_args());
	}
	public static function GetLabel(DataModels\Shipment $oShipment, $sLabelType)
	{
		$api = self::_GetApiForShipment($oShipment);
		return self::_CallApiFunction($api, __FUNCTION__, func_get_args());
	}
	public static function GetPnpReport(DataModels\DhlApi $oApi, DataModels\DhlUser $oDhlUser, \DateTime $oDate, $params = null)
	{
		self::_CheckDhlUserWithApi($oDhlUser, $oApi);
		$args=func_get_args();
		unset($args[0]);
		return self::_CallApiFunction($oApi, __FUNCTION__, $args);
	}
	public static function GetShipmentScan(DataModels\Shipment $oShipment)
	{
		$api = self::_GetApiForShipment($oShipment);
		return self::_CallApiFunction($api, __FUNCTION__, func_get_args());
	}
	public static function GetPostalCodeServices(DataModels\DhlApi $oApi, DataModels\DhlUser $oDhlUser, $sPostalCode, \DateTime $oPickupDate)
	{
		self::_CheckDhlUserWithApi($oDhlUser, $oApi);
		$args=func_get_args();
		unset($args[0]);
		return self::_CallApiFunction($oApi, __FUNCTION__, $args);
	}
	public static function GetTrackAndTraceInfo(DataModels\Shipment $oShipment)
	{
		$api = self::_GetApiForShipment($oShipment);
		return self::_CallApiFunction($api, __FUNCTION__, func_get_args());
	}
	public static function GetNearestServicePoints(DataModels\DhlApi $oApi, DataModels\DhlUser $oDhlUser, $sPostCode, $sCity, $iRadius, $sCountryCode = null)
	{
		self::_CheckDhlUserWithApi($oDhlUser, $oApi);
		$args=func_get_args();
		unset($args[0]);
		return self::_CallApiFunction($oApi, __FUNCTION__, $args);
	}
	public static function TestAuthData(DataModels\DhlApi $oApi, DataModels\DhlUser $oDhlUser, $bRaiseException = true)
	{
		$args=func_get_args();
		unset($args[0]);
		return self::_CallApiFunction($oApi, __FUNCTION__, $args);
	}
	public static function GetTrackLink(DataModels\Shipment $oShipment)
	{
		$api = self::_GetApiForShipment($oShipment);
		return self::_CallApiFunction($api, __FUNCTION__, func_get_args());
	}
	/**
	 * @param DataModels\DhlApi $oApi
	 * @param string $sFunctionName
	 * @throws Exceptions\LoggedException
	 */
	protected static function _CheckIfFeatureSupported(DataModels\DhlApi $oApi, $sFeatureName)
	{
		if (!$oApi->IsFeatureSupported($sFeatureName))
			throw new Exceptions\LoggedException("API {$oApi->Name} nie wspiera funkcji '{$sFeatureName}'!");
	}
	/**
	 * @param DataModels\DhlUser $oDhlUser
	 * @param DataModels\DhlApi $oApi
	 * @throws Exceptions\LoggedException
	 */
	protected static function _CheckDhlUserWithApi(DataModels\DhlUser $oDhlUser, DataModels\DhlApi $oApi)
	{
		if (!$oDhlUser->HasAuthDataForApiCode($oApi->Code))
			throw new Exceptions\LoggedException("Użytkownik DHL '{$oDhlUser->Name}' nie ma danych logowania do API {$oApi->Name}!");
	}
	/**
	 * @param DataModels\Shipment $oShipment
	 * @throws Exceptions\LoggedException
	 * @return DataModels\DhlApi|NULL
	 */
	protected static function _GetApiForShipment(DataModels\Shipment $oShipment, $bCheckDhlUser = true)
	{
		if ($oShipment->ServiceType == '')
			throw new Exceptions\LoggedException("Nie wybrano usługi dla przesyłki!");
        $api = Managers\DhlApiManager::GetDhlApiForServiceCode($oShipment->ServiceType);
		if ($api === null)
			throw new Exceptions\LoggedException("Brak zdefiniowanych API obsługujących usługę {$oShipment->ServiceType}!");
		if ($bCheckDhlUser)
			self::_CheckDhlUserWithApi($oShipment->GetDhlUser(), $api);
		return $api;
	}
}
?>