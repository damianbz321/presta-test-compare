<?php

namespace DhlAssistant\Classes\Controllers;

use DhlAssistant\Core;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Forms;
use DhlAssistant\Traits;

class ShipmentPresetEdit extends Models\Controller
{
    use Traits\ControllerWrappedOutput;

    /**
     * @inheritDoc
     */
    public function Go()
    {
        $shipment_preset_form = new Forms\ShipmentPreset();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $values = $shipment_preset_form->PopulateWithPostValues()->GetValues();

            if (!isset($values['Id'])) {
                throw new Exceptions\LoggedException('Brak wymaganych parametrów!');
            }

            $id = (int)$values['Id'];

            if ($id) {
                if (!Wrappers\DbWrapper::Exists(DataModels\ShipmentPreset::GetTreeDataObjectInfo(), $id)) {
                    throw new Exceptions\SourceLoggedException("Błąd wczytywania predefinicji o ID #{$id}");
                }

                $shipment_preset = Wrappers\DbWrapper::Load(DataModels\ShipmentPreset::GetTreeDataObjectInfo(), $id);
            } else {
                $shipment_preset = new DataModels\ShipmentPreset();
            }

            /* @var $shipment_preset DataModels\ShipmentPreset */
            if ($shipment_preset_form->Validate()) {
                $tree_values = Core\TreeDataHandling::FlatData2TreeData($values);
                $shipment_preset->SetTreeDataValues('PostSet', $tree_values, true);
                $valiadtion_result = $shipment_preset->Validate();
                $shipment_preset_form = new Forms\ShipmentPreset();

                if ($valiadtion_result->IsSuccess()) {
                    if (isset($values['Action:Save'])) {
                        $shipment_preset = Wrappers\DbWrapper::Save($shipment_preset);

                        $shipment_preset_form->PopulateWithValues(
                            Core\TreeDataHandling::TreeData2FlatData(
                                $shipment_preset->GetTreeDataValues(
                                    'PostGet',
                                    true
                                )
                            )
                        );

                        $shipment_preset_form->AddGeneralNotice('Predefinicja zapisana.');
                    } else {
                        throw new Exceptions\LoggedException('Nieznana akcja!');
                    }
                } else {
                    $shipment_preset_form->PopulateWithValues(
                        Core\TreeDataHandling::TreeData2FlatData(
                            $shipment_preset->GetTreeDataValues(
                                'PostGet',
                                true
                            )
                        )
                    );

                    $shipment_preset_form->PopulateWithValidationResult($valiadtion_result);
                }
            }
        } else {
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];

                if (!Wrappers\DbWrapper::Exists(DataModels\ShipmentPreset::GetTreeDataObjectInfo(), $id)) {
                    throw new Exceptions\SourceLoggedException("Predefinicja #{$id} nie istnieje!");
                }

                $shipment_preset = Wrappers\DbWrapper::Load(DataModels\ShipmentPreset::GetTreeDataObjectInfo(), $id);
            } else {
                $shipment_preset = new DataModels\ShipmentPreset();
            }

            $shipment_preset_form->PopulateWithValues(
                Core\TreeDataHandling::TreeData2FlatData(
                    $shipment_preset->GetTreeDataValues(
                        'PostGet',
                        true
                    )
                )
            );
        }

        $this->MakeWrappedOutput(
            Wrappers\ConfigWrapper::Get('FullName') . ' - edycja predefinicji przesyłki',
            Core\Template::Render('ShipmentPresetEdit', ['Form' => $shipment_preset_form])
        );
    }
}
