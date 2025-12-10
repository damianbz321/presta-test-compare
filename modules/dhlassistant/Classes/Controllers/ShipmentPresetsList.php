<?php

namespace DhlAssistant\Classes\Controllers;

use DhlAssistant\Core;
use DhlAssistant\Traits;
use DhlAssistant\Wrappers;
use DhlAssistant\Core\Models;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Classes\DataModels;

class ShipmentPresetsList extends Models\Controller
{
    use Traits\ControllerWrappedOutput;
    use Traits\ControllerPaginationPreparer;

    /**
     * @inheritDoc
     */
    public function Go()
    {
        $message = null;
        $where = null;
        $where_values = [];

        $action = isset($_GET['action']) ? $_GET['action'] : 'default';
        $shipment_preset_object_info = DataModels\ShipmentPreset::GetTreeDataObjectInfo();

        if ($action == 'delete') {
            if (!isset($_GET['id'])) {
                throw new Exceptions\LoggedException("Brak parametru ID!");
            }

            $id = (int)$_GET['id'];

            if (!Wrappers\DbWrapper::Exists($shipment_preset_object_info, $id)) {
                throw new Exceptions\SourceLoggedException("Predefinicja o ID #{$id} nie istnieje!");
            }

            $shipment_preset = Wrappers\DbWrapper::Load($shipment_preset_object_info, $id);
            Wrappers\DbWrapper::Delete($shipment_preset);

            $message = 'Predefinicja przesyłki została usunięta.';
        }

        $total_results = Wrappers\DbWrapper::Count($shipment_preset_object_info, $where, $where_values);
        $pagination = $this->PreparePagination($total_results);

        $shipment_presets = Wrappers\DbWrapper::SearchAndLoad(
            $shipment_preset_object_info,
            $where,
            $where_values,
            'Id ASC',
            $pagination['PerPage'],
            $pagination['Page'] - 1
        );

        $this->MakeWrappedOutput(
            Wrappers\ConfigWrapper::Get('FullName') . ' - lista predefinicji przesyłek',
            Core\Template::Render(
                'ShipmentPresetsList',
                [
                    'Message' => $message,
                    'ShipmentPresets' => $shipment_presets,
                    'Pagination' => $pagination,
                ]
            )
        );
    }
}

?>