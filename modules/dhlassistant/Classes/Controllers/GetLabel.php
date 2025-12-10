<?php

namespace DhlAssistant\Classes\Controllers;

use DhlAssistant\Core\Exceptions;
use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Dhl\Enums;

class GetLabel extends Models\Controller
{

    /**
     * @inheritDoc
     */
    public function Go()
    {
        if (!isset($_GET['id']) || !isset($_GET['type'])) {
            throw new Exceptions\LoggedException('Brak wymaganych parametrów!');
        }

        $id = (int)$_GET['id'];
        $type = $_GET['type'];

        $shipment_object_info = DataModels\Shipment::GetTreeDataObjectInfo();

        if (!Wrappers\DbWrapper::Exists($shipment_object_info, $id)) {
            throw new Exceptions\SourceLoggedException("Zlecenie przesyłki o ID #{$id} nie istnieje!");
        }

        /* @var $shipment DataModels\Shipment */
        $shipment = Wrappers\DbWrapper::Load($shipment_object_info, $id);

        if (!$shipment->IsSended()) {
            throw new Exceptions\LoggedException("Zlecenie przesyłki o ID #{$id} nie zostało jeszcze wysłane!");
        }

        if (!in_array($type, $shipment->GetAvailableLabelTypes())) {
            throw new Exceptions\LoggedException("Nieprawidłowy typ eykiety!");
        }

        $label = Wrappers\DhlWrapper::GetLabel($shipment, $type);

        switch ($type) {
            case Enums\LabelType::LP:
            case Enums\LabelType::BLP:
            case Enums\LabelType::LBLP:
                $extension = '.pdf';
                $content_type = 'application/pdf';
                break;
            case Enums\LabelType::ZBLP:
                $extension = '.zpl';
                $content_type = 'text/plain';
                break;
            default:
                throw new Exceptions\LoggedException("Nieprawidłowy typ etykiety ('{$type}')!");
        }

        $filename = $shipment->DhlShipmentId . '_' . $type . $extension;

        ob_end_clean();

        header('Content-type: ' . $content_type);
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo $label;
        exit;
    }
}
