<?php
namespace DhlAssistant\Classes\Dhl\DhlPsWebApiClasses;

use DhlAssistant\Classes\DataModels;

class DhlPsWebApiClasses
{
	public static function GetClassList()
	{ 
		return array
		(
			'AuthdataStructure' => null,
			'AddressStructure' => null,
			'ContactStructure' => null,
			'PreavisoStructure' => null,
			'FullAddressDataStructure' => null,
			'ShipStructure' => null,
			'BillingStructure' => null,
			'ServiceStructure' => null,
			'ArrayOfServicestructure' => null,
			'ShipmentInfoStructure' => null,
			'PieceStructure' => null,
			'ArrayOfPiecestructure' => null,
			'ShipmentStructure' => null,
			'CreateShipmentStructure' => null,
			'createShipment' => null,
			'LabelStructure' => null,
			'CreateShipmentResponseStructure' => null,
			'createShipmentResponse' => null,
			'DeleteShipmentStructure' => null,
			'deleteShipment' => null,
			'DeleteShipmentResponseStructure' => null,
			'deleteShipmentResponse' => null,
			'GetLabelStructure' => null,
			'getLabel' => null,
			'getLabelResponse' => null,
			'GetPnpStructure' => null,
			'getPnp' => null,
			'getPnpResponse' => null,
			'GetNearestServicepointsStructure' => null,
			'getNearestServicepoints' => null,
			'SearchAddressStructure' => null,
			'PointStructure' => null,
			'ArrayOfPointstructure' => null,
			'GetNearestServicepointsResponseStructure' => null,
			'getNearestServicepointsResponse' => null,
		);
	}
	public static function GetClassMap()
	{
		$result = array();
		foreach (self::GetClassList() as $class_name => $map_name)
			$result[is_null($map_name) ? $class_name : $map_name]=__NAMESPACE__.'\\'.$class_name;
		return $result;	
	}
}

class AuthdataStructure
{
	public $username = null; //string (32)
	public $password = null; //string (32)
	
	public function __construct(DataModels\DhlUser $oDhlUser)
	{
		/* @var $oDhlUser DataModels\DhlUser */
		$this->username = $oDhlUser->DhlPsLogin;
		$this->password = $oDhlUser->DhlPsPassword;
	}
}
class AddressStructure
{
	public $name = null; //string(60) 	Tak 	Nazwa (imię i nazwisko bądź nazwa firmy)
	public $postcode = null; //string(10) 	Tak 	Kod pocztowy (bez myślnika)
	public $city = null; //string(17) 	Tak 	Miejscowość
	public $street = null; //string(22) 	Tak 	Ulica
	public $houseNumber = null; //string(7) 	Tak 	Numer domu
	public $apartmentNumber = null; //string(7) 	Nie 	Numer lokalu
}
class ContactStructure
{
	public $personName = null; //string(50) 	Nie 	Nazwa osoby kontaktowej
	public $phoneNumber = null; //string(9) 	Nie 	Numer telefonu (9 cyfr)
	public $emailAddress = null; //string(100) 	Nie 	Adres email
}
class PreavisoStructure
{
	public $personName = null; //string(50) 	Nie 	Nazwa osoby kontaktowej
	public $phoneNumber = null; //string(9) 	Nie 	Numer telefonu (9 cyfr)
	public $emailAddress = null; //string(100) 	Nie 	Adres email
}
class FullAddressDataStructure
{
	public $address = null; //AddressStructure
	public $contact = null; //ContactStructure
	public $preaviso = null; //PreavisoStructure
}
class ShipStructure
{
	public $shipper; //FullAddressDataStructure
	public $receiver; //FullAddressDataStructure
	public $servicePointAccountNumber; //string(7): Numer SAP service pointu, wymagany do pobrania adresu
}
class BillingStructure
{
	public $shippingPaymentType = null; //string(*16) 	Tak 	Określenie strony, która zostanie obciążona kosztami, możliwe opcje: SHIPPER - nadawca, RECEIVER - odbiorca, USER - strona trzecia
	public $billingAccountNumber = null; //integer 	Tak 	Numer klienta, który będzie obciążony kosztami
	public $paymentType = null; //string(*16) 	Tak 	Wybór sposobu płatności, możliwe wartości: CASH - płatność gotówką, BANK_TRANSFER - przelew (tylko dla klientów z umową i numerem SAP)
	public $costsCenter = null; //string(20) 	Tak 	Miejsce powstawania kosztów
}
class ServiceStructure
{
	public $serviceType = null; //string 	Tak 	Rodzaj usługi dodatkowej, możliwe wartości: 1722 - Doręczenie w godzinach 18-22, SOBOTA - Doręczenie w sobotę, NAD_SOBOTA - Nadanie w sobotę, UBEZP - Ubezpieczenie przesyłki, COD - Zwrot pobrania, PDI - Informacje przed doręczeniem, ROD - Zwrot potwierdzonych dokumentów, POD - Potwierdzenie doręczenia, SAS - Doręczenie do sąsiada ODB - Odbiór własny
	public $serviceValue = null; //float 	Nie 	Deklarowana wartość, wymagana podczas zamawiania usług ubezpieczenie lub zwrot pobrania
	public $textInstruction = null; //string(32) 	Nie 	Nazwa dokumentu zwrotnego w usłudze ROD
	public $collectOnDeliveryForm = null; //string(*16) 	Nie 	Forma zwrotu pobrania w usłudze COD, dostępne wartości: CASH - płatność gotówką, BANK_TRANSFER - przelew (tylko dla klientów z umową i numerem SAP)
	
	public function __construct($sServiceType, $aValues = null)
	{
		$this->serviceType = $sServiceType;
		if ($aValues && is_array($aValues))
			foreach ($aValues as $key=>$value)
				if (property_exists($this, $key))
					$this->$key = $value;
	}
}
class ArrayOfServicestructure
{
	/**
	 * @var ServiceStructure[]
	 */
	public $item = array(); //array<ServiceStructure>
}
class ShipmentInfoStructure
{
	public $dropOffType = null; //string
	public $serviceType = null; //string
	public $billing = null; //BillingStructure
	public $specialServices = null; //ArrayOfServicestructure
	public $shipmentDate = null; //string 	Tak 	Data nadania w formacie RRRR-MM-DD
	public $shipmentStartHour = null; //string(32) 	Tak 	Początek zakresu godzinowego, w którym kurier ma odebrać przesyłkę; w formacie GG:MM
	public $shipmentEndHour = null; //string(32) 	Tak 	Koniec zakresu godzinowego, w którym kurier ma odebrać przesyłkę; w formacie GG:MM
	public $labelType = null; //string
}
class PieceStructure
{
	public $type = null; //string(*16) 	Tak 	Typ paczki, możliwe wartości: ENVELOPE - przesyłka kopertowa, PACKAGE - paczka, PALLET - paleta
	public $width = null; //integer 	Tak 	Szerokość paczki (w centymetrach), nie wymagane dla type = ENVELOPE
	public $height = null; //integer 	Tak 	Wysokość paczki (w centymetrach), nie wymagane dla type = ENVELOPE
	public $lenght = null; //integer 	Tak 	Długość paczki (w centymetrach), nie wymagane dla type = ENVELOPE
	public $weight = null; //integer 	Tak 	Waga paczki(w kilogramach), nie wymagane dla type = ENVELOPE
	public $quantity = null; //integer 	Tak 	Ilość paczek tego typu
	public $nonStandard = null; //bool 	Nie 	Oznaczenie, że paczka jest niestandardowa (true / false)
	public $blpPieceId = null; //string(32) 	Nie 	Identyfikator paczki (JJD) na etykietę BLP (dla klientów, którzy prowadzą własną numerację)
}
class ArrayOfPiecestructure
{
	/**
	 * @var PieceStructure[]
	 */
	public $item = array(); //array<PieceStructure>
}
class ShipmentStructure
{
	/**
	 * @var ShipStructure
	 */
	public $ship = null; //ShipStructure
	/**
	 * @var ShipmentInfoStructur
	 */
	public $shipmentInfo = null; //ShipmentInfoStructure
	/**
	 * @var ArrayOfPiecestructure
	 */
	public $pieceList = null; //ArrayOfPiecestructure
	public $content = null; //string(30)	Tak 	Zawartość przesyłki
	public $comment = null; //string(100)	Nie 	Dodatkowy komentarz, do wydruku na liście przewozowym. Pierwsze 50 znaków wiadomości zostanie również przekazane kurierowi jako uwagi zleceniodawcy
	public $reference = null; //string(20)	Nie 	Numer referencyjny przesyłki
}
class CreateShipmentStructure
{
	public $authData = null; //AuthdataStructure
	/**
	 * @var ShipmentStructure
	 */
	public $shipmentData = null; //ShipmentStructure
}
class createShipment
{
	/**
	 * @var CreateShipmentStructure
	 */
	public $shipment = null; //CreateShipmentStructure
}
class LabelStructure //resp
{
	public $labelType; //string - Typ etykiety zwrotnej (BLP/ZBLP/LBLP?/PNP)
	public $labelFormat; //string - Format MIME etykiety
	public $labelContent; //string - Zawartość etykiety (zakodowana w base64)
	public $labelName; //string - Nazwa pliku etykiety
}
class CreateShipmentResponseStructure //resp
{
	public $shipmentNumber; //string: Numer listu przewozowego przesyłki
	/**
	 * @var LabelStructure
	 */
	public $label; //LabelStructure
	public $dispatchNumber; //string: ?Identyfikator zamówienia kuriera?
}
class createShipmentResponse //resp
{
	/**
	 * @var CreateShipmentResponseStructure
	 */
	public $createShipmentResult; //CreateShipmentResponseStructure
}
class DeleteShipmentStructure
{
	public $authData = null; //AuthdataStructure
	public $shipment = null; //string(11): Numer przesyłki
}
class deleteShipment
{
	/**
	 * @var DeleteShipmentStructure
	 */
	public $shipment = null; //DeleteShipmentStructure
}
class DeleteShipmentResponseStructure //resp
{
	public $status; //string
}
class deleteShipmentResponse //resp
{
	/**
	 * @var DeleteShipmentResponseStructure
	 */
	public $deleteShipmentResult; //DeleteShipmentResponseStructure
}
class GetLabelStructure
{
	public $authData = null; //AuthdataStructure
	public $shipment = null; //string(11): Numer przesyłki
	public $type = null; //string(4): BLP/ZBLP/LBLP?
}
class getLabel
{
	/**
	 * @var GetLabelStructure
	 */
	public $structure = null; //GetLabelStructure
}
class getLabelResponse //resp
{
	/**
	 * @var LabelStructure
	 */
	public $getLabelResult; //LabelStructure
}
class GetPnpStructure
{
	public $authData = null; //AuthdataStructure
	public $shipmentDate = null; //string(10): Data dla której ma zostać wygenerowany raport PNP (RRRR-MM-DD
}
class getPnp
{
	/**
	 * @var GetPnpStructure
	 */
	public $structure = null; //GetPnpStructure
}
class getPnpResponse //resp
{
	/**
	 * @var LabelStructure
	 */
	public $getPnpResult; //LabelStructure
}
class GetNearestServicepointsStructure
{
	public $authData = null; //AuthdataStructure
	public $postcode = null; //string(12*): Kod pocztowy, bez myślnika
	public $city = null; //string(17*): Nazwa miejscowości
	public $radius = null; //int: Promień w km
}
class getNearestServicepoints
{
	/**
	 * @var GetNearestServicepointsStructure
	 */
	public $structure = null; //GetNearestServicepointsStructure
}
class SearchAddressStructure //resp
{
	public $name; //string
	public $postcode; //string
	public $city; //string
	public $street; //string
	public $houseNumber; //string
	public $apartmentNumber; //string
}
class PointStructure //resp
{
	/**
	 * @var SearchAddressStructure
	 */
	public $address; //SearchAddressStructure
	public $description; //string
	public $longitude; //string
	public $latitude; //string
	public $sap; //string
	public $monOpen; //string
	public $monClose; //string
	public $tueOpen; //string
	public $tueClose; //string
	public $wedOpen; //string
	public $wedClose; //string
	public $thuOpen; //string
	public $thuClose; //string
	public $friOpen; //string
	public $friClose; //string
	public $satOpen; //string
	public $satClose; //string
	public $sunOpen; //string
	public $sunClose; //string
	public $workInHoliday; //bool
	public $name; //string
}
class ArrayOfPointstructure //resp
{
	/**
	 * @var PointStructure[]
	 */
	public $item = array(); //array<PointStructure>
}
class GetNearestServicepointsResponseStructure //resp
{
	/**
	 * @var ArrayOfPointstructure
	 */
	public $points; //ArrayOfPointstructure
	public $count; //int: ?nieudokumentowane
}
class getNearestServicepointsResponse //resp
{
	/**
	 * @var GetNearestServicepointsResponseStructure
	 */
	public $getNearestServicepointsResult; //GetNearestServicepointsResponseStructure
}
?>