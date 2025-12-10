<?php
namespace DhlAssistant\Classes\Dhl;

use DhlAssistant\Core;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\Managers;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Dhl;
use DhlAssistant\Classes\Dhl\Enums;
use DhlAssistant\Classes\Dhl\DhlPsWebApiClasses;

class DhlPsWebApiWrapper
{
	public static $_Service = null;

	public static function GetService()
	{
		if (self::$_Service == null)
			self::$_Service = new Dhl\ApiClient(Managers\DhlApiManager::GetDhlApiByCode(Enums\DhlWebApi::DHLPS));
		return self::$_Service;
	}
	public static function Send(DataModels\Shipment $oShipment)
	{
		/* @var $oShipment DataModels\Shipment */
		self::_CheckIfSended($oShipment, false);
		if ($oShipment->ServiceType == '')
			throw new Exceptions\SourceLoggedException("Nie wybrano usługi dla przesyłki!");
		$api = Managers\DhlApiManager::GetDhlApiByCode(Enums\DhlWebApi::DHLPS);
		if (!$api->IsAvailableServiceByCode($oShipment->ServiceType))
			throw new Exceptions\SourceLoggedException("API {$api->Name} nie obsługuje usługi {$oShipment->ServiceType}!");
		$target_country = Managers\DhlCountryManager::GetCountryByCode($oShipment->GetTargetCountryCode());
		if (!$api->IsAvailableCountryByCode($target_country->Code))
			throw new Exceptions\SourceLoggedException("Kraj docelowy {$target_country->Name} nie obsługiawny przez API {$api->Name}!");
		if (!$target_country->IsSupportingServiceCode($oShipment->ServiceType))
			throw new Exceptions\SourceLoggedException("Kraj docelowy {$target_country->Name} nie obsługuje usługi {$oShipment->ServiceType}!");
		$request = new DhlPsWebApiClasses\createShipment();
		$request->shipment = new DhlPsWebApiClasses\CreateShipmentStructure();
		$request->shipment->authData = new DhlPsWebApiClasses\AuthdataStructure($oShipment->GetDhlUser());
		$request->shipment->shipmentData = self::_ConvertShipmentToApiRequest($oShipment);
		$result = self::GetService()->createShipment($request);
		/* @var $result DhlPsWebApiClasses\createShipmentResponse */
		$oShipment->DhlShipmentId = $result->createShipmentResult->shipmentNumber;
		$oShipment->DhlOrderId = $result->createShipmentResult->dispatchNumber;
		$oShipment->DhlShipmentCreationDateTime = new \DateTime('now');
		//file_put_contents(Wrappers\ConfigWrapper::Get('LabelsDir').$oShipment->GetLabelFileName(), base64_decode($result->createShipmentResult->label->labelContent));
		return true;
	}
	public static function Delete(DataModels\Shipment $oShipment)
	{
		self::_CheckIfSended($oShipment, true);
		if ($oShipment->HasBookedCourier())
			$delete_request->dispatchIdentificationNumber = $oShipment->DhlOrderId;
		$request = new DhlPsWebApiClasses\deleteShipment();
		$request->shipment = new DhlPsWebApiClasses\DeleteShipmentStructure();
		$request->shipment->authData = new DhlPsWebApiClasses\AuthdataStructure($oShipment->GetDhlUser());
		$request->shipment->shipment = $oShipment->DhlShipmentId;
		/* @var $result DhlPsWebApiClasses\deleteShipmentResponse */
		$result = self::GetService()->deleteShipment($request);
		if ($result->deleteShipmentResult->status != 'OK')
			throw new Exceptions\SourceLoggedException('Bład usuwania przesyłki!');
		$oShipment->DhlShipmentId = null;
		$oShipment->DhlOrderId = null;
		$oShipment->DhlShipmentCreationDateTime = null;
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
		$request = new DhlPsWebApiClasses\getLabel();
		$request->structure = new DhlPsWebApiClasses\GetLabelStructure();
		$request->structure->authData = new DhlPsWebApiClasses\AuthdataStructure($oShipment->GetDhlUser());
		$request->structure->shipment = $oShipment->DhlShipmentId;
		$request->structure->type = $sLabelType;
		$result = self::GetService()->getLabel($request);
		/* @var $result DhlPsWebApiClasses\getLabelResponse */
		return base64_decode($result->getLabelResult->labelContent);
	}
	public static function GetPnpReport(DataModels\DhlUser $oDhlUser, \DateTime $oDate, $params = null)
	{
        /**
         * This will suppress UnusedLocalVariable
         * warnings in this method
         *
         * @SuppressWarnings(PHPMD.UnusedLocalVariable)
         */

		$request = new DhlPsWebApiClasses\getPnp();
		$request->structure = new DhlPsWebApiClasses\GetPnpStructure();
		$request->structure->authData = new DhlPsWebApiClasses\AuthdataStructure($oDhlUser);
		$request->structure->shipmentDate = $oDate->format('Y-m-d');
		$result = self::GetService()->getPnp($request);
		/* @var $result DhlPsWebApiClasses\getPnpResponse */
		$filename = substr(md5(Wrappers\ConfigWrapper::Get('FileNameSalt').'_'.$result->getPnpResult->labelName.'_'.Enums\DhlWebApi::DHLPS),0,16).'.pdf';
		file_put_contents(Wrappers\ConfigWrapper::Get('ReportsDir').$filename, base64_decode($result->getPnpResult->labelContent));
		return $filename;
	}
	public static function GetNearestServicePoints(DataModels\DhlUser $oDhlUser, $sPostCode, $sCity, $iRadius, $sCountryCode = null)
	{
		$request = new DhlPsWebApiClasses\getNearestServicepoints();
		$request->structure = new DhlPsWebApiClasses\GetNearestServicepointsStructure();
		$request->structure->authData = new DhlPsWebApiClasses\AuthdataStructure($oDhlUser);
		$request->structure->postcode = Core\Filters::LoggedMaxLen($sPostCode, 12, 'GetNearestServicePoints:PostCode');
		$request->structure->city = Core\Filters::LoggedMaxLen($sCity, 17, 'GetNearestServicePoints:City');
		$request->structure->radius = (int)$iRadius;
		/* @var $result DhlPsWebApiClasses\getNearestServicepointsResponse */
		$result = self::GetService()->getNearestServicepoints($request);
		foreach ($result->getNearestServicepointsResult->points->item as $point)
		{
			$point_obj = new DataModels\ServicePointInfo();
			$point_obj->Name = $point->name;
			$point_obj->Description = $point->description;
			$point_obj->Address = new DataModels\Address();
			$point_obj->Lat = $point->latitude;
			$point_obj->Lon = $point->longitude;
			$point_obj->ParcelIdent = $point->sap;
			$point_obj->WorkInHoliday = $point->workInHoliday;
			
			$point_obj->Address->Name = $point->address->name;
			$point_obj->Address->PostalCode = $point->address->postcode;
			$point_obj->Address->City = $point->address->city;
			$point_obj->Address->Street = $point->address->street;
			$point_obj->Address->HouseNumber = $point->address->houseNumber;
			$point_obj->Address->ApartmentNumber = $point->address->apartmentNumber;
			
			foreach (array('mon','tue','wed','thu','fri','sat','sun') as $day_of_week_number => $day_name)
			{
				$open_name = $day_name.'Open';
				$close_name = $day_name.'Close';
				if ($point->$open_name && $point->$close_name)
					$point_obj->OpeningHours[$day_of_week_number] = $point->$open_name.' - '.$point->$close_name;
				else
					$point_obj->OpeningHours[$day_of_week_number] = null;
			}
			$return_value[] = $point_obj;
		}
		return $return_value;
	}
	public static function TestAuthData(DataModels\DhlUser $oDhlUser, $bRaiseException = true)
	{
		if (!$oDhlUser->HasAuthDataForApiCode(Enums\DhlWebApi::DHLPS))
		{
			if ($bRaiseException)
				throw new Exceptions\SourceLoggedException("Brak danych do logowania!");
			else
				return false;
		}
		$request = new DhlPsWebApiClasses\getLabel();
		$request->structure = new DhlPsWebApiClasses\GetLabelStructure();
		$request->structure->authData = new DhlPsWebApiClasses\AuthdataStructure($oDhlUser);
		$request->structure->shipment = '10000000000';
		$request->structure->type = Enums\LabelType::BLP;
		try
		{
			self::GetService()->getLabel($request);
		}
		catch (\SoapFault $ex)
		{
			if ($ex->faultcode == '306')
				return true;
			if ($ex->faultcode == '300')
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
	}
	protected static function _ConvertShipmentToApiAddress(DataModels\Shipment $oShipment, $sSideName)
	{
		/* @var $side DataModels\ShipmentSide */
		$side = $oShipment->$sSideName;
		$dcs = $oShipment->GetTargetCountryService();
		$result = new DhlPsWebApiClasses\AddressStructure();
		$postal_code_max_len = $dcs->PostalCodeMaxLength;
		$result->name = Core\Filters::LoggedMaxLen($side->Address->Name, 60, $sSideName.':Name'); //string(60)
		if (!Core\Validators::MaxLen($side->Address->PostalCode, $postal_code_max_len))
			throw new Exceptions\LoggedException("Kod pocztowy odbiorcy zbyt długi!");
		$result->postcode = $side->Address->PostalCode;
		$result->city = Core\Filters::LoggedMaxLen($side->Address->City, 17, $sSideName.':City'); //string(17)
		$result->street = Core\Filters::LoggedMaxLen($side->Address->Street, 22, $sSideName.':Street'); //string(22)
		$result->houseNumber = Core\Filters::LoggedMaxLen($side->Address->HouseNumber, 7, $sSideName.':HouseNumber'); //string(7)
		$result->apartmentNumber = Core\Filters::LoggedMaxLen($side->Address->ApartmentNumber, 7, $sSideName.':ApartmentNumber'); //string(7)
		return $result;
	}
	protected static function _ConvertContactToApiContact(DataModels\Contact $oContact, $sContext)
	{
		/* @var $oContact DataModels\Contact */
		$result = new DhlPsWebApiClasses\ContactStructure();
		$result->personName = Core\Filters::LoggedMaxLen($oContact->Name, 50, $sContext.':Contact:Name'); //string(50)
		$result->phoneNumber = Core\Filters::LoggedMaxLen($oContact->Phone, 9, $sContext.':Contact:Phone'); //string(9)
		$result->emailAddress = Core\Filters::LoggedMaxLen($oContact->Email, 100, $sContext.':Contact:Email'); //string(100)
		return $result;
	}
	protected static function _ConvertContactToApiPreaviso(DataModels\Contact $oPreaviso, $sContext)
	{
		/* @var $oPreaviso DataModels\Contact */
		$result = new DhlPsWebApiClasses\PreavisoStructure();
		$result->personName = Core\Filters::LoggedMaxLen($oPreaviso->Name, 50, $sContext.':Preaviso:Name'); //string(50)
		$result->phoneNumber = Core\Filters::LoggedMaxLen($oPreaviso->Phone, 9, $sContext.':Preaviso:Phone'); //string(9)
		$result->emailAddress = Core\Filters::LoggedMaxLen($oPreaviso->Email, 100, $sContext.':Preaviso:Email'); //string(100)
		return $result;
	}
	protected static function _ConvertShipmentToApiFullAddress(DataModels\Shipment $oShipment, $sSideName)
	{
		/* @var $side DataModels\ShipmentSide */
		$side = $oShipment->$sSideName;
		$result = new DhlPsWebApiClasses\FullAddressDataStructure();
		$result->address = self::_ConvertShipmentToApiAddress($oShipment, $sSideName); //AddressStructure
		if ($side->Contact->IsFilled())
			$result->contact = self::_ConvertContactToApiContact($side->Contact, $sSideName); //ContactStructure
		if ($side->Preaviso->IsFilled())
			$result->preaviso = self::_ConvertContactToApiPreaviso($side->Preaviso, $sSideName); //PreavisoStructure
		return $result;
	}
	protected static function _ConvertShipmentToApiShip(DataModels\Shipment $oShipment)
	{
		$dcs = $oShipment->GetTargetCountryService();
		$ship = new DhlPsWebApiClasses\ShipStructure();
		$ship->shipper = self::_ConvertShipmentToApiFullAddress($oShipment, 'Shipper'); //FullAddressDataStructure
		$ship->receiver = self::_ConvertShipmentToApiFullAddress($oShipment, 'Receiver'); //FullAddressDataStructure
		if (($oShipment->ParcelIdent == '') || $oShipment->ParcelIdent == null)
			throw new Exceptions\LoggedException("Dostarczenie do ParcelShop wymaga podania nr. identyfikacyjnego");

		$ship->servicePointAccountNumber = $oShipment->ParcelIdent;
		return $ship;
	}
	protected static function _ConvertShipmentToApiBilling(DataModels\Shipment $oShipment)
	{
		if (!isset(Enums\ShippingPaymentType::$Descriptions[$oShipment->ShippingPaymentType]))
			throw new Exceptions\SourceLoggedException("Nieznane oznaczenie sposobu płatności ('{$oShipment->ShippingPaymentType}')!");
		if (!isset(Enums\PaymentType::$Descriptions[$oShipment->PaymentType]))
			throw new Exceptions\SourceLoggedException("Nieznane oznaczenie strony płacącej ('{$oShipment->PaymentType}')!");
		$dcs = $oShipment->GetTargetCountryService();
		$result = new DhlPsWebApiClasses\BillingStructure();
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
		$result->costsCenter = Core\Filters::LoggedMaxLen($oShipment->CostsCenter, 20, 'CostsCenter'); //string(20)
		return $result;
	}
	protected static function _ConvertShipmentToApiArrayOfService(DataModels\Shipment $oShipment)
	{
		self::_CheckIfSpecialServicesAllowed($oShipment);
		/* @var $result DhlPsWebApiClasses\ArrayOfServicestructure */
		$result = new DhlPsWebApiClasses\ArrayOfServicestructure();
		$s_services = $oShipment->SpecialServices;
		if ($s_services->S_UBEZP)
			$result->item[] = new DhlPsWebApiClasses\ServiceStructure(Enums\SpecialService::S_UBEZP, array('serviceValue' => $s_services->UBEZP_Value));
        if ($s_services->S_COD) {
            $result->item[] = new DhlPsWebApiClasses\ServiceStructure(
                Enums\SpecialService::S_COD,
                ['serviceValue' => $s_services->COD_Value, 'collectOnDeliveryForm' => Enums\PaymentType::BANK_TRANSFER]
            );
        }
		if ($s_services->S_ROD)
			$result->item[] = new DhlPsWebApiClasses\ServiceStructure(Enums\SpecialService::S_ROD, array ('textInstruction' => $s_services->ROD_Instruction));
		return $result;
	}
	protected static function _ConvertShipmentToApiShipmentInfo(DataModels\Shipment $oShipment)
	{
		if (!isset(Enums\LabelType::$Descriptions[$oShipment->LabelType]))
			throw new Exceptions\SourceLoggedException("Nieznane oznaczenie typu etykiety ('{$oShipment->LabelType}')!");
		$dcs = $oShipment->GetTargetCountryService();
		$shipment_info = new DhlPsWebApiClasses\ShipmentInfoStructure();
		$shipment_info->dropOffType = $oShipment->DropOffType;
		$shipment_info->serviceType = $oShipment->ServiceType;
		$shipment_info->billing = self::_ConvertShipmentToApiBilling($oShipment);
		$shipment_info->specialServices = self::_ConvertShipmentToApiArrayOfService($oShipment);
		$shipment_info->shipmentDate = $oShipment->ShipmentDate->format('Y-m-d'); //YYYY-MM-DD
		$shipment_info->shipmentStartHour = $oShipment->ShipmentStartHour->format('H:i'); //HH:MM
		$shipment_info->shipmentEndHour = $oShipment->ShipmentEndHour->format('H:i'); //HH:MM
		//
		if (!in_array($oShipment->LabelType, $dcs->AvailableLabelTypes))
		{
			$label_type_name = Enums\LabelType::$Descriptions[$oShipment->LabelType];
			throw new Exceptions\SourceLoggedException("Etykieta typu '{$label_type_name}' nie jest dostępna w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
		}
		$shipment_info->labelType = $oShipment->LabelType;
		return $shipment_info;
	}
	protected static function _ConvertShipmentToApiArrayOfPiece(DataModels\Shipment $oShipment)
	{
		if (!isset(Enums\PackageType::$Descriptions[$oShipment->Package->Type]))
			throw new Exceptions\SourceLoggedException("Nieznane oznaczenie typu przesyłki ('{$oShipment->Package->Type}')!");
		$dcs = $oShipment->GetTargetCountryService();
		/* @var $result DhlPsWebApiClasses\ArrayOfPiecestructure */
		$result = new DhlPsWebApiClasses\ArrayOfPiecestructure();
		$item = new Dhl\DhlPsWebApiClasses\PieceStructure();
		$s_package = $oShipment->Package;
		$package_type_name = Enums\PackageType::$Descriptions[$s_package->Type];
		if (!in_array($s_package->Type, $dcs->AvailablePackageTypes))
			throw new Exceptions\SourceLoggedException("Przesyłka typu '{$package_type_name}' nie jest dostępna w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
		$item->type = $s_package->Type; //string(*16)
		if ($s_package->Type !== Enums\PackageType::ENVELOPE)
		{
			if (!$s_package->Width)
				throw new Exceptions\SourceLoggedException("Parametr 'szerokość' jest wymagany dla przesyłki typu '{$package_type_name}'!");
			$item->width = $s_package->Width; //int
			if (!$s_package->Height)
				throw new Exceptions\SourceLoggedException("Parametr 'wysokość' jest wymagany dla przesyłki typu '{$package_type_name}'!");
			$item->height = $s_package->Height; //int
			if (!$s_package->Length)
				throw new Exceptions\SourceLoggedException("Parametr 'długość' jest wymagany dla przesyłki typu '{$package_type_name}'!");
			$item->lenght = $s_package->Length; //int
			if (!$s_package->Weight)
				throw new Exceptions\SourceLoggedException("Parametr 'waga' jest wymagany dla przesyłki typu '{$package_type_name}'!");
			$item->weight = $s_package->Weight; //int
		}
		$item->quantity = $s_package->Quantity; //int
	
		if ($s_package->NonStandard && !$dcs->AllowNst)
			throw new Exceptions\SourceLoggedException("Opcja NST nie jest dostępna w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}!");
		if ($s_package->NonStandard && $oShipment->SendToParcelShop && !$dcs->AllowNstForParcel)
		{
			throw new Exceptions\SourceLoggedException("Wybrana opcja nie jest dostępna w usłudze '{$dcs->GetName()}' w kraju {$oShipment->GetTargetCountry()->Name}! przy wysyłce do ParcelShop");
		}
		$item->nonStandard = $s_package->NonStandard; //bool
		//$item->blpPieceId = null; //string(32) //UNUSED
		$result->item[] = $item;
		return $result;
	}
	protected static function _ConvertShipmentToApiRequest(DataModels\Shipment $oShipment)
	{
		$request = new DhlPsWebApiClasses\ShipmentStructure();
		$request->ship = self::_ConvertShipmentToApiShip($oShipment); //ShipStructure
		$request->shipmentInfo = self::_ConvertShipmentToApiShipmentInfo($oShipment); //ShipmentInfoStructure
		$request->pieceList = self::_ConvertShipmentToApiArrayOfPiece($oShipment); //ArrayOfPiecestructure
		$request->content = Core\Filters::LoggedMaxLen($oShipment->Content, 30, 'Content'); //string(30)
		$request->comment = Core\Filters::LoggedMaxLen($oShipment->Comment, 100, 'Comment'); //string(100)
		$request->reference = Core\Filters::LoggedMaxLen($oShipment->Reference, 20, 'Reference'); //string(20)
		return $request;
	}
}
?>