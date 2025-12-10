<?php

namespace DhlAssistant\Classes\Dhl\Dhl24WebApiClasses;

use DhlAssistant\Classes\DataModels;

class Dhl24WebApiClasses
{
    /**
     * @return array
     */
    public static function GetClassList()
    {
        return [
            'AuthData' => null,
            'Billing' => null,
            'Service' => null,
            'ArrayOfService' => null,
            'ShipmentTime' => null,
            'ShipmentInfo' => null,
            'Address' => null,
            'ReceiverAddress' => null,
            'PreavisoContact' => null,
            'NeighbourAddress' => null,
            'Addressat' => null,
            'ReceiverAddressat' => null,
            'Ship' => null,
            'Package' => null,
            'ArrayOfPiackage' => null,
            'CreateShipmentRequest' => null,
            'createShipment' => null,
            'Label' => null,
            'CreateShipmentResponse' => null,
            'createShipmentResponse_ret' => 'createShipmentResponse',
            'ItemToPrint' => null,
            'ArrayOfItemtoprint' => null,
            'getLabes' => null,
            'ItemToPrintResponse' => null,
            'ArrayOfItemtoprintresponse' => null,
            'getLabelsResponse' => null,
            'DeleteShipmentRequest' => null,
            'deleteShipment' => null,
            'DeleteShipmentResponse' => null,
            'deleteShipmentResponse_ret' => 'deleteShipmentResponse',
            'ArrayOfString' => null,
            'bookCourier' => null,
            'bookCourierResponse' => null,
            'cancelCourierBooking' => null,
            'DeleteNotification' => null,
            'ArrayOfDeletenotification' => null,
            'cancelCourierBookingResponse' => null,
            'PnpRequest' => null,
            'getPnp' => null,
            'PnpResponse' => null,
            'getPnpResponse' => null,
            'getShipmentScan' => null,
            'ScanToPrintResponse' => null,
            'getShipmentScanResponse' => null,
            'getPostalCodeServices' => null,
            'PostalCodeServicesResponse' => null,
            'getPostalCodeServicesResponse' => null,
            'getTrackAndTraceInfo' => null,
            'TrackAndTraceEvent' => null,
            'ArrayOfTrackandtraceevent' => null,
            'TrackAndTraceResponse' => null,
            'getTrackAndTraceInfoResponse' => null,
        ];
    }

    /**
     * @return array
     */
    public static function GetClassMap()
    {
        $result = [];

        foreach (self::GetClassList() as $class_name => $map_name) {
            $result[is_null($map_name) ? $class_name : $map_name] = __NAMESPACE__ . '\\' . $class_name;
        }

        return $result;
    }
}

class AuthData
{
    public $username = null;
    public $password = null;

    /**
     * @param DataModels\DhlUser $oDhlUser
     */
    public function __construct(DataModels\DhlUser $oDhlUser)
    {
        /* @var $oDhlUser DataModels\DhlUser */
        $this->username = $oDhlUser->Dhl24Login;
        $this->password = $oDhlUser->Dhl24Password;
    }
}

/**
 *
 */
class Billing
{
    public $shippingPaymentType = null;
    public $billingAccountNumber = null;
    public $paymentType = null;
    public $costsCenter = null;
}

/**
 *
 */
class Service
{
    public $serviceType = null;
    public $serviceValue = null;
    public $textInstruction = null;
    public $collectOnDeliveryForm = null;

    /**
     * @param $sServiceType
     * @param null $aValues
     */
    public function __construct($sServiceType, $aValues = null)
    {
        $this->serviceType = $sServiceType;

        if ($aValues && is_array($aValues)) {
            foreach ($aValues as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }
}

/**
 *
 */
class ArrayOfService
{
    /**
     * @var Service[]
     */
    public $item = [];
}

/**
 *
 */
class ShipmentTime
{
    public $shipmentDate = null;
    public $shipmentStartHour = null;
    public $shipmentEndHour = null;
}

/**
 *
 */
class ShipmentInfo
{
    public $dropOffType = null;
    public $serviceType = null;
    public $billing = null;
    public $specialServices = null;
    public $shipmentTime = null;
    public $labelType = null;
}

/**
 *
 */
class Address
{
    public $name = null;
    public $postalCode = null;
    public $city = null;
    public $street = null;
    public $houseNumber = null;
    public $apartmentNumber = null;
}

/**
 *
 */
class ReceiverAddress
{
    public $country = null;
    public $isPackstation = null;
    public $isPostfiliale = null;
    public $postnummer = null;
    public $name = null;
    public $postalCode = null;
    public $city = null;
    public $street = null;
    public $houseNumber = null;
    public $apartmentNumber = null;
}

/**
 *
 */
class PreavisoContact
{
    public $personName = null;
    public $phoneNumber = null;
    public $emailAddress = null;
}

/**
 *
 */
class NeighbourAddress
{
    public $name = null;
    public $postalCode = null;
    public $city = null;
    public $street = null;
    public $houseNumber = null;
    public $apartmentNumber = null;
    public $contactPhone = null;
    public $contactEmail = null;
}

/**
 *
 */
class Addressat
{
    public $preaviso = null;
    public $contact = null;
    public $address = null;
}

/**
 *
 */
class ReceiverAddressat
{
    public $preaviso = null;
    public $contact = null;
    public $address = null;
}

/**
 *
 */
class Ship
{
    public $shipper = null;
    public $receiver = null;
    public $neighbour = null;
}

/**
 *
 */
class Package
{
    public $type = null;
    public $weight = null;
    public $width = null;
    public $height = null;
    public $length = null;
    public $quantity = null;
    public $nonStandard = null;
    public $euroReturn = null;
    public $blpPieceId = null;
}

/**
 *
 */
class ArrayOfPackage
{
    public $item = [];
}

/**
 *
 */
class CreateShipmentRequest
{
    public $shipmentInfo = null;
    public $content = null;
    public $comment = null;
    public $reference = null;
    public $ship = null;
    public $pieceList = null;
}

/**
 *
 */
class createShipment
{
    public $authData = null;
    public $shipment = null;
}

/**
 *
 */
class Label //resp
{
    public $labelType;
    public $labelFormat;
    public $labelContent;
}

/**
 *
 */
class CreateShipmentResponse
{
    public $shipmentNotificationNumber;
    public $shipmentTrackingNumber;
    public $packagesTrackingNumbers;
    public $dispatchNotificationNumber;

    /**
     * @var Label
     */
    public $label;
}

/**
 *
 */
class createShipmentResponse_ret
{
    /**
     * @var CreateShipmentResponse
     */
    public $createShipmentResult;
}

/**
 *
 */
class ItemToPrint
{
    public $labelType = null;
    public $shipmentId = null;
}

/**
 *
 */
class ArrayOfItemtoprint
{
    /**
     * @var ItemToPrint[]
     */
    public $item = [];
}

/**
 *
 */
class getLabels
{
    public $authData = null;

    /**
     * @var ArrayOfItemtoprint
     */
    public $itemsToPrint = null;
}

/**
 *
 */
class ItemToPrintResponse
{
    public $labelType;
    public $shipmentId;
    public $labelName;
    public $labelData;
    public $labelMimeType;
}

/**
 *
 */
class ArrayOfItemtoprintresponse
{
    /**
     * @var ItemToPrintResponse[]
     */
    public $item = [];
}

/**
 *
 */
class getLabelsResponse
{
    /**
     * @var ArrayOfItemtoprintresponse
     */
    public $getLabelsResult;
}

/**
 *
 */
class DeleteShipmentRequest
{
    public $shipmentIdentificationNumber = null;
    public $dispatchIdentificationNumber = null;
}

/**
 *
 */
class deleteShipment
{
    public $authData = null;
    public $shipment = null;
}

/**
 *
 */
class DeleteShipmentResponse
{
    public $id;
    public $result;
    public $error;
}

/**
 *
 */
class deleteShipmentResponse_ret
{
    /**
     * @var DeleteShipmentResponse
     */
    public $deleteShipmentResult;
}

/**
 *
 */
class ArrayOfString
{
    public $item = [];
}

/**
 *
 */
class bookCourier
{
    public $authData = null;
    public $pickupDate = null;
    public $pickupTimeFrom = null;
    public $pickupTimeTo = null;
    public $additionalInfo = null;

    /**
     * @var ArrayOfString
     */
    public $shipmentIdList = null;

    public $shipmentOrderInfo = null;
}

/**
 *
 */
class bookCourierResponse
{
    /**
     * @var ArrayOfString
     */
    public $bookCourierResult;
}

/**
 *
 */
class cancelCourierBooking
{
    public $authData = null;

    /**
     * @var ArrayOfString
     */
    public $orders = null;
}

/**
 *
 */
class DeleteNotification
{
    public $id;
    public $result;
    public $error;
}

/**
 *
 */
class ArrayOfDeletenotification
{
    /**
     * @var DeleteNotification[]
     */
    public $item = [];
}

/**
 *
 */
class cancelCourierBookingResponse
{
    /**
     * @var ArrayOfDeletenotification
     */
    public $cancelCourierBookingResult;
}

/**
 *
 */
class PnpRequest
{
    public $authData = null;
    public $date = null;
    public $type = null;
}

/**
 *
 */
class getPnp
{
    /**
     * @var PnpRequest
     */
    public $pnpRequest;
}

/**
 *
 */
class PnpResponse
{
    public $fileName;
    public $fileData;
    public $fileMimeType;
}

class getPnpResponse
{
    /**
     * @var PnpResponse
     */
    public $getPnpResult;
}

/**
 *
 */
class getShipmentScan
{
    public $authData = null;
    public $shipmentId = null;
}

/**
 *
 */
class ScanToPrintResponse
{
    public $scanData;
    public $scanMimeType;
}

/**
 *
 */
class getShipmentScanResponse
{
    /**
     * @var ScanToPrintResponse
     */
    public $getShipmentScanResult;
}

/**
 *
 */
class getPostalCodeServices
{
    public $authData = null;
    public $postCode = null;
    public $pickupDate = null;
}

/**
 *
 */
class PostalCodeServicesResponse
{
    public $domesticExpress9;
    public $domesticExpress12;
    public $deliveryEvening;
    public $pickupOnSaturday;
    public $deliverySaturday;
    public $exPickupFrom;
    public $exPickupTo;
    public $drPickupFrom;
    public $drPickupTo;
}

/**
 *
 */
class getPostalCodeServicesResponse
{
    /**
     * @var PostalCodeServicesResponse
     */
    public $getPostalCodeServicesResult;
}

/**
 *
 */
class getTrackAndTraceInfo
{
    public $authData = null;
    public $shipmentId = null;
}

/**
 *
 */
class TrackAndTraceEvent
{
    public $status;
    public $description;
    public $terminal;
    public $timestamp;
}

/**
 *
 */
class ArrayOfTrackandtraceevent
{
    /**
     * @var TrackAndTraceEvent[]
     */
    public $item = [];
}

/**
 *
 */
class TrackAndTraceResponse
{
    public $shipmentId;
    public $receivedBy;

    /**
     * @var ArrayOfTrackandtraceevent
     */
    public $events;
}

/**
 *
 */
class getTrackAndTraceInfoResponse
{
    /**
     * @var TrackAndTraceResponse
     */
    public $getTrackAndTraceInfoResult;
}

?>