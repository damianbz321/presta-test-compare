<?php

namespace DhlAssistant\Classes\Controllers;

use DhlAssistant\Classes\DataModels\ShipmentSpecialServices;
use DhlAssistant\Core;
use DhlAssistant\Traits;
use DhlAssistant\Wrappers;
use DhlAssistant\Core\Models;
use DhlAssistant\Classes\Forms;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Managers\DhlApiManager;

use DhlAssistant\Wrappers\DbWrapper;

use DhlAssistant\Classes\DataModels\DhlUser;
use DhlAssistant\Classes\Dhl\Dhl24WebApiWrapper;
use DhlAssistant\Classes\Dhl\Dhl24WebApiClasses\bookCourier;
use DhlAssistant\Classes\Dhl\Dhl24WebApiClasses\Dhl24WebApiClasses;

class ShipmentEdit extends Models\Controller
{
    use Traits\ControllerWrappedOutput;

    /**
     * @var string
     */
    protected $date;
    protected $postalCodeServices = [];
    protected $downloadLabel;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->date = $this->getCurrentDate();
    }

    /**
     * @inheritDoc
     */
    public function Go()
    {
        Wrappers\ConfigWrapper::CheckIsModuleConfigured();

        $shipment = new DataModels\Shipment();
        $shipment_form = new Forms\Shipment();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $values = $shipment_form->PopulateWithPostValues()->GetValues();

            if (!isset($values['Id'])) {
                throw new Exceptions\LoggedException('Brak wymaganych parametrów!');
            }

            $id = (int)$values['Id'];

            if (!Wrappers\DbWrapper::Exists(DataModels\Shipment::GetTreeDataObjectInfo(), $id)) {
                throw new Exceptions\SourceLoggedException("Błąd wczytywania przesyłki o ID #{$id}");
            }

            $shipment = Wrappers\DbWrapper::Load(DataModels\Shipment::GetTreeDataObjectInfo(), $id);

            /* @var $shipment DataModels\Shipment */
            if ($shipment->IsSended()) {
                throw new Exceptions\LoggedException('Ta przesyłka została już zlecona!');
            }

            $tree_values = Core\TreeDataHandling::FlatData2TreeData($values);
            $shipment->SetTreeDataValues('PostSet', $tree_values, true);

            if (isset($values['ShipmentPreset:Apply'])) {
                $shipment_preset_id = (int)$values['ShipmentPreset:ShipmentPresetId'];

                if (!Wrappers\DbWrapper::Exists(
                    DataModels\ShipmentPreset::GetTreeDataObjectInfo(),
                    $shipment_preset_id)
                ) {
                    throw new Exceptions\SourceLoggedException(
                        "Wybrana predefinicja przesyłki nie istnieje! (ID #{$shipment_preset_id})"
                    );
                }

                $shipment_preset = Wrappers\DbWrapper::Load(
                    DataModels\ShipmentPreset::GetTreeDataObjectInfo(),
                    $shipment_preset_id
                );

                $shipment->ApplyShipmentPreset($shipment_preset);

                $shipment_form->PopulateWithValues(
                    Core\TreeDataHandling::TreeData2FlatData(
                        $shipment->GetTreeDataValues(
                            'PostGet',
                            true
                        )
                    )
                );

                $shipment_form->AddGeneralNotice('Predefinicja przesyłki załadowana');
            } else {
                if ($shipment_form->Validate()) {
                    $shipment->ClearErrors();
                    if (isset($values['ShipmentPreset:Apply'])) {
                        $shipment_preset_id = (int)$values['ShipmentPreset:ShipmentPresetId'];

                        if (!Wrappers\DbWrapper::Exists(
                            DataModels\ShipmentPreset::GetTreeDataObjectInfo(),
                            $shipment_preset_id)
                        ) {
                            throw new Exceptions\SourceLoggedException(
                                "Wybrana predefinicja przesyłki nie istnieje! (#{$shipment_preset_id})"
                            );
                        }

                        $shipment_preset = Wrappers\DbWrapper::Load(
                            DataModels\ShipmentPreset::GetTreeDataObjectInfo(),
                            $shipment_preset_id
                        );

                        $shipment->ApplyShipmentPreset($shipment_preset);
                    }

                    $valiadtion_result = $shipment->Validate('CheckLegal');
                    $shipment_form = new Forms\Shipment();

                    if ($valiadtion_result->IsSuccess()) {
                        if (isset($values['Action:Save'])) {
                            $shipment = Wrappers\DbWrapper::Save($shipment);
                            $shipment_form->AddGeneralNotice('Przesyłka zapisana.');
                        } else {
                            if (isset($values['Action:Send'])) {
                                try {
                                    $shipment->ApplyShipperPreset(
                                        Wrappers\ConfigWrapper::Get('DefaultShipperPreset')
                                    );

                                    if ($shipment->ShippingPaymentType == \DhlAssistant\Classes\Dhl\Enums\ShippingPaymentType::USER ) {
                                        $fullAddressName = $shipment->SenderCompany . ' ' . $shipment->SenderName;
                                        $shipment->Shipper->Address->Name = trim($fullAddressName);
                                        $shipment->Shipper->Address->PostalCode = $shipment->SenderPostalCode;
                                        $shipment->Shipper->Address->City = $shipment->SenderCity;
                                        $shipment->Shipper->Address->Street = $shipment->SenderStreet;
                                        $shipment->Shipper->Address->HouseNumber = $shipment->SenderHouseNumber;
                                        $shipment->Shipper->Address->ApartmentNumber = $shipment->SenderApartmentNumber;
                                        $shipment->Shipper->Contact->Phone = $shipment->SenderPhone;
                                        $shipment->Shipper->Contact->Email = $shipment->SenderEmail;
                                        $shipment->Shipper->Contact->Name = $shipment->SenderName;

                                        $shipment->Shipper->Preaviso->Name = $shipment->SenderName;
                                        $shipment->Shipper->Preaviso->Phone = $shipment->SenderPhone;
                                        $shipment->Shipper->Preaviso->Email = $shipment->SenderEmail;
                                    }
                                    $shipment->Shipper->Address->PostalCode = preg_replace(
                                        "/[^0-9]/",
                                        "",
                                        $shipment->Shipper->Address->PostalCode
                                    );

                                    Wrappers\DhlWrapper::Send($shipment);
                                    $shipment = Wrappers\DbWrapper::Save($shipment);
                                    $this->setDownloadLabel($shipment);
                                    $shipment_form->AddGeneralNotice('Zlecenie przesyłki wysłane.');
                                    Wrappers\SourceWrapper::ShipmentSendedEvent($shipment); // can throw exception
                                } catch (\Exception $Ex) {
                                    $shipment_form->AddGeneralError($Ex->getMessage());
                                }
                            } else {
                                throw new Exceptions\LoggedException('Nieznana akcja!');
                            }
                        }
                    } else {
                        $shipment_form->PopulateWithValidationResult($valiadtion_result);
                    }

                    $shipment_form->PopulateWithValues(
                        Core\TreeDataHandling::TreeData2FlatData(
                            $shipment->GetTreeDataValues(
                                'PostGet',
                                true
                            )
                        )
                    );
                }
            }
        } else {
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                if (!Wrappers\DbWrapper::Exists(DataModels\Shipment::GetTreeDataObjectInfo(), $id)) {
                    throw new Exceptions\SourceLoggedException("Przesyłka #{$id} nie istnieje!");
                }

                $shipment = Wrappers\DbWrapper::Load(DataModels\Shipment::GetTreeDataObjectInfo(), $id);
            } else {
                if (isset($_GET['source_id'])) {
                    $source_id = (int)$_GET['source_id'];

                    if (!Wrappers\SourceWrapper::SourceExists($source_id)) {
                        throw new Exceptions\SourceLoggedException(
                            "Wybrane zamówienie nie istnieje! (#{$source_id}"
                        );
                    }

                    if (Wrappers\SourceWrapper::HasSourceShipment($source_id)) {
                        $shipment = Wrappers\SourceWrapper::GetShipmentForSource($source_id);
                    } else {
                        $shipment = Wrappers\SourceWrapper::ImportShipmentBySourceId($source_id);
                    }
                } else {
                    throw new Exceptions\LoggedException("Nie wskazano źródła zlecenia!");
                }
            }

            $action = 'default';

            if (isset($_GET['action'])) {
                $action = $_GET['action'];
            }

            if ($action == 'cancel') {
                if (!$shipment->IsSended()) {
                    throw new Exceptions\LoggedException("Nie można wycofać nie zleconej przesyłki!");
                }
                try {
                    Wrappers\DhlWrapper::Delete($shipment);
                    $shipment = Wrappers\DbWrapper::Save($shipment);
                    $shipment_form->AddGeneralNotice("Zlecenie przesyłki zostało wycofane.");
                    Wrappers\SourceWrapper::ShipmentCanceledEvent($shipment); // can throw exception
                } catch (\Exception $Ex) {
                    $shipment_form->AddGeneralError($Ex->getMessage());
                }
            }

            $flatData = Core\TreeDataHandling::TreeData2FlatData(
                $shipment->GetTreeDataValues(
                    'PostGet',
                    true)
            );
            if (array_key_exists('CostsCenter', $flatData) && empty($flatData['CostsCenter'])) {
                $flatData['CostsCenter'] = Wrappers\ConfigWrapper::Get('HasDefaultShipperPreset') ?
                    Wrappers\ConfigWrapper::Get('DefaultShipperPreset')->CostsCenter :
                    '';
            }

            if (array_key_exists('DropOffType', $flatData) && empty($flatData['DropOffType'])) {
                $flatData['DropOffType'] = Wrappers\ConfigWrapper::Get('HasDefaultShipperPreset') ?
                    Wrappers\ConfigWrapper::Get('DefaultShipperPreset')->DropOffType :
                    '';
            }

            if (array_key_exists('LabelType', $flatData) && empty($flatData['LabelType'])) {
                $flatData['LabelType'] = Wrappers\ConfigWrapper::Get('HasDefaultShipperPreset') ?
                    Wrappers\ConfigWrapper::Get('DefaultShipperPreset')->LabelType :
                    '';
            }

            if (array_key_exists('Package:Weight', $flatData) && empty($flatData['Package:Weight'])) {
                $flatData['Package:Weight'] = Wrappers\ConfigWrapper::Get('HasDefaultShipperPreset') ?
                    Wrappers\ConfigWrapper::Get('DefaultShipperPreset')->Weight :
                    '';
            }

            if (array_key_exists('Package:Width', $flatData) && empty($flatData['Package:Width'])) {
                $flatData['Package:Width'] = Wrappers\ConfigWrapper::Get('HasDefaultShipperPreset') ?
                    Wrappers\ConfigWrapper::Get('DefaultShipperPreset')->Width :
                    '';
            }

            if (array_key_exists('Package:Height', $flatData) && empty($flatData['Package:Height'])) {
                $flatData['Package:Height'] = Wrappers\ConfigWrapper::Get('HasDefaultShipperPreset') ?
                    Wrappers\ConfigWrapper::Get('DefaultShipperPreset')->Height :
                    '';
            }

            if (array_key_exists('Package:Length', $flatData) && empty($flatData['Package:Length'])) {
                $flatData['Package:Length'] = Wrappers\ConfigWrapper::Get('HasDefaultShipperPreset') ?
                    Wrappers\ConfigWrapper::Get('DefaultShipperPreset')->Length :
                    '';
            }

            $order_id = $shipment->IdSource;
            $order = new \Order((int)$order_id);
            $customer_message_nka=nl2br($order->getFirstMessage());

            $flatData['Comment'] = $customer_message_nka;

            $shipment_form->PopulateWithValues($flatData);

            if ($shipment->HasError) {
                foreach (explode("\n", $shipment->ErrorMessage) as $err_msg) {
                    $shipment_form->AddGeneralError($err_msg);
                }
            }
        }

        $shipmentDateField = $shipment_form->Fields['ShipmentDate'];
        $nearestAvailableShipmentDateField = $shipment_form->Fields['NearestAvailableShipmentDate'];
        $currentShipmentDateField = $shipment_form->Fields['CurrentShipmentDate'];
        $shipmentStartHour = $shipment_form->Fields['ShipmentStartHour'];
        $shipmentEndHour = $shipment_form->Fields['ShipmentEndHour'];
        if ($shipment->DhlShipmentId) {
            $shipmentDateField->Value = $shipment->ShipmentDate->format('Y-m-d');
            $nearestAvailableShipmentDateField->Value = $shipment->ShipmentDate->format('Y-m-d');
            $currentShipmentDateField->Value = $shipment->ShipmentDate->format('Y-m-d');
            $shipmentStartHour->Value = $shipment->ShipmentStartHour->format('H:i');
            $shipmentEndHour->Value = $shipment->ShipmentEndHour->format('H:i');
        } else {
            $currentDate = new \DateTime();
            $shipmentDateField->Value = isset($_POST['ShipmentDate']) ? $_POST['ShipmentDate'] : $currentDate->format('Y-m-d');
            $currentShipmentDateField->Value = isset($_POST['CurrentShipmentDate']) ? $_POST['CurrentShipmentDate'] : $currentDate->format('Y-m-d');
            $nearestAvailableShipmentDateField->Value = isset($_POST['ShipmentDate']) ? $_POST['ShipmentDate'] : $currentDate->format('Y-m-d');
        }



        $postalServicesEndpoint = new PostalServices();
        $updatePostalServicesOnLoad = !isset($_POST['ShipmentDate']);
        $this->MakeWrappedOutput(
            Wrappers\ConfigWrapper::Get('FullName') . ' - edycja przesyłki',
            Core\Template::Render(
                'ShipmentEdit',
                [
                    'Form' => $shipment_form,
                    'ViewMode' => $shipment->IsSended(),
                    'Shipment' => $shipment,
                    'DownloadLabel' => $this->downloadLabel,
                    'PostalCodeServices' => $postalServicesEndpoint->GetLink(),
                    'UpdatePostalServicesOnLoad' => $updatePostalServicesOnLoad
                ]
            )
        );
    }



    /**
     * Get current date.
     *
     * @return string
     */
    protected function getCurrentDate()
    {
        $date = new \DateTime();

        return $date->format('Y-m-d');
    }

    protected function setDownloadLabel($shipment)
    {
        $labelType = $shipment->LabelType;
        if (empty($labelType)) {
            $labelType = Wrappers\ConfigWrapper::Get('HasDefaultShipperPreset') ?
                Wrappers\ConfigWrapper::Get('DefaultShipperPreset')->LabelType : null;
        }

        $availableTypes = $shipment->GetAvailableLabelTypes();
        if ($labelType && in_array($labelType, $availableTypes)) {
            $this->downloadLabel = $labelType;
            return;
        }

        $this->downloadLabel = false;
    }

}
