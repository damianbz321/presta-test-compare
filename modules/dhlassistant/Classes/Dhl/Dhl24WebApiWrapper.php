<?php
namespace DhlAssistant\Classes\Dhl;

use DhlAssistant\Core;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\Managers;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Dhl;
use DhlAssistant\Classes\Dhl\Enums;
use DhlAssistant\Classes\Dhl\Dhl24WebApiClasses;

class Dhl24WebApiWrapper
{
	public static $_Service = null;

	public static function GetService()
	{
		if (self::$_Service == null)
			self::$_Service = new Dhl\ApiClient(Managers\DhlApiManager::GetDhlApiByCode(Enums\DhlWebApi::DHL24));
		return self::$_Service;
	}
	public static function Send(DataModels\Shipment $oShipment)
	{
		/* @var $oShipment DataModels\Shipment */
		self::_CheckIfSended($oShipment, false);
		if ($oShipment->ServiceType == '')
			throw new Exceptions\SourceLoggedException("Nie wybrano usługi dla przesyłki!");
		$api = Managers\DhlApiManager::GetDhlApiByCode(Enums\DhlWebApi::DHL24);
		if (!$api->IsAvailableServiceByCode($oShipment->ServiceType))
			throw new Exceptions\SourceLoggedException("API {$api->Name} nie obsługuje usługi {$oShipment->ServiceType}!");
		$target_country = Managers\DhlCountryManager::GetCountryByCode($oShipment->GetTargetCountryCode());
		if (!$api->IsAvailableCountryByCode($target_country->Code))
			throw new Exceptions\SourceLoggedException("Kraj docelowy {$target_country->Name} nie obsługiawny przez API {$api->Name}!");
		if (!$target_country->IsSupportingServiceCode($oShipment->ServiceType))
			throw new Exceptions\SourceLoggedException("Kraj docelowy {$target_country->Name} nie obsługuje usługi {$oShipment->ServiceType}!");
		$request = new Dhl24WebApiClasses\createShipment();
		$request->authData = new Dhl24WebApiClasses\AuthData($oShipment->GetDhlUser());
		$request->shipment = self::_ConvertShipmentToApiRequest($oShipment);
		$result = self::GetService()->createShipment($request);
		/* @var $result Dhl24WebApiClasses\createShipmentResponse_ret */
		$oShipment->DhlShipmentId = $result->createShipmentResult->shipmentNotificationNumber;
		$oShipment->DhlOrderId = $result->createShipmentResult->dispatchNotificationNumber;
		$oShipment->DhlShipmentCreationDateTime = new \DateTime('now');
		return true;
	}
	public static function Delete(DataModels\Shipment $oShipment)
	{
		self::_CheckIfSended($oShipment, true);
		$request = new Dhl24WebApiClasses\deleteShipment();
		$request->authData = new Dhl24WebApiClasses\AuthData($oShipment->GetDhlUser());
		$request->shipment = new Dhl24WebApiClasses\DeleteShipmentRequest();
		$request->shipment->shipmentIdentificationNumber = $oShipment->DhlShipmentId;
		if ($oShipment->HasBookedCourier())
			$request->shipment->dispatchIdentificationNumber = $oShipment->DhlOrderId;
		/* @var $result Dhl24WebApiClasses\deleteShipmentResponse_ret */
		$result = self::GetService()->deleteShipment($request);
		if (!$result->deleteShipmentResult->result)
			throw new Exceptions\SourceLoggedException($result->deleteShipmentResult->error);
		$oShipment->DhlShipmentId = null;
		$oShipment->DhlOrderId = null;
		$oShipment->DhlShipmentCreationDateTime = null;
		return true;
	}
	public static function BookCourier(DataModels\Shipment $oShipment, \DateTime $oDate, \DateTime $oStartHour, \DateTime $oEndHour)
	{
		self::_CheckIfSended($oShipment, true);
		if ($oShipment->HasBookedCourier())
			throw new Exceptions\SourceLoggedException("Przesyłka ma już zamówionego kuriera!");
		if ($oShipment->ShipmentDate->format('Y-m-d') !== $oDate->format('Y-m-d')) //sprawdzanie czy daty się zgadzają
			throw new Exceptions\LoggedException("Nie można zamówić kuriera na inną datę niż data odbioru zdefiniowana dla tej przesyłki!");
		$request = new Dhl24WebApiClasses\bookCourier();
		$request->authData = new Dhl24WebApiClasses\AuthData($oShipment->GetDhlUser());
		$request->shipmentIdList = new Dhl24WebApiClasses\ArrayOfString();
		$request->shipmentIdList->item[] = $oShipment->DhlShipmentId;
		$request->pickupDate = $oDate->format('Y-m-d'); //YYYY-MM-DD
		$request->pickupTimeFrom = $oStartHour->format('H:i'); //HH:MM
		$request->pickupTimeTo = $oEndHour->format('H:i'); //HH:MM
		/* @var $result Dhl24WebApiClasses\bookCourierResponse */
		$result = self::GetService()->bookCourier($request);
		$oShipment->DhlOrderId = $result->bookCourierResult->item;
		return true;
	}
	public static function CancelCourierBooking(DataModels\Shipment $oShipment)
	{
		self::_CheckIfSended($oShipment, true);
		if (!$oShipment->HasBookedCourier())
			throw new Exceptions\SourceLoggedException("Przesyłka nie ma zamówionego kuriera!");
		$request = new Dhl24WebApiClasses\cancelCourierBooking();
		$request->authData = new Dhl24WebApiClasses\AuthData($oShipment->GetDhlUser());
 		$request->orders = new Dhl24WebApiClasses\ArrayOfString();
 		$request->orders->item[] = $oShipment->DhlOrderId;
 		$result = self::GetService()->cancelCourierBooking($request);
 		/* @var $result Dhl24WebApiClasses\cancelCourierBookingResponse */
 		if (!$result->cancelCourierBookingResult->item->result)
 			throw new Exceptions\SourceLoggedException($result->cancelCourierBookingResult->item->error);
		$oShipment->DhlOrderId = null;
		return true;
	}
	public static function GetLabel(DataModels\Shipment $oShipment, $sLabelType)
	{
		self::_CheckIfSended($oShipment, true);
		if (!isset(Enums\LabelType::$Descriptions[$sLabelType]))
			throw new Exceptions\SourceLoggedException("Nieznane oznaczenie typu etykiety ('{$sLabelType}')!");
		$label_name = Enums\LabelType::$Descriptions[$sLabelType];
		if (!in_array($sLabelType, $oShipment->GetAvailableLabelTypes()))
			throw new Exceptions\SourceLoggedException("Etykieta typu '{$label_name}' nie jest dozwolona dla tej przesyłki!");
		$request = new Dhl24WebApiClasses\getLabels();
		$request->authData = new Dhl24WebApiClasses\AuthData($oShipment->GetDhlUser());
		$request->itemsToPrint = new Dhl24WebApiClasses\ArrayOfItemtoprint();
		$item = new Dhl24WebApiClasses\ItemToPrint();
		$item->labelType = $sLabelType;
		$item->shipmentId = $oShipment->DhlShipmentId;
		$request->itemsToPrint->item[] = $item;
		$result = self::GetService()->getLabels($request);
		/* @var $result Dhl24WebApiClasses\getLabelsResponse */
		return base64_decode($result->getLabelsResult->item->labelData);
	}
	public static function GetPnpReport(DataModels\DhlUser $oDhlUser, \DateTime $oDate, $params = null)
	{
        /**
         * This will suppress UnusedLocalVariable
         * warnings in this method
         *
         * @SuppressWarnings(PHPMD.UnusedLocalVariable)
         */

		if (!is_array($params) || !isset($params['PackageType']) || !isset(Enums\PnpReportPackageType::$Descriptions[$params['PackageType']]))
			throw new Exceptions\LoggedException('Nieprawidłowe parametry!');
		$request = new Dhl24WebApiClasses\getPnp();
		$request->pnpRequest = new Dhl24WebApiClasses\PnpRequest();
		$request->pnpRequest->authData = new Dhl24WebApiClasses\AuthData($oDhlUser);
		$request->pnpRequest->date = $oDate->format('Y-m-d');
		$request->pnpRequest->type = $params['PackageType'];
		$result = self::GetService()->getPnp($request);
		/* @var $result Dhl24WebApiClasses\getPnpResponse */
		$filename = substr(md5(Wrappers\ConfigWrapper::Get('FileNameSalt').'_'.$result->getPnpResult->fileName.'_'.Enums\DhlWebApi::DHL24),0,16).'.pdf';
		file_put_contents(Wrappers\ConfigWrapper::Get('ReportsDir').$filename, base64_decode($result->getPnpResult->fileData));
		return $filename;
	}
	public static function GetShipmentScan(DataModels\Shipment $oShipment)
	{
		self::_CheckIfSended($oShipment, true);
		$request = new Dhl24WebApiClasses\getShipmentScan();
		$request->authData = new Dhl24WebApiClasses\AuthData($oShipment->GetDhlUser());
		$request->shipmentId = $oShipment->DhlShipmentId;
		$result = self::GetService()->getShipmentScan($request);
		/* @var $result Dhl24WebApiClasses\getShipmentScanResponse */
		switch ($result->getShipmentScanResult->scanMimeType)
		{
			case 'image/tiff':
				$extension = '.tiff';
				break;	
			case 'image/png':
				$extension = '.png';
				break;	
			default:
				throw new Exceptions\SourceLoggedException("Nieznany typ MIME: '{$result->getShipmentScanResult->scanMimeType}'!");
		}
		$filename = substr(md5(Wrappers\ConfigWrapper::Get('FileNameSalt').'_'.$oShipment->DhlShipmentId),0,16).$extension;
		file_put_contents(Wrappers\ConfigWrapper::Get('ScansDir').$filename, base64_decode($result->getShipmentScanResult->scanData));
		return $filename;
	}
	public static function GetPostalCodeServices(DataModels\DhlUser $oDhlUser, $sPostalCode, \DateTime $oPickupDate)
	{
		$request = new Dhl24WebApiClasses\getPostalCodeServices();
		$request->authData = new Dhl24WebApiClasses\AuthData($oDhlUser);
		$request->postCode = Core\Filters::MaxLen($sPostalCode, 255);
		$request->pickupDate = $oPickupDate->format('Y-m-d');
		$result = self::GetService()->getPostalCodeServices($request);
		/* @var $result Dhl24WebApiClasses\getPostalCodeServicesResponse */
		$result_info = $result->getPostalCodeServicesResult;
		$return_value = new DataModels\PostalCodeServicesInfo();
		$return_value->PostalCode = Core\Filters::MaxLen($sPostalCode, 255);
		$return_value->PickupDate = $oPickupDate;
		$return_value->Domestic09 = $result_info->domesticExpress9;
		$return_value->Domestic12 = $result_info->domesticExpress12;
		$return_value->DeliveryEvening = $result_info->deliveryEvening;
		$return_value->PickupOnSaturday = $result_info->pickupOnSaturday;
		$return_value->DeliverySaturday = $result_info->deliverySaturday;
		$return_value->ExPickupFrom = $result_info->exPickupFrom;
		$return_value->ExPickupTo = $result_info->exPickupTo;
		$return_value->DrPickupFrom = $result_info->drPickupFrom;
		$return_value->DrPickupTo = $result_info->drPickupTo;
		return $return_value;
	
	}
	public static function GetTrackAndTraceInfo(DataModels\Shipment $oShipment)
	{
		self::_CheckIfSended($oShipment, true);
		$request = new Dhl24WebApiClasses\getTrackAndTraceInfo();
		$request->authData = new Dhl24WebApiClasses\AuthData($oShipment->GetDhlUser());
		$request->shipmentId = $oShipment->DhlShipmentId;
		$result = self::GetService()->getTrackAndTraceInfo($request);
		/* @var $result Dhl24WebApiClasses\getTrackAndTraceInfoResponse */
		$return_value = array();
		foreach ($result->getTrackAndTraceInfoResult->events->item as $event)
		{
			$event_obj = new DataModels\TrackEvent();
			$event_obj->Description = $event->description;
			$event_obj->Terminal = $event->terminal; 
			$event_obj->Timestamp = new \DateTime($event->timestamp);
			$return_value[] = $event_obj;
		}
		return $return_value;
	
	}
	public static function TestAuthData(DataModels\DhlUser $oDhlUser, $bRaiseException = true)
	{
		if (!$oDhlUser->HasAuthDataForApiCode(Enums\DhlWebApi::DHL24))
		{
			if ($bRaiseException)
				throw new Exceptions\LoggedException("Brak danych do logowania!");
			else
				return false;
		}
		$request = new Dhl24WebApiClasses\getLabels();
		$request->authData = new Dhl24WebApiClasses\AuthData($oDhlUser);
		$request->itemsToPrint = new Dhl24WebApiClasses\ArrayOfItemtoprint();
		try
		{
			self::GetService()->getLabels($request);
		}
		catch (\SoapFault $ex)
		{
			if ($ex->faultcode == '101')
				return true;
			if ($ex->faultcode == '100')
			{
				if ($bRaiseException)
					throw new Exceptions\SourceLoggedException("Błędne dane logowania!");
				else
					return false;
			}
			throw new Exceptions\SourceLoggedException("Błąd połączenia z API: [{$ex->faultcode}] {$ex->faultstring}");
		}
		return true;
	}
	public static function GetTrackLink(DataModels\Shipment $oShipment)
	{
		self::_CheckIfSended($oShipment, true);
		return sprintf(Wrappers\ConfigWrapper::Get('TrackLinkTemplate'), $oShipment->DhlShipmentId);
	}
	/**
	 * 
	 * @param DataModels\Shipment $oShipment
	 * @param bool $bShouldBeSended
	 * @throws Exceptions\LoggedException
	 */
	protected static function _CheckIfSended(DataModels\Shipment $oShipment, $bShouldBeSended = true)
	{
		if (! $oShipment->IsSended() === $bShouldBeSended)
		{
			if ($bShouldBeSended)
				throw new Exceptions\LoggedException("Ta przesyłka nie została jeszcze zlecona!");
			else
				throw new Exceptions\LoggedException("Ta przesyłka została już zlecona!");
		}
	}
	protected static function _CheckIfSpecialServicesAllowed(DataModels\Shipment $oShipment)
	{
		$dcs = $oShipment->GetTargetCountryService();
		foreach (array_keys(Enums\SpecialService::$Descriptions) as $service_code)
		{
			$service_name = 'S_'.$service_code;	
			if ($oShipment->SpecialServices->$service_name && !$dcs->AvailableSpecialServices->$service_name)
				throw new Exceptions\SourceLoggedException("Usługa specjalna '{$service_code}' nie jest dostępna w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
		}
		if ($oShipment->SpecialServices->S_COD && $dcs->CodRequireUbezp && !$oShipment->SpecialServices->S_UBEZP)
			throw new Exceptions\SourceLoggedException("Usługa specjalna 'COD' wymaga usługi specjalnej 'UBEZP' w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
		if ($oShipment->SendToParcelShop && $oShipment->SpecialServices->S_COD && !$dcs->AllowCodForParcelShop)
			throw new Exceptions\SourceLoggedException("Usługa specjalna 'COD' nie jest dostępna przy wysyłce do ParcelShop w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
		if ($oShipment->SendToParcelLocker && $oShipment->SpecialServices->S_COD && !$dcs->AllowCodForParcelLocker)
			throw new Exceptions\SourceLoggedException("Usługa specjalna 'COD' nie jest dostępna przy wysyłce do Parcelstation w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
	}
	protected static function _ConvertShipmentToApiBilling(DataModels\Shipment $oShipment)
	{
		if (!isset(Enums\ShippingPaymentType::$Descriptions[$oShipment->ShippingPaymentType]))
			throw new Exceptions\SourceLoggedException("Nieznane oznaczenie sposobu płatności ('{$oShipment->ShippingPaymentType}')!");
		if (!isset(Enums\PaymentType::$Descriptions[$oShipment->PaymentType]))
			throw new Exceptions\SourceLoggedException("Nieznane oznaczenie strony płacącej ('{$oShipment->PaymentType}')!");
		
		$dcs = $oShipment->GetTargetCountryService();
		$result = new Dhl24WebApiClasses\Billing();
		$shipment_payment_type_name = Enums\ShippingPaymentType::$Descriptions[$oShipment->ShippingPaymentType];
		if (!isset($dcs->AvailableShippingPaymentTypes[$oShipment->ShippingPaymentType]))
			throw new Exceptions\SourceLoggedException("Rozliczenie przesyłki przez '{$shipment_payment_type_name}' nie jest dostępne w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
		$result->shippingPaymentType = $oShipment->ShippingPaymentType; //string(*16)
		$result->billingAccountNumber = $oShipment->BillingAccountNumber; //int
		
		if (!in_array($oShipment->PaymentType, $dcs->AvailableShippingPaymentTypes[$oShipment->ShippingPaymentType]))
		{
			$payment_type_name = Enums\PaymentType::$Descriptions[$oShipment->PaymentType];
			throw new Exceptions\SourceLoggedException("Rozliczenie przesyłki typu '{$payment_type_name}' przez '{$shipment_payment_type_name}' nie jest dostępne w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
		}
		$result->paymentType = $oShipment->PaymentType; //string(*16)
		$result->costsCenter = Core\Filters::LoggedMaxLen($oShipment->CostsCenter, 255, 'CostsCenter');
		return $result;
	}
	protected static function _ConvertShipmentToApiArrayOfService(DataModels\Shipment $oShipment)
	{
		self::_CheckIfSpecialServicesAllowed($oShipment);
		$result = new Dhl24WebApiClasses\ArrayOfService();
		$s_services = $oShipment->SpecialServices;
		if ($s_services->S_1722)
			$result->item[] = new Dhl24WebApiClasses\Service(Enums\SpecialService::S_1722);
		if ($s_services->S_SOBOTA)
			$result->item[] = new Dhl24WebApiClasses\Service(Enums\SpecialService::S_SOBOTA);
		if ($s_services->S_NAD_SOBOTA)
			$result->item[] = new Dhl24WebApiClasses\Service(Enums\SpecialService::S_NAD_SOBOTA);
		if ($s_services->S_UBEZP)
			$result->item[] = new Dhl24WebApiClasses\Service(Enums\SpecialService::S_UBEZP, array('serviceValue' => $s_services->UBEZP_Value));
		if ($s_services->S_COD)
			$result->item[] = new Dhl24WebApiClasses\Service(Enums\SpecialService::S_COD, array('serviceValue' => $s_services->COD_Value, 'collectOnDeliveryForm' => Enums\PaymentType::BANK_TRANSFER));
		if ($s_services->S_PDI)
			$result->item[] = new Dhl24WebApiClasses\Service(Enums\SpecialService::S_PDI);
		if ($s_services->S_ROD)
			$result->item[] = new Dhl24WebApiClasses\Service(Enums\SpecialService::S_ROD, array ('textInstruction' => $s_services->ROD_Instruction));
		if ($s_services->S_POD)
			$result->item[] = new Dhl24WebApiClasses\Service(Enums\SpecialService::S_POD);
		if ($s_services->S_SAS)
			$result->item[] = new Dhl24WebApiClasses\Service(Enums\SpecialService::S_SAS);
		if ($s_services->S_ODB)
			$result->item[] = new Dhl24WebApiClasses\Service(Enums\SpecialService::S_ODB);
		if ($s_services->S_UTIL)
			$result->item[] = new Dhl24WebApiClasses\Service(Enums\SpecialService::S_POD);  //tak to się zgłasza w API...
		return $result;
	}
	protected static function _ConvertShipmentToApiShipmentInfo(DataModels\Shipment $oShipment)
	{
		if (!isset(Enums\LabelType::$Descriptions[$oShipment->LabelType]))
			throw new Exceptions\SourceLoggedException("Nieznane oznaczenie typu etykiety ('{$oShipment->LabelType}')!");
		$dcs = $oShipment->GetTargetCountryService();
		$shipment_info = new Dhl24WebApiClasses\ShipmentInfo();
		$shipment_info->dropOffType = $oShipment->DropOffType;
		$shipment_info->serviceType = $oShipment->ServiceType;
		$shipment_info->billing = self::_ConvertShipmentToApiBilling($oShipment);
		$shipment_info->specialServices = self::_ConvertShipmentToApiArrayOfService($oShipment);
		$shipment_info->shipmentTime = new Dhl24WebApiClasses\ShipmentTime();
		//ShipmentTime
		$shipment_info->shipmentTime->shipmentDate = $oShipment->ShipmentDate->format('Y-m-d'); //YYYY-MM-DD
		$shipment_info->shipmentTime->shipmentStartHour = $oShipment->ShipmentStartHour->format('H:i'); //HH:MM
		$shipment_info->shipmentTime->shipmentEndHour = $oShipment->ShipmentEndHour->format('H:i'); //HH:MM
		if (!in_array($oShipment->LabelType, $dcs->AvailableLabelTypes))
		{
			$label_type_name = Enums\LabelType::$Descriptions[$oShipment->LabelType];
			throw new Exceptions\SourceLoggedException("Etykieta typu '{$label_type_name}' nie jest dostępna w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
		}
		$shipment_info->labelType = $oShipment->LabelType;
		return $shipment_info;
	}
	protected static function _ConvertShipmentToApiAddress(DataModels\Shipment $oShipment) // -> Shipper
	{
		$result = new Dhl24WebApiClasses\Address();
		$result->name = Core\Filters::LoggedMaxLen($oShipment->Shipper->Address->Name, 255, 'Shipper:Name'); //string(60)
		$result->postalCode = Core\Filters::LoggedMaxLen($oShipment->Shipper->Address->PostalCode, 255, 'Shipper:PostalCode'); //string(12)
		$result->city = Core\Filters::LoggedMaxLen($oShipment->Shipper->Address->City, 255, 'Shipper:City'); //string(17)
		$result->street = Core\Filters::LoggedMaxLen($oShipment->Shipper->Address->Street, 255, 'Shipper:Street'); //string(22)
		$result->houseNumber = Core\Filters::LoggedMaxLen($oShipment->Shipper->Address->HouseNumber, 255, 'Shipper:HouseNumber'); //string(7)
		$result->apartmentNumber = Core\Filters::LoggedMaxLen($oShipment->Shipper->Address->ApartmentNumber, 255, 'Shipper:ApartmentNumber'); //string(7)
		return $result;
	}
	protected static function _ConvertShipmentToApiReceiverAddress(DataModels\Shipment $oShipment) // -> Receiver
	{
		$dcs = $oShipment->GetTargetCountryService();
		$result = new Dhl24WebApiClasses\ReceiverAddress();
		$postal_code_max_len = $dcs->PostalCodeMaxLength;
		$result->name = Core\Filters::LoggedMaxLen($oShipment->Receiver->Address->Name, 255, 'Receiver:Name'); //string(60)
		if (!Core\Validators::MaxLen($oShipment->Receiver->Address->PostalCode, $postal_code_max_len))
			throw new Exceptions\SourceLoggedException("Kod pocztowy odbiorcy zbyt długi!");
		$result->postalCode = $oShipment->Receiver->Address->PostalCode; //string(?)
		$result->city = Core\Filters::LoggedMaxLen($oShipment->Receiver->Address->City, 255, 'Receiver:City'); //string(17)
		$result->street = Core\Filters::LoggedMaxLen($oShipment->Receiver->Address->Street, 255, 'Receiver:Street'); //string(22)
		$result->houseNumber = Core\Filters::LoggedMaxLen($oShipment->Receiver->Address->HouseNumber, 255, 'Receiver:HouseNumber'); //string(7)
		$result->apartmentNumber = Core\Filters::LoggedMaxLen($oShipment->Receiver->Address->ApartmentNumber, 255, 'Receiver:ApartmentNumber'); //string(7)
		$result->country = $oShipment->GetTargetCountryCode(); //string(2)
		if ($oShipment->SendToParcelShop && !$dcs->AllowParcelShop)
			throw new Exceptions\SourceLoggedException("Dostarczenie do ParcelShop nie jest dostępne w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
		if ($oShipment->SendToParcelLocker && !$dcs->AllowParcelLocker)
		{
			throw new Exceptions\SourceLoggedException("Dostarczenie do Parcelstation nie jest dostępne w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
		}
		if ($oShipment->SendToParcelShop && $oShipment->SendToParcelLocker)
			throw new Exceptions\SourceLoggedException("Nie można zlecić jednocześnie wysyłki do ParcelShop i Parcelstation!");
		if ($oShipment->SendToParcelShop || $oShipment->SendToParcelLocker)
		{
			$parcelname = $oShipment->SendToParcelShop ? "ParcelShop" : "Parcelstation";
			if (($oShipment->ParcelIdent == '') || $oShipment->ParcelIdent == null)
				throw new Exceptions\LoggedException("Dostarczenie do {$parcelname} wymaga podania nr. identyfikacyjnego");
			$result->houseNumber =$oShipment->ParcelIdent; //string(7)
			if ($oShipment->SendToParcelShop)
			{
				$result->isPostfiliale = true;
			}
			if ($oShipment->SendToParcelLocker)
			{
				$result->isPackstation = true;
				if ($dcs->RequirePostnummerForParcelLocker)
				{
					if (strlen($oShipment->Postnummer == 0))
						throw new Exceptions\LoggedException("Dostarczenie do Parcelstation w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name} wymaga podania numeru Postnummer!");
					if (!Core\Validators::MaxLen($oShipment->Postnummer, 255))
						throw new Exceptions\LoggedException("Podany kod Postnummer jest zbyt długi!");
					$result->postnummer = $oShipment->Postnummer; //string(10)
				}
			}
			if ($dcs->RequirePostalCodeForParcel)
			{
				if (strlen($oShipment->ParcelPostalCode == 0))
					throw new Exceptions\LoggedException("Dostarczenie do {$parcelname} w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name} wymaga podania jego kodu pocztowego!");
				if (!Core\Validators::MaxLen($oShipment->ParcelPostalCode, $postal_code_max_len))
					throw new Exceptions\LoggedException("Podany kod pocztowy {$parcelname} jest zbyt długi w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
				$result->postalCode = $oShipment->ParcelPostalCode; //string(?)
			}
		}
		return $result;
	}
	protected static function _ConvertContactToApiPreavisoContact(DataModels\Contact $oContact, $sContext)
	{
		/* @var $oContact DataModels\Contact */
		$result = new Dhl24WebApiClasses\PreavisoContact();
		$result->personName = Core\Filters::LoggedMaxLen($oContact->Name, 50, $sContext.':Name'); //string(50)
		$result->phoneNumber = Core\Filters::LoggedMaxLen($oContact->Phone, 9, $sContext.':Phone'); //string(9)
		$result->emailAddress = Core\Filters::LoggedMaxLen($oContact->Email, 100, $sContext.':Email'); //string(100)
		return $result;
	}
	protected static function _ConvertShipmentToApiNeighbourAddress(DataModels\Shipment $oShipment)
	{
		$result = new Dhl24WebApiClasses\NeighbourAddress();
		$result->name = Core\Filters::LoggedMaxLen($oShipment->Neighbour->Name, 255, 'Neighbour:Name'); //string(60)
		if (!Core\Validators::MaxLen($oShipment->Receiver->Address->PostalCode, 255))
			throw new Exceptions\LoggedException("Kod pocztowy sąsiada zbyt długi!");
		$result->postalCode = Core\Filters::LoggedMaxLen($oShipment->Neighbour->PostalCode, 255, 'Neighbour:PostalCode'); //string(5?)
		$result->city = Core\Filters::LoggedMaxLen($oShipment->Neighbour->City, 255, 'Neighbour:City'); //string(17)
		$result->street = Core\Filters::LoggedMaxLen($oShipment->Neighbour->Street, 255, 'Neighbour:Street'); //string(22)
		$result->houseNumber = Core\Filters::LoggedMaxLen($oShipment->Neighbour->HouseNumber, 255, 'Neighbour:HouseNumber'); //string(7)
		$result->apartmentNumber = Core\Filters::LoggedMaxLen($oShipment->Neighbour->ApartmentNumber, 255, 'Neighbour:ApartmentNumber'); //string(7)
		$result->contactPhone = Core\Filters::LoggedMaxLen($oShipment->Neighbour->Phone, 255, 'Neighbour:Phone'); //string(9)
		$result->contactEmail = Core\Filters::LoggedMaxLen($oShipment->Neighbour->Email, 255, 'Neighbour:Email'); //string(100)
		return $result;
	}
	protected static function _ConvertShipmentToApiAddressat(DataModels\Shipment $oShipment)
	{
		$result = new Dhl24WebApiClasses\Addressat();
		if ($oShipment->Shipper->Preaviso->IsFilled())
			$result->preaviso = self::_ConvertContactToApiPreavisoContact($oShipment->Shipper->Preaviso, 'Shipper:Preaviso'); //PreavisoContact
		if ($oShipment->Shipper->Contact->IsFilled())
			$result->contact = self::_ConvertContactToApiPreavisoContact($oShipment->Shipper->Contact, 'Shipper:Contact'); //PreavisoContact
		$result->address = self::_ConvertShipmentToApiAddress($oShipment); //Address
		return $result;
	}
	protected static function _ConvertShipmentToApiReceiverAddressat(DataModels\Shipment $oShipment)
	{
		$result = new Dhl24WebApiClasses\ReceiverAddressat();
		if ($oShipment->Receiver->Preaviso->IsFilled())
			$result->preaviso = self::_ConvertContactToApiPreavisoContact($oShipment->Receiver->Preaviso, 'Receiver:Preaviso'); //PreavisoContact
		if ($oShipment->Receiver->Contact->IsFilled())
			$result->contact = self::_ConvertContactToApiPreavisoContact($oShipment->Receiver->Contact, 'Receiver:Contact'); //PreavisoContact
		$result->address = self::_ConvertShipmentToApiReceiverAddress($oShipment); //ReceiverAddress
		return $result;
	}
	protected static function _ConvertShipmentToApiShip(DataModels\Shipment $oShipment)
	{
		$dcs = $oShipment->GetTargetCountryService();
		$ship = new Dhl24WebApiClasses\Ship();
		$ship->shipper = self::_ConvertShipmentToApiAddressat($oShipment); //Addressat
		$ship->receiver = self::_ConvertShipmentToApiReceiverAddressat($oShipment); //ReceiverAddressat
		if ($oShipment->SpecialServices->S_SAS && $dcs->AvailableSpecialServices->S_SAS)
			$ship->neighbour = self::_ConvertShipmentToApiNeighbourAddress($oShipment); //NeighbourAddress
		return $ship;
	}
	protected static function _ConvertShipmentToApiArrayOfPackage(DataModels\Shipment $oShipment)
	{
		if (!isset(Enums\PackageType::$Descriptions[$oShipment->Package->Type]))
			throw new Exceptions\SourceLoggedException("Nieznane oznaczenie typu przesyłki ('{$oShipment->Package->Type}')!");
		$dcs = $oShipment->GetTargetCountryService();
		$result = new Dhl24WebApiClasses\ArrayOfPackage();
		$item = new Dhl\Dhl24WebApiClasses\Package();
		$s_package = $oShipment->Package;
		$package_type_name = Enums\PackageType::$Descriptions[$s_package->Type];
		if (!in_array($s_package->Type, $dcs->AvailablePackageTypes))
			throw new Exceptions\SourceLoggedException("Przesyłka typu '{$package_type_name}' nie jest dostępna w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
		$item->type = $s_package->Type; //string(*16)
 		if ($s_package->Type !== Enums\PackageType::ENVELOPE)
 		{
			if (!$s_package->Weight)
				throw new Exceptions\SourceLoggedException("Parametr 'waga' jest wymagany dla przesyłki typu '{$package_type_name}'!");
 			$item->weight = $s_package->Weight; //int
			if (!$s_package->Width)
				throw new Exceptions\SourceLoggedException("Parametr 'szerokość' jest wymagany dla przesyłki typu '{$package_type_name}'!");
			$item->width = $s_package->Width; //int
			if (!$s_package->Height)
				throw new Exceptions\SourceLoggedException("Parametr 'wysokość' jest wymagany dla przesyłki typu '{$package_type_name}'!");
			$item->height = $s_package->Height; //int
			if (!$s_package->Length)
				throw new Exceptions\SourceLoggedException("Parametr 'długość' jest wymagany dla przesyłki typu '{$package_type_name}'!");
			$item->length = $s_package->Length; //int
		}
		$item->quantity = $s_package->Quantity; //int

		if ($s_package->NonStandard && !$dcs->AllowNst)
			throw new Exceptions\SourceLoggedException("Opcja NST nie jest dostępna w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
		if ($s_package->NonStandard && ($oShipment->SendToParcelShop || $oShipment->SendToParcelLocker) && !$dcs->AllowNstForParcel && $dcs->Code != Enums\ServiceType::EK)
		{
			$parcel_name = 'ParcelShop';
			if ($oShipment->SendToParcelLocker)
				$parcel_name = 'Parcelstation';
			throw new Exceptions\SourceLoggedException("Wybrana opcja nie jest dostępna w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}! przy wysyłce do {$parcel_name}");
		}
		$item->nonStandard = $s_package->NonStandard; //bool
		if ($s_package->EuroReturn && $s_package->Type != Enums\PackageType::PALLET)
		{
			$pallet_package_type_name = Enums\PackageType::$Descriptions[Enums\PackageType::PALLET];
			throw new Exceptions\SourceLoggedException("Opcja EuroReturn jest dostępna tylko dla typu przesyłki '{$pallet_package_type_name}'!");
		}
		if ($s_package->EuroReturn && !$dcs->AllowEuroReturn)
			throw new Exceptions\SourceLoggedException("Opcja EuroReturn nie jest dostępna w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
		$item->euroReturn = $s_package->EuroReturn; //bool
		$result->item[] = $item;
		return $result;
	}
	protected static function _ConvertShipmentToApiRequest(DataModels\Shipment $oShipment)
	{
		$request = new Dhl24WebApiClasses\CreateShipmentRequest();
		$request->shipmentInfo = self::_ConvertShipmentToApiShipmentInfo($oShipment); //ShipmentInfo
		$request->content = Core\Filters::LoggedMaxLen($oShipment->Content, 255, 'Content'); //string(30)
		$request->comment = Core\Filters::LoggedMaxLen($oShipment->Comment, 255, 'Comment'); //string(100)
		$request->reference = Core\Filters::LoggedMaxLen($oShipment->Reference, 255, 'Reference'); //string(20)
		$request->ship = self::_ConvertShipmentToApiShip($oShipment); //Ship
		$request->pieceList = self::_ConvertShipmentToApiArrayOfPackage($oShipment); //ArrayOfPackage
		return $request;
	}
}
?>