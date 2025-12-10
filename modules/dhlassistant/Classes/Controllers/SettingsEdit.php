<?php

namespace DhlAssistant\Classes\Controllers;

use DhlAssistant\Core;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\Dhl\Enums;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Managers;
use DhlAssistant\Classes\Forms;
use DhlAssistant\Traits;
use DhlAssistant\Wrappers\ConfigWrapper;

class SettingsEdit extends Models\Controller
{
    use Traits\ControllerWrappedOutput;

    /**
     * @inheritDoc
     */
    public function Go()
    {
        $settings_form = new Forms\Settings();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $values = $settings_form->PopulateWithPostValues()->GetValues();

            /* @var $dhl_user DataModels\DhlUser */
            /* @var $shipper_preset DataModels\ShipperPreset */
            if ($settings_form->Validate()) {
                $tree_values = Core\TreeDataHandling::FlatData2TreeData($values);

                if (Wrappers\ConfigWrapper::Get('HasDefaultDhlUser')) {
                    $dhl_user = Wrappers\ConfigWrapper::Get('DefaultDhlUser');
                } else {
                    $dhl_user = new DataModels\DhlUser();
                }

                $set_dhl24_user = (isset($tree_values['DhlUser']['Dhl24Login'])
                        && $tree_values['DhlUser']['Dhl24Login'] != $dhl_user->Dhl24Login)
                    || (isset($tree_values['DhlUser']['Dhl24Password'])
                        && $tree_values['DhlUser']['Dhl24Password']);

                $set_dhlps_user = (isset($tree_values['DhlUser']['DhlPsLogin'])
                        && $tree_values['DhlUser']['DhlPsLogin'] != $dhl_user->DhlPsLogin)
                    || (isset($tree_values['DhlUser']['DhlPsPassword'])
                        && $tree_values['DhlUser']['DhlPsPassword']);

                $set_dhlps_link24 = (isset($tree_values['DhlUser']['Dhl24Link'])
                    && ($tree_values['DhlUser']['Dhl24Link'] != $dhl_user->Dhl24Link));

                $set_dhlps_linkps = (isset($tree_values['DhlUser']['DhlPsLink'])
                    && ($tree_values['DhlUser']['DhlPsLink'] != $dhl_user->DhlPsLink));

                $dhl_user_validation_result = new Models\ValidationResult();

                if ($set_dhl24_user || $set_dhlps_user || $set_dhlps_link24 || $set_dhlps_linkps) {
                    if (!$set_dhl24_user) {
                        unset($tree_values['DhlUser']['Dhl24Login']);
                        unset($tree_values['DhlUser']['Dhl24Password']);
                    }

                    if (!$set_dhlps_user) {
                        unset($tree_values['DhlUser']['DhlPsLogin']);
                        unset($tree_values['DhlUser']['DhlPsPassword']);
                    }

                    if (!$set_dhlps_link24) {
                        unset($tree_values['DhlUser']['Dhl24Link']);
                    }

                    if (!$set_dhlps_linkps) {
                        unset($tree_values['DhlUser']['DhlPsLink']);
                    }

                    $dhl_user->SetTreeDataValues(
                        'PostSet',
                        $tree_values['DhlUser'],
                        true
                    );

                    if ($set_dhl24_user) {
                        if (!Wrappers\DhlWrapper::TestAuthData(
                            Managers\DhlApiManager::GetDhlApiByCode(Enums\DhlWebApi::DHL24),
                            $dhl_user,
                            false
                        )) {
                            $dhl_user_validation_result
                                ->AddError('Dhl24Login', 'Błędne dane logowania')
                                ->AddError('Dhl24Password', 'Błędne dane logowania')
                                ->Fail();
                        }
                    }

                    if ($set_dhlps_user) {
                        if (!Wrappers\DhlWrapper::TestAuthData(
                            Managers\DhlApiManager::GetDhlApiByCode(Enums\DhlWebApi::DHLPS),
                            $dhl_user,
                            false
                        )) {
                            $dhl_user_validation_result
                                ->AddError('DhlPsLogin', 'Błędne dane logowania')
                                ->AddError('DhlPsPassword', 'Błędne dane logowania')
                                ->Fail();
                        }
                    }

                    if ($set_dhlps_link24) {
                        try {
                            !Wrappers\DhlWrapper::TestAuthData(
                                Managers\DhlApiManager::GetDhlApiByCode(Enums\DhlWebApi::DHL24),
                                $dhl_user,
                                false
                            );
                        } catch (Exceptions\SourceLoggedException $exception) {
                            $dhl_user_validation_result
                                ->AddError(
                                    'Dhl24Link',
                                    sprintf('Błędne dane logowania. %s', $exception->getMessage())
                                )->Fail();
                        }
                    }

                    if ($set_dhlps_linkps) {
                        try {
                            !Wrappers\DhlWrapper::TestAuthData(
                                Managers\DhlApiManager::GetDhlApiByCode(Enums\DhlWebApi::DHLPS),
                                $dhl_user,
                                false
                            );
                        } catch (Exceptions\SourceLoggedException $exception) {
                            $dhl_user_validation_result
                                ->AddError(
                                    'DhlPsLink',
                                    sprintf('Błędne dane logowania. %s', $exception->getMessage())
                                )->Fail();
                        }
                    }

                    if ($dhl_user_validation_result->IsSuccess()) {
                        $dhl_user_validation_result->AddGeneralNotice('Dane logowania poprawne');
                    }
                } else {
                    if (!Wrappers\ConfigWrapper::Get('HasDefaultDhlUser')) {
                        $dhl_user_validation_result->AddGeneralError('Należy podać dane logowania do DHL 24 WebAPI i/lub DHL Parcelshop Manager WebAPI')->Fail();
                    }
                }

                if (Wrappers\ConfigWrapper::Get('HasDefaultShipperPreset')) {
                    $shipper_preset = Wrappers\ConfigWrapper::Get('DefaultShipperPreset');
                } else {
                    $shipper_preset = new DataModels\ShipperPreset();
                }

                $shipper_preset->SetTreeDataValues(
                    'PostSet',
                    $tree_values['ShipperPreset'],
                    true
                );

                $shipper_preset_valiadtion_result = $shipper_preset->Validate();

                $settings = new DataModels\Settings();

                if (isset($tree_values['Settings'])) {
                    $settings->SetTreeDataValues('PostSet', $tree_values['Settings']);
                }

                $settings_valiadtion_result = $settings->Validate();

                $valiadtion_result = new Models\ValidationResult();
                $valiadtion_result->Merge($dhl_user_validation_result, 'DhlUser');
                $valiadtion_result->Merge($shipper_preset_valiadtion_result, 'ShipperPreset');
                $valiadtion_result->Merge($settings_valiadtion_result, 'Settings');
                $settings_form = new Forms\Settings();

                if ($valiadtion_result->IsSuccess()) {
                    if (isset($values['Action:Save'])) {
                        $old_configuration_status = ConfigWrapper::Get('IsModuleConfigured');
                        $dhl_user = Wrappers\DbWrapper::Save($dhl_user);
                        $shipper_preset = Wrappers\DbWrapper::Save($shipper_preset);
                        $settings->Save();

                        $settings_form->PopulateWithValues(
                            Core\TreeDataHandling::TreeData2FlatData(
                                [
                                    'DhlUser' => $dhl_user->GetTreeDataValues('PostGet', true),
                                    'ShipperPreset' => $shipper_preset->GetTreeDataValues('PostGet', true),
                                    'Settings' => $settings->GetTreeDataValues('PostGet'),
                                ]
                            )
                        );

                        $settings_form->AddGeneralNotice('Ustawienia zapisane.');

                        if ($old_configuration_status != ConfigWrapper::Get('IsModuleConfigured')) {
                            Wrappers\SourceWrapper::ModuleConfiguredEvent();
                        }
                    } else {
                        throw new Exceptions\LoggedException('Nieznana akcja!');
                    }
                } else {
                    $settings_form->PopulateWithValues(
                        Core\TreeDataHandling::TreeData2FlatData(
                            [
                                'DhlUser' => $dhl_user->GetTreeDataValues('PostGet', true),
                                'ShipperPreset' => $shipper_preset->GetTreeDataValues('PostGet', true),
                                'Settings' => $settings->GetTreeDataValues('PostGet'),
                            ]
                        )
                    );

                    $settings_form->PopulateWithValidationResult($valiadtion_result);
                }
            }

            if ($settings_form->HasError) {
                $settings_form->Fields['DhlUser:Dhl24Password']->Value = '';
                $settings_form->Fields['DhlUser:DhlPsPassword']->Value = '';
            }
        } else {
            if (Wrappers\ConfigWrapper::Get('HasDefaultDhlUser')) {
                $dhl_user = Wrappers\ConfigWrapper::Get('DefaultDhlUser');
            } else {
                $dhl_user = new DataModels\DhlUser();
            }

            if (Wrappers\ConfigWrapper::Get('HasDefaultShipperPreset')) {
                $shipper_preset = Wrappers\ConfigWrapper::Get('DefaultShipperPreset');
            } else {
                $shipper_preset = new DataModels\ShipperPreset();
            }

            $settings = new DataModels\Settings();

            $settings_form->PopulateWithValues(
                Core\TreeDataHandling::TreeData2FlatData(
                    [
                        'DhlUser' => $dhl_user->GetTreeDataValues('PostGet', true),
                        'ShipperPreset' => $shipper_preset->GetTreeDataValues('PostGet', true),
                        'Settings' => $settings->GetTreeDataValues('PostGet'),
                    ]
                )
            );
        }

        $this->MakeWrappedOutput(
            Wrappers\ConfigWrapper::Get('FullName') . ' - edycja ustawień',
            Core\Template::Render('SettingsEdit', ['Form' => $settings_form])
        );
    }
}
