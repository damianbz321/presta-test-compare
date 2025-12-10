<?php

namespace DhlAssistant\Classes\Controllers;

use DhlAssistant\Core;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Forms;
use DhlAssistant\Traits;

class ShipmentsList extends Models\Controller
{
    use Traits\ControllerWrappedOutput;
    use Traits\ControllerPaginationPreparer;

    /**
     * @inheritDoc
     */
    public function Go()
    {
        Wrappers\ConfigWrapper::CheckIsModuleConfigured();

        $message = null;
        $form = new Forms\ShipmentListFilters();
        $where = null;
        $where_values = [];
        $action = isset($_GET['action']) ? $_GET['action'] : 'default';
        $shipment_object_info = DataModels\Shipment::GetTreeDataObjectInfo();
        $using_filters = true;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['Action:Search'])) {
                $form->PopulateWithPostValues();
            }
        } else {
            $form->PopulateWithGetValues();
        }

        $filters = $form->GetValues();

        if ($action == 'delete') {
            if (!isset($_GET['id'])) {
                throw new Exceptions\LoggedException("Brak parametru ID!");
            }

            $id = (int)$_GET['id'];

            if (!Wrappers\DbWrapper::Exists($shipment_object_info, $id)) {
                throw new Exceptions\SourceLoggedException("Przesyłka o ID #{$id} nie istnieje!");
            }

            $shipment = Wrappers\DbWrapper::Load($shipment_object_info, $id);
            Wrappers\DbWrapper::Delete($shipment);

            $message = 'Zlecenie przesyłki zostało usunięte.';
        }
        if ($form->Validate()) {
            $where_array = [];
            $filter_values = Core\TreeDataHandling::FlatData2TreeData($filters)['Filter'];

            if (strlen($filter_values['Id']) > 0) {
                $where_array[] = 'Id = :Id';
                $where_values[':Id'] = $filter_values['Id'];
            }

            if (strlen($filter_values['Content']) > 0) {
                $where_array[] = 'Content LIKE :Content';
                $where_values[':Content'] = '%' . $filter_values['Content'] . '%';
            }

            if (strlen($filter_values['DateFrom']) > 0) {
                $where_array[] = 'CreationDateTime >= :DateFrom';
                $where_values[':DateFrom'] = $filter_values['DateFrom'] . ' 00:00:00';
            }

            if (strlen($filter_values['DateTo']) > 0) {
                $where_array[] = 'CreationDateTime <= :DateTo';
                $where_values[':DateTo'] = $filter_values['DateTo'] . ' 23:59:59';
            }

            if (strlen($filter_values['Status']) > 0) {
                switch ($filter_values['Status']) {
                    case 'Waiting':
                        $where_array[] = 'DhlShipmentCreationDateTime IS NULL';
                        break;
                    case 'Sended':
                        $where_array[] = 'DhlShipmentCreationDateTime IS NOT NULL';
                        break;
                }
            }

            if ($where_array) {
                $where = implode(' AND ', $where_array);
            } else {
                $using_filters = false;
            }
        } else {
            $filters = (new Forms\ShipmentListFilters())->GetValues();
            $using_filters = false;
        }

        $total_results = Wrappers\DbWrapper::Count($shipment_object_info, $where, $where_values);
        $pagination = $this->PreparePagination($total_results, $filters);

        $shipments = Wrappers\DbWrapper::SearchAndLoad(
            $shipment_object_info,
            $where,
            $where_values,
            'Id DESC',
            $pagination['PerPage'],
            $pagination['Page'] - 1
        );

        $this->MakeWrappedOutput(
            Wrappers\ConfigWrapper::Get('FullName') . ' - lista przesyłek',
            Core\Template::Render(
                'ShipmentsList',
                [
                    'Message' => $message,
                    'Shipments' => $shipments,
                    'Pagination' => $pagination,
                    'Filters' => $filters,
                    'UsingFilters' => $using_filters,
                ]
            )
        );
    }
}

?>